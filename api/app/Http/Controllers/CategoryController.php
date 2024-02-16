<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
    
   // GET CATEGORY
   public function getCategory(Request $request)
   {
       $return = array('status'=>true,'message'=>"",'data'=>null);
       $getAuth = $this->validateAuth($request->_s);
       if ($getAuth['status']) {
       $query = "SELECT ID, ClientID, Name, QtyAlert, BGColor
           FROM MsCategory
           WHERE MsCategory.ClientID = ?"; 
           $data = DB::select($query,[$getAuth['ClientID']]);
       $return['data'] = $data[0];
       if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
   } else $return = array('status'=>false,'message'=>"");
   return response()->json($return, 200);
   }
   // END GET CATEGORY

   // POST CATEGORY
   public function doSaveCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsCategory
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, QtyAlert, BGColor)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->CategoryName,
                    $request->tyAlert,
                    $request->BGColor,
                ]);
                $return['message'] = "Category successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsCategory
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    Name=?,
                    QtyAlert=?,
                    BGColor=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->CategoryName,
                    $request->QtyAlert,
                    $request->BGColor,
                    $request->ID
                ]);
                $return['message'] = "Category successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsCategory
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Category successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }
    // END POST CATEGORY
}