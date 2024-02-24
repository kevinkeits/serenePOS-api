<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class UserController extends Controller
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
                $query = "  SELECT MsClient.ImgUrl imgUrl, MsClient.Name storeName, MsUser.Name name, MsUser.PhoneNumber phoneNumber
                                FROM MsClient
                                JOIN MsUser
                                ON MsUser.ClientID = MsClient.ID
                                WHERE ID = ?";
                $details = DB::select($query,[$request->ID])[0];

                $query = "  SELECT MsOutlet.ID, MsOutlet.Name OutletName, MsOutlet.PhoneNumber, MsOutlet.IsPrimary, MsOutlet.Address
                                FROM MsOutlet
                                WHERE ID = ?
                                ORDER BY Name ASC" ;
                $outlet = DB::select($query,[$request->ID]);

                $return['data'] = array('details'=>$details,'outlet'=>$outlet);
           } else {
                $query = "  SELECT MsUser.ID id, MsUser.Name name, MsUser.Email email, MsUser.PhoneNumber phoneNumber, MsOutlet.Name outletName
                                FROM MsUser
                                JOIN MsOutlet
                                ON MsOutlet.ID = MsUser.OutletID
                                WHERE MsUser.ClientID = ?
                                ORDER BY MsUser.Name ASC";
                $data = DB::select($query, [$getAuth['ClientID']]);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
   }
   // END GET VARIANT
    
    // GET USER DETAIL
    public function getSetting(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,''=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT MsUser.ID, MsOutlet.ID OutletID, MsOutlet.Name OutletName, MsUser.Name, MsUser.PhoneNumber, MsUser.Email, MsUser.Password
            FROM MsUser
            JOIN MsOutlet
            ON MsOutlet.ID = MsUser.OutletID
            WHERE MsOutlet.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
          $return['data'] = $data[0];
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"",''=>"doHandlerNotAuthorized()");
    return response()->json($return, 200);
    }
    // END GET USER DETAIL
}