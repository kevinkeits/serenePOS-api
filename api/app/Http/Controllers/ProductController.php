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
                    $query = "  SELECT MsProduct.ID id, MsProduct.ProductSKU productSKU, MsProduct.Name name, MsCategory.ID categoryID, MsCategory.Name categoryName, MsProduct.Qty qty, MsProduct.Price price, MsProduct.Notes notes,CASE ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('https://serenepos.temandigital.id/api/uploaded/product/', ImgUrl)) END imgUrl, MsProduct.MimeType mimeType
                                    FROM MsProduct
                                    JOIN MsCategory
                                    ON MsProduct.CategoryID = MsCategory.ID
                                    WHERE MsProduct.IsDeleted=0 AND MsCategory.IsDeleted=0 AND MsProduct.ClientID=? AND MsProduct.ID = ?";
                    $product = DB::select($query,[$getAuth['ClientID'],$request->ID])[0];

                    $query = "  SELECT IFNULL(MsProductVariantOption.ID,'') productVariantOptionID, 
                                    CASE WHEN IFNULL(MsProductVariantOption.IsSelected,0) = 1 THEN 'T' ELSE 'F' END isSelected, 
                                    MsVariant.ID variantID, 
                                    MsVariant.Name name, 
                                    MsVariant.Type type, 
                                    MsVariantOption.ID variantOptionID, 
                                    MsVariantOption.Label label, 
                                    MsVariantOption.Price price
                                    FROM MsVariant
                                    JOIN MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                                    JOIN MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                                    LEFT JOIN MsProductVariantOption on (MsProductVariantOption.VariantOptionID = MsVariantOption.ID AND MsProductVariantOption.ProductID = MsVariantProduct.ProductID)
                                    WHERE MsVariant.IsDeleted=0 AND MsVariant.ClientID=? AND MsVariantProduct.ProductID = ?
                                    ORDER BY MsVariant.Name ASC, MsVariantOption.Label ASC";
                    $variant = DB::select($query,[$getAuth['ClientID'],$request->ID]);

                    $return['data'] = array('product'=>$product, 'variant'=>$variant);
                } else {
                    if ($request->CategoryID != '') {
                        $query = "  SELECT ID id, Name name, Price price, Qty qty, Notes notes, CASE ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('https://serenepos.temandigital.id/api/uploaded/product/', ImgUrl)) END imgUrl
                                        FROM MsProduct
                                        WHERE IsDeleted=0 AND ClientID=? AND CategoryID = ?
                                        ORDER BY Name ASC";
                        $data = DB::select($query, [$getAuth['ClientID'],$request->CategoryID]);
                        if ($data) $return['data'] = $data;
                    } else {
                        $query = "  SELECT ID id, Name name, Price price, Qty qty, Notes notes, CASE ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('https://serenepos.temandigital.id/api/uploaded/product/', ImgUrl)) END imgUrl
                                        FROM MsProduct
                                        WHERE IsDeleted=0 AND ClientID=?
                                        ORDER BY Name ASC";
                        $data = DB::select($query,[$getAuth['ClientID']]);
                        if ($data) $return['data'] = $data;
                    }
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

                $fileName = "";
                $mimeType = "";

                if ($request->fileName != "") {
                    $base64string = $request->fileData;
                    $mime = explode(";base64,", $base64string);
                    $mimeType = str_replace('data:', '', $mime[0]);


                    $fileData = base64_decode($mime[1]);
                    //$uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/product/';
                    $uploadDirectory = base_path('public/uploaded/product');
                    $fileName = $request->fileName;
                    //$fileExt = explode(".", $fileName)[count(explode(".", $fileName))-1];
                    $filePath = $uploadDirectory . $fileName;
                    file_put_contents($filePath, $fileData);
                }

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
                    //$uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/product/';
                    $uploadDirectory = base_path('public/uploaded/product/');
                    $fileName = $request->fileName;
                    //$fileExt = explode(".", $fileName)[count(explode(".", $fileName))-1];
                    $filePath = $uploadDirectory . $fileName;
                    file_put_contents($filePath, $fileData);
    
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
                        WHERE ClientID=? AND ID = ?";
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
                        $getAuth['ClientID'],
                        $request->id
                    ]);
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
                        WHERE ClientID=? AND ID = ?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->name,
                        $request->notes,
                        $request->qty,
                        $request->price,
                        $request->categoryID,
                        $request->productSKU,
                        $getAuth['ClientID'],
                        $request->id
                    ]);
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
                                    WHERE ClientID=? AND ID=?";
                                DB::update($query, [
                                    $getAuth['UserID'],
                                    $request->id,
                                    $variantOptionID[$i],
                                    ($isSelected[$i] == "T" ? 1 : 0),
                                    $getAuth['ClientID'],
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
                                WHERE ClientID=? AND ID=?";
                            DB::update($query, [
                                $getAuth['UserID'],
                                $request->id,
                                $request->variantOptionID,
                                ($request->isSelected == "T" ? 1 : 0),
                                $getAuth['ClientID'],
                                $request->productVariantOptionID
                            ]);
                }
                $return['message'] = "Product successfully modified.";
            }
            if ($request->action == "delete") {
                if (str_contains($request->id,',')) {
                    $tempID = explode(',',$request->id);
                    foreach ($tempID as $key => $ID) {
                        $query = "UPDATE MsProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ClientID=? AND ID=?";
                        DB::update($query, [$getAuth['UserID'],$getAuth['ClientID'],$ID]);

                        $query = "DELETE FROM MsVariantProduct WHERE ClientID=? AND  ProductID=?";
                        DB::delete($query, [$getAuth['ClientID'],$ID]);
                    }
                    $return['message'] = "Product successfully deleted";
                } else {
                    $query = "UPDATE MsProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ClientID=? AND ID=?";
                    DB::update($query, [$getAuth['UserID'],$getAuth['ClientID'],$request->id]);

                    $query = "DELETE FROM MsVariantProduct WHERE ClientID=? AND ProductID=?";
                    DB::delete($query, [$getAuth['ClientID'],$request->id]);
                    $return['message'] = "Product successfully deleted";
                }
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST PRODUCT
}