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
            $query = "SELECT ID, Name, QtyAlert, BGColor
                FROM MsCategory
                WHERE ClientID = ?"; 
            if ($request->ID) {
                $query .= " AND ID = ? ";
                $return['data'] = DB::select($query,[$getAuth['ClientID'], $request->ID])[0];
            } else {
                $query .= " ORDER BY Name ASC";
                $return['data'] = DB::select($query,[$getAuth['ClientID']]);
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized!",'data'=>null);
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
            if ($request->Action == "add") {
                $query = "INSERT INTO MsCategory
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, QtyAlert, BGColor)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->Name,
                    $request->QtyAlert,
                    $request->BGColor,
                ]);
                $return['message'] = "Category successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsCategory
                            SET UserUp=?,
                                DateUp=NOW(),
                                Name=?,
                                QtyAlert=?,
                                BGColor=?
                                WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->Name,
                    $request->QtyAlert,
                    $request->BGColor,
                    $request->ID
                ]);
                $return['message'] = "Category successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "UPDATE MsCategory SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                DB::update($query, [$getAuth['UserID'],$request->ID]);
                $return['message'] = "Category successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized!",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST CATEGORY
}