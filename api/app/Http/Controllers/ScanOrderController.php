<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ScanOrderController extends Controller
{
    // GET SCAN ORDER
    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        if ($request->ID) {
            $query = "SELECT MsProduct.ID id, MsProduct.Name name, MsCategory.ID idCategory, MsCategory.Name nameCategory, MsProduct.Qty qty, MsProduct.Price price, MsProduct.Notes notes, CASE ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('https://serenepos.temandigital.id/api/uploaded/product/', ImgUrl)) END imgUrl
                        FROM MsProduct
                        JOIN MsCategory
                        ON MsProduct.CategoryID = MsCategory.ID
                        WHERE MsProduct.IsDeleted = 0 AND MsProduct.ID = ?";
            $product = DB::select($query,[$request->ID])[0];

            $query = "SELECT MsVariant.ID variantID, MsVariant.Name name, MsVariant.Type type, MsVariantProduct.ID variantProductID, MsVariantOption.ID variantOptionID, MsVariantOption.Label label, MsVariantOption.Price price
                        FROM MsVariant
                        JOIN MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                        JOIN MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                        WHERE MsVariant.IsDeleted = 0 AND MsVariantProduct.ProductID = ?
                        ORDER BY MsVariant.Name ASC, MsVariantOption.Label ASC";
            $variant = DB::select($query,[$request->ID]);

            $return['data'] = array('product'=>$product, 'variant'=>$variant);
        } else {
            $query = "SELECT MsProduct.ID id, MsProduct.Name name, MsTable.Name tableName, MsClient.Name clientName, MsCategory.ID idCategory, MsCategory.Name categoryName, MsProduct.Notes notes, MsProduct.Price price, CASE MsProduct.ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('https://serenepos.temandigital.id/api/uploaded/product/', MsProduct.ImgUrl)) END imgUrl
                        FROM MsProduct
                        JOIN MsCategory
                        ON MsCategory.ID = MsProduct.CategoryID
                        JOIN MsTable
                        ON MsTable.ClientID = MsProduct.ClientID
                        JOIN MsClient
                        ON MsClient.ID = MsProduct.ClientID
                        WHERE MsProduct.IsDeleted = 0 AND MsTable.ID = ?
                        ORDER BY Name ASC";
            $data = DB::select($query, [$request->TableID]);
            if ($data) {
                $return['data'] = $data;
            }
        }
        return response()->json($return, 200);
    }
    // END GET SCAN ORDER
    
    // POST SCAN ORDER
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        if ($request->action == "add") {
                $query = "SELECT UUID() GenID";
                $transactionID = DB::select($query)[0]->GenID;

                $getID = "SELECT MsClient.ID clientID, MsUser.ID userID, MsOutlet.ID outletID, MsClient.Name clientName
                FROM MsClient
                JOIN MsOutlet
                ON MsOutlet.ClientID = MsClient.ID
                JOIN MsTable 
                ON MsTable.ClientID = MsClient.ID
                JOIN MsUser
                ON MsUser.ClientID = MsClient.ID
                WHERE MsTable.ID = ?";
                $getDataID = DB::select($getID, [$request->id]);

                $countTransaction = "SELECT COUNT(TransactionNumber) +1 as transNumber FROM TrTransaction 
                WHERE TrTransaction.ClientID = ? ";
                $incrementTransaction = DB::select($countTransaction, [$getDataID[0]->clientID]);

                $initials = '';
                $clientName = strtoupper(trim($getDataID[0]->clientName));
                if (str_contains($getDataID[0]->clientName,' ')) {
                    $arrInitials = explode(' ',$clientName);
                    $initials = substr($arrInitials[0],0,1).substr($arrInitials[1],0,1);
                } else {
                    $initials = substr($getDataID[0]->clientName,0,1);
                }

                $query = "INSERT INTO TrTransaction
                            (IsDeleted, UserIn, DateIn, ID, TransactionNumber, ClientID, OutletID, PaymentID, TransactionDate, PaidDate, CustomerName, SubTotal, Discount, Tax, TotalPayment, PaymentAmount, Changes, Status, Notes)
                            VALUES
                            (0, ?, NOW(), ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getDataID[0]->userID,
                    $transactionID,
                    $initials.$incrementTransaction[0]->transNumber,
                    $getDataID[0]->clientID,
                    $getDataID[0]->outletID,
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
                                $getDataID [0]->userID,
                                $transactionID."~".$productID[$i],
                                $getDataID [0]->clientID,
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
                                $getDataID [0]->userID,
                                $transactionID."~".$request->productID,
                                $getDataID [0]->clientID,
                                $request->productID,
                                $TransactionID,
                                intval($request->qty),
                                floatval($request->unitPrice),
                                floatval($request->discountProduct),
                                floatval($request->unitPrice) - floatval($request->discountProduct),
                                $request->notesProduct,
                            ]);
                            $return['message'] = "Transaction Product successfully created.";
                }

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
                                $getDataID [0]->userID,
                                $getDataID [0]->clientID,
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
                                $getDataID [0]->userID,
                                $getDataID [0]->clientID,
                                $transactionID,
                                $transactionID."~".explode('~',$request->variantOptionID)[0],
                                $request->transactionProductIDVariant,
                                explode('~',$request->variantOptionID)[1],
                                explode('~',$request->variantLabel)[2],
                                floatval(explode('~',$request->variantPrice)[2])
                            ]);
                }
                $return['message'] = "Transaction successfully created.";
            }
        return response()->json($return, 200);
    }
    // END POST SCAN ORDER
}