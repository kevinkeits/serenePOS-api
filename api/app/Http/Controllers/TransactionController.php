<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
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
   
    // GET TRANSACTION
    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "  SELECT  TrTransaction.ID, 
                                    TrTransaction.ClientID, 
                                    TrTransaction.TransactionNumber, 
                                    MsPayment.ID PaymentID, 
                                    MsPayment.PaymentCash, 
                                    MsPayment.PaymentCredit, 
                                    MsPayment.PaymentDebit, 
                                    MsPayment.PaymentQRIS, 
                                    MsPayment.PaymentTransfer, 
                                    MsPayment.PaymentEWallet, 
                                    TrTransaction.TransactionDate, 
                                    TrTransaction.PaidDate, 
                                    TrTransaction.CustomerName, 
                                    TrTransaction.SubTotal, 
                                    TrTransaction.Discount, 
                                    TrTransaction.Tax, 
                                    TrTransaction.TotalPayment, 
                                    TrTransaction.PaymentAmount, 
                                    TrTransaction.Changes, 
                                    TrTransaction.Status, 
                                    TrTransaction.Notes
                            FROM    TrTransaction
                            JOIN    MsPayment ON MsPayment.ID = TrTransaction.PaymentID
                            WHERE   {definedFilter}
                            ORDER BY TransactionDate ASC";
                            $definedFilter = "1=1";
            if ($getAuth['ClientID'] != "") $definedFilter = "TrTransaction.ClientID = '".$getAuth['ClientID']."'";
            if ($request->_i) {
                $definedFilter = "TrTransaction.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT    TrTransactionProduct.ID,
                                        TrTransactionProduct.ClientID,
                                        TrTransactionProduct.ProductID,
                                        TrTransactionProduct.TransactionID,
                                        TrTransactionProduct.Qty,
                                        TrTransactionProduct.UnitPrice,
                                        TrTransactionProduct.Discount,
                                        TrTransactionProduct.UnitPriceAfterDiscount,
                                        TrTransactionProduct.Notes,
                                        TrTransactionProductVariant.VariantOptionID,
                                        MsVariantOption.Label,
                                        MsVariantOption.Price
                                FROM    TrTransactionProduct
                                JOIN    TrTransactionProductVariant on TrTransactionProductVariant.TransactionProductID = TrTransactionProduct.ID
                                JOIN    MsVariantOption on MsVariantOption.ID = TrTransactionProductVariant.VariantOptionID
                                WHERE   TrTransactionProduct.TransactionID = ?
                                ORDER BY  TrTransactionProduct.TransactionID ASC";
                    $selVariant = DB::select($query,[$request->_i]);
                    $arrData = [];
                    if ($selVariant) {
                        foreach ($selVariant as $key => $value) {
                            array_push($arrData,$value->ID);
                        }
                    }
                    $return['data'] = array('header'=>$data[0], 'selVariant'=> $selVariant);
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }
    // END GET TRANSACTION

    // GET TRANSACTION HISTORY
    public function getHistory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "  SELECT      TrTransaction.TransactionNumber, 
                                        MsPayment.ID PaymentID, 
                                        MsPayment.PaymentCash Cash, 
                                        MsPayment.PaymentCredit Credit, 
                                        MsPayment.PaymentDebit Debit, 
                                        MsPayment.PaymentQRIS QRIS, 
                                        MsPayment.PaymentTransfer Transfer, 
                                        MsPayment.PaymentEWallet EWallet, 
                                        TrTransaction.TransactionDate, 
                                        TrTransaction.PaidDate, 
                                        TrTransaction.CustomerName, 
                                        TrTransaction.SubTotal, 
                                        TrTransaction.Discount, 
                                        TrTransaction.Tax, 
                                        TrTransaction.TotalPayment, 
                                        TrTransaction.PaymentAmount, 
                                        TrTransaction.Changes, 
                                        TrTransaction.Status, 
                                        TrTransaction.Notes,
                                        MsClient.Name CashierName,
                                        MsClient.OutletID,
                                        MsOutlet.Name Outlet
                                FROM    TrTransaction
                                JOIN    MsPayment ON MsPayment.ID = TrTransaction.PaymentID
                                JOIN    MsClient ON MsClient.ID = TrTransaction.ClientID
                                JOIN    MsOutlet ON MsOutlet.ID = MsClient.OutletID
                                WHERE   {definedFilter}
                                ORDER BY TransactionDate ASC";
                                $definedFilter = "1=1";
            if ($getAuth['ClientID'] != "") $definedFilter = "TrTransaction.ClientID = '".$getAuth['ClientID']."'";
            if ($request->_i) {
                $definedFilter = "TrTransaction.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT    TrTransactionProduct.ID,
                                        TrTransactionProduct.ProductID,
                                        MsProduct.Name ProductName,
                                        TrTransactionProduct.Qty,
                                        TrTransactionProduct.UnitPrice,
                                        TrTransactionProduct.Discount,
                                        TrTransactionProduct.UnitPriceAfterDiscount,
                                        TrTransactionProduct.Notes,
                                        TrTransactionProductVariant.VariantOptionID,
                                        MsVariantOption.Label,
                                        MsVariantOption.Price
                                FROM    TrTransactionProduct
                                JOIN    MsProduct on MsProduct.ID = TrTransactionProduct.ProductID
                                JOIN    TrTransactionProductVariant on TrTransactionProductVariant.TransactionProductID = TrTransactionProduct.ID
                                JOIN    MsVariantOption on MsVariantOption.ID = TrTransactionProductVariant.VariantOptionID
                                WHERE   TrTransactionProduct.TransactionID = ?
                                ORDER BY  TrTransactionProduct.TransactionID ASC";
                    $selVariant = DB::select($query,[$request->_i]);
                    $arrData = [];
                    if ($selVariant) {
                        foreach ($selVariant as $key => $value) {
                            array_push($arrData,$value->ID);
                        }
                    }
                    $return['data'] = array('header'=>$data[0], 'selVariant'=> $selVariant);
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }
    // END GET TRANSACTION HISTORY

    // POST TRANSACTION
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO TrTransaction
                        (IsDeleted, UserIn, DateIn, ID, TransactionNumber, ClientID, PaymentID, TransactionDate, PaidDate, CustomerName, SubTotal, Discount, Tax, TotalPayment, PaymentAmount, Changes, Status, Notes)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $request->TransactionNumber,
                    $getAuth['ClientID'],
                    $request->PaymentID,
                    $request->CustomerName,
                    $request->SubTotal,
                    $request->Discount,
                    $request->Tax,
                    $request->TotalPayment,
                    $request->PaymentAmount,
                    $request->Changes,
                    $request->Status,
                    $request->Notes,
                ]);
                $return['message'] = "Transaction successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE TrTransaction
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    TransactionNumber=?,
                    ClientID=?,
                    PaymentID=?,
                    TransactionDate=NOW(),
                    PaidDate=NOW(),
                    CustomerName=?,
                    SubTotal=?,
                    Discount=?,
                    TotalPayment=?,
                    PaymentAmount=?,
                    Changes=?,
                    Status=?,
                    Notes=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->TransactionNumber,
                    $getAuth['ClientID'],
                    $request->PaymentID,
                    $request->CustomerName,
                    $request->SubTotal,
                    $request->Discount,
                    $request->TotalPayment,
                    $request->PaymentAmount,
                    $request->Changes,
                    $request->Status,
                    $request->Notes,
                    $request->ID
                ]);
                $return['message'] = "Transaction successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM TrTransaction
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Transaction successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO TrTransactionProduct
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, TransactionID, Qty, UnitPrice, Discount, UnitPriceAfterDiscount, Notes)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductID,
                    $request->TransactionID,
                    $request->Qty,
                    $request->UnitPrice,
                    $request->Discount,
                    $request->UnitPriceAfterDiscount,
                    $request->UnitNotes,
                ]);
                $return['message'] = "Transaction Product successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE TrTransactionProduct
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    ProductID=?,
                    TransactionID=?,
                    Qty=?,
                    UnitPrice=?,
                    Discount=?,
                    UnitPriceAfterDiscount=?,
                    Notes=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductID,
                    $request->TransactionID,
                    $request->Qty,
                    $request->UnitPrice,
                    $request->Discount,
                    $request->UnitPriceAfterDiscount,
                    $request->Notes,
                    $request->ID
                ]);
                $return['message'] = "Transaction Product successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM TrTransactionProduct
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Transaction Product successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveProductVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO TrTransactionProductVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, VariantOptionID)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductID,
                    $request->VariantOptionID,
                ]);
                $return['message'] = "Transaction Product Variant successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE TrTransactionProductVariant
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    ProductID=?,
                    VariantOptionID=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductID,
                    $request->VariantOptionID,
                    $request->ID
                ]);
                $return['message'] = "Transaction Product Variant successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM TrTransactionProductVariant
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Transaction Product Variant successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }
    // END POST TRANSACTION
}