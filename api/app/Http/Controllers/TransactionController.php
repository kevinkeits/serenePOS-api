<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private function validateAuth($Token)
    {
        if ($Token != null) $Token = trim(str_replace("Bearer","",$Token));
        $return = array('status'=>false,'ID'=>"");
        $query = "SELECT MsUser.ID UserID, MsUser.ClientID, MsUser.OutletID, MsUser.Name, MsUser.PhoneNumber, MsUser.Email
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
                'OutletID' => $data->OutletID,
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
                                                TrTransaction.TransactionNumber transactionNumber, 
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
                                                TrTransaction.TotalPayment totalPayment,
                                                TrTransaction.Status isPaid
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

    // POST TRANSACTION
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "add") {
                $query = "SELECT UUID() GenID";
                $transactionID = DB::select($query)[0]->GenID;

                $countTransaction = "SELECT COUNT(TransactionNumber) +1 as transNumber FROM TrTransaction WHERE TrTransaction.ClientID = ? ";
                $incrementTransaction = DB::select($countTransaction, [$getAuth['ClientID']]);
                
                $query = "INSERT INTO TrTransaction
                            (IsDeleted, UserIn, DateIn, ID, TransactionNumber, ClientID, OutletID, PaymentID, TransactionDate, PaidDate, CustomerName, SubTotal, Discount, Tax, TotalPayment, PaymentAmount, Changes, Status, Notes)
                            VALUES
                            (0, ?, NOW(), ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $transactionID,
                    $incrementTransaction[0]->transNumber,
                    $getAuth['ClientID'],
                    $getAuth['OutletID'],
                    $request->paymentID,
                    $request->customerName,
                    $request->subTotal,
                    $request->discount,
                    $request->tax,
                    $request->totalPayment,
                    $request->isPaid == "F" ? 0 : $request->paymentAmount,
                    $request->changes,
                    $request->isPaid == "T" ? 1 : 0,
                    $request->notes,
                ]);

                if (str_contains($request->productID,',')) {
                    //$transactionProductID = explode(',',$request->transactionProductID);
                    $productID = explode(',',$request->productID);
                    $qty = explode(',',$request->qty);
                    $unitPrice = explode(',',$request->unitPrice);
                    $discountProduct = explode(',',$request->discountProduct);
                    $notesProduct = explode(',',$request->notesProduct);
                    for ($i=0; $i<count($productID); $i++)
                    {
                        $query = "INSERT INTO TrTransactionProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, TransactionID, Qty, UnitPrice, Discount, UnitPriceAfterDiscount, Notes)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                //$transactionProductID[$i],
                                $getAuth['ClientID'],
                                $productID[$i],
                                $transactionID,
                                intval($qty[$i]),
                                floatval($unitPrice[$i]),
                                floatval($discountProduct[$i]),
                                floatval($unitPrice[$i]) - floatval($discountProduct[$i]),
                                $notesProduct[$i],
                            ]);
                    }
                } else {
                    $query = "INSERT INTO TrTransactionProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, TransactionID, Qty, UnitPrice, Discount, UnitPriceAfterDiscount, Notes)
                                VALUES
                                (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                //$request->transactionProductID
                                $getAuth['ClientID'],
                                $request->productID,
                                $transactionID,
                                intval($request->qty),
                                floatval($request->unitPrice),
                                floatval($request->discountProduct),
                                floatval($request->unitPrice) - floatval($request->discountProduct),
                                $request->notesProduct,
                            ]);
                            $return['message'] = "Transaction Product successfully created.";
                }

                if ($request->variantOptionID != '') {
                    if (str_contains($request->variantOptionID,',')) {
                        $transactionProductIDVariant = explode(',',$request->transactionProductIDVariant);
                        $variantOptionID = explode(',',$request->variantOptionID);
                        $variantLabel = explode(',',$request->variantLabel);
                        $variantPrice = explode(',',$request->variantPrice);
                        for ($i=0; $i<count($variantOptionID); $i++)
                        {
                            $query = "INSERT INTO TrTransactionProductVariant
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, TransactionID, TransactionProductID, VariantOptionID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?)";
                                DB::insert($query, [
                                    $getAuth['UserID'],
                                    $getAuth['ClientID'],
                                    $transactionID,
                                    $transactionProductIDVariant[$i],
                                    $variantOptionID[$i],
                                    $variantLabel[$i],
                                    floatval($variantPrice[$i])
                                ]);
                        }
                    } else {
                        $query = "INSERT INTO TrTransactionProductVariant
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, TransactionID, TransactionProductID, VariantOptionID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?)";
                                DB::insert($query, [
                                    $getAuth['UserID'],
                                    $getAuth['ClientID'],
                                    $transactionID,
                                    $request->transactionProductIDVariant,
                                    $request->variantOptionID,
                                    $request->variantLabel,
                                    floatval($request->variantPrice)
                                ]);
                    }
                }
                
                $return['message'] = "Transaction successfully created.";
            }
            if ($request->action == "edit") {
                $query = "UPDATE TrTransaction
                            SET IsDeleted=0,
                                UserUp=?,
                                DateUp=NOW(),
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
                    $request->paymentID,
                    $request->customerName,
                    $request->subTotal,
                    $request->discount,
                    $request->totalPayment,
                    $request->isPaid == "F" ? 0 : $request->paymentAmount,
                    $request->changes,
                    $request->isPaid == "T" ? 1 : 0,
                    $request->notes,
                    $request->id
                ]);

                if (str_contains($request->productID,',')) {
                    $transactionProductID = explode(',',$request->transactionProductID);
                    $productID = explode(',',$request->productID);
                    $qty = explode(',',$request->qty);
                    $unitPrice = explode(',',$request->unitPrice);
                    $discountProduct = explode(',',$request->discountProduct);
                    $notesProduct = explode(',',$request->notesProduct);
                    for ($i=0; $i<count($productID); $i++)
                    {
                        $query = "UPDATE TrTransactionProduct
                        SET UserUp=?,
                            DateUp=NOW(),
                            ProductID=?,
                            Qty=?,
                            UnitPrice=?,
                            Discount=?,
                            UnitPriceAfterDiscount=?,
                            Notes=?
                            WHERE ID=?";
                        DB::update($query, [
                            $getAuth['UserID'],
                            $productID[$i],
                            $qty[$i],
                            $unitPrice[$i],
                            $discountProduct[$i],
                            $unitPrice[$i] - $discountProduct[$i],
                            $notesProduct[$i],
                            $transactionProductID[$i],
                        ]);
                    }
                } else {
                    $query = "UPDATE TrTransactionProduct 
                    SET UserUp=?,
                        DateUp=NOW(),
                        ProductID=?,
                        Qty=?,
                        UnitPrice=?,
                        discount=?,
                        Discount=?
                        UnitPriceAfterDiscount=?,
                        Notes=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->productID,
                        $request->qty,
                        $request->unitPrice,
                        $request->discountProduct,
                        $request->unitPrice - $request->discountProduct,
                        $request->notesProduct,
                        $request->transactionProductID
                    ]);
                }

                if (str_contains($request->variantOptionID,',')) {
                    $transactionProductIDVariant = explode(',',$request->transactionProductIDVariant);
                    $variantOptionID = explode(',',$request->variantOptionID);
                    $variantLabel = explode(',',$request->variantLabel);
                    $variantPrice = explode(',',$request->variantPrice);
                    for ($i=0; $i<count($variantOptionID); $i++)
                    {
                        $query = "UPDATE TrTransactionProductVariant
                        SET UserUp=?,
                            DateUp=NOW(),
                            VariantOptionID=?,
                            Label=?,
                            Price=?
                            WHERE ID=?";
                        DB::update($query, [
                            $getAuth['UserID'],
                            $variantOptionID[$i],
                            $variantLabel[$i],
                            strval($variantPrice[$i]),
                            $transactionProductIDVariant[$i]
                        ]);
                    }
                } else {
                    $query = "UPDATE TrTransactionProductVariant 
                    SET UserUp=?,
                        DateUp=NOW(),
                        VariantOptionID=?,
                        Label=?,
                        Price=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->variantOptionID,
                        $request->variantLabel,
                        strval($request->variantPrice),
                        $request->transactionProductIDVariant
                    ]);
                }
                $return['message'] = "Transaction successfully modified.";
            }
            if ($request->action == "delete") {
                $query = "UPDATE TrTransaction SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                DB::update($query, [$getAuth['UserID'],$request->id]);

                $query = "UPDATE TrTransactionProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE TransactionID=?";
                DB::update($query, [$getAuth['UserID'],$request->transactionID]);

                $query = "UPDATE TrTransactionProductVariant SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE TransactionID=?";
                DB::update($query, [$getAuth['UserID'],$request->transactionID]);
                $return['message'] = "Transaction successfully deleted";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
}