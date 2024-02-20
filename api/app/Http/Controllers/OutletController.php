<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class OutletController extends Controller
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
                $query = "  SELECT MsOutlet.ID, MsOutlet.Name OutletName, MsOutlet.PhoneNumber, MsOutlet.IsPrimary, MsOutlet.Address
                            FROM MsOutlet
                            WHERE ID = ?
                                ORDER BY ID ASC";
                $details = DB::select($query,[$request->ID])[0];

                $return['data'] = array('details'=>$details);
            } else {
                $query = "  SELECT MsOutlet.ID, MsOutlet.Name, MsOutlet.IsPrimary, MsOutlet.Address
                            FROM MsOutlet
                            WHERE IsDeleted=0
                                AND ClientID = ?
                                ORDER BY Name ASC";
                $data = DB::select($query, [$getAuth['ClientID']]);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
   }
    
    // POST OUTLET
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->Action == "add") {

                $query = "SELECT UUID() GenID";
                $OutletID = DB::select($query)[0]->GenID;
                $query = "INSERT INTO MsOutlet
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, PhoneNumber, IsPrimary, Address)
                        VALUES
                        (0, ?, NOW(), ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $OutletID,
                    $getAuth['ClientID'],
                    $request->Name,
                    $request->PhoneNumber,
                    $request->IsPrimary == "T" ? 1 : 0,
                    $request->Address,
                ]);
                $return['message'] = "Outlet successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsOutlet
                SET UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PhoneNumber=?,
                    IsPrimary=?,
                    Address=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->Name,
                    $request->PhoneNumber,
                    $request->IsPrimary == "T" ? 1 : 0,
                    $request->Address,
                    $request->ID
                ]);
                $return['message'] = "Outlet successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsOutlet
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Outlet successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST OUTLET
}