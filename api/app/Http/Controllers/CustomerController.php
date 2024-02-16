<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
    
    // GET CUSTOMER
    public function getCustomer(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT ID, ClientID, Name, HandphoneNumber, Address, Gender
            FROM MsCustomer
            WHERE MsCustomer.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
        $return['data'] = $data[0];
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
    }
    // END GET CUSTOMER

   // POST CUSTOMER
   public function doSaveCustomer(Request $request)
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