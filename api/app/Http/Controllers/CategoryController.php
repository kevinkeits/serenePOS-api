<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
    
    // GET CATEGORY
    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
                $query = "SELECT ID id, Name name, QtyAlert qtyAlert, BGColor bgColor
                    FROM MsCategory
                    WHERE IsDeleted=0
                        AND ClientID = ?"; 
                if ($request->ID) {
                    $query .= " AND ID = ? ";
                    $return['data'] = DB::select($query,[$getAuth['ClientID'], $request->ID])[0];
                } else {
                    $query .= " ORDER BY Name ASC";
                    $return['data'] = DB::select($query,[$getAuth['ClientID']]);
                }
            } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
            return response()->json($return, 200);
    }
    // END GET CATEGORY

   // POST CATEGORY
   public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "add") {
                $query = "INSERT INTO MsCategory
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, QtyAlert, BGColor)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->name,
                    $request->qtyAlert,
                    $request->bgColor,
                ]);
                $return['message'] = "Category successfully created";
            } 
            if ($request->action == "edit") {
                $query = "UPDATE MsCategory
                            SET UserUp=?,
                                DateUp=NOW(),
                                Name=?,
                                QtyAlert=?,
                                BGColor=?
                                WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->name,
                    $request->qtyAlert,
                    $request->bgColor,
                    $request->id
                ]);
                $return['message'] = "Category successfully modified";
            }
            if ($request->action == "delete") {
                if (str_contains($request->id,',')) {
                    $tempID = explode(',',$request->id);
                    foreach ($tempID as $key => $ID) {
                        $query = "UPDATE MsCategory SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                        DB::update($query, [$getAuth['UserID'],$ID]);
                    }
                    $return['message'] = "Category successfully deleted";
                } else {
                    $query = "UPDATE MsCategory SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                    DB::update($query, [$getAuth['UserID'],$request->id]);
                    $return['message'] = "Category successfully deleted";
                }
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST CATEGORY
}