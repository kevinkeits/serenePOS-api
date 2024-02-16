<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ExternalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    private function randomString($length) {
		$characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
	private function strEncrypt($salt,$string) {
		$return = array('result'=>"",'iv'=>NULL,'tag'=>NULL);
        $cipher = "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $return['result'] = openssl_encrypt($string, $cipher, $salt, $options=0, $iv, $tag);
            $return['iv'] = $iv;
            $return['tag'] = $tag;
        }
        return $return;
	}
    private function strDecrypt($salt,$iv,$tag,$encrypted) {
		$return = "";
        $cipher = "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $return = openssl_decrypt($encrypted, $cipher, $salt, $options=0, $iv, $tag);
        }
        return $return;
	}

    // public function getUser(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,''=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //     $query = "SELECT MsUser.ID, MsOutlet.ID OutletID, MsOutlet.Name OutletName, MsUser.Name, MsUser.PhoneNumber, MsUser.Email, MsUser.Password
    //         FROM MsUser
    //         JOIN MsOutlet
    //         ON MsOutlet.ID = MsUser.OutletID
    //         WHERE MsOutlet.ClientID = ?";
    //         $data = DB::select($query,[$getAuth['ClientID']]);
    //       $return['data'] = $data[0];
    //     if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
    // } else $return = array('status'=>false,'message'=>"",''=>"doHandlerNotAuthorized()");
    // return response()->json($return, 200);
    // }

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

    public function getUser(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT MsUser.ID UserID, MsUser.ClientID, MsUser.Name, MsUser.PhoneNumber, MsUser.Email
                FROM MsUser
                WHERE MsUser.ID=?";
            $data = DB::select($query,[$getAuth['UserID']]);
            $return['data'] = $data[0];
            if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }

    // GET CATEGORY
    public function getCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT ID, ClientID, Name, QtyAlert, BGColor
            FROM MsCategory
            WHERE MsCategory.ClientID = ?"; 
            $data = DB::select($query,[$getAuth['ClientID']]);
        $return['data'] = $data[0];
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
    }
    // END GET CATEGORY

    // GET CLIENT
    public function getClient(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $query = "SELECT MsClient.ID, MsOutlet.Address, MsClient.Name, MsOutlet.PhoneNumber, MsClient.PlanType, MsClient.Message, MsClient.ImgUrl, MsClient.MimeType, MsOutlet.Name OutlatName, MsOutlet.IsPrimary
            FROM MsClient
            JOIN MsOutlet
            ON MsOutlet.ID = MsClient.OutletID
            ORDER BY ID ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }
    // END GET CLIENT

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

    // GET OUTLET
    public function getOutlet(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT MsOutlet.ID, MsOutlet.ClientID, MsOutlet.Name, MsOutlet.PhoneNumber, MsOutlet.IsPrimary, MsOutlet.Address
            FROM MsOutlet
            WHERE MsOutlet.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
            $return['data'] = $data[0];
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"");
    return response()->json($return, 200);
    }
    // END GET OUTLET

    /* START : PROFILE */
   

    public function doSaveCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsCategory
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, QtyAlert, BGColor)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->CategoryName,
                    $request->tyAlert,
                    $request->BGColor,
                ]);
                $return['message'] = "Category successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsCategory
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    Name=?,
                    QtyAlert=?,
                    BGColor=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->CategoryName,
                    $request->QtyAlert,
                    $request->BGColor,
                    $request->ID
                ]);
                $return['message'] = "Category successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsCategory
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Category successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Type)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantName,
                    $request->VariantType,
                ]);
                $return['message'] = "Variant successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsVariant
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    Name=?,
                    Type=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantName,
                    $request->ariantType,
                    $request->ID
                ]);
                $return['message'] = "Variant successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsVariant
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Variant successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveVariantOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsVariantOption
                        (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, Label, Price)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantID,
                    $request->Label,
                    $request->Price,
                ]);
                $return['message'] = "Variant Option successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsVariantOption
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    VariantID=?,
                    Label=?,
                    Price=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->VariantID,
                    $request->Label,
                    $request->Price,
                    $request->ID
                ]);
                $return['message'] = "Variant Option successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsVariantOption
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Variant Option successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveOutlet(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsOutlet
                        (IsDeleted, UserIn, DateIn, ID, Name, PhoneNumber, IsPrimary, Address)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $request->OutletName,
                    $request->PhoneNumber,
                    $request->IsPrimary,
                    $request->Address,
                ]);
                $return['message'] = "Outlet successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsOutlet
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PhoneNumber=?,
                    IsPrimary=?,
                    Address=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->OutletName,
                    $request->PhoneNumber,
                    $request->IsPrimary,
                    $request->Address,
                    $request->ID
                ]);
                $return['message'] = "Outlet successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsOutlet
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Outlet successfully deleted.";
            }
            
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

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

    public function doSaveProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsProduct
                    (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Notes, Qty, Price, CategoryID, ProductSKU, ImgUrl, MimeType)
                    VALUES
                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductName,
                    $request->Notes,
                    $request->Qty,
                    $request->Price,
                    $request->CategoryID,
                    $request->ProductSKU,
                    $request->ImgUrl,
                    $request->MimeType,
                ]);
                $return['message'] = "Product successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsProduct
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?,
                    Name=?,
                    Notes=?,
                    Qty=?,
                    Price=?,
                    CategoryID=?,
                    ImgUrl=?,
                    MimeType=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductName,
                    $request->Notes,
                    $request->Qty,
                    $request->Price,
                    $request->CategoryID,
                    $request->ImgUrl,
                    $request->MimeType,
                    $request->ID
                ]);
                $return['message'] = "Product successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsProduct
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Product successfully deleted.";
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
                $query = "INSERT INTO MsProductVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, VariantID)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductID,
                    $request->VariantID,
                ]);
                $return['message'] = "Product Variant successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsProductVariant
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductID,
                    $request->VariantID,
                    $request->ID
                ]);
                $return['message'] = "Product Variant successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsProductVariant
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Product Variant successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveProductVariantOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsProductVariantOption
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, VariantOptionID)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->ProductID,
                    $request->VariantOptionID,
                ]);
                $return['message'] = "Product Variant Option successfully created.";
            } 
            if ($request->Action == "edit") {
                $query = "UPDATE MsProductVariantOption
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
                $return['message'] = "Product Variant Option successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsProductVariantOption
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Product Variant Option successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveTransaction(Request $request)
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

    public function doSaveTransactionProduct(Request $request)
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

    public function doSaveTransactionProductVariant(Request $request)
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

    public function doSaveClient(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                $query = "INSERT INTO MsClient
                        (IsDeleted, UserIn, DateIn, ID, Name, PlanType, ImgUrl, MimeType)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $request->ClientName,
                    $request->PlanType,
                    $request->ImgUrl,
                    $request->MimeType,
                ]);
                $return['message'] = "Client successfully created.";
            }
            if ($request->Action == "edit") {
                $query = "UPDATE MsClient
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PlanType=?,
                    ImgUrl=?,
                    MimeType=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->ClientName,
                    $request->PlanType,
                    $request->ImgUrl,
                    $request->MimeType,
                    $request->ID
                ]);
                $return['message'] = "Client successfully modified.";
            }
            if ($request->Action == "delete") {
                $query = "DELETE FROM MsClient
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "Client successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }
}