<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
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

    // GET CUSTOMER
    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
                $query = "  SELECT ID id, ClientID clientID, Name name, HandphoneNumber hanphoneNumber, Address address, Gender gender
                    FROM MsCustomer
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
    // END GET CUSTOMER
    
   // POST CUSTOMER
   public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsCustomer
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, HandphoneNumber, Address, Gender)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->CustomerName,
                    $request->PhoneNumber,
                    $request->Address,
                    $request->Gender,
                ]);
                $return['message'] = "Customer successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsCustomer
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    Name=?,
                    HandphoneNumber=?,
                    Address=?,
                    Gender=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->CustomerName,
                    $request->PhoneNumber,
                    $request->Address,
                    $request->Gender,
                    $request->ID
                ]);
                $return['message'] = "Customer successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsCustomer
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Customer successfully deleted.";
            }
            
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }
    // END POST CUSTOMER
}