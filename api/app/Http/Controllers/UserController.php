<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class UserController extends Controller
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
    
    // GET USER
    public function getUser(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT MsUser.ID UserID, MsUser.ClientID, MsUser.Name, MsUser.PhoneNumber, MsUser.Email
                FROM MsUser
                WHERE MsUser.ID=?";
            $data = DB::select($query,[$getAuth['UserID']]);
            $return['data'] = $data[0];
            if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }
    // END GET USER

    // GET USER DETAIL
    public function getUserDetail(Request $request)
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