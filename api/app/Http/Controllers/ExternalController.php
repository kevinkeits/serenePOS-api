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
        $return = array('status'=>false,'message'=>"",'data'=>null);
        $isValid = true;
        $_message = "";
        if (!filter_var($request->Email, FILTER_VALIDATE_EMAIL)) {
            $_message = "Please fill in with the correct email address.";
            $isValid = false;
        }
        if ($isValid) {
            $query = "SELECT ID FROM MsUser WHERE UPPER(Email) = UPPER(?)";
            $data = DB::select($query,[$request->Email]);
            if ($data) {
                $_message = "This email has been registered";
                $isValid = false;
            }
        }
        if ($isValid) {
            $query = "SELECT UUID() GenID";
            $ClientID = DB::select($query)[0]->GenID;
            $query = "INSERT INTO MsClient (IsDeleted, UserIn, DateIn, ID, Name, PlanType)
                    VALUES (0, 'SYSTEM', NOW(), ?, ?, '1')";
            DB::insert($query, [
                $ClientID,
                $request->StoreName,
            ]);

            $query = "SELECT UUID() GenID";
            $OutletID = DB::select($query)[0]->GenID;
            $query = "INSERT INTO MsOutlet (IsDeleted, UserIn, DateIn, ClientID, ID, Name, IsPrimary)
                    VALUES (0, 'SYSTEM', NOW(), ?, ?, ?, 1)";
            DB::insert($query, [
                $ClientID,
                $OutletID,
                $request->StoreName,
            ]);
            
            $key = $this->randomString(10);
            $encrypt = $this->strEncrypt($key,$request->Password);
            $query = "SELECT UUID() GenID";
            $UserID = DB::select($query)[0]->GenID;
            $query = "INSERT INTO MsUser
                            (IsDeleted, UserIn, DateIn, ID, ClientID, OutletID, RegisterFrom, Name, Email, Password, Salt, IVssl, Tagssl)
                        VALUES(0, 'SYSTEM', NOW(), ?, ?, ?, 'App', ?, ?, ?, ?, ?, ?)";
            DB::insert($query, [
                $UserID,
                $ClientID,
                $OutletID,
                
                $request->Name,
                $request->Email,
                base64_encode($encrypt['result']),
                base64_encode($key),
                base64_encode($encrypt['iv']),
                base64_encode($encrypt['tag']),
            ]);
            $isValid = true;
            $_message = "Registration successful, please log in!";
        }
        $return['status'] = $isValid;
        $return['message'] = $_message;
        return response()->json($return, 200);
    }

    public function doLogin(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null);
        $query = "SELECT IsDeleted, ID, Name, Email, Password, Salt, IVssl, Tagssl
                    FROM MsUser
                    WHERE (UPPER(Email) = UPPER(?))
                        AND RegisterFrom = 'app'";
        $data = DB::select($query,[$request->Email]);
        if ($data) {
            $data = $data[0];
            if ($data->IsDeleted==0) {
                $decrypted = $this->strDecrypt(base64_decode($data->Salt),base64_decode($data->IVssl),base64_decode($data->Tagssl),base64_decode($data->Password));
                if ($decrypted == $request->Password) {
                    $SessionID = base64_encode($this->randomString(64).base64_encode(md5($data->ID).time()));
                    $query = "INSERT INTO TrSession (IsDeleted, UserIn, DateIn, ID, UserID, IsLoggedOut)
                            VALUES (0, 'SYSTEM', NOW(), ?, ?, 0)";
                    DB::insert($query, [
                        $SessionID,
                        $data->ID,
                    ]);
                    
                    $return['data'] = array( 
                        'Token' => $SessionID,
                        'UserID' => $data->ID,
                        'Name' => $data->Name
                    );
                    $return['status'] = true;
                } else {
                    $return['message'] = "Incorrect Username or Password.";
                }
            } else {
                $return['message'] = "User is not active.";
            }
        } else {
            $return['message'] = "Incorrect Username or Password.";
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
}