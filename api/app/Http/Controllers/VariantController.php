<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class VariantController extends Controller
{
   
   private function validateAuth($Token)
   {
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
       $getAuth = $this->validateAuth($request->_s);
       if ($getAuth['status']) {
           $mainQuery = "  SELECT ID, Name, Type, 
                           (SELECT COUNT(ProductID)
                               FROM MsVariantProduct
                               WHERE MsVariantProduct.VariantID = MsVariant.ID) Count,
                               (SELECT GROUP_CONCAT(Label SEPARATOR ', ')
                                       FROM MsVariantOption
                                       WHERE MsVariantOption.VariantID = MsVariant.ID 
                                       GROUP BY VariantID) ListLabel
                               FROM MsVariant
                               WHERE {definedFilter}
                               ORDER BY MsVariant.Name ASC";
                           $definedFilter = "1=1";
           if ($getAuth['ClientID'] != "") $definedFilter = "MsVariant.ClientID = '".$getAuth['ClientID']."'";
           if ($request->_i) {
               $definedFilter = "MsVariant.ID=?";
               $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
               $data = DB::select($query,[$request->_i]);
               if ($data) {
                   $query = "SELECT    MsVariant.ID,
                                       MsVariant.Name,
                                       MsVariant.Type,
                                       MsVariantOption.ID VariantOptionID,
                                       MsVariantOption.Label,
                                       MsVariantOption.Price,
                                       MsVariantProduct.ProductID
                               FROM    MsVariant
                               JOIN    MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                               JOIN    MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                               WHERE   MsVariantOption.VariantID = ? 
                               ORDER BY  MsVariant.Name ASC";
                   $selVariant = DB::select($query,[$request->_r]);
                   $arrData = [];
                   if ($selVariant) {
                       foreach ($selVariant as $key => $value) {
                           array_push($arrData,$value->ID);
                       }
                   }
                   $return['data'] = array('header'=>$data[0], 'selVariant'=> $selVariant);
               }
           } else {
               $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
               $data = DB::select($query);
               if ($data) $return['data'] = $data;
           }
       } else $return = array('status'=>false,'message'=>"");
       return response()->json($return, 200);
   }
   // END GET VARIANT

   // GET VARIANT OPTION
   public function getOption(Request $request)
   {
       $return = array('status'=>true,'message'=>"",'data'=>null);
       $getAuth = $this->validateAuth($request->_s);
       if ($getAuth['status']) {
       $query = "SELECT ID, VariantID, Label, Price
           FROM MsVariantOption
           WHERE MsVariantOption.ClientID = ?";
           $data = DB::select($query,[$getAuth['ClientID']]);
        $return['data'] = $data[0];
       if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
   } else $return = array('status'=>false,'message'=>"");
   return response()->json($return, 200);
   }
   // END GET VARIANT OPTION

   // POST VARIANT
   public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Type)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantName,
                    $request->VariantType,
                ]);
                $return['message'] = "Variant successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsVariant
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    Name=?,
                    Type=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantName,
                    $request->ariantType,
                    $request->ID
                ]);
                $return['message'] = "Variant successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsVariant
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Variant successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsVariantOption
                        (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, Label, Price)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantID,
                    $request->Label,
                    $request->Price,
                ]);
                $return['message'] = "Variant Option successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsVariantOption
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    VariantID=?,
                    Label=?,
                    Price=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantID,
                    $request->Label,
                    $request->Price,
                    $request->ID
                ]);
                $return['message'] = "Variant Option successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsVariantOption
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Variant Option successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }
    // END POST VARIANT
}