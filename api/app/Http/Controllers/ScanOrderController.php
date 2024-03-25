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
                        WHERE MsProduct.ID = ?";
            $product = DB::select($query,[$request->ID])[0];

            $query = "SELECT MsVariant.ID variantID, MsVariant.Name name, MsVariant.Type type, MsVariantProduct.ID variantProductID, MsVariantOption.ID variantOptionID, MsVariantOption.Label label, MsVariantOption.Price price
                        FROM MsVariant
                        JOIN MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                        JOIN MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                        WHERE MsVariantProduct.ProductID = ?
                        ORDER BY MsVariant.Name ASC, MsVariantOption.Label ASC";
            $variant = DB::select($query,[$request->ID]);

            $return['data'] = array('product'=>$product, 'variant'=>$variant);
        } else {
            $query = "SELECT MsProduct.ID id, MsProduct.Name name, MsCategory.ID idCategory, MsCategory.Name categoryName, MsProduct.Notes notes, MsProduct.Price price, CASE ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('https://serenepos.temandigital.id/api/uploaded/product/', ImgUrl)) END imgUrl
                        FROM MsProduct
                        JOIN MsCategory
                        ON MsCategory.ID = MsProduct.CategoryID
                        ORDER BY Name ASC";
            $data = DB::select($query, [$request->ID]);
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

                $countTransaction = "SELECT COUNT(TransactionNumber) +1 as transNumber FROM TrTransaction 
                WHERE TrTransaction.ClientID = ? ";
                $incrementTransaction = DB::select($countTransaction, [$getAuth['ClientID']]);

                $query = "INSERT INTO TrTransaction
                            (IsDeleted, UserIn, DateIn, ID, TransactionNumber, ClientID, OutletID, PaymentID, TransactionDate, PaidDate, CustomerName, SubTotal, Discount, Tax, TotalPayment, PaymentAmount, Changes, Status, Notes)
                            VALUES
                            (0, '7b8e8da2-cc9f-11ee-8603-ca13603aef66', NOW(), ?, ?, 'afe4146f-cc82-11ee-8603-ca13603aef66', ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
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
                    $transactionProductID = explode(',',$request->transactionProductID);
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
                                (0,'7b8e8da2-cc9f-11ee-8603-ca13603aef66', NOW(), ?, 'afe4146f-cc82-11ee-8603-ca13603aef66', ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $transactionProductID[$i],
                                $productID[$i],
                                $transactionID,
                                $qty[$i],
                                $unitPrice[$i],
                                $discountProduct[$i],
                                $unitPrice[$i] - $discountProduct[$i],
                                $notesProduct[$i],
                            ]);
                    }
                } else {
                    $query = "INSERT INTO TrTransactionProduct
                                (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, TransactionID, Qty, UnitPrice, Discount, UnitPriceAfterDiscount, Notes)
                                VALUES
                                (0, '7b8e8da2-cc9f-11ee-8603-ca13603aef66' , NOW(), ?, 'afe4146f-cc82-11ee-8603-ca13603aef66' , ?, ?, ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $request->transactionProductID,
                                $request->productID,
                                $TransactionID,
                                $request->qty,
                                $request->unitPrice,
                                $request->discountProduct,
                                $request->unitPrice - $request->discountProduct,
                                $request->notesProduct,
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
                                (0, '7b8e8da2-cc9f-11ee-8603-ca13603aef66' , NOW(), UUID(), 'afe4146f-cc82-11ee-8603-ca13603aef66' , ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $transactionID,
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
                                (0, '7b8e8da2-cc9f-11ee-8603-ca13603aef66', NOW(), UUID(), 'afe4146f-cc82-11ee-8603-ca13603aef66' , ?, ?, ?, ?, ?)";
                            DB::insert($query, [
                                $transactionID,
                                $request->transactionProductIDVariant,
                                $request->variantOptionID,
                                $request->variantLabel,
                                strval($request->variantPrice)
                            ]);
                }
                $return['message'] = "Transaction successfully created.";
            }
        return response()->json($return, 200);
    }
    // END POST SCAN ORDER
}