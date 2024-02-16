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

    private function validateAuth($Token)
    {
        $return = array('status'=>false,'ID'=>"");
        $query = "SELECT MsUser.ID UserID, MsUser.ClientID, MsUser.Name, MsUser.PhoneNumber, MsUser.Email
                    FROM MsUser
                    WHERE MsUser.ID=?";
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

    public function test() {
        $query = "SELECT ord.ID OrderID, opy.ID, opy.PaymentMethodCategory, r.Field2 PaymentMethod, 
                        r.Field3 PaymentLogo, opy.ReferenceID, opy.GopayDeepLink, opy.GrossAmount, opy.IsPaid, opy.ExpiredDate,
                        CASE WHEN (NOW() >= opy.ExpiredDate) THEN 1 ELSE 0 END IsExpired
                    FROM TR_ORDER_PAYMENT opy
                    JOIN TR_ORDER ord ON ord.PaymentID=opy.ID
                        LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
                    WHERE opy.IsCancelled=0
                        AND ord.Status=1
                        AND ord.IsB2B = 0
                    ORDER BY ord.CreatedDate DESC";
        $data = DB::select($query);
        foreach ($data as $item) {
            if ($item->IsExpired) {
                $query = "UPDATE TR_ORDER_PAYMENT SET IsCancelled=1 WHERE ID=?";
                DB::update($query, [$item->ID]);
                $query = "UPDATE TR_ORDER SET Status=5,CancelledDate=NOW(),CancelledReason='Pembatalan otomatis, Batas waktu pembayaran telah berakhir' WHERE PaymentID=?";
                DB::update($query, [$item->ID]);

                $query = "SELECT ProductID, Qty FROM TR_ORDER_PRODUCT WHERE OrderID=?";
                $product = DB::select($query, [$item->OrderID]);
                foreach ($product as $key => $value) {
                    $query = "UPDATE MS_PRODUCT
                                SET Stock=(Stock+".$value->Qty.")
                                WHERE ID=?";
                    DB::update($query, [
                        $value->ProductID
                    ]);
                } 
            }
        }
    }


    public function redirectBlog(Request $request) 
    {
        header("Location: http://catatanella.com");
    }
    public function doRegister(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $isValid = true;
        $_message = "";
        if (strpos($request->txtName, '@') && !filter_var($request->txtName, FILTER_VALIDATE_EMAIL)) {
            $_message = "Please fill in with the correct email address.";
            $isValid = false;
        }
        if ($isValid) {
            $query = "SELECT ID,Status FROM MsUser WHERE (UPPER(Phone) = UPPER(?) OR UPPER(Email) = UPPER(?))";
            $data = DB::select($query,[$request->txtName,$request->txtName]);
            if ($data) {
                $_message = (strpos($request->txtName, '@') ? "Email" : "Number Phone"). " has been registered";
                $isValid = false;
            }
        }
        if ($isValid) {
            $key = $this->randomString(10);
            $encrypt = $this->strEncrypt($key,$request->txtPassword);
            $query = "SELECT UUID() GenID";
            $ID = DB::select($query)[0]->GenID;
            $query = "INSERT INTO MsUser
                            (IsDeleted, UserIn, DateIn, ID, ClientID, OutletID, RegisterFrom, Name, Email, PhoneNumber, Password, Salt, IVssl, Tagssl)
                        VALUES(0, ?, NOW(), ?, ClientID, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            DB::insert($query, [
                $getAuth['UserID'],
                $ID,
                $getAuth['ClientID'],
                $request->txtOutletID,
                "app",
                (strpos($request->txtName, '@') ? $request->txtName : ""),
                $request->txtPhoneNumber,
                base64_encode($encrypt['result']),
                base64_encode($key),
                base64_encode($encrypt['iv']),
                base64_encode($encrypt['tag']),
                "SYSTEM"
            ]);
            $isValid = true;
            $_message = "Registration successful, please log in.!";
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        }
        $return['status'] = $isValid;
        $return['message'] = $_message;
        return response()->json($return, 200);
    }

    public function doLogin(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsUser.ID, MsUser.Name, MsUser.Email, MsUser.PhoneNumber, MsUser.Password, u.Salt, u.IVssl, u.Tagssl
                    FROM MsUser
                    WHERE (UPPER(MsUser.Email) = UPPER(?))
                        AND MsUser.RegisterFrom = 'app'";
        $data = DB::select($query,[$request->txtName,$request->txtName]);
        if ($data) {
            $data = $data[0];
            if ($data->Status==1) {
                $decrypted = $this->strDecrypt(base64_decode($data->Salt),base64_decode($data->IVssl),base64_decode($data->Tagssl),base64_decode($data->Password));
                if ($decrypted == $request->txtPassword) {
                    $SessionID = base64_encode($this->randomString(64).base64_encode(md5($data->ID).time()));
                    $query = "UPDATE MsUser SET Name=? WHERE ID=?";
                    DB::update($query, [
                        $SessionID,
                        $data->ID
                    ]);
                    $return['data'] = array( 
                        'Token' => $SessionID,
                        'Name' => $data->Name
                    );
                    $return['status'] = true;
                    $return['callback'] = "doHandlerLogin(e.data)";
                } else {
                    $return['message'] = "Name and password you entered are incorrect.";
                }
            } else {
                $return['message'] = "User is not active.";
            }
        } else {
            $return['message'] = "Name and password you entered are incorrect.";
        }
        return response()->json($return, 200);
    }

    public function doAuthGoogle(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID,Status,FullName FROM MS_USER WHERE UPPER(Email) = UPPER(?) AND RegisterFrom = 'google'";
        $data = DB::select($query,[$request->Email]);
        $_status = false;
        $_message = "";
        $SessionID = base64_encode($this->randomString(64).base64_encode(md5($request->ID).time()));
        if ($data) {
            $data = $data[0];
            if ($data->Status==1) {
                $query = "UPDATE MS_USER SET Field2=? WHERE Field1=?";
                    DB::update($query, [
                        $SessionID,
                        $request->ID
                    ]);
                    $_status = true;
            } else {
                $_message = "User tidak aktif";
            }
        } else {
            $query = "SELECT UUID() GenID";
            $ID = DB::select($query)[0]->GenID;
            $query = "INSERT INTO MS_USER
                            (ID, UserName, FullName, Email, Field1, Field2, RegisterFrom, AccountType, Status, CreatedDate, CreatedBy)
                        VALUES(?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)";
            DB::insert($query, [
                $ID,
                $request->Email,
                $request->Name,
                $request->Email,
                $request->ID,
                $SessionID,
                "google",
                2,
                "SYSTEM"
            ]);
            $query = "INSERT INTO MS_CUSTOMER
                    (ID, UserID, Code, Name, Email, IsB2B, Status, CreatedDate, CreatedBy)
                    VALUES
                    (UUID(), ?, ?, ?, ?, 0, 1, NOW(), ?)";
                DB::insert($query, [
                    $ID,
                    $this->randomString(10),
                    $request->Name,
                    $request->Email,
                    "SYSTEM"
                ]);
            $_status = true;
        }
        if ($_status) {
            $return['data'] = array(
                'Token' => $SessionID,
                'Name' => $request->Name
            );
            $return['callback'] = "doHandlerLogin(e.data)";
        }
        $return['status'] = $_status;
        $return['message'] = $_message;
        return response()->json($return, 200);
    }
    
    public function doLogout(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID,RegisterFrom
                        FROM MS_USER
                        WHERE ID = UPPER(?)";
            $data = DB::select($query,[$getAuth['UserID']]);
            if ($data) {
                $query = "UPDATE MS_USER SET Field2=NULL WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID']
                ]);
                $return['status'] = true;
                $return['callback'] = "doHandlerLogout('".$data[0]->RegisterFrom."')";
            }
        } else $return = array('status'=>false,'message'=>"Oops! sepertinya kamu belum Login");
        return response()->json($return, 200);
    }

    public function getCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT ID, ClientID, Name, QtyAlert, BGColor
            FROM MsCategory
            WHERE MsCategory.ClientID = ?"; 
            $data = DB::select($query,[$getAuth['ClientID']]);
        $return['data'] = $data[0];
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    return response()->json($return, 200);
    }

    public function getVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "  SELECT ID, Name, Type, 
                            (SELECT COUNT(ProductID)
                                FROM MsVariantProduct
                                WHERE MsVariantProduct.VariantID = MsVariant.ID) Count,
                                (SELECT GROUP_CONCAT(Label SEPARATOR ', ')
                                        FROM MsVariantOption
                                        WHERE MsVariantOption.VariantID = MsVariant.ID 
                                        GROUP BY VariantID) ListLabel
                                FROM MsVariant
                                WHERE {definedFilter}
                                ORDER BY MsVariant.Name ASC";
                            $definedFilter = "1=1";
            if ($getAuth['ClientID'] != "") $definedFilter = "MsVariant.ClientID = '".$getAuth['ClientID']."'";
            if ($request->_i) {
                $definedFilter = "MsVariant.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT    MsVariant.ID,
                                        MsVariant.Name,
                                        MsVariant.Type,
                                        MsVariantOption.ID VariantOptionID,
                                        MsVariantOption.Label,
                                        MsVariantOption.Price,
                                        MsVariantProduct.ProductID
                                FROM    MsVariant
                                JOIN    MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                                JOIN    MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                                WHERE   MsVariantOption.VariantID = ? 
                                ORDER BY  MsVariant.Name ASC";
                    $selVariant = DB::select($query,[$request->_r]);
                    $arrData = [];
                    if ($selVariant) {
                        foreach ($selVariant as $key => $value) {
                            array_push($arrData,$value->ID);
                        }
                    }
                    $return['data'] = array('header'=>$data[0], 'selVariant'=> $selVariant);
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }

    // public function getPosVariant(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //     $query = "SELECT ID, Name, Type, 
    //     (SELECT COUNT(ProductID)
    //         FROM MsVariantProduct
    //         WHERE MsVariantProduct.VariantID = MsVariant.ID) Count,
    //         (SELECT GROUP_CONCAT(Label SEPARATOR ', ')
    //                 FROM MsVariantOption
    //                 WHERE MsVariantOption.VariantID = MsVariant.ID 
    //                 GROUP BY VariantID) ListLabel
    //         FROM MsVariant
    //         WHERE MsVariant.ClientID = ?";
    //         $data = DB::select($query,[$getAuth['ClientID']]);
    //      $return['data'] = $data;
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    // } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    // return response()->json($return, 200);
    // }

    public function getVariantOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT ID, VariantID, Label, Price
            FROM MsVariantOption
            WHERE MsVariantOption.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
         $return['data'] = $data[0];
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    return response()->json($return, 200);
    }
    
    // public function getPosProduct(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //     $query = "SELECT MsProduct.ID, MsProduct.Name, MsProduct.Notes, MsProduct.Qty, MsProduct.Price, MsCategory.ID CategoryID ,MsCategory.Name Category, MsProduct.ProductSKU, MsProduct.ImgUrl, MsProduct.MimeType 
    //         FROM MsProduct
    //         JOIN MsCategory
    //         ON MsProduct.CategoryID = MsCategory.ID
    //         WHERE MsProduct.ClientID = ?";
    //         $data = DB::select($query,[$getAuth['ClientID']]);
    //     $return['data'] = $data[0];
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    // } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    // return response()->json($return, 200);
    // }

    public function getProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "  SELECT 
                                    MsProduct.ID,
                                    MsProduct.ClientID,
                                    MsProduct.Name, 
                                    MsProduct.Notes, 
                                    MsProduct.Qty, 
                                    MsProduct.Price, 
                                    MsCategory.ID CategoryID,
                                    MsCategory.Name Category, 
                                    MsProduct.ProductSKU, 
                                    MsProduct.ImgUrl, 
                                    MsProduct.MimeType 
                            FROM    MsProduct
                            JOIN    MsCategory ON MsProduct.CategoryID = MsCategory.ID
                            WHERE   {definedFilter}
                            ORDER BY MsProduct.Name ASC";
                            $definedFilter = "1=1";
            if ($getAuth['ClientID'] != "") $definedFilter = "MsProduct.ClientID = '".$getAuth['ClientID']."'";
            if ($request->_i) {
                $definedFilter = "MsProduct.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT    MsVariant.ID,
                                        MsVariant.Name,
                                        MsVariant.Type,
                                        MsVariantOption.ID VariantOptionID,
                                        MsVariantOption.Label,
                                        MsVariantOption.Price
                                FROM    MsVariant
                                JOIN    MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                                JOIN    MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                                WHERE   MsVariantProduct.ProductID = ?
                                ORDER BY  MsVariant.Name ASC";
                    $selVariant = DB::select($query,[$request->_i]);
                    $arrData = [];
                    if ($selVariant) {
                        foreach ($selVariant as $key => $value) {
                            array_push($arrData,$value->ID);
                        }
                    }
                    $return['data'] = array('header'=>$data[0], 'selVariant'=> $selVariant);
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }

    public function getProductVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsProductVariant.ID, MsProduct.ID ProductID, MsVariant.ID VariantID, MsVariant.Name
            FROM MsProductVariant
            JOIN MsProduct
            ON MsProduct.ID = MsProductVariant.ProductID
            JOIN MsVariant
            ON MsVariant.ID = MsProductVariant.VariantID
            WHERE MsProductVariant.ClientID = ''
            ORDER BY ID ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function getProductVariantOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsProductVariantOption.ID, MsProduct.ID ProductID, MsVariantOption.ID VariantOptionID, MsVariantOption.Label, MsVariantOption.Price
            FROM MsProductVariantOption
            JOIN MsProduct
            ON MsProduct.ID = MsProductVariantOption.ProductID
            JOIN MsVariantOption
            ON MsVariantOption.ID = MsProductVariantOption.VariantOptionID
            WHERE MsProductVariantOption.ClientID = ''
            ORDER BY ID ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function getTransaction(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
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
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }

    // public function getPosTransaction(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT TrTransaction.ID, TrTransaction.TransactionNumber, MsPayment.ID PaymentID, MsPayment.PaymentCash, MsPayment.PaymentCredit, MsPayment.PaymentDebit, MsPayment.PaymentQRIS, MsPayment.PaymentTransfer, MsPayment.PaymentEWallet, TrTransaction.TransactionDate, TrTransaction.PaidDate, TrTransaction.CustomerName, TrTransaction.SubTotal, TrTransaction.Discount, TrTransaction.Tax, TrTransaction.TotalPayment, TrTransaction.PaymentAmount, TrTransaction.Changes, TrTransaction.Status, TrTransaction.Notes
    //         FROM TrTransaction
    //         JOIN MsPayment
    //         ON MsPayment.ID = TrTransaction.PaymentID
    //         WHERE TrTransaction.ClientID = ''
    //         ORDER BY TransactionDate ASC";
    //     $return['data'] = DB::select($query);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    public function getTransactionHistory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
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
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }

    // public function getPosTransactionHistory(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //     $query = "SELECT    TrTransaction.ID, 
    //                         TrTransaction.ClientID, 
    //                         TrTransaction.TransactionNumber, 
    //                         MsPayment.ID PaymentID, 
    //                         MsPayment.PaymentCash Cash, 
    //                         MsPayment.PaymentCredit Credit, 
    //                         MsPayment.PaymentDebit Debit, 
    //                         MsPayment.PaymentQRIS QRIS, 
    //                         MsPayment.PaymentTransfer Transfer, 
    //                         MsPayment.PaymentEWallet EWallet, 
    //                         TrTransaction.TransactionDate, 
    //                         TrTransaction.PaidDate, 
    //                         TrTransaction.CustomerName, 
    //                         TrTransaction.SubTotal, 
    //                         TrTransaction.Discount, 
    //                         TrTransaction.Tax, 
    //                         TrTransaction.TotalPayment, 
    //                         TrTransaction.PaymentAmount, 
    //                         TrTransaction.Changes, 
    //                         TrTransaction.Status, 
    //                         TrTransaction.Notes, 
    //                         MsClient.Name CashierName, 
    //                         MsClient.OutletID, 
    //                         MsOutlet.Name Outlet
    //                 FROM    TrTransaction
    //                 JOIN    MsPayment ON MsPayment.ID = TrTransaction.PaymentID
    //                 JOIN    MsClient ON MsClient.ID = TrTransaction.ClientID
    //                 JOIN    MsOutlet ON MsOutlet.ID = MsClient.OutletID
    //                 WHERE   TrTransaction.ClientID = ?";
    //                 $data = DB::select($query,[$getAuth['ClientID']]);
    //                 $return['data'] = $data;
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    // } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    // return response()->json($return, 200);
    // }

    // public function getPosTransactionProduct(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT TrTransactionProduct.ID, MsProduct.ID ProductID, MsProduct.Name, MsProduct.Price UnitPrice, TrTransactionProduct.Qty, TrTransactionProduct.UnitPrice, TrTransactionProduct.Discount, TrTransactionProduct.UnitPriceAfterDiscount, TrTransactionProduct.Notes
    //         FROM TrTransactionProduct
    //         JOIN MsProduct
    //         ON MsProduct.ID = TrTransactionProduct.ProductID
    //         WHERE TrTransactionProduct.ClientID = ''
    //         ORDER BY ID ASC";
    //     $return['data'] = DB::select($query);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    // public function getPosTransactionProductVariant(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT TrTransactionProductVariant.ID, MsProduct.ID ProductID, MsVariantOption.ID VariantOptionID, MsVariantOption.VariantID, MsVariantOption.Label, MsVariantOption.Price
    //         FROM TrTransactionProductVariant
    //         JOIN MsProduct
    //         ON MsProduct.ID = TrTransactionProductVariant.ProductID
    //         JOIN MsVariantOption
    //         ON MsVariantOption.ID = TrTransactionProductVariant.VariantOptionID
    //         WHERE TrTransactionProductVariant.ClientID = ''
    //         ORDER BY ID ASC";
    //     $return['data'] = DB::select($query);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    public function getClient(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsClient.ID, MsClient.StoreName, MsClient.Address, MsClient.Name, MsClient.PhoneNumber, MsClient.Message, MsClient.ImgUrl, MsClient.MimeType, MsOutlet.ID OutletID, MsOutlet.Name OutlateName, MsOutlet.DetailsAddress, MsOutlet.IsPrimary
            FROM MsClient
            JOIN MsOutlet
            ON MsOutlet.ID = MsClient.OutletID
            ORDER BY ID ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function getCustomer(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT ID, ClientID, Name, HandphoneNumber, Address, Gender
            FROM MsCustomer
            WHERE MsCustomer.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
        $return['data'] = $data[0];
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    return response()->json($return, 200);
    }

    public function getPayment(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT MsPayment.ID, MsPayment.ClientID, MsPayment.PaymentCash, MsPayment.PaymentCredit, MsPayment.PaymentDebit, MsPayment.PaymentQRIS,  MsPayment.PaymentTransfer, MsPayment.PaymentEWallet
            FROM MsPayment
            WHERE MsPayment.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
        $return['data'] = $data[0];
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    return response()->json($return, 200);
    }

    public function getOutlet(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
        $query = "SELECT MsOutlet.ID, MsOutlet.ClientID, MsOutlet.Name, MsOutlet.PhoneNumber, MsOutlet.PlanType, MsOutlet.IsPrimary, MsOutlet.DetailsAddress
            FROM MsOutlet
            WHERE MsOutlet.ClientID = ?";
            $data = DB::select($query,[$getAuth['ClientID']]);
            $return['data'] = $data[0];
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    return response()->json($return, 200);
    }

    // public function getUser(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //     $query = "SELECT MsUser.ID, MsOutlet.ID OutletID, MsOutlet.Name OutletName, MsUser.Name, MsUser.PhoneNumber, MsUser.Email, MsUser.Password
    //         FROM MsUser
    //         JOIN MsOutlet
    //         ON MsOutlet.ID = MsUser.OutletID
    //         WHERE MsOutlet.ClientID = ?";
    //         $data = DB::select($query,[$getAuth['ClientID']]);
    //       $return['data'] = $data[0];
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    // } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    // return response()->json($return, 200);
    // }

    /* START: PRODUCT */

    // public function getAllProduct(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	// $getAuth = $this->validateAuth($request->_s);
    //     $query = "SELECT * 
    //                 FROM (
    //                         SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
    //                                 IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
    //                                 (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
    //                                 IFNULL((SELECT SUM(Qty) FROM TR_ORDER_PRODUCT WHERE ProductID=p.ID),0) ItemSold,
    //                                 p.Stock,
    //                                 IFNULL(d.DiscountType,0) DiscountType,
    //                                 IFNULL(d.Discount,0) Discount,
	// 			    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty
    //                             FROM MS_PRODUCT p
    //                                 JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                                 LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
    //                             WHERE p.Status = 1 
    //                                 AND b.Status = 1
    //                                 AND p.Stock > 0
    //                                 {definedFilter}
    //                             ORDER BY p.Name ASC
    //                     ) TEMP
    //                 UNION 
    //                 SELECT * 
    //                 FROM (
    //                         SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
    //                                 IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
    //                                 (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
    //                                 IFNULL((SELECT SUM(Qty) FROM TR_ORDER_PRODUCT WHERE ProductID=p.ID),0) ItemSold,
    //                                 p.Stock,
    //                                 IFNULL(d.DiscountType,0) DiscountType,
    //                                 IFNULL(d.Discount,0) Discount,
	// 			    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty

    //                             FROM MS_PRODUCT p
    //                                 JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                                 LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
    //                             WHERE p.Status = 1 
    //                                 AND b.Status = 1
    //                                 AND p.Stock = 0
    //                                 {definedFilter}
    //                             ORDER BY p.Name ASC
    //                     ) TEMP";
    //     $definedFilter = "AND 1=1";
    //     if ($request->BranchID!="") $definedFilter .= " AND b.ID = '".$request->BranchID."'";
    //     if ($request->CatID!="") $definedFilter .= " AND p.ID IN (SELECT ProductID FROM MS_PRODUCT_CATEGORY WHERE CategoryID = '".$request->CatID."')";
    //     if ($request->Keyword!="") $definedFilter .= " AND p.Name LIKE '%".str_replace("'","",$request->Keyword)."%'";
    //     $query = str_replace("{definedFilter}",$definedFilter,$query);
    //     $return['data'] = DB::select($query);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    // public function getProductDetail(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	// $getAuth = $this->validateAuth($request->_s);
    //     $query = "SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
    //                 IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
    //                 IFNULL((SELECT MIN(MinOrder) FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID),0) MinOrder,
    //                 p.Description,
    //                 IFNULL(d.DiscountType,0) DiscountType,
    //                 IFNULL(d.Discount,0) Discount,
    //                 p.Stock,
	// 	    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty
    //             FROM MS_PRODUCT p
    //                 JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                 LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
    //             WHERE p.Status = 1 
    //                 AND b.Status = 1
    //                 AND p.ID = ?";
    //     $data = DB::select($query,[$request->_i]);
    //     $return['data'] = $data[0];
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }
    // public function getProductImage(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT ID, ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=? ORDER BY IsMain DESC";
    //     $return['data'] = DB::select($query,[$request->_i]);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }
    // public function getProductPrice(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT ID, MinOrder, MaxOrder, Price
    //                 FROM MS_PRODUCT_PRICE 
    //                 WHERE ProductID=?
    //                 ORDER BY MinOrder ASC";
    //     $return['data'] = DB::select($query,[$request->ProductID]);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    /* START : PROFILE */
    public function getUser(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT MsUser.ID UserID, MsUser.ClientID, MsUser.Name, MsUser.PhoneNumber, MsUser.Email
                FROM MsUser
                WHERE MsUser.ID=?";
            $data = DB::select($query,[$getAuth['UserID']]);
            $return['data'] = $data[0];
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }

    public function doSaveCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsCategory
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, QtyAlert, BGColor)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmCategoryName,
                    $request->txtFrmQtyAlert,
                    $request->txtFrmBGColor,
                ]);
                $return['message'] = "Category successfully created.";
            } 
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmCategoryName,
                    $request->txtFrmQtyAlert,
                    $request->txtFrmBGColor,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Category successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsCategory
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Category successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Type)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmVariantName,
                    $request->txtFrmVariantType,
                ]);
                $return['message'] = "Variant successfully created.";
            }
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmVariantName,
                    $request->txtFrmVariantType,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Variant successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsVariant
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Variant successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveVariantOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsVariantOption
                        (IsDeleted, UserIn, DateIn, ID, ClientID, VariantID, Label, Price)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmVariantID,
                    $request->txtFrmLabel,
                    $request->txtFrmPrice,
                ]);
                $return['message'] = "Variant Option successfully created.";
            }
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmVariantID,
                    $request->txtFrmLabel,
                    $request->txtFrmPrice,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Variant Option successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsVariantOption
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Variant Option successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveOutlet(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsOutlet
                        (IsDeleted, UserIn, DateIn, ID, Name, PhoneNumber, PlanType, IsPrimary, DetailsAddress)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $request->txtFrmOutletName,
                    $request->txtFrmPhoneNumber,
                    $request->txtFrmPlanType,
                    $request->txtFrmIsPrimary,
                    $request->txtFrmDetailsAddress,
                ]);
                $return['message'] = "Outlet successfully created.";
            } 
            if ($request->hdnAction == "edit") {
                $query = "UPDATE MsOutlet
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PhoneNumber=?,
                    PlanType=?,
                    IsPrimary=?,
                    DetailsAddress=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->txtFrmOutletName,
                    $request->txtFrmPhoneNumber,
                    $request->txtFrmPlanType,
                    $request->txtFrmIsPrimary,
                    $request->txtFrmDetailsAddress,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Outlet successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsOutlet
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Outlet successfully deleted.";
            }
            
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveCustomer(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsCustomer
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, HandphoneNumber, Address, Gender)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmCustomerName,
                    $request->txtFrmPhoneNumber,
                    $request->txtFrmAddress,
                    $request->txtFrmGender,
                ]);
                $return['message'] = "Customer successfully created.";
            } 
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmCustomerName,
                    $request->txtFrmPhoneNumber,
                    $request->txtFrmAddress,
                    $request->txtFrmGender,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Customer successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsCustomer
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Customer successfully deleted.";
            }
            
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsProduct
                    (IsDeleted, UserIn, DateIn, ID, ClientID, Name, Notes, Qty, Price, CategoryID, ProductSKU, ImgUrl, MimeType)
                    VALUES
                    (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmProductName,
                    $request->txtFrmNotes,
                    $request->txtFrmQty,
                    $request->txtFrmPrice,
                    $request->txtFrmCategoryID,
                    $request->txtFrmProductSKU,
                    $request->txtFrmImgUrl,
                    $request->txtFrmMimeType,
                ]);
                $return['message'] = "Product successfully created.";
            } 
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmProductName,
                    $request->txtFrmNotes,
                    $request->txtFrmQty,
                    $request->txtFrmPrice,
                    $request->txtFrmCategoryID,
                    $request->txtFrmImgUrl,
                    $request->txtFrmMimeType,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Product successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsProduct
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Product successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveProductVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsProductVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, VariantID)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmProductID,
                    $request->txtFrmVariantID,
                ]);
                $return['message'] = "Product Variant successfully created.";
            } 
            if ($request->hdnAction == "edit") {
                $query = "UPDATE MsProductVariant
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    ClientID=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmProductID,
                    $request->txtFrmVariantID,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Product Variant successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsProductVariant
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Product Variant successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveProductVariantOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsProductVariantOption
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, VariantOptionID)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmProductID,
                    $request->txtFrmVariantOptionID,
                ]);
                $return['message'] = "Product Variant Option successfully created.";
            } 
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmProductID,
                    $request->txtFrmVariantOptionID,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Product Variant Option successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsProductVariantOption
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Product Variant Option successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveTransaction(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO TrTransaction
                        (IsDeleted, UserIn, DateIn, ID, TransactionNumber, ClientID, PaymentID, TransactionDate, PaidDate, CustomerName, SubTotal, Discount, Tax, TotalPayment, PaymentAmount, Changes, Status, Notes)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $request->txtFrmTransactionNumber,
                    $getAuth['ClientID'],
                    $request->txtFrmPaymentID,
                    $request->txtFrmCustomerName,
                    $request->txtFrmSubTotal,
                    $request->txtFrmDiscount,
                    $request->txtFrmTax,
                    $request->txtFrmTotalPayment,
                    $request->txtFrmPaymentAmount,
                    $request->txtFrmChanges,
                    $request->txtFrmStatus,
                    $request->txtFrmNotes,
                ]);
                $return['message'] = "Transaction successfully created.";
            } 
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmTransactionNumber,
                    $getAuth['ClientID'],
                    $request->txtFrmPaymentID,
                    $request->txtFrmCustomerName,
                    $request->txtFrmSubTotal,
                    $request->txtFrmDiscount,
                    $request->txtFrmTotalPayment,
                    $request->txtFrmPaymentAmount,
                    $request->txtFrmChanges,
                    $request->txtFrmStatus,
                    $request->txtFrmNotes,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Transaction successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM TrTransaction
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Transaction successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveTransactionProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO TrTransactionProduct
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, TransactionID, Qty, UnitPrice, Discount, UnitPriceAfterDiscount, Notes)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmProductID,
                    $request->txtFrmTransactionID,
                    $request->txtFrmQty,
                    $request->txtFrmUnitPrice,
                    $request->txtFrmDiscount,
                    $request->txtFrmUnitPriceAfterDiscount,
                    $request->txtFrmUnitNotes,
                ]);
                $return['message'] = "Transaction Product successfully created.";
            }
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmProductID,
                    $request->txtFrmTransactionID,
                    $request->txtFrmQty,
                    $request->txtFrmUnitPrice,
                    $request->txtFrmDiscount,
                    $request->txtFrmUnitPriceAfterDiscount,
                    $request->txtFrmNotes,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Transaction Product successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM TrTransactionProduct
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Transaction Product successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveTransactionProductVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO TrTransactionProductVariant
                        (IsDeleted, UserIn, DateIn, ID, ClientID, ProductID, VariantOptionID)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmProductID,
                    $request->txtFrmVariantOptionID,
                ]);
                $return['message'] = "Transaction Product Variant successfully created.";
            }
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmProductID,
                    $request->txtFrmVariantOptionID,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Transaction Product Variant successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM TrTransactionProductVariant
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Transaction Product Variant successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }

    public function doSavePayment(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsPayment
                        (IsDeleted, UserIn, DateIn, ID, ClientID, PaymentCash, PaymentCredit, PaymentDebit, PaymentQRIS, PaymentTransfer, PaymentEWallet)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $getAuth['ClientID'],
                    $request->txtFrmPaymentCash,
                    $request->txtFrmPaymentCredit,
                    $request->txtFrmPaymentDebit,
                    $request->txtFrmPaymentQRIS,
                    $request->txtFrmPaymentTransfer,
                    $request->txtFrmPaymentEWallet,
                ]);
                $return['message'] = "Payment successfully created.";
            }
            if ($request->hdnAction == "edit") {
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
                    $request->txtFrmPaymentCash,
                    $request->txtFrmPaymentCredit,
                    $request->txtFrmPaymentDebit,
                    $request->txtFrmPaymentQRIS,
                    $request->txtFrmPaymentTransfer,
                    $request->txtFrmPaymentEWallet,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Payment successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsPayment
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Payment successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    public function doSaveClient(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MsClient
                        (IsDeleted, UserIn, DateIn, ID, OutletID, StoreName, Address, Name, PhoneNumber, Message, ImgUrl, MimeType)
                        VALUES
                        (0, ?, NOW(), UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $request->txtFrmOutletID,
                    $request->txtFrmStoreName,
                    $request->txtFrmClientAddress,
                    $request->txtFrmClientName,
                    $request->txtFrmClientPhoneNumber,
                    $request->txtFrmMessage,
                    $request->txtFrmImgUrl,
                    $request->txtFrmMimeType,
                ]);
                $return['message'] = "Client successfully created.";
            }
            if ($request->hdnAction == "edit") {
                $query = "UPDATE MsClient
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    StoreName=?,
                    Address=?,
                    Name=?,
                    PhoneNumber=?,
                    Message=?,
                    ImgUrl=?,
                    MimeType=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->txtFrmStoreName,
                    $request->txtFrmClientAddress,
                    $request->txtFrmClientName,
                    $request->txtFrmClientPhoneNumber,
                    $request->txtFrmMessage,
                    $request->txtFrmImgUrl,
                    $request->txtFrmMimeType,
                    $request->hdnFrmID
                ]);
                $return['message'] = "Client successfully modified.";
            }
            if ($request->hdnAction == "delete") {
                $query = "DELETE FROM MsClient
                WHERE ID=?";
                DB::delete($query, [$request->hdnFrmID]);
                $return['message'] = "Client successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"Oops! It seems you haven't logged in yet.");
        return response()->json($return, 200);
    }

    /* START : CART */
    // public function getCart(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
    //                         IFNULL(
    //                             (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID AND c.Qty BETWEEN MinOrder AND MaxOrder LIMIT 0,1),
    //                             IFNULL(
    //                                 (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MaxOrder DESC LIMIT 0,1),
    //                                 0
    //                             )
    //                         ) Price,
    //                         (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
    //                         IFNULL(d.DiscountType,0) DiscountType,
    //                         IFNULL(d.Discount,0) Discount,
	// 			p.Stock,
    //                         c.Qty,
    //                         c.Notes
    //                     FROM TR_CART c
    //                         JOIN MS_PRODUCT p ON p.ID = c.ProductID
    //                         JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                         LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
    //                     WHERE p.Status = 1 
    //                         AND b.Status = 1
    //                         AND c.CustomerID = ?
    //                 ORDER BY b.Name ASC, p.Name ASC";
    //         $return['data'] = DB::select($query,[$getAuth['CustomerID']]);
    //         if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }
    // public function doCalculateDelivery(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	// $id = $request->addressId;
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "SELECT DistrictID
    //                     FROM MS_CUSTOMER_ADDRESS a
    //                     WHERE CustomerID=?
    //                     ORDER BY IsDefault DESC";
    //         $addr = DB::select($query,[$getAuth['CustomerID']]);
    //         if ($addr) {
    //             $addr = $addr[0];
    //             $query = "SELECT b.Branch,
    //                             IFNULL((SELECT Fee FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) Fee,
    //                             IFNULL((SELECT 1 FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) IsFound
    //                         FROM (
    //                                 SELECT DISTINCT b.DistrictID, b.Name Branch
    //                                 FROM TR_CART c
    //                                     JOIN MS_PRODUCT p ON p.ID = c.ProductID
    //                                     JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                                 WHERE p.Status = 1 
    //                                     AND b.Status = 1
    //                                     AND c.CustomerID = ?
    //                             ) b";
    //             $return['data'] = DB::select($query,[$addr->DistrictID,$addr->DistrictID,$getAuth['CustomerID']]);
    //             $return['query'] = $query;
    //             if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //         }
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }

    // public function doCalculateDeliveryNew(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	// $id = $request->addressId;
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "SELECT DistrictID
    //                     FROM MS_CUSTOMER_ADDRESS a
    //                     WHERE ID=?
    //                     ORDER BY IsDefault DESC";
    //         $addr = DB::select($query,[$id]);
    //         if ($addr) {
    //             $addr = $addr[0];
    //             $query = "SELECT b.Branch,
    //                             IFNULL((SELECT Fee FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) Fee,
    //                             IFNULL((SELECT 1 FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) IsFound
    //                         FROM (
    //                                 SELECT DISTINCT b.DistrictID, b.Name Branch
    //                                 FROM TR_CART c
    //                                     JOIN MS_PRODUCT p ON p.ID = c.ProductID
    //                                     JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                                 WHERE p.Status = 1 
    //                                     AND b.Status = 1
    //                                     AND c.CustomerID = ?
    //                             ) b";
    //             $return['data'] = DB::select($query,[$addr->DistrictID,$addr->DistrictID,$getAuth['CustomerID']]);
    //             $return['query'] = $addr;
    //             if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //         }
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }

    // public function doSaveCart(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     $isValid = true;
    //     if ($getAuth['status']) {
    //         $query = "SELECT ID, Qty FROM TR_CART WHERE CustomerID=? AND ProductID=?";
    //         $data = DB::select($query,[$getAuth['CustomerID'],$request->ProductID]);

    //         $query = "SELECT ID, Name, Stock FROM MS_PRODUCT WHERE ID=?";
    //         $product = DB::select($query,[$request->ProductID]);
    //         if ($data) {
    //             if ($request->Qty == 0) {
    //                 $query = "DELETE FROM TR_CART WHERE ID=?";
    //                 DB::delete($query, [$data[0]->ID]);
    //                 //$return['message'] = "Produk berhasil dihapus";
    //             } else {
    //                 if ($request->Qty <= $product[0]->Stock) {
    //                     $query = "UPDATE TR_CART
    //                                 SET Qty=?,
    //                                     Notes=?,
    //                                     ModifiedDate=NOW()
    //                                 WHERE ID=?";
    //                     DB::update($query, [
    //                         $request->Source == "product" ? (intval($data[0]->Qty) + 1) : $request->Qty,
    //                         $request->Notes,
    //                         $data[0]->ID
    //                     ]);
    //                     //$return['message'] = "Keranjang berhasil dirubah";
    //                 } else {
    //                     $isValid = false;
    //                     $return['message'] = "Stock hanya tersedia ".$product[0]->Stock;
    //                 }
    //             }
    //         } else {
    //             if ($request->Qty <= $product[0]->Stock) {
    //                 $query = "INSERT INTO TR_CART
    //                                 (ID, CustomerID, ProductID, Qty, Notes, CreatedDate)
    //                             VALUES
    //                                 (UUID(), ?, ?, ?, ?, NOW())";
    //                 DB::insert($query, [
    //                     $getAuth['CustomerID'],
    //                     $request->ProductID,
    //                     $request->Qty,
    //                     $request->Notes
    //                 ]);
    //                 //$return['message'] = "Produk berhasil ditambahkan";
    //             } else {
    //                 $isValid = false;
    //                 $return['message'] = "Stock hanya tersedia ".$product[0]->Stock;
    //             }
    //         }
    //         $return['callback'] = "doHandlerSaveCart('".$request->Source."','".$isValid."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }

    // public function doUpdateCart(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     $isValid = true;
    //     if ($getAuth['status']) {
    //         $i=0;
    //         $return['message'] = "";
    //         foreach ($request->ProductID as $key => $value) {
    //             if ($isValid)
    //             {
    //                 $query = "SELECT ID, Name, Stock FROM MS_PRODUCT WHERE ID=?";
    //                 $product = DB::select($query,[$request->ProductID[$i]]);
    //                 if ($request->Qty[$i] <= $product[0]->Stock) {
    //                     $query = "UPDATE TR_CART
    //                                 SET Qty=?,
    //                                     Notes=?,
    //                                     ModifiedDate=NOW()
    //                                 WHERE ProductID=?
    //                                     AND CustomerID=?";
    //                     DB::update($query, [
    //                         $request->Qty[$i],
    //                         $request->Notes[$i],
    //                         $request->ProductID[$i],
    //                         $getAuth['CustomerID']
    //                     ]);
    //                 } else {
    //                     $isValid = false;
    //                     $return['message'] = "Stok ". $product[0]->Name ." tidak cukup";
    //                 }
    //             }
    //             $i++;
    //         }
    //         $return['callback'] = "doHandlerSaveCartNew('".$isValid."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }
    
    // public function getPaymentMethod(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "SELECT Field1 Category, Field2 ID, Field3 ImagePath FROM MS_REFERENCES WHERE Type='PaymentMethod' AND Status='1' ORDER BY Field1 ASC, Field2 ASC";
    //         $return['data'] = DB::select($query);
    //         if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }
    // public function doPay(Request $request) {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $isValid = true;

    //         $itemParams = [];
    //         $subTotal = 0;
    //         $query = "SELECT b.ID BranchID, 
    //                         p.ID ProductID,
    //                         p.Code,
    //                         p.Name,
    //                         IFNULL(
    //                             (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID AND c.Qty BETWEEN MinOrder AND MaxOrder LIMIT 0,1),
    //                             IFNULL(
    //                                 (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MaxOrder DESC LIMIT 0,1),
    //                                 0
    //                             )
    //                         ) Price,
    //                         IFNULL(d.DiscountType,0) DiscountType,
    //                         IFNULL(d.Discount,0) Discount,
    //                         p.Stock,
    //                         c.Qty,
    //                         c.Notes
    //                     FROM TR_CART c
    //                         JOIN MS_PRODUCT p ON p.ID = c.ProductID
    //                         JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                         LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
    //                     WHERE p.Status = 1 
    //                         AND b.Status = 1
    //                         AND c.CustomerID = ?
    //                 ORDER BY b.Name ASC, p.Name ASC";
    //         $cartData = DB::select($query,[$getAuth['CustomerID']]);
    //         foreach ($cartData as $cart) {
    //             if ($cart->Qty > $cart->Stock) $isValid = false;
    //             if ($isValid) {
    //                 $price = $cart->DiscountType == 0 ? $cart->Price : ($cart->DiscountType == 1 ? ($cart->Price - $cart->Discount) : ($cart->Price - (($cart->Price * $cart->Discount)/100)));
    //                 $subTotal += ($price * $cart->Qty);
    //                 $arrData = array(
    //                     'id' => $cart->Code,
    //                     'price' => $price,
    //                     'quantity' => $cart->Qty,
    //                     'name' => $cart->Name
    //                 );
    //                 array_push($itemParams,$arrData);
    //             }
    //         }
    //         if ($isValid)
    //         {
    //             $query = "SELECT Name, Phone, Email FROM MS_CUSTOMER WHERE ID=?";
    //             $custData = DB::select($query,[$getAuth['CustomerID']])[0];
    
    //             $query = "SELECT a.ID, 
    //                             a.Name,
    //                             a.Phone,
    //                             st.Field2 StateName,
    //                             ct.Field2 CityName,
    //                             dt.Field2 DistrictName,
    //                             a.DistrictID,
    //                             a.PostalCode,
    //                             a.Address
    //                         FROM MS_CUSTOMER_ADDRESS a
    //                             JOIN MS_REFERENCES st ON st.ID = a.StateID
    //                             JOIN MS_REFERENCES ct ON ct.ID = a.CityID
    //                             JOIN MS_REFERENCES dt ON dt.ID = a.DistrictID
    //                         WHERE a.CustomerID=?
    //                         ORDER BY a.IsDefault DESC";
    //             $addressData = DB::select($query,[$getAuth['CustomerID']])[0];
    
    //             $query = "SELECT Field1,Field2,Field3 FROM MS_REFERENCES WHERE Field2 LIKE '%".$request->paymentMethod."%' AND Type = 'PaymentMethod'";
    //             $paymentData = DB::select($query)[0];
    
                
    //             $params = null;
    //             $query = "SELECT d.Fee, b.BranchID, b.Branch
    //                         FROM MS_DELIVERYCOST d
    //                         JOIN (
    //                             SELECT DISTINCT b.DistrictID, b.ID BranchID, b.Name Branch
    //                             FROM TR_CART c
    //                                 JOIN MS_PRODUCT p ON p.ID = c.ProductID
    //                                 JOIN MS_BRANCH b ON b.ID = p.BranchID
    //                             WHERE p.Status = 1
    //                                 AND b.Status = 1
    //                                 AND c.CustomerID = ?
    //                         ) b ON d.FromDistrictID = b.DistrictID
    //                         WHERE d.ToDistrictID = ?";
    //             $deliveryData = DB::select($query,[$getAuth['CustomerID'],$addressData->DistrictID]);
    //             foreach ($deliveryData as $item) {
    //                 $subTotal += $item->Fee;
    //                 $arrData = array(
    //                     'id' => $item->BranchID,
    //                     'price' => $item->Fee,
    //                     'quantity' => 1,
    //                     'name' => 'Ongkir '.$item->Branch
    //                 );
    //                 array_push($itemParams,$arrData);
    //             }
    
    //             $params = [];
    //             $PaymentID = "INV/".date("Ymd")."/".$this->randomString(11);
    //             if ($paymentData->Field1 == "bank_transfer") {
    //                 $params = array(
    //                     'payment_type' => $paymentData->Field1,
    //                     'transaction_details' => array(
    //                         'order_id' => $PaymentID,
    //                         'gross_amount' => $subTotal,
    //                     ),
    //                     'customer_details' => array(
    //                         'email' => $custData->Email,
    //                         'first_name' => $custData->Name,
    //                         'last_name' => '',
    //                         'phone' => $custData->Phone
    //                     ),
    //                     'item_details' => $itemParams,
    //                     'bank_transfer' => array(
    //                         'bank' => $request->paymentMethod,
    //                     ),
    //                 );
    //             }
    //             if ($paymentData->Field1 == "cstore") {
    //                 $params = array(
    //                     'payment_type' => $paymentData->Field1,
    //                     'transaction_details' => array(
    //                         'order_id' => $PaymentID,
    //                         'gross_amount' => $subTotal,
    //                     ),
    //                     'customer_details' => array(
    //                         'email' => $custData->Email,
    //                         'first_name' => $custData->Name,
    //                         'last_name' => '',
    //                         'phone' => $custData->Phone
    //                     ),
    //                     'item_details' => $itemParams,
    //                     'cstore' => array(
    //                         'store' => $request->paymentMethod,
    //                     ),
    //                 );
    //             }
    //             if ($paymentData->Field1 == "gopay") {
    //                 $params = array(
    //                     'payment_type' => $paymentData->Field1,
    //                     'transaction_details' => array(
    //                         'order_id' => $PaymentID,
    //                         'gross_amount' => $subTotal,
    //                     ),
    //                     'customer_details' => array(
    //                         'email' => $custData->Email,
    //                         'first_name' => $custData->Name,
    //                         'last_name' => '',
    //                         'phone' => $custData->Phone
    //                     ),
    //                     'item_details' => $itemParams,
    //                     'gopay' => array(
    //                         'enable_callback' => false
    //                     ),
    //                 );
    //             }
	// 	if ($paymentData->Field1 == "echannel") {
    //                 $params = array(
    //                     'payment_type' => $paymentData->Field1,
    //                     'transaction_details' => array(
    //                         'order_id' => $PaymentID,
    //                         'gross_amount' => $subTotal,
    //                     ),
    //                     'customer_details' => array(
    //                         'email' => $custData->Email,
    //                         'first_name' => $custData->Name,
    //                         'last_name' => '',
    //                         'phone' => $custData->Phone
    //                     ),
    //                     'item_details' => $itemParams,
    //                     'echannel' => array(
    //                         'bill_info1' => 'Payment',
	// 		    'bill_info2' => $PaymentID
    //                     ),
    //                 );
    //             }


    //             $isPaymentValid = false;
    //             $referenceNumber = "";
    //             $followupNumber = "";
    //             $callbackUrl = ""; 
    //             if ($paymentData->Field1 == "direct") {
    //                 $isPaymentValid = true;
    //                 if (explode("|",$paymentData->Field2)[0] == "transfer") $followupNumber = "BCA a/n TJO FELIANA: 2388820999";
    //             }
    //             else
    //             {
    //                 //$serverKey = base64_encode('SB-Mid-server-kO99X1M9McX_o8aq2G5XQWOS:');
    //                 $serverKey = base64_encode('Mid-server-I0gfGfjWQb_FcQOZ5Z-7iOFl:');
    //                 $curl = curl_init();
    //                 curl_setopt_array($curl, array(
    //                     //CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/charge",
    //                     CURLOPT_URL => "https://api.midtrans.com/v2/charge",
    //                     CURLOPT_RETURNTRANSFER => true,
    //                     CURLOPT_ENCODING => "",
    //                     CURLOPT_MAXREDIRS => 10,
    //                     CURLOPT_TIMEOUT => 0,
    //                     CURLOPT_FOLLOWLOCATION => true,
    //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //                     CURLOPT_CUSTOMREQUEST => "POST",
    //                     CURLOPT_POSTFIELDS => json_encode($params),
    //                     CURLOPT_HTTPHEADER => array(
    //                         "Accept: application/json",
    //                         "Content-Type: application/json",
    //                         "Authorization: Basic ".$serverKey
    //                     ),
    //                 ));
    //                 $response = curl_exec($curl);
    //                 curl_close($curl);
    //                 $response = json_decode($response);
                    
    //                 if (isset($response->status_code)) {
    //                     if ($response->status_code != 201) {
    //                         $return['status'] = false;
    //                         $return['message'] = $response->status_message;
    //                     } else {
    //                         $isPaymentValid = true;
    //                         $referenceNumber = $response->transaction_id; 
    //                         $followupNumber = ($paymentData->Field1 == "bank_transfer" ? (explode("|",$paymentData->Field2)[0] == "permata" ? $response->permata_va_number : $response->va_numbers[0]->va_number) : ($paymentData->Field1 == "cstore" ? $response->payment_code : ($paymentData->Field1 == "echannel" ? $response->bill_key : "")));
    //                         $callbackUrl = $paymentData->Field1 == "gopay" ? $response->actions[1]->url : "";
    //                     }
    //                 } else {
    //                     $return['midtrans'] = $response;
    //                     $return['status'] = false;
    //                     $return['message'] = "Error processing data to Midtrans";
    //                 }
    //             }
                
    //             if ($isPaymentValid) {
    //                 $query = "SELECT DATE_ADD(NOW(), INTERVAL 1 DAY) ExpiredDate, DATE_ADD(NOW(), INTERVAL 30 MINUTE) ExpiredDateGopay";
    //                 if ($paymentData->Field1 == "gopay") $ExpiredDate = DB::select($query)[0]->ExpiredDateGopay;
    //                 else $ExpiredDate = DB::select($query)[0]->ExpiredDate;
    //                 $query = "INSERT INTO TR_ORDER_PAYMENT
    //                                 (ID, TransactionID, ExpiredDate, PaymentMethodCategory, PaymentMethod, ReferenceID, GopayDeepLink, GrossAmount, IsPaid, IsCancelled, CreatedDate)
    //                             VALUES
    //                                 (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW())";
    //                 DB::insert($query, [
    //                     $PaymentID,
    //                     $referenceNumber,
    //                     $ExpiredDate,
    //                     $paymentData->Field1,
    //                     $request->paymentMethod,
    //                     $followupNumber,
    //                     $callbackUrl,
    //                     $subTotal
    //                 ]);
    //                 $grossAmount = $subTotal;
    
    //                 $query = "INSERT INTO TR_ORDER_ADDRESS
    //                                 (ID, PaymentID, Name, Phone, StateName, CityName, DistrictName, PostalCode, Address)
    //                             VALUES
    //                                 (UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
    //                 DB::insert($query, [
    //                     $PaymentID,
    //                     $custData->Name,
    //                     $addressData->Phone,
    //                     $addressData->StateName,
    //                     $addressData->CityName,
    //                     $addressData->DistrictName,
    //                     $addressData->PostalCode,
    //                     $addressData->Address
    //                 ]);
                    
    //                 foreach ($deliveryData as $item) {
    //                     $query = "SELECT UUID() GenID";
    //                     $ID = DB::select($query)[0]->GenID;
    //                     $OrderID = 'ORDER-'.date("Ymd").'-'.$this->randomString(10);
    //                     $subTotal = 0;
    //                     foreach ($cartData as $cart) {
    //                         if ($cart->BranchID == $item->BranchID) {
    //                             $price = $cart->DiscountType == 0 ? $cart->Price : ($cart->DiscountType == 1 ? ($cart->Price - $cart->Discount) : ($cart->Price - (($cart->Price * $cart->Discount)/100)));
    //                             $subTotal += ($price * $cart->Qty);
    //                         }
    //                     }
    //                     $query = "INSERT INTO TR_ORDER
    //                                     (ID, BranchID, CustomerID, PaymentID, IsB2B, 
    //                                     OrderNumber, SubTotal, DeliveryFee, Total, 
    //                                     Status, CreatedDate, CreatedBy)
    //                                 VALUES
    //                                     (?, ?, ?, ?, ?,
    //                                     ?, ?, ?, ?, 
    //                                     ?, NOW(), ?)";
    //                     DB::insert($query, [
    //                         $ID,
    //                         $item->BranchID,
    //                         $getAuth['CustomerID'],
    //                         $PaymentID,
    //                         0,
    //                         $OrderID,
    //                         $subTotal,
    //                         $item->Fee,
    //                         ($subTotal + $item->Fee),
    //                         1,
    //                         'SYSTEM'
    //                     ]);
        
    //                     foreach ($cartData as $cart) {
    //                         if ($cart->BranchID == $item->BranchID) {
    //                             $discount = $cart->DiscountType == 0 ? 0 : ($cart->DiscountType == 1 ? $cart->Discount : ($cart->Price * $cart->Discount)/100);
    //                             $query = "INSERT INTO TR_ORDER_PRODUCT
    //                                             (ID, OrderID, ProductID, Qty, SourcePrice, DiscountPrice, ItemPrice, Notes)
    //                                         VALUES
    //                                             (UUID(), ?, ?, ?, ?, ?, ?, ?)";
    //                             DB::insert($query, [
    //                                 $ID,
    //                                 $cart->ProductID,
    //                                 $cart->Qty,
    //                                 $cart->Price,
    //                                 $discount,
    //                                 $cart->Price - $discount,
    //                                 $cart->Notes
    //                             ]);
    
    //                             $query = "UPDATE MS_PRODUCT
    //                                         SET Stock=(Stock-".$cart->Qty.")
    //                                         WHERE ID=?";
    //                             DB::update($query, [
    //                                 $cart->ProductID
    //                             ]);
    //                         }
    //                     }
    //                 }
    
    //                 $query = "DELETE FROM TR_CART WHERE CustomerID=?";
    //                 DB::delete($query, [$getAuth['CustomerID']]);
    
    //                 $return['status'] = true;
    //                 $return['data'] = array(
    //                     'PaymentMethodCategory' => $paymentData->Field1,
    //                     'PaymentMethodLogo' => $paymentData->Field3,
    //                     'PaymentMethod' => explode("|",$paymentData->Field2)[1],
    //                     'ExpiredDate' => $ExpiredDate,
    //                     'ReferenceID' => $followupNumber,
    //                     'GrossAmount' => $grossAmount,
    //                     'GoPayDeepLink' => $callbackUrl
    //                 );
    //                 $return['callback'] = "onCompleteDoPay(e.data)";
    //             }
    //         }
    //         else $return = array('status'=>false,'message'=>"Yahh kamu kalah cepat, sudah keburu dibeli orang");
    //     } 
    //     return response()->json($return, 200);
    // }
    // public function getMidtransNotification(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"");
    //     $orderId = $request->order_id;
    //     $statusCode = $request->status_code;
    //     $grossAmount = $request->gross_amount;
    //     //$serverKey = "SB-Mid-server-kO99X1M9McX_o8aq2G5XQWOS";
    //     $serverKey = "Mid-server-I0gfGfjWQb_FcQOZ5Z-7iOFl";
    //     if ($request->signature_key == openssl_digest($orderId.$statusCode.$grossAmount.$serverKey, 'sha512')) {
    //         if ($request->transaction_status == "settlement" || $request->transaction_status == "capture") {
    //             $query = "UPDATE TR_ORDER SET Status=2, ModifiedBy='Midtrans', ModifiedDate=NOW() WHERE PaymentID=?";
    //             DB::update($query, [$request->order_id]);

    //             $query = "UPDATE TR_ORDER_PAYMENT SET IsPaid=1, PaidDate=? WHERE ID=?";
    //             DB::update($query, [$request->settlement_time, $request->order_id]);
    //         }
    //     }
    //     return response()->json($return, 200);
    // }



    /* START : CHAT */
    // public function getChatList(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "SELECT b.ID, 
    //                         b.Name,
    //                         IFNULL((SELECT SUBSTR(Message,1,100) FROM TR_CHAT_MESSAGE WHERE CustomerID=? AND BranchID=b.ID Order BY CreatedDate DESC LIMIT 0,1),'') LastMessage,
    //                         (SELECT COUNT(ID) FROM TR_CHAT_MESSAGE WHERE CustomerID=? AND BranchID=b.ID AND IsReadByCustomer=0) UnreadMessage
    //                     FROM MS_BRANCH b
    //                     WHERE b.Status = 1";
    //         $return['data'] = DB::select($query,[$getAuth['CustomerID'],$getAuth['CustomerID']]);
    //         if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }
    // public function getChatDetail(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "UPDATE TR_CHAT_MESSAGE SET IsReadByCustomer=1 WHERE BranchID=? AND CustomerID=?";
    //         DB::update($query, [$request->BranchID, $getAuth['CustomerID']]);

    //         $query = "SELECT ID, Message, IsReadByBranch, IsReadByCustomer,
    //                         CASE WHEN (CreatedBy=?) THEN 0 ELSE 1 END IsReply
    //                     FROM TR_CHAT_MESSAGE
    //                     WHERE CustomerID=?
    //                         AND BranchID=?
    //                         AND Status=1
    //                     ORDER BY CreatedDate ASC";
    //         $return['data'] = DB::select($query,[$getAuth['UserID'],$getAuth['CustomerID'],$request->BranchID]);
    //         if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }
    // public function doSaveMessage(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "INSERT INTO TR_CHAT_MESSAGE
    //                         (ID, CustomerID, BranchID, Message, IsReadByBranch, IsReadByCustomer, Status, CreatedDate, CreatedBy)
    //                     VALUES
    //                         (UUID(), ?, ?, ?, 0, 1, 1, NOW(), ?)";
    //         DB::insert($query, [
    //             $getAuth['CustomerID'],
    //             $request->BranchID,
    //             $request->Message,
    //             $getAuth['UserID']
    //         ]);
    //         $return['callback'] = "reloadChatMessage()";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }



    /* START: Transaction */
    // public function getUnpaidTransaction(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "SELECT ord.ID OrderID, opy.ID, opy.PaymentMethodCategory, r.Field2 PaymentMethod, 
    //                         r.Field3 PaymentLogo, opy.ReferenceID, opy.GopayDeepLink, opy.GrossAmount, opy.IsPaid, opy.ExpiredDate,
    //                         CASE WHEN (NOW() >= opy.ExpiredDate) THEN 1 ELSE 0 END IsExpired
    //                     FROM TR_ORDER_PAYMENT opy
    //                     JOIN TR_ORDER ord ON ord.PaymentID=opy.ID
    //                         LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
    //                     WHERE ord.CustomerID=?
    //                         AND opy.IsCancelled=0
    //                         AND ord.Status=1
    //                         AND ord.IsB2B = 0
    //                     ORDER BY ord.CreatedDate DESC";
    //         $data = DB::select($query,[$getAuth['CustomerID']]);
    //         foreach ($data as $item) {
    //             if ($item->IsExpired) {
    //                 $query = "UPDATE TR_ORDER_PAYMENT SET IsCancelled=1 WHERE ID=?";
    //                 DB::update($query, [$item->ID]);
    //                 $query = "UPDATE TR_ORDER SET Status=5,CancelledDate=NOW(),CancelledReason='Pembatalan otomatis, Batas waktu pembayaran telah berakhir' WHERE PaymentID=?";
    //                 DB::update($query, [$item->ID]);

    //                 $query = "SELECT ProductID, Qty FROM TR_ORDER_PRODUCT WHERE OrderID=?";
    //                 $product = DB::select($query, [$item->OrderID]);
    //                 foreach ($product as $key => $value) {
    //                     $query = "UPDATE MS_PRODUCT
    //                                 SET Stock=(Stock+".$value->Qty.")
    //                                 WHERE ID=?";
    //                     DB::update($query, [
    //                         $value->ProductID
    //                     ]);
    //                 } 
    //             }
    //         }
    //         $return['data'] = DB::select($query,[$getAuth['CustomerID']]);
    //         if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }
    
    // public function getTransactionDetail(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $getAuth = $this->validateAuth($request->_s);
    //     if ($getAuth['status']) {
    //         $query = "SELECT opy.ID, opy.CreatedDate, opy.IsPaid, opy.PaidDate, opy.GrossAmount, opy.PaymentMethodCategory, r.Field2 PaymentMethod, opy.GrossAmount, 
    //                         oad.Name, oad.Phone, oad.StateName, oad.CityName, oad.DistrictName, oad.PostalCode, oad.Address
    //                     FROM TR_ORDER_PAYMENT opy
    //                         JOIN TR_ORDER_ADDRESS oad ON oad.PaymentID=opy.ID
    //                         LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
    //                     WHERE opy.ID=?";
    //         $paymentData = DB::select($query,[$request->ID])[0];
    //         $query = "SELECT o.ID, o.OrderNumber, b.Name Branch, b.ID BranchID, o.SubTotal, o.DeliveryFee, o.Discount, o.ShippingDate, o.TrackingNumber, o.ShippingMethod, o.Status,
    //                         p.Name Product, op.Qty, op.Notes, o.CancelledReason, (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
    //                         op.SourcePrice, op.ItemPrice, (IFNULL(op.DiscountPrice,0) * op.Qty) SubDiscount, (IFNULL(op.SourcePrice,0) * op.Qty) SubTotal
    //                     FROM TR_ORDER o
    //                         JOIN MS_BRANCH b ON b.ID=o.BranchID
    //                         JOIN TR_ORDER_PRODUCT op ON op.OrderID=o.ID
    //                         JOIN MS_PRODUCT p ON p.ID=op.ProductID
    //                     WHERE o.PaymentID=?
    //                     ORDER BY b.Name, p.Name ASC";
    //         $orderData = DB::select($query,[$request->ID]);
    //         $return['data'] = array('ID' => urlencode($paymentData->ID), 'paymentData' => $paymentData, 'orderData' => $orderData);
    //         if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
    //     return response()->json($return, 200);
    // }



    /* START : HELP */
    // public function getHelpList(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT Field1 ID, Field2 Title, Field3 Content
    //                 FROM MS_REFERENCES
    //                 WHERE Status = 1
    //                     AND Type = 'FAQ'
    //                 ORDER BY Field1 ASC";
    //     $return['data'] = DB::select($query);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    // public function printOrder(Request $request)
    // {
    //     $result = '
    //     <!doctype html>
    //     <html lang="en">
    //         <head>
    //             <meta charset="utf-8">
    //             <meta name="viewport" content="width=device-width, initial-scale=1">
    //             <link href="https://ellafroze.com/assets/favicon.ico" rel="shortcut icon" />
    //             <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    //             <title>Ella Froze - Invoice</title>
    //         </head>
    //         <body style="font-size:7pt">
    //             <div class="col-lg-8 mx-auto p-3 py-md-5">
    //                 <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
    //                     <div class="col-xs-12">
    //                         <img src="https://ellafroze.com/assets/img/logo.png" height="100px" />
    //                     </div>
    //                 </header>
    //                 <main>{dataObject}</main>
    //                 <footer class="pt-5 my-5 text-muted">
    //                     Dicetak pada: '.date("Y-m-d").'
    //                 </footer>
    //             </div>
    //             <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    //         </body>
    //     </html>';
    //     if ($request->i) {
    //         $query = "SELECT opy.ID, opy.CreatedDate, opy.IsPaid, opy.PaidDate, opy.GrossAmount, opy.PaymentMethodCategory, r.Field2 PaymentMethod, opy.GrossAmount, 
    //                         oad.Name, oad.Phone, oad.StateName, oad.CityName, oad.DistrictName, oad.PostalCode, oad.Address
    //                     FROM TR_ORDER_PAYMENT opy
    //                         JOIN TR_ORDER_ADDRESS oad ON oad.PaymentID=opy.ID
    //                         LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
    //                     WHERE opy.ID=?";
    //         $paymentData = DB::select($query,[urldecode($request->i)])[0];
    //         $query = "SELECT o.ID, o.OrderNumber, b.Name Branch, b.ID BranchID, o.SubTotal, o.DeliveryFee, o.Discount, o.ShippingDate, o.TrackingNumber, o.ShippingMethod, o.Status,
    //                         p.Name Product, op.Qty, op.Notes, o.CancelledReason,
    //                         op.SourcePrice, op.ItemPrice, (IFNULL(op.DiscountPrice,0) * op.Qty) SubDiscount, (IFNULL(op.SourcePrice,0) * op.Qty) SubTotal
    //                     FROM TR_ORDER o
    //                         JOIN MS_BRANCH b ON b.ID=o.BranchID
    //                         JOIN TR_ORDER_PRODUCT op ON op.OrderID=o.ID
    //                         JOIN MS_PRODUCT p ON p.ID=op.ProductID
    //                     WHERE o.PaymentID=?
    //                     ORDER BY b.Name, p.Name ASC";
    //         $orderData = DB::select($query,[urldecode($request->i)]);


    //         if ($paymentData) {
    //             $arrData = '
    //             <table>
    //             <tbody>
    //                 <tr>
    //                     <td width="150px">No. Invoice</td>
    //                     <td>: <b>'.$paymentData->ID.'<b></td>
    //                 </tr>
    //                 <tr>
    //                     <td>Tgl. Pesanan</td>
    //                     <td>: '.$paymentData->CreatedDate.'</td>
    //                 </tr>
    //                 <tr>
    //                     <td valign="top">Pengiriman</td>
    //                     <td>: '.$paymentData->Name.'<br />&nbsp;&nbsp;'.$paymentData->StateName.', '.$paymentData->CityName.', '.$paymentData->DistrictName.'<br />&nbsp;&nbsp;'.$paymentData->Address.'<br />&nbsp;&nbsp;'.$paymentData->PostalCode.'<br />&nbsp;&nbsp;'.$paymentData->Phone.'</td>
    //                 </tr>
    //             </tbody>
    //             </table>
    //             <table class="table">
    //             <thead>
    //                 <tr>
    //                     <th scope="col">Item</th>
    //                     <th scope="col">Jumlah</th>
    //                     <th scope="col">Harga Barang</th>
    //                     <th scope="col">Subtotal</th>
    //                 </tr>
    //             </thead>
    //             <tbody>';
    //             $subTotal = 0;
    //             $deliveryFee = 0;
    //             $totalDiscount = 0;
    //             $total = 0;
    //             foreach($orderData as $item) {
    //                 $arrData .= '<tr>
    //                                 <td>'.$item->Product.'</td>
    //                                 <td>'.number_format($item->Qty).'</td>
    //                                 <td>'.($item->SourcePrice != $item->ItemPrice ? '<small><s>Rp ' .number_format($item->SourcePrice). '</s></small> Rp '.number_format($item->ItemPrice) : 'Rp '.number_format($item->ItemPrice)).'</td>
    //                                 <td>Rp '.number_format($item->ItemPrice * $item->Qty).'</td>
    //                             </tr>';
    //                 $arrData .= '<tr><td colspan="3">Catatan: '.$item->Notes.'</td></tr>';

    //                 $subTotal += $item->SubTotal;
    //                 $deliveryFee = $item->DeliveryFee;
    //                 $totalDiscount += $item->SubDiscount;
    //             }
    //             $arrData .= '   
    //             <tr>
    //                 <th scope="col" colspan="3">Subtotal Harga Barang</th>
    //                 <th scope="col">Rp '.number_format($subTotal).'</th>
    //             </tr>
    //             <tr>
    //                 <th scope="col" colspan="3">Total Discount</th>
    //                 <th scope="col">- Rp '.number_format($totalDiscount).'</th>
    //             </tr>
    //             <tr>
    //                 <th scope="col" colspan="3">Ongkos Kirim</th>
    //                 <th scope="col">Rp '.number_format($deliveryFee).'</th>
    //             </tr>
    //             <tr>
    //                 <th scope="col" colspan="3"><b>Total Bayar</b></th>
    //                 <th scope="col"><b>Rp '.number_format(($subTotal + $deliveryFee) - $totalDiscount).'</b></th>
    //             </tr>
    //             </tbody>
    //             </table>';
    //             $result = str_replace("{dataObject}",$arrData,$result);
    //         } else {
    //             //$result = "Not Authorized";
    //         }
    //     } else {
    //         //$result = "Not Authorized";
    //     }
    //     return response($result);
    // }

    // /* START : ARTICLE */
    // public function getArticle(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT ID, Type, Title, ImageUrl, Contents
    //                 FROM MS_ARTICLE
    //                 WHERE Status = 1
    //                     AND Type = ?
    //                 ORDER BY CreatedDate DESC";
    //     $return['data'] = DB::select($query,[$request->Type]);
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    // public function getArticleDetail(Request $request)
    // {
    //     $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
    //     $query = "SELECT ID, Type, Title, ImageUrl, Contents
    //                 FROM MS_ARTICLE
    //                 WHERE Status = 1
    //                     AND ID = ? ";
    //     $return['data'] = DB::select($query,[$request->ID])[0];
    //     if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
    //     return response()->json($return, 200);
    // }

    // public function viewArticleDetail(Request $request)
    // {
	// $query = "SELECT ID, Type, Title, ImageUrl, Contents
    //                 FROM MS_ARTICLE
    //                 WHERE Status = 1
    //                     AND ID = ? ";
    //     $articleData = DB::select($query,[urldecode($request->i)])[0];

    //     $result = '
    //     <!doctype html>
    //     <html lang="en">
    //         <head>
    //             <meta charset="utf-8">
    //             <meta name="viewport" content="width=device-width, initial-scale=1">
    //             <link href="https://ellafroze.com/assets/favicon.ico" rel="shortcut icon" />
    //             <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    //             <title>Ella Froze - '.$articleData->Title.'</title>
    //         </head>
    //         <body style="font-size:7pt">
    //             <div class="col-lg-8 mx-auto p-3 py-md-5">
	// 	    <img src="https://ellafroze.com/api/uploaded/article/'.$articleData->ImageUrl.'" style="width: 100%;height: auto;" />
	// 	    <br /><br /><br /><br />
	// 	    <h3>'.$articleData->Title.'</h3>
    //                 <main style="font-size:12pt">'.$articleData->Contents.'</main>
    //             </div>
    //             <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    //         </body>
    //     </html>';
    //     return response($result);
    // }
}