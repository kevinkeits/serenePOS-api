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
        $query = "SELECT MsUser.ID UserID, MsUser.ClientID, MsUser.OutletID, MsUser.Name, MsUser.PhoneNumber, MsUser.Email, MsClient.Name ClientName
                    FROM MsUser
                    JOIN TrSession ON TrSession.UserID = MsUser.ID
                    JOIN MsClient ON MsClient.ID = MsUser.ClientID
                    WHERE TrSession.ID=?
                        AND TrSession.IsLoggedOut=0";
        $checkAuth = DB::select($query,[$Token]);
        if ($checkAuth) {
            $data = $checkAuth[0];
            $return = array(
                'status' => true,
                'UserID' => $data->UserID,
                'ClientID' => $data->ClientID,
                'ClientName' => $data->ClientName,
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
                                                
                                                TrTransaction.PaymentID paymentID,
                                                (SELECT Name FROM MsPayment WHERE ID=TrTransaction.PaymentID) payment,

                                                TrTransaction.SubTotal subTotal, 
                                                TrTransaction.Discount discount, 
                                                TrTransaction.Tax tax, 
                                                TrTransaction.TotalPayment totalPayment, 
                                                TrTransaction.PaymentAmount paymentAmount, 
                                                TrTransaction.Changes changes, 
                                                TrTransaction.Status isPaid, 
                                                TrTransaction.Notes notes
                                        FROM    TrTransaction
                                        JOIN    MsOutlet ON MsOutlet.ID = TrTransaction.OutletID
                                        WHERE   TrTransaction.ID = ?
						AND TrTransaction.IsDeleted = 0
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

                            $query = "  SELECT MsProductVariantOption.ID productVariantOptionID, 
                                            CASE WHEN MsProductVariantOption.IsSelected = 1 THEN 'T' ELSE 'F' END isSelected, 
                                            MsVariant.ID variantID, 
                                            MsVariant.Name name, 
                                            MsVariant.Type type, 
                                            MsVariantOption.ID variantOptionID, 
                                            MsVariantOption.Label label, 
                                            MsVariantOption.Price price,
                                            IFNULL(TrTransactionProductVariant.ID,'') id,
                                            IFNULL(TrTransactionProductVariant.TransactionProductID,'') transactionProductID,
                                            IFNULL(TrTransactionProduct.ProductID,'') productID
                                        FROM TrTransactionProduct 
                                            JOIN MsVariantProduct on MsVariantProduct.ProductID = TrTransactionProduct.ProductID
                                            JOIN MsVariant ON MsVariant.ID = MsVariantProduct.VariantID
                                            JOIN MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                                            JOIN MsProductVariantOption on (MsProductVariantOption.VariantOptionID = MsVariantOption.ID AND MsProductVariantOption.ProductID = MsVariantProduct.ProductID)
                                            LEFT JOIN TrTransactionProductVariant ON (TrTransactionProduct.ID = TrTransactionProductVariant.TransactionProductID AND TrTransactionProductVariant.VariantOptionID = MsVariantOption.ID)
                                        WHERE MsVariant.IsDeleted=0 AND TrTransactionProduct.TransactionID = ?
                                        ORDER BY MsVariant.Name ASC, MsVariantOption.Label ASC";
                                        
                            /*$query = "  SELECT  TrTransactionProductVariant.ID id,
                                                TrTransactionProductVariant.TransactionProductID transactionProductID,
                                                TrTransactionProduct.ProductID productID,
                                                TrTransactionProductVariant.VariantOptionID variantOptionID,
                                                TrTransactionProductVariant.Label label,
                                                TrTransactionProductVariant.Price price
                                        FROM    TrTransactionProductVariant
                                        JOIN    TrTransactionProduct 
                                        ON      TrTransactionProduct.ID = TrTransactionProductVariant.TransactionProductID
                                        WHERE   TrTransactionProductVariant.TransactionID = ?
                                        ORDER BY TrTransactionProductVariant.ID DESC";*/
                            $detailsVariant = DB::select($query,[$request->ID]);

                            $return['data'] = array('details'=>$details,'detailsProduct'=>$detailsProduct,'detailsVariant'=>$detailsVariant);
                        } else {
                            $query = "SELECT    TrTransaction.ID id, 
                                                TrTransaction.TransactionNumber transactionNumber, 
                                                TrTransaction.TransactionDate transactionDate, 
                                                TrTransaction.PaidDate paidDate, 
                                                TrTransaction.CustomerName customerName, 
                                                TrTransaction.PaymentID paymentID,
                                                (SELECT Name FROM MsPayment WHERE ID=TrTransaction.PaymentID) payment,
                                                TrTransaction.TotalPayment totalPayment,
                                                TrTransaction.Status isPaid
                                        FROM    TrTransaction
                                        LEFT JOIN    MsPayment ON MsPayment.ID = TrTransaction.PaymentID
                                        WHERE   TrTransaction.ClientID = ?
						AND TrTransaction.IsDeleted = 0
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

                $initials = '';
                $clientName = strtoupper(trim($getAuth['ClientName']));
                if (str_contains($getAuth['ClientName'],' ')) {
                    $arrInitials = explode(' ',$clientName);
                    $initials = substr($arrInitials[0],0,1).substr($arrInitials[1],0,1);
                } else {
                    $initials = substr($getAuth['ClientName'],0,1);
                }
                
                $query = "INSERT INTO TrTransaction
                            (IsDeleted, UserIn, DateIn, ID, TransactionNumber, ClientID, OutletID, PaymentID, TransactionDate, PaidDate, CustomerName, SubTotal, Discount, Tax, TotalPayment, PaymentAmount, Changes, Status, Notes)
                            VALUES
                            (0, ?, NOW(), ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $transactionID,
                    $initials.$incrementTransaction[0]->transNumber,
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
                                (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $transactionID."~".$productID[$i],
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
                                (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $transactionID."~".$request->productID,
                                $getAuth['ClientID'],
                                $request->productID,
                                $transactionID,
                                intval($request->qty),
                                floatval($request->unitPrice),
                                floatval($request->discountProduct),
                                floatval($request->unitPrice) - floatval($request->discountProduct),
                                $request->notesProduct,
                            ]);
                }

                if ($request->variantOptionID != '') {
                    if (str_contains($request->variantOptionID,',')) {
                        $transactionProductVariantID = explode(',',$request->transactionProductIDVariant);
                        $variantOptionID = explode(',',$request->variantOptionID);
                        $variantLabel = explode(',',$request->variantLabel);
                        $variantPrice = explode(',',$request->variantPrice);
                        for ($i=0; $i<count($variantOptionID); $i++)
                        {
                            $query = "INSERT INTO TrTransactionProductVariant
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, TransactionID, TransactionProductID, TransactionProductVariantID, VariantOptionID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?)";
                                DB::insert($query, [
                                    $getAuth['UserID'],
                                    $getAuth['ClientID'],
                                    $transactionID,
                                    $transactionID."~".explode('~',$variantOptionID[$i])[0],
                                    $transactionProductVariantID[$i],
                                    explode('~',$variantOptionID[$i])[1],
                                    explode('~',$variantLabel[$i])[2],
                                    floatval(explode('~',$variantPrice[$i])[2])
                                ]);
                        }
                    } else {
                        $query = "INSERT INTO TrTransactionProductVariant
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, TransactionID, TransactionProductID, TransactionProductVariantID, VariantOptionID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?)";
                                DB::insert($query, [
                                    $getAuth['UserID'],
                                    $getAuth['ClientID'],
                                    $transactionID,
                                    $transactionID."~".explode('~',$request->variantOptionID)[0],
                                    $request->transactionProductIDVariant,
                                    explode('~',$request->variantOptionID)[1],
                                    explode('~',$request->variantLabel)[2],
                                    floatval(explode('~',$request->variantPrice)[2])
                                ]);
                    }
                }
                
                $return['message'] = "Transaction successfully created.";
            }
            if ($request->action == "edit") {
                $transactionID = $request->id;
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
                    $transactionID
                ]);

                $query = "DELETE FROM TrTransactionProduct WHERE ClientID=? AND TransactionID=?";
                DB::delete($query, [$getAuth['ClientID'],$transactionID]);

                $query = "DELETE FROM TrTransactionProductVariant WHERE ClientID=? AND TransactionID=?";
                DB::delete($query, [$getAuth['ClientID'],$transactionID]);

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
                                (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $transactionID."~".$productID[$i],
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
                                (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $getAuth['UserID'],
                                $transactionID."~".$request->productID,
                                $getAuth['ClientID'],
                                $request->productID,
                                $transactionID,
                                intval($request->qty),
                                floatval($request->unitPrice),
                                floatval($request->discountProduct),
                                floatval($request->unitPrice) - floatval($request->discountProduct),
                                $request->notesProduct,
                            ]);
                }

                if ($request->variantOptionID != '') {
                    if (str_contains($request->variantOptionID,',')) {
                        //$transactionProductID = explode(',',$request->transactionProductID);
                        $transactionProductVariantID = explode(',',$request->transactionProductIDVariant);
                        $variantOptionID = explode(',',$request->variantOptionID);
                        $variantLabel = explode(',',$request->variantLabel);
                        $variantPrice = explode(',',$request->variantPrice);
                        for ($i=0; $i<count($variantOptionID); $i++)
                        {
                            $query = "INSERT INTO TrTransactionProductVariant
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, TransactionID, TransactionProductID, TransactionProductVariantID, VariantOptionID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?)";
                                DB::insert($query, [
                                    $getAuth['UserID'],
                                    $getAuth['ClientID'],
                                    $transactionID,
                                    $transactionID."~".explode('~',$variantOptionID[$i])[0],
                                    $transactionProductVariantID[$i],
                                    explode('~',$variantOptionID[$i])[1],
                                    explode('~',$variantLabel[$i])[2],
                                    floatval(explode('~',$variantPrice[$i])[2])
                                ]);
                        }
                    } else {
                        $query = "INSERT INTO TrTransactionProductVariant
                                    (IsDeleted, UserIn, DateIn, ID, ClientID, TransactionID, TransactionProductID, TransactionProductVariantID, VariantOptionID, Label, Price)
                                    VALUES
                                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?)";
                                DB::insert($query, [
                                    $getAuth['UserID'],
                                    $getAuth['ClientID'],
                                    $transactionID,
                                    $transactionID."~".explode('~',$request->variantOptionID)[0],
                                    $request->transactionProductIDVariant,
                                    explode('~',$request->variantOptionID)[1],
                                    explode('~',$request->variantLabel)[2],
                                    floatval(explode('~',$request->variantPrice)[2])
                                ]);
                    }
                }
                $return['message'] = "Transaction successfully modified.";
            }
            if ($request->action == "delete") {
                $query = "UPDATE TrTransaction SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ClientID=? AND ID=?";
                DB::update($query, [$getAuth['UserID'], $getAuth['ClientID'], $request->id]);

                $query = "UPDATE TrTransactionProduct SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ClientID=? AND TransactionID=?";
                DB::update($query, [$getAuth['UserID'], $getAuth['ClientID'], $request->id]);

                $query = "UPDATE TrTransactionProductVariant SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ClientID=? AND TransactionID=?";
                DB::update($query, [$getAuth['UserID'], $getAuth['ClientID'], $request->id]);
                $return['message'] = "Transaction successfully deleted";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
}