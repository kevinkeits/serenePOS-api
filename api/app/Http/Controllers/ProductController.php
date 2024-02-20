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
                $query = "  SELECT MsProduct.ID, MsProduct.ProductSKU, MsProduct.Name, MsCategory.ID CategoryID, MsCategory.Name CategoryName, MsProduct.Qty, MsProduct.Price, MsProduct.Notes, MsProduct.ImgUrl, MsProduct.MimeType 
                                FROM MsProduct
                                JOIN MsCategory
                                ON MsProduct.CategoryID = MsCategory.ID
                                WHERE MsProduct.ID = ?";
                $product = DB::select($query,[$request->ID])[0];

                $query = "  SELECT MsVariant.ID VariantID, MsVariant.Name, MsVariant.Type, MsVariantOption.ID VariantOptionID, MsVariantOption.Label, MsVariantOption.Price
                                FROM MsVariant
                                JOIN MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                                JOIN MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                                WHERE MsVariantProduct.ProductID = ?
                                ORDER BY MsVariant.Name ASC, MsVariantOption.Label ASC";
                $variant = DB::select($query,[$request->ID]);

                $return['data'] = array('product'=>$product, 'variant'=>$variant);
            } else {
                $query = "  SELECT ID, Name, Price, Notes, ImgUrl
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
            if ($request->Action == "add") {
                $query = "SELECT UUID() GenID";
                $ProductID = DB::select($query)[0]->GenID;

                $query = "INSERT INTO MsProduct
                    (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Notes, Qty, Price, CategoryID, ProductSKU, ImgUrl, MimeType)
                    VALUES
                    (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $ProductID,
                    $getAuth['ClientID'],
                    $request->Name,
                    $request->Notes,
                    $request->Qty,
                    $request->Price,
                    $request->CategoryID,
                    $request->ProductSKU,
                    $request->ImgUrl,
                    $request->FileData,
                    $request->FileName,
                    $request->MimeType,
                ]);
                $return['message'] = "Product successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsProduct
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    Notes=?,
                    Qty=?,
                    Price=?,
                    CategoryID=?,
                    ProductSKU=?,
                    ImgUrl=?,/
                    MimeType=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->Name,
                    $request->Notes,
                    $request->Qty,
                    $request->Price,
                    $request->CategoryID,
                    $request->ProductSKU,
                    $request->FileName,
                    $request->MimeType,
                    $request->ID
                ]);
               
                if (str_contains($request->ProductVariantOptionID,',')) {
                    $ProductVariantOptionID = explode(',',$request->ProductVariantOptionID);
                    $VariantOptionID = explode(',',$request->VariantOptionID);
                    $IsSelected = explode(',',$request->IsSelected);
                    for ($i=0; $i<count($ProductVariantOptionID); $i++)
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
                                $request->ID,
                                $VariantOptionID[$i],
                                ($IsSelected[$i] == "T" ? "1" : "0"),
                                $ProductVariantOptionID[$i],
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
                            $request->ID,
                            $request->VariantOptionID,
                            ($request->IsSelected == "T" ? 1 : 0),
                            $request->ProductVariantOptionID
                        ]);
                    }
                    $return['message'] = "Product successfully Changed";
                }
                if ($request->Action == "delete") {
                    if (str_contains($request->ID,',')) {
                        $tempID = explode(',',$request->ID);
                        foreach ($tempID as $key => $ID) {
                            $query = "UPDATE MsProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                            DB::update($query, [$getAuth['UserID'],$ID]);

                            $query = "DELETE FROM MsVariantProduct WHERE ProductID=?";
                            DB::delete($query, [$ID]);
                        }
                        $return['message'] = "Product successfully deleted";
                    } else {
                        $query = "UPDATE MsProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                        DB::update($query, [$getAuth['UserID'],$request->ID]);

                        $query = "DELETE FROM MsVariantProduct WHERE ProductID=?";
                        DB::delete($query, [$request->ID]);
                        $return['message'] = "Product successfully deleted";
                    }
                }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
}