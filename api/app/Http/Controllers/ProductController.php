<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    private function validateAuth($Token) {
        $return = array('status'=>false,'UserID'=>"");
        $query = "SELECT u.ID, u.AccountType, b.BranchID
                    FROM MS_USER u
                        JOIN TR_SESSION s ON s.UserID = u.ID 
                        LEFT JOIN MS_BRANCH_ADMIN b ON b.UserID = u.ID
                    WHERE s.Token=?
                        AND s.LogoutDate IS NULL";
        $checkAuth = DB::select($query,[$Token]);
        if ($checkAuth) {
            $data = $checkAuth[0];
            $arrBranch = "";
            if ($data->BranchID) {
                foreach ($checkAuth as $key => $value) {
                    if ($arrBranch != "") $arrBranch .= ",";
                    $arrBranch .= "'".$value->BranchID."'";
                }
            }
            $query = "UPDATE TR_SESSION SET LastActive=NOW() WHERE Token=?";
            DB::update($query,[$Token]);
            $return = array(
                'status' => true,
                'UserID' => $data->ID,
                'AccountType' => $data->AccountType,
                'BranchAuth' => $arrBranch
            );
        }
        return $return;
    }

    public function getAll(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "  SELECT      p.ID,
                                        p.Code,
                                        p.Name,
                                        p.Description,
                                        p.Photo,
                                        p.BranchID,
                                        b.Name as BranchName,
                                        p.Weight,
                                        p.Stock,
                                        p.Status
                            FROM        MS_PRODUCT p
                            LEFT JOIN   MS_BRANCH b on b.ID = p.BranchID
                            WHERE {definedFilter}
                            ORDER BY p.Name ASC";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "p.BranchID IN (".$getAuth['BranchAuth'].")";
            if ($request->_i) {
                $definedFilter = "p.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT    p.CategoryID as ID
                                FROM    MS_PRODUCT_CATEGORY p
                                JOIN    MS_CATEGORY c on c.ID = p.CategoryID
                                WHERE   p.ProductID = ?
                                ORDER BY  c.Name ASC";
                    $selBranch = DB::select($query,[$request->_i]);
                    $arrData = [];
                    if ($selBranch) {
                        foreach ($selBranch as $key => $value) {
                            array_push($arrData,$value->ID);
                        }
                    }
                    $return['data'] = array('header'=>$data[0], 'selCategory'=>$arrData);
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getPriceDetail(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "
                    SELECT    ID,
                              MinOrder,
                              MaxOrder,
                              Price
                    FROM      MS_PRODUCT_PRICE
                    WHERE     ProductID=?
                    AND       BranchID=?";
            $return['data'] = DB::select($query,[$request->_p,$request->branchid]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getImageProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if($request->_i) {
                $query = "  SELECT  ID,
                            ImagePath,
                            IsMain,
                            SequenceNo
                    FROM    MS_PRODUCT_IMAGE
                    WHERE   ID=?";
                $return['data'] = DB::select($query,[$request->_i]);
            } else {
                $query = "  SELECT  ID,
                            ImagePath,
                            IsMain,
                            SequenceNo
                    FROM    MS_PRODUCT_IMAGE
                    WHERE   ProductID=?";
                $return['data'] = DB::select($query,[$request->_p]);
            }
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {

            // Start Generate imgID
            $imgID1 = "";
            $imgID2 = "";
            $imgID3 = "";
            $imgID4 = "";
            
            if ($request->inpFileID_1=="") {
                $query = "SELECT UUID() GenID";
                $imgID1 = DB::select($query)[0]->GenID;
            } else {
                $imgID1 = $request->inpFileID_1;
            }
            if ($request->inpFileID_2=="") {
                $query = "SELECT UUID() GenID";
                $imgID2 = DB::select($query)[0]->GenID;
            } else {
                $imgID2 = $request->inpFileID_2;
            }
            if ($request->inpFileID_3=="") {
                $query = "SELECT UUID() GenID";
                $imgID3 = DB::select($query)[0]->GenID;
            } else {
                $imgID3 = $request->inpFileID_3;
            }
            if ($request->inpFileID_4=="") {
                $query = "SELECT UUID() GenID";
                $imgID4 = DB::select($query)[0]->GenID;
            } else {
                $imgID4 = $request->inpFileID_4;
            }
            // End Generate imgID

            if ($request->hdnFrmAction=="add") {
                $query = "SELECT UUID() GenID";
                $ID = DB::select($query)[0]->GenID;
                $query = "SELECT ID FROM MS_PRODUCT WHERE Code=?";
                $isExist = DB::select($query, [$request->txtFrmCode]);
                if (!$isExist) {
                    if ($request->hdnAttached !== "") {
                        $query = "INSERT INTO MS_PRODUCT
                                    (ID, Code, Name, Description, Photo, BranchID, Weight, Stock, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
                        DB::insert($query, [
                            $ID,
                            $request->txtFrmCode,
                            $request->txtFrmName,
                            $request->txtFrmDesc,
                            $request->txtFrmPhoto,
                            $request->selFrmBranch,
                            $request->txtFrmWeight,
                            $request->txtFrmStock,
                            $request->radFrmStatus==1 ? 1 : 0,
                            $getAuth['UserID']
                        ]);

                        if ($request->minOrder) {
                            if (count($request->minOrder) > 0) {
                                $i=0;
                                foreach ($request->minOrder as $key => $value) {
                                    $query = "INSERT INTO MS_PRODUCT_PRICE (ID, ProductID, BranchID, MinOrder, MaxOrder, Price) 
                                    VALUES (UUID(), ?, ? ,?, ?, ?)";
                                    DB::insert($query, [
                                        $ID,
                                        $request->selFrmBranch,
                                        $request->minOrder[$i],
                                        $request->maxOrder[$i],
                                        $request->priceOrder[$i]
                                    ]);
                                    $i++;
                                }
                            }
                        }

                        if ($request->selFrmProductCategory) {
                            if (count($request->selFrmProductCategory) > 0) {
                                foreach ($request->selFrmProductCategory as $key => $value) {
                                    $query = "INSERT INTO MS_PRODUCT_CATEGORY (ID, ProductID, CategoryID) 
                                    VALUES (UUID(), ?, ?)";
                                    DB::insert($query, [
                                        $ID,
                                        $value
                                    ]);
                                }
                            }
                        }

                        $return['message'] = "Data berhasil tersimpan!";
						$return['callback'] = "execUpload('".$ID."','".$imgID1."','".$imgID2."','".$imgID3."','".$imgID4."')";
					} else {
                        $return['status'] = false;
						$return['message'] = "Mohon untuk pilih gambar terlebih dahulu!";
					}

                } else {
                    $return['status'] = false;
                    $return['message'] = "SKU sudah terdaftar";
                }
            }
            if ($request->hdnFrmAction=="edit") {
                $query = "SELECT ID FROM MS_PRODUCT WHERE Code=? AND ID!=?";
                $isExist = DB::select($query, [$request->txtFrmCode, $request->hdnFrmID]);
                if (!$isExist) {
                    if ($request->hdnAttached !== "") {
                        $query = "UPDATE MS_PRODUCT
                                    SET Name=?, 
                                        Description=?,
                                        Photo=?,
                                        BranchID=?,
                                        Weight=?,
                                        Stock=?,
                                        Status=?,
                                        ModifiedDate=NOW(), 
                                        ModifiedBy=?
                                    WHERE ID=?";
                        DB::update($query, [
                            $request->txtFrmName,
                            $request->txtFrmDesc,
                            $request->txtFrmPhoto,
                            $request->selFrmBranch,
                            $request->txtFrmWeight,
                            $request->txtFrmStock,
                            $request->radFrmStatus==1 ? 1 : 0,
                            $getAuth['UserID'],
                            $request->hdnFrmID
                        ]);

                        $query = "DELETE FROM MS_PRODUCT_PRICE WHERE ProductID=?";
                        DB::delete($query, [$request->hdnFrmID, $request->selFrmBranch]);
                        
                        if ($request->minOrder) {
                            if (count($request->minOrder) > 0) {
                                $i=0;
                                foreach ($request->minOrder as $key => $value) {
                                    $query = "INSERT INTO MS_PRODUCT_PRICE (ID, ProductID, BranchID, MinOrder, MaxOrder, Price) 
                                    VALUES (UUID(), ?, ?, ?, ?, ?)";
                                    DB::insert($query, [
                                        $request->hdnFrmID,
                                        $request->selFrmBranch,
                                        $request->minOrder[$i],
                                        $request->maxOrder[$i],
                                        $request->priceOrder[$i]
                                    ]);
                                    $i++;
                                }
                            }
                        }
                        $query = "DELETE FROM MS_PRODUCT_CATEGORY WHERE ProductID=?";
                        DB::delete($query, [$request->hdnFrmID]);
                        if ($request->selFrmProductCategory) {
                            if (count($request->selFrmProductCategory) > 0) {
                                foreach ($request->selFrmProductCategory as $key => $value) {
                                    $query = "INSERT INTO MS_PRODUCT_CATEGORY (ID, ProductID, CategoryID) 
                                    VALUES (UUID(), ?, ?)";
                                    DB::insert($query, [
                                        $request->hdnFrmID,
                                        $value
                                    ]);
                                }
                            }
                        }

                        $return['message'] = "Data berhasil tersimpan!";
                        $return['callback'] = "execUpload('".$request->hdnFrmID."','".$imgID1."','".$imgID2."','".$imgID3."','".$imgID4."')";

                    } else {
                        $query = "SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=?";
                        $isExist = DB::select($query, [$request->hdnFrmID]);
                        if ($isExist[0]->ImagePath != '' && $isExist[0]->ImagePath != null) {
                            $query = "UPDATE MS_PRODUCT
                                        SET Name=?, 
                                            Description=?,
                                            Photo=?,
                                            BranchID=?,
                                            Weight=?,
                                            Stock=?,
                                            Status=?,
                                            ModifiedDate=NOW(), 
                                            ModifiedBy=?
                                        WHERE ID=?";
                            DB::update($query, [
                                $request->txtFrmName,
                                $request->txtFrmDesc,
                                $request->txtFrmPhoto,
                                $request->selFrmBranch,
                                $request->txtFrmWeight,
                                $request->txtFrmStock,
                                $request->radFrmStatus==1 ? 1 : 0,
                                $getAuth['UserID'],
                                $request->hdnFrmID
                            ]);

                            $query = "DELETE FROM MS_PRODUCT_PRICE WHERE ProductID=?";
                            DB::delete($query, [$request->hdnFrmID, $request->selFrmBranch]);
                            
                            if ($request->minOrder) {
                                if (count($request->minOrder) > 0) {
                                    $i=0;
                                    foreach ($request->minOrder as $key => $value) {
                                        $query = "INSERT INTO MS_PRODUCT_PRICE (ID, ProductID, BranchID, MinOrder, MaxOrder, Price) 
                                        VALUES (UUID(), ?, ?, ?, ?, ?)";
                                        DB::insert($query, [
                                            $request->hdnFrmID,
                                            $request->selFrmBranch,
                                            $request->minOrder[$i],
                                            $request->maxOrder[$i],
                                            $request->priceOrder[$i]
                                        ]);
                                        $i++;
                                    }
                                }
                            }
                            $query = "DELETE FROM MS_PRODUCT_CATEGORY WHERE ProductID=?";
                            DB::delete($query, [$request->hdnFrmID]);
                            if ($request->selFrmProductCategory) {
                                if (count($request->selFrmProductCategory) > 0) {
                                    foreach ($request->selFrmProductCategory as $key => $value) {
                                        $query = "INSERT INTO MS_PRODUCT_CATEGORY (ID, ProductID, CategoryID) 
                                        VALUES (UUID(), ?, ?)";
                                        DB::insert($query, [
                                            $request->hdnFrmID,
                                            $value
                                        ]);
                                    }
                                }
                            }
                            
                            //$lstImage = explode("|",$request->hdnAttached);
                            //$return['callback'] = "execUpload('".$request->hdnFrmID."','".$imgID1."','".$imgID2."','".$imgID3."','".$imgID4."')";
                            $return['message'] = "Data berhasil tersimpan!";
                            $return['callback'] = "doReloadTable()";
                        } else {
                            $return['status'] = false;
						    $return['message'] = "Mohon untuk pilih gambar terlebih dahulu!";
                        }
                    }
                } else {
                    $return['status'] = false;
                    $return['message'] = "SKU sudah terdaftar";
                }
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doUpload(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->file('inpFile_'.$request->_data3)->isValid()) {
                try
                {
                    $query = "SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ID=?";
                    $isExist = DB::select($query, [$request->_data2]);
                    if ($isExist) {
                        if(file_exists(base_path('public/uploaded/product').$isExist[0]->ImagePath)) {
                            unlink(base_path('public/uploaded/product').$isExist[0]->ImagePath);
                        }
                    }

                    $tempFile = 'temp-'.time().$request->_data3.'.'.$request->file('inpFile_'.$request->_data3)->getClientOriginalExtension();
                    $request->file('inpFile_'.$request->_data3)->move(base_path('public/uploaded/product'), $tempFile);
                    
                    if ($isExist) {
                        $query = "UPDATE MS_PRODUCT_IMAGE
                                SET ImagePath=?, 
                                    SequenceNo=?
                                WHERE ID=?";
                        DB::update($query, [
                            $tempFile,
                            $request->_data3 == 1 ? 1 : 0,
                            $request->_data3,
                            $request->_data2
                        ]);
                    } else {
                        $query = "INSERT INTO MS_PRODUCT_IMAGE (ID, ProductID, ImagePath, IsMain, SequenceNo)
                                VALUES (?, ?, ?, ?, ?)";
                        DB::insert($query, [
                            $request->_data2,
                            $request->_data1,
                            $tempFile,
                            $request->_data3 == 1 ? 1 : 0,
                            $request->_data3
                        ]);
                    }

                    $return['status'] = true;
                    //$return['message'] = "Data berhasil tersimpan!";
                    //$return['callback'] = "doReloadTable()";
                } catch(Exception $e) {
                    $return['status'] = false;
                    $return['message'] = $e->getMessage();
                }
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    /*
    public function doFinish(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                $query = "UPDATE MS_PRODUCT_IMAGE
                        SET IsMain=1
                        WHERE ID=?";
                DB::update($query, [
                    $request->_i
                ]);
            }

            $return['status'] = true;
            $return['message'] = "Data berhasil tersimpan!";
            $return['callback'] = "doReloadTable()";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }
    */

    public function doDelete(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID FROM MS_PRODUCT WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                try
                {
                    $query = "SELECT ID FROM TR_ORDER_PRODUCT WHERE ProductID=?";
                    $isExistTrans = DB::select($query, [$request->_i]);

                    $query = "SELECT ID FROM TR_CART WHERE ProductID=?";
                    $isExistCart = DB::select($query, [$request->_i]);
                    if ($isExistTrans || $isExistCart) {
                        $return['status'] = false;
                        $return['message'] = "Produk yang sudah dibuat dalam transaksi tidak dapat dihapus";
                    } else {
                        $query = "SELECT ID, ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=?";
                        $isImgExist = DB::select($query, [$request->_i]);
                        for ($i=0; $i <= count($isImgExist)-1; $i++) {
                            if ($isImgExist[$i]->ImagePath != '' && $isImgExist[$i]->ImagePath != null) {
                                if (file_exists(base_path('public/uploaded/product').$isImgExist[$i]->ImagePath)) {
                                    unlink(base_path('public/uploaded/product').$isImgExist[$i]->ImagePath);
                                }
                            }
                        }
    
                        
                        $query = "DELETE FROM MS_DISCOUNT WHERE ProductID=?";
                        DB::delete($query, [$request->_i]);
                        
                        $query = "DELETE FROM MS_PRODUCT_IMAGE WHERE ProductID=?";
                        DB::delete($query, [$request->_i]);
    
                        $query = "DELETE FROM MS_PRODUCT_PRICE WHERE ProductID=?";
                        DB::delete($query, [$request->_i]);
    
                        $query = "DELETE FROM MS_PRODUCT_CATEGORY WHERE ProductID=?";
                        DB::delete($query, [$request->_i]);
                        
                        $query = "DELETE FROM MS_PRODUCT WHERE ID=?";
                        DB::delete($query, [$request->_i]);
    
                        $return['message'] = "Data berhasil dihapus!";
                        $return['callback'] = "doReloadTable()";
                    }
                } catch(Exception $e) {
                    $return['status'] = false;
                    $return['message'] = $e->getMessage();
                }
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doDeletePic(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID, ImagePath FROM MS_PRODUCT_IMAGE WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                try
                {
                    if ($isExist[0]->ImagePath != '' && $isExist[0]->ImagePath != null) {
                        if (file_exists(base_path('public/uploaded/product').$isExist[0]->ImagePath)) {
                            unlink(base_path('public/uploaded/product').$isExist[0]->ImagePath);
                        }
                    }
                    $query = "DELETE FROM MS_PRODUCT_IMAGE WHERE ID=?";
                    DB::delete($query, [$request->_i]);
                    $return['message'] = "Data berhasil dihapus!";
                    $return['callback'] = "doReloadTable()";
                } catch(Exception $e) {
                    $return['status'] = false;
                    $return['message'] = $e->getMessage();
                }
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

}