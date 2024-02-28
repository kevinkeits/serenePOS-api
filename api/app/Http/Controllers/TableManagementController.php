<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class TableManagementController extends Controller
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
           {
                $query = "  SELECT MsTableManagement.ID id, MsTableManagement.ClientID clientID, MsTableManagement.OutletID outletID, MsTableManagement.TableName tableName, MsTableManagement.Capacity capacity, MsTableManagement.Status status
                                FROM MsTableManagement
                                WHERE ClientID = ?
                                ORDER BY Name ASC";
                $data = DB::select($query, [$getAuth['ClientID']]);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
   }
    
   // POST PAYMENT
   public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "add") {
                $query = "SELECT UUID() GenID";
                $tableID = DB::select($query)[0]->GenID;
                $query = "INSERT INTO MsTableManagement
                        (IsDeleted, UserIn, DateIn, ID, ClientID, OutletID, TableName, Capacity)
                        VALUES
                        (0, ?, NOW(), ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $tableID,
                    $getAuth['ClientID'],
                    $request->outletID,
                    $request->tableName,
                    $request->capacity,
                ]);
                $return['message'] = "Table successfully created.";
            }
            if ($request->action == "edit") {
                $query = "UPDATE MsTableManagement
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    OutletID=?,
                    TableName=?,
                    Capacity=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->outletID,
                    $request->tableName,
                    $request->capacity,
                    $request->id
                ]);
                $return['message'] = "Table successfully modified.";
            }
            if ($request->action == "delete") {
                $query = "UPDATE MsTableManagement SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                DB::update($query, [$getAuth['UserID'],$request->id]);
                $return['message'] = "Table successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }
    // END POST PAYMENT
}