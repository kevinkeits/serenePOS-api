<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ClientController extends Controller
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
    
   // GET CLIENT
   public function get(Request $request)
   {
       $return = array('status'=>true,'message'=>"",'data'=>null);
       $query = "SELECT MsClient.ID, MsOutlet.Address, MsClient.Name, MsOutlet.PhoneNumber, MsClient.PlanType, MsClient.Message, MsClient.ImgUrl, MsClient.MimeType, MsOutlet.Name OutlatName, MsOutlet.IsPrimary
           FROM MsClient
           JOIN MsOutlet
           ON MsOutlet.ID = MsClient.OutletID
           ORDER BY ID ASC";
       $return['data'] = DB::select($query);
       if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
       return response()->json($return, 200);
   }
   // END GET CLIENT

   // POST CLIENT
   public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsClient
                        (IsDeleted, UserIn, DateIn, ID, Name, PlanType, ImgUrl, MimeType)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $request->ClientName,
                    $request->PlanType,
                    $request->ImgUrl,
                    $request->MimeType,
                ]);
                $return['message'] = "Client successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsClient
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PlanType=?,
                    ImgUrl=?,
                    MimeType=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->ClientName,
                    $request->PlanType,
                    $request->ImgUrl,
                    $request->MimeType,
                    $request->ID
                ]);
                $return['message'] = "Client successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsClient
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Client successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }
    // END POST CLIENT
}