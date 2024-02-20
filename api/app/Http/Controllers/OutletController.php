<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class OutletController extends Controller
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

//     // GET CATEGORY
//     public function get(Request $request)
//     {
//         $return = array('status'=>true,'message'=>"",'data'=>null);
//         $header = $request->header('Authorization');
//         $getAuth = $this->validateAuth($header);
//         if ($getAuth['status']) {
//                 $query = "SELECT ID, Name, QtyAlert, BGColor
//                     FROM MsCategory
//                     WHERE IsDeleted=0
//                         AND ClientID = ?"; 
//                 if ($request->ID) {
//                     $query .= " AND ID = ? ";
//                     $return['data'] = DB::select($query,[$getAuth['ClientID'], $request->ID])[0];
//                 } else {
//                     $query .= " ORDER BY Name ASC";
//                     $return['data'] = DB::select($query,[$getAuth['ClientID']]);
//                 }
//             } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
//             return response()->json($return, 200);
//     }
//    // END GET CATEGORY
    
    // GET OUTLET
    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            $query = "SELECT MsOutlet.ID, MsOutlet.ClientID, MsOutlet.Name, MsOutlet.PhoneNumber, MsOutlet.IsPrimary, MsOutlet.Address
                FROM MsOutlet
                WHERE IsDeleted=0
                    AND ClientID = ?";
                 if ($request->ID) {
                    $query .= " AND ID = ? ";
                    $return['data'] = DB::select($query,[$getAuth['ClientID'], $request->ID])[0];
                } else {
                    $query .= " ORDER BY Name ASC";
                    $return['data'] = DB::select($query,[$getAuth['ClientID']]);
                }
            } else $return = array('status'=>false,'message'=>"");
            return response()->json($return, 200);
    }
    // END GET OUTLET

    // POST OUTLET
    public function doSave(Request $request)
        {
            $return = array('status'=>true,'message'=>"",'data'=>null);
            $getAuth = $this->validateAuth($request->_s);
            if ($getAuth['status']) {
                if ($request->Action == "add") {
                    $query = "INSERT INTO MsOutlet
                            (IsDeleted, UserIn, DateIn, ID, Name, PhoneNumber, IsPrimary, Address)
                            VALUES
                            (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?)";
                    DB::insert($query, [
                        $getAuth['UserID'],
                        $request->OutletName,
                        $request->PhoneNumber,
                        $request->IsPrimary,
                        $request->Address,
                    ]);
                    $return['message'] = "Outlet successfully created.";
                } 
                if ($request->Action == "edit") {
                    $query = "UPDATE MsOutlet
                    SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        PhoneNumber=?,
                        IsPrimary=?,
                        Address=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->OutletName,
                        $request->PhoneNumber,
                        $request->IsPrimary,
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
                
            } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
            return response()->json($return, 200);
        }
        // END POST OUTLET
}