<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ReportController extends Controller
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

    // GET TRANSACTION
    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
                if ($request->ID) {
                            $query = "SELECT    TrTransaction.ID transactionID, 
                                                TrTransaction.TransactionNumber transcationNumber, 
                                                TrTransaction.TransactionDate transactionDate, 
                                                TrTransaction.UserIn userIn,
                                                TrTransaction.CustomerName customerName,
                                                MsOutlet.Name outletName,
                                                
                                                MsPayment.ID paymentID,
                                                MsPayment.Name payment, 
                                                MsPayment.Description description, 
                                                MsPayment.IsActive isActive,

                                                TrTransaction.SubTotal subTotal, 
                                                TrTransaction.Discount discount, 
                                                TrTransaction.Tax tax, 
                                                TrTransaction.TotalPayment totalPayment, 
                                                TrTransaction.PaymentAmount paymentAmount, 
                                                TrTransaction.Changes changes, 
                                                TrTransaction.Status isPaid, 
                                                TrTransaction.Notes notes
                                        FROM    TrTransaction
                                        JOIN    MsPayment ON MsPayment.ID = TrTransaction.PaymentID
                                        JOIN    MsOutlet ON MsOutlet.ID = TrTransaction.OutletID
                                        WHERE   TrTransaction.ID = ?
                                        ORDER BY TransactionDate DESC";
                            $details = DB::select($query,[$request->ID])[0];

                            $query = "  SELECT  TrTransactionProduct.ID transactionProductID,
                                                TrTransactionProduct.ProductID productID,
                                                MsProduct.Name productName,
                                                TrTransactionProduct.Qty qty,
                                                TrTransactionProduct.UnitPrice unitPrice,
                                                TrTransactionProduct.Discount discount,
                                                TrTransactionProduct.UnitPriceAfterDiscount unitPriceAfterDiscount,
                                                TrTransactionProduct.Notes notes
                                        FROM    TrTransactionProduct
                                        JOIN    MsProduct on MsProduct.ID = TrTransactionProduct.ProductID
                                        WHERE   TrTransactionProduct.TransactionID = ?
                                        ORDER BY MsProduct.Name DESC";
                            $detailsProduct = DB::select($query,[$request->ID]);

                            $query = "  SELECT  TrTransactionProductVariant.ID id,
                                                TrTransactionProductVariant.TransactionProductID transactionProductID,
                                                TrTransactionProduct.ProductID productID,
                                                TrTransactionProductVariant.VariantOptionID variantOptionID,
                                                TrTransactionProductVariant.Label label,
                                                TrTransactionProductVariant.Price price
                                        FROM    TrTransactionProductVariant
                                        JOIN    TrTransactionProduct 
                                        ON      TrTransactionProduct.ID = TrTransactionProductVariant.TransactionProductID
                                        WHERE   TrTransactionProductVariant.TransactionID = ?
                                        ORDER BY TrTransactionProductVariant.ID DESC";
                            $detailsVariant = DB::select($query,[$request->ID]);

                            $return['data'] = array('details'=>$details,'detailsProduct'=>$detailsProduct,'detailsVariant'=>$detailsVariant);
                        } else {
                            $query = "SELECT    TrTransaction.ID id, 
                                                TrTransaction.TransactionNumber transactionNumber, 
                                                TrTransaction.TransactionDate transactionDate, 
                                                TrTransaction.PaidDate paidDate, 
                                                TrTransaction.CustomerName customerName, 
                                                MsPayment.ID paymentID, 
                                                MsPayment.Name payment, 
                                                MsPayment.Description description, 
                                                MsPayment.IsActive isActive, 
                                                TrTransaction.TotalPayment totalPayment
                                        FROM    TrTransaction
                                        JOIN    MsPayment ON MsPayment.ID = TrTransaction.PaymentID
                                        WHERE   TrTransaction.ClientID = ?
                                        ORDER BY TransactionDate DESC";
                            $data = DB::select($query, [$getAuth['ClientID']]);
                            if ($data) $return['data'] = $data;
                        }
            } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }
    // END TRANSACTION
}