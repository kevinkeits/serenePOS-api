<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function validateAuth($Token)
    {
        if ($Token != null) $Token = trim(str_replace("Bearer","",$Token));
        $return = array('status'=>false,'ID'=>"");
        $query = "SELECT MsUser.ID UserID, MsUser.ClientID, MsUser.Name, MsUser.PhoneNumber, MsUser.Email
                    FROM MsUser
                    JOIN TrSession ON TrSession.UserID = MsUser.ID
                    WHERE TrSession.ID=?
                        AND TrSession.IsLoggedOut=0";
        $checkAuth = DB::select($query,[$Token]);
        if ($checkAuth) {
            $data = $checkAuth[0];
            $return = array(
                'status' => true,
                'UserID' => $data->UserID,
                'ClientID' => $data->ClientID,
            );
        }
        return $return;
    }

    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
                if ($request->ID) {
                    $query = "  SELECT MsProduct.ID id, MsProduct.ProductSKU productSku, MsProduct.Name name, MsCategory.ID categoryId, MsCategory.Name categoryName, MsProduct.Qty qty, MsProduct.Price price, MsProduct.Notes notes, (SELECT CONCAT('http://localhost/serenePOS-api/api/public/uploaded/product/', ImgUrl)) imgUrl, MsProduct.MimeType mimeType
                                    FROM MsProduct
                                    JOIN MsCategory
                                    ON MsProduct.CategoryID = MsCategory.ID
                                    WHERE MsProduct.ID = ?";
                    $product = DB::select($query,[$request->ID])[0];

                    $query = "  SELECT MsVariant.ID variantId, MsVariant.Name name, MsVariant.Type type, MsVariantOption.ID variantOptionId, MsVariantOption.Label label, MsVariantOption.Price price
                                    FROM MsVariant
                                    JOIN MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                                    JOIN MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                                    WHERE MsVariantProduct.ProductID = ?
                                    ORDER BY MsVariant.Name ASC, MsVariantOption.Label ASC";
                    $variant = DB::select($query,[$request->ID]);

                    $return['data'] = array('product'=>$product, 'variant'=>$variant);
                } else {
                    $query = "  SELECT ID id, Name name, Price price, Notes notes, ImgUrl imgUrl
                                    FROM MsProduct
                                    WHERE CategoryID = ?
                                    ORDER BY Name ASC";
                    $data = DB::select($query, [$request->CategoryID]);
                    if ($data) $return['data'] = $data;
                }
            } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }

    // POST PRODUCT
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "add") {

                $base64string = $request->fileData;
                $mime = explode(";base64,", $base64string);
                $mimeType = str_replace('data:', '', $mime[0]);

                $fileData = base64_decode($mime[1]);
                $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/product/';
                $fileName = $request->fileName;
                $filePath = $uploadDirectory . $fileName;

                file_put_contents($filePath, $fileData);

                $query = "SELECT UUID() GenID";
                $productID = DB::select($query)[0]->GenID;
                    $query = "INSERT INTO MsProduct
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Notes, Qty, Price, CategoryID, ProductSKU, ImgUrl, MimeType)
                        VALUES
                        (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    DB::insert($query, [
                        $getAuth['UserID'],
                        $productID,
                        $getAuth['ClientID'],
                        $request->name,
                        $request->notes,
                        $request->qty,
                        $request->price,
                        $request->categoryID,
                        $request->productSKU,
                        $fileName,
                        $mimeType,
                    ]);
                    $return['message'] = "Product successfully created.";
            }
            if ($request->action == "edit") {
                if ($request->fileData != "") {

                    $base64string = $request->fileData;
                    $mime = explode(";base64,", $base64string);
                    $mimeType = str_replace('data:', '', $mime[0]);
                    $fileData = base64_decode($mime[1]);
                    $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/product/';
                    $fileName = $request->fileName;
    
                    $query = "UPDATE MsProduct
                        SET IsDeleted = 0,
                            UserUp = ?,
                            DateUp = NOW(),
                            Name = ?,
                            Notes = ?,
                            Qty = ?,
                            Price = ?,
                            CategoryID = ?,
                            ProductSKU = ?,
                            ImgUrl = ?,
                            MimeType = ?
                        WHERE ID = ?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->name,
                        $request->notes,
                        $request->qty,
                        $request->price,
                        $request->categoryID,
                        $request->productSKU,
                        $fileName,
                        $mimeType,
                        $request->id
                    ]);
                    $return['message'] = "Product successfully modified with image.";
                } else {
                    $query = "UPDATE MsProduct
                        SET IsDeleted = 0,
                            UserUp = ?,
                            DateUp = NOW(),
                            Name = ?,
                            Notes = ?,
                            Qty = ?,
                            Price = ?,
                            CategoryID = ?,
                            ProductSKU = ?
                        WHERE ID = ?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->name,
                        $request->notes,
                        $request->qty,
                        $request->price,
                        $request->categoryID,
                        $request->productSKU,
                        $request->id
                    ]);
                    $return['message'] = "Product successfully modified without image.";
                }
                
                if (str_contains($request->productVariantOptionID,',')) {
                    $productVariantOptionID = explode(',',$request->productVariantOptionID);
                    $variantOptionID = explode(',',$request->variantOptionID);
                    $isSelected = explode(',',$request->isSelected);
                    for ($i=0; $i<count($productVariantOptionID); $i++)
                    {
                        $query = "UPDATE MsProductVariantOption
                        SET UserUp=?,
                            DateUp=NOW(),
                            ProductID=?,
                            VariantOptionID=?,
                            IsSelected=?
                            WHERE ID=?";
                        DB::update($query, [
                            $getAuth['UserID'],
                            $request->id,
                            $variantOptionID[$i],
                            ($isSelected[$i] == "T" ? 1 : 0),
                            $productVariantOptionID[$i]
                        ]);
                    }
                    } else {
                        $query = "UPDATE MsProductVariantOption 
                        SET UserUp=?,
                            DateUp=NOW(),
                            ProductID=?,
                            VariantOptionID=?,
                            IsSelected=?
                            WHERE ID=?";
                        DB::update($query, [
                            $getAuth['UserID'],
                            $request->id,
                            $request->variantOptionID,
                            ($request->isSelected == "T" ? 1 : 0),
                            $request->productVariantOptionID
                        ]);
                    }
                    $return['message'] = "Product successfully modified.";
                }
                if ($request->action == "delete") {
                    if (str_contains($request->id,',')) {
                        $tempID = explode(',',$request->id);
                        foreach ($tempID as $key => $ID) {
                            $query = "UPDATE MsProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                            DB::update($query, [$getAuth['UserID'],$ID]);

                            $query = "DELETE FROM MsVariantProduct WHERE ProductID=?";
                            DB::delete($query, [$ID]);
                        }
                        $return['message'] = "Product successfully deleted";
                    } else {
                        $query = "UPDATE MsProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                        DB::update($query, [$getAuth['UserID'],$request->id]);

                        $query = "DELETE FROM MsVariantProduct WHERE ProductID=?";
                        DB::delete($query, [$request->id]);
                        $return['message'] = "Product successfully deleted";
                    }
                }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST PRODUCT

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

                    $tempFile = 'temp-'.time().$request->data3.'.'.$request->file('inpFile'.$request->_data3)->getClientOriginalExtension();
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
        return response()->json($return, 200);
    }
}