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
                                                
                                                MsPayment.ID paymentId,
                                                MsPayment.Name name, 
                                                MsPayment.Description description, 
                                                MsPayment.IsActive isActive,

                                                TrTransaction.SubTotal subTotal, 
                                                TrTransaction.Discount discount, 
                                                TrTransaction.Tax tax, 
                                                TrTransaction.TotalPayment totalPayment, 
                                                TrTransaction.PaymentAmount paymentAmount, 
                                                TrTransaction.Changes changes, 
                                                TrTransaction.Status status, 
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
                                                MsPayment.ID PaymentID paymentID, 
                                                MsPayment.Name name, 
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

    // GET HISTORY TRANSACTION
    public function getHistory(Request $request)
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
                                                
                                                MsPayment.ID paymentId,
                                                MsPayment.Name name, 
                                                MsPayment.Description description, 
                                                MsPayment.IsActive isActive,

                                                TrTransaction.SubTotal subTotal, 
                                                TrTransaction.Discount discount, 
                                                TrTransaction.Tax tax, 
                                                TrTransaction.TotalPayment totalPayment, 
                                                TrTransaction.PaymentAmount paymentAmount, 
                                                TrTransaction.Changes changes, 
                                                TrTransaction.Status status, 
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
                            $query = "SELECT    TrTransaction.ID, 
                                                TrTransaction.TransactionNumber, 
                                                TrTransaction.TransactionDate, 
                                                TrTransaction.PaidDate, 
                                                TrTransaction.CustomerName, 
                                                MsPayment.ID PaymentID, 
                                                MsPayment.Name, 
                                                MsPayment.Description, 
                                                MsPayment.IsActive, 
                                                TrTransaction.TotalPayment
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
    // END HISTORY TRANSACTION
   
    // POST TRANSACTION
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "SELECT UUID() GenID";
                $TransactionID = DB::select($query)[0]->GenID;
                $query = "INSERT INTO TrTransaction
                            (IsDeleted, UserIn, DateIn, ID, TransactionNumber, ClientID, OutletID, PaymentID, TransactionDate, PaidDate, CustomerName, SubTotal, Discount, Tax, TotalPayment, PaymentAmount, Changes, Status, Notes)
                            VALUES
                            (0, ?, NOW(), ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $TransactionID,
                    $request->TransactionNumber,
                    $getAuth['ClientID'],
                    $request->OutletID,
                    $request->PaymentID,
                    $request->CustomerName,
                    $request->SubTotal,
                    $request->Discount,
                    $request->Tax,
                    $request->TotalPayment,
                    $request->PaymentAmount,
                    $request->Changes,
                    $request->IsPaid == "T" ? 1 : 0,
                    $request->Notes,
                ]);

                if (str_contains($request->productID,',')) {
                    $transactionProductID = explode(',',$request->transactionProductID);
                    $productID = explode(',',$request->productID);
                    $qty = explode(',',$request->qty);
                    $unitPrice = explode(',',$request->unitPrice);
                    $discount = explode(',',$request->discount);
                    $notes = explode(',',$request->notes);
                    for ($i=0; $i<count($productID); $i++)
                    {
                        $query = "INSERT INTO TrTransactionProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, TransactionID, Qty, UnitPrice, Discount, UnitPriceAfterDiscount, Notes)
                                VALUES
                                (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $transactionProductID[$i],
                                $getAuth['ClientID'],
                                $productID[$i],
                                $TransactionID,
                                $qty[$i],
                                $unitPrice[$i],
                                $discount[$i],
                                $unitPrice[$i] - $discount[$i],
                                $notes[$i],
                            ]);
                    }
                } else {
                    $query = "INSERT INTO TrTransactionProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, TransactionID, Qty, UnitPrice, Discount, UnitPriceAfterDiscount, Notes)
                                VALUES
                                (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $request->transactionProductID,
                                $getAuth['ClientID'],
                                $request->productID,
                                $TransactionID,
                                $request->qty,
                                $request->unitPrice,
                                $request->discount,
                                $request->unitPrice - $request->discount,
                                $request->notes,
                            ]);
                            $return['message'] = "Transaction Product successfully created.";
                }

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
                                $TransactionID,
                                $transactionProductIDVariant[$i],
                                $variantOptionID[$i],
                                $variantLabel[$i],
                                strval($variantPrice[$i])
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
                                $TransactionID,
                                $request->transactionProductIDVariant,
                                $request->variantOptionID,
                                $request->variantLabel,
                                strval($request->variantPrice)
                            ]);
                }
                $return['message'] = "Transaction successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE TrTransaction
                            SET IsDeleted=0,
                                UserUp=?,
                                DateUp=NOW(),
                                -- TransactionNumber=?,
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
                    // $request->TransactionNumber,
                    $request->PaymentID,
                    $request->CustomerName,
                    $request->SubTotal,
                    $request->Discount,
                    $request->TotalPayment,
                    $request->PaymentAmount,
                    $request->Changes,
                    $request->IsPaid == "T" ? 1 : 0,
                    $request->Notes,
                    $request->ID
                ]);

                if (str_contains($request->productID,',')) {
                    $transactionProductID = explode(',',$request->transactionProductID);
                    $productID = explode(',',$request->productID);
                    $qty = explode(',',$request->qty);
                    $unitPrice = explode(',',$request->unitPrice);
                    $discount = explode(',',$request->discount);
                    $notes = explode(',',$request->notes);
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
                            $discount[$i],
                            $unitPrice[$i] - $discount[$i],
                            $notes[$i],
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
                        $request->discount,
                        $request->unitPrice - $request->discount,
                        $request->notes,
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
            if ($request->Action == "delete") {
                $query = "DELETE FROM TrTransaction
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Transaction successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
}