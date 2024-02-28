<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class VariantController extends Controller
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
   
   // GET VARIANT
   public function get(Request $request)
   {
       $return = array('status'=>true,'message'=>"",'data'=>array());
       $header = $request->header('Authorization');
       $getAuth = $this->validateAuth($header);
       if ($getAuth['status']) {
            if ($request->ID) {
                $query = "  SELECT MsVariant.ID id, MsVariant.Name name, MsVariant.Type type
                                FROM MsVariant
                                WHERE ID = ?";
                $details = DB::select($query,[$request->ID])[0];

                $query = "  SELECT MsVariantOption.ID id, MsVariantOption.Label label, MsVariantOption.Price price
                                FROM MsVariantOption
                                WHERE VariantID = ?
                                ORDER BY ID ASC";
                $options = DB::select($query,[$request->ID]);

                $query = "  SELECT MsProduct.ID id, MsProduct.Name name, MsProduct.ImgUrl imgurl
                                FROM MsVariantProduct
                                JOIN MsProduct ON MsProduct.ID = MsVariantProduct.ProductID
                                WHERE VariantID = ?
                                ORDER BY MsProduct.Name ASC";
                $product = DB::select($query,[$request->ID]);

                $return['data'] = array('details'=>$details,'options'=>$options,'product'=>$product);
           } else {
                $query = "  SELECT ID id, Name name, Type type, 
                                    (SELECT COUNT(ProductID)
                                        FROM MsVariantProduct
                                        WHERE MsVariantProduct.VariantID = MsVariant.ID) count,
                                    (SELECT GROUP_CONCAT(Label SEPARATOR ', ')
                                        FROM MsVariantOption
                                        WHERE MsVariantOption.VariantID = MsVariant.ID 
                                        GROUP BY VariantID) listlabel
                                FROM MsVariant
                                WHERE ClientID = ?
                                ORDER BY Name ASC";
                $data = DB::select($query, [$getAuth['ClientID']]);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
   }
   // END GET VARIANT

   // POST VARIANT
   public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "add") {

                $query = "SELECT UUID() GenID";
                $variantID = DB::select($query)[0]->GenID;
                $query = "INSERT INTO MsVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Type)
                        VALUES
                        (0, ?, NOW(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $variantID,
                    $getAuth['ClientID'],
                    $request->name,
                    $request->type,
                ]);
                
                if (str_contains($request->optionLabel,',')) {
                    $optionLabel = explode(',',$request->optionLabel);
                    $optionPrice = explode(',',$request->optionPrice);
                    for ($i=0; $i<count($optionLabel); $i++)
                    {
                        $query = "INSERT INTO MsVariantOption
                                (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, Label, Price)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $variantID,
                                $optionLabel[$i],
                                $optionPrice[$i],
                            ]);
                    }
                } else {
                    $query = "INSERT INTO MsVariantOption
                                (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, Label, Price)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $variantID,
                                $request->optionLabel,
                                $request->optionPrice
                            ]);
                }

                if (str_contains($request->productID,',')) {
                    $productID = explode(',',$request->productID);
                    for ($i=0; $i<count($productID); $i++)
                    {
                        $query = "INSERT INTO MsVariantProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, ProductID)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $variantID,
                                $productID[$i],
                            ]);
                    }
                } else {
                    $query = "INSERT INTO MsVariantProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, ProductID)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $variantID,
                                $request->productID
                            ]);
                }
                $return['message'] = "Variant successfully created.";
            }

            if ($request->action == "edit") {
                $query = "  UPDATE MsVariant
                            SET UserUp=?,
                                DateUp=NOW(),
                                Name=?,
                                Type=?
                                WHERE ID=?";
                        DB::update($query, [
                            $getAuth['UserID'],
                            $request->name,
                            $request->type,
                            $request->id
                        ]);
                if (str_contains($request->optionID,',')) {
                    $optionID = explode(',',$request->optionID);
                    $optionLabel = explode(',',$request->optionLabel);
                    $optionPrice = explode(',',$request->optionPrice);
                    for ($i=0; $i<count($optionID); $i++)
                    {
                        if ($optionID[$i] == "") {
                            $query = "INSERT INTO MsVariantOption
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $request->id,
                                $optionLabel[$i],
                                $optionPrice[$i],
                            ]);
                        } else {
                            $query = "UPDATE MsVariantOption 
                            SET UserUp=?,
                                DateUp=NOW(),
                                Label=?,
                                Price=?
                                WHERE ID=?";
                            DB::update($query, [
                                $getAuth['UserID'],
                                $optionLabel[$i],
                                $optionPrice[$i],
                                $optionID[$i]
                            ]);
                        }
                        $return['message'] = "Variant successfully modified.";
                    }
                } else {
                    if ($request->optionID == "") {
                        $query = "INSERT INTO MsVariantOption
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $request->id,
                                $request->optionLabel,
                                $request->optionPrice
                            ]);
                    } else {
                        $query = "UPDATE MsVariantOption 
                        SET UserUp=?,
                            DateUp=NOW(),
                            Label=?,
                            Price=?
                            WHERE ID=?";
                        DB::update($query, [
                            $getAuth['UserID'],
                            $request->optionLabel,
                            $request->optionPrice,
                            $request->optionID
                        ]);
                    }
                    $return['message'] = "Variant successfully modified.";
                }

                if (str_contains($request->optionIDDelete,',')) {
                    $optionIDDelete = explode(',',$request->optionIDDelete);
                    for ($i=0; $i<count($optionIDDelete); $i++)
                    {
                        $query = "DELETE FROM MsVariantOption WHERE ID=?";
                        DB::delete($query, [$optionIDDelete[$i]]);
                        $return['message'] = "Variant successfully deleted.";
                    }
                } else {
                    $query = "DELETE FROM MsVariantOption WHERE ID=?";
                    DB::delete($query, [$request->optionIDDelete]);
                    $return['message'] = "Variant successfully deleted.";
                }

                $query = "DELETE FROM MsVariantProduct WHERE VariantID=?";
                DB::delete($query, [$request->id]);
                if (str_contains($request->productID,',')) {
                    $productID = explode(',',$request->productID);
                    for ($i=0; $i<count($productID); $i++)
                    {
                        $query = "INSERT INTO MsVariantProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, ProductID)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?)";
                        DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $request->id,
                                $productID[$i],
                        ]);
                    }
                } else {
                    $query = "INSERT INTO MsVariantProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, ProductID)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?)";
                        DB::insert($query, [
                                $getAuth['UserID'],
                                $getAuth['ClientID'],
                                $request->id,
                                $request->productID,
                        ]);
                }
                $return['message'] = "Variant successfully modified.";
            }
            if ($request->action == "delete") {
                if (str_contains($request->id,',')) {
                    $tempID = explode(',',$request->id);
                    foreach ($tempID as $key => $ID) {
                        $query = "UPDATE MsVariant SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                        DB::update($query, [$getAuth['UserID'],$ID]);
                        $return['message'] = "Variant successfully deleted";
                    }
                } else {
                    $query = "UPDATE MsVariant SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                    DB::update($query, [$getAuth['UserID'],$request->id]);
                    $return['message'] = "Variant successfully deleted";

                    $query = "UPDATE MsVariantOption SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE VariantID=?";
                    DB::update($query, [$getAuth['UserID'],$request->variantID]);
    
                    $query = "UPDATE MsVariantProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE VariantID=?";
                    DB::update($query, [$getAuth['UserID'],$request->variantID]);
                    $return['message'] = "variant successfully deleted";
                }
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
}