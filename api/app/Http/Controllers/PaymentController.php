<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
    
    // GET PAYMENT
    public function getPayment(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT MsPayment.ID, MsPayment.ClientID, MsPayment.PaymentCash, MsPayment.PaymentCredit, MsPayment.PaymentDebit, MsPayment.PaymentQRIS,  MsPayment.PaymentTransfer, MsPayment.PaymentEWallet
            FROM MsPayment
            WHERE MsPayment.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
        $return['data'] = $data[0];
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
    }
    // END GET PAYMENT

   // POST PAYMENT
   public function doSavePayment(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsPayment
                        (IsDeleted, UserIn, DateIn, ID, ClientID, PaymentCash, PaymentCredit, PaymentDebit, PaymentQRIS, PaymentTransfer, PaymentEWallet)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->PaymentCash,
                    $request->PaymentCredit,
                    $request->PaymentDebit,
                    $request->PaymentQRIS,
                    $request->PaymentTransfer,
                    $request->PaymentEWallet,
                ]);
                $return['message'] = "Payment successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsPayment
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    PaymentCash=?,
                    PaymentCredit=?,
                    PaymentDebit=?,
                    PaymentQRIS=?,
                    PaymentTransfer=?,
                    PaymentEWallet=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->PaymentCash,
                    $request->PaymentCredit,
                    $request->PaymentDebit,
                    $request->PaymentQRIS,
                    $request->PaymentTransfer,
                    $request->PaymentEWallet,
                    $request->ID
                ]);
                $return['message'] = "Payment successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsPayment
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Payment successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }
    // END POST PAYMENT
}