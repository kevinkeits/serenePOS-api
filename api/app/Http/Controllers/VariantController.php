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
   public function getVariant(Request $request)
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
   public function getVariantOption(Request $request)
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
}