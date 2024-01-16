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
    private function validateAuth($Token) {
        $return = array('status'=>false,'UserID'=>"");
        $query = "SELECT u.ID, c.ID CustomerID
                    FROM MS_USER u
                        JOIN MS_CUSTOMER c ON c.UserID = u.ID
                    WHERE u.Field2=?";
        $checkAuth = DB::select($query,[$Token]);
        if ($checkAuth) {
            $data = $checkAuth[0];
            $return = array(
                'status' => true,
                'UserID' => $data->ID,
                'CustomerID' => $data->CustomerID
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
        if (strpos($request->txtUsername, '@') && !filter_var($request->txtUsername, FILTER_VALIDATE_EMAIL)) {
            $_message = "Mohon isi dengan Email atau No. Telepon yang benar";
            $isValid = false;
        }
        if ($isValid) {
            $query = "SELECT ID,Status FROM MS_CUSTOMER WHERE IsB2B = 0 AND (UPPER(Phone) = UPPER(?) OR UPPER(Email) = UPPER(?))";
            $data = DB::select($query,[$request->txtUsername,$request->txtUsername]);
            if ($data) {
                $_message = (strpos($request->txtUsername, '@') ? "Email" : "No. Telepon"). " ini sudah terdaftar";
                $isValid = false;
            }
        }
        if ($isValid) {
            $key = $this->randomString(10);
            $encrypt = $this->strEncrypt($key,$request->txtPassword);
            $query = "SELECT UUID() GenID";
            $ID = DB::select($query)[0]->GenID;
            $query = "INSERT INTO MS_USER
                            (ID, UserName, FullName, Email, RegisterFrom, AccountType, Password, Salt, IVssl, Tagssl, Status, CreatedDate, CreatedBy)
                        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)";
            DB::insert($query, [
                $ID,
                $request->txtUsername,
                $request->txtName,
                (strpos($request->txtUsername, '@') ? $request->txtUsername : ""),
                "app",
                2,
                base64_encode($encrypt['result']),
                base64_encode($key),
                base64_encode($encrypt['iv']),
                base64_encode($encrypt['tag']),
                "SYSTEM"
            ]);
            $query = "INSERT INTO MS_CUSTOMER
                    (ID, UserID, Code, Name, Email, Phone, IsB2B, Status, CreatedDate, CreatedBy)
                    VALUES
                    (UUID(), ?, ?, ?, ?, ?, 0, 1, NOW(), ?)";
                DB::insert($query, [
                    $ID,
                    $this->randomString(10),
                    $request->txtName,
                    (strpos($request->txtUsername, '@') ? $request->txtUsername : ""),
                    (strpos($request->txtUsername, '@') ? "" : $request->txtUsername),
                    "SYSTEM"
                ]);
            $isValid = true;
            $_message = "Pendaftaran Sukses, silahkan Login!";
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        }
        $return['status'] = $isValid;
        $return['message'] = $_message;
        return response()->json($return, 200);
    }
    public function doLogin(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT u.ID,u.Status,u.FullName,u.Password,u.Salt,u.IVssl,u.Tagssl
                    FROM MS_USER u
                    JOIN MS_CUSTOMER c ON c.UserID = u.ID
                    WHERE (UPPER(c.Email) = UPPER(?) OR UPPER(c.Phone) = UPPER(?))
                        AND u.RegisterFrom = 'app'";
        $data = DB::select($query,[$request->txtUsername,$request->txtUsername]);
        if ($data) {
            $data = $data[0];
            if ($data->Status==1) {
                $decrypted = $this->strDecrypt(base64_decode($data->Salt),base64_decode($data->IVssl),base64_decode($data->Tagssl),base64_decode($data->Password));
                if ($decrypted == $request->txtPassword) {
                    $SessionID = base64_encode($this->randomString(64).base64_encode(md5($data->ID).time()));
                    $query = "UPDATE MS_USER SET Field2=? WHERE ID=?";
                    DB::update($query, [
                        $SessionID,
                        $data->ID
                    ]);
                    $return['data'] = array( 
                        'Token' => $SessionID,
                        'Name' => $data->FullName
                    );
                    $return['status'] = true;
                    $return['callback'] = "doHandlerLogin(e.data)";
                } else {
                    $return['message'] = "Username atau Password salah";
                }
            } else {
                $return['message'] = "User tidak aktif";
            }
        } else {
            $return['message'] = "Username atau Password salah";
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

    public function getBranch(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID,Name
                    FROM MS_BRANCH
                    WHERE Status=1
                    ORDER BY Name ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }
    public function getBanner(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, Name, Keyword, URL, ImagePath
                    FROM MS_BANNER 
                    WHERE Status=1 
                    ORDER BY CreatedDate DESC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }
    public function getDiscount(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	$getAuth = $this->validateAuth($request->_s);
if ($getAuth['status']) {
        $query = "SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
                    IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
                    (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
                    IFNULL((SELECT SUM(Qty) FROM TR_ORDER_PRODUCT WHERE ProductID=p.ID),0) ItemSold,
                    IFNULL(d.DiscountType,0) DiscountType,
                    IFNULL(d.Discount,0) Discount,
                    p.Stock,
		    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty
                FROM MS_PRODUCT p
                    JOIN MS_BRANCH b ON b.ID = p.BranchID
                    JOIN MS_DISCOUNT d ON d.ProductID = p.ID
                WHERE p.Status = 1 
                    AND b.Status = 1
                    AND d.Status = 1
                    {definedFilter}
                    AND CURDATE() BETWEEN d.StartDate AND d.EndDate
               	ORDER BY p.CreatedDate DESC
		LIMIT 10";
        $definedFilter = " AND 1=1";
        if ($request->BranchID!="" && $request->BranchID!="undefined") $definedFilter = " AND b.ID = '".$request->BranchID."'";
        $query = str_replace("{definedFilter}",$definedFilter,$query);
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
} else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");

        return response()->json($return, 200);
    }

    public function getHighestSold(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	$getAuth = $this->validateAuth($request->_s);
if ($getAuth['status']) {
        $query = "SELECT * FROM ( SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
                    IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
                    (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
                    IFNULL((SELECT SUM(Qty) FROM TR_ORDER_PRODUCT WHERE ProductID=p.ID),0) ItemSold,
                    IFNULL((SELECT DiscountType FROM MS_DISCOUNT WHERE Status = 1 AND ProductID = p.ID AND CURDATE() BETWEEN StartDate AND EndDate LIMIT 1),0) DiscountType,
		    IFNULL((SELECT Discount FROM MS_DISCOUNT WHERE Status = 1 AND ProductID = p.ID AND CURDATE() BETWEEN StartDate AND EndDate LIMIT 1),0) Discount,
                    p.Stock,
		    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty
                FROM MS_PRODUCT p
                    JOIN MS_BRANCH b ON b.ID = p.BranchID
                WHERE p.Status = 1 
                    AND b.Status = 1
                    {definedFilter}
		) A 
               	ORDER BY A.ItemSold DESC
		LIMIT 10";
        $definedFilter = " AND 1=1";
        if ($request->BranchID!="" && $request->BranchID!="undefined") $definedFilter = " AND b.ID = '".$request->BranchID."'";
        $query = str_replace("{definedFilter}",$definedFilter,$query);
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
} else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");

        return response()->json($return, 200); 
    }

    public function getNotification(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT IFNULL(COUNT(DISTINCT ProductID),0) Total FROM TR_CART WHERE CustomerID=?";
            $cartData = DB::select($query,[$getAuth['CustomerID']]);

            $query = "SELECT IFNULL(COUNT(ID),0) Total FROM TR_CHAT_MESSAGE WHERE IsReadByCustomer='0' AND CustomerID=?";
            $messageData = DB::select($query,[$getAuth['CustomerID']]);

            $query = "SELECT IFNULL(COUNT(ID),0) Total FROM TR_ORDER WHERE Status IN ('1','2','3') AND CustomerID=?";
            $orderData = DB::select($query,[$getAuth['CustomerID']]);

            if ($request->_cb) $return['callback'] = $request->_cb."(e.data)";
            $return['data'] = array('cartData' => intval($cartData[0]->Total), 'messageData' => intval($messageData[0]->Total), 'orderData' => intval($orderData[0]->Total));
        } else {
            $return['data'] = array('cartData' => 0, 'messageData' => 0, 'orderData' => 0);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data)";
        }
        return response()->json($return, 200);
    }

    public function testProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsProduct.ID, MsProduct.ClientID, MsProduct.Name, MsProduct.Description, MsProduct.Qty, MsProduct.Price, MsProduct.CategoryID, MsCategory.Name, MsProduct.ProductSKU 
        FROM MsProduct
        JOIN MsCategory
        ON MsProduct.CategoryID = MsCategory.ID
        ORDER BY ProductSKU ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testProductVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsProductVariant.ID, MsProductVariant.ClientID, MsProductVariant.ProductID, MsProductVariant.VariantID, MsProductVariant.Label, MsProductVariant.AdditionalPrice
        FROM MsProductVariant
        ORDER BY Label ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, ClientID, Name 
        FROM MsCategory
        ORDER BY Name ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsVariant.ID, MsVariant.ClientID, MsVariant.Name, MsVariant.Type
        FROM MsVariant
        ORDER BY Name ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testTransaction(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT TrTransaction.ID, TrTransaction.ClientID, TrTransaction.TransactionDate, TrTransaction.PaidDate, TrTransaction.CustomerName, TrTransaction.HandphoneNumber, TrTransaction.SubTotal, TrTransaction.Discount, TrTransaction.Tax, TrTransaction.TotalPayment, TrTransaction.PaymentAmount, TrTransaction.Changes, TrTransaction.Status, TrTransaction.Notes
        FROM TrTransaction
        ORDER BY DateIn ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testTransactionProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT TrTransactionProduct.ID, TrTransactionProduct.ClientID, TrTransactionProduct.ProductID, TrTransactionProduct.TransactionID, TrTransactionProduct.Qty, TrTransactionProduct.UnitPrice,TrTransactionProduct.Discount, TrTransactionProduct.UnitPriceAfterDiscount, TrTransactionProduct.Notes
        FROM TrTransactionProduct
        JOIN TrTransaction
        ON TrTransactionProduct.TransactionID = TrTransaction.ID
        ORDER BY DateIn ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testTransactionPayment(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT TrTransactionPayment.ID, TrTransactionPayment.ClientID, TrTransactionPayment.TransactionID, TrTransactionPayment.PaymentID, TrTransactionPayment.Amount
        FROM TrTransactionPayment
        ORDER BY DateIn ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testClient(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, Name 
        from MsClient
        ORDER BY Name ASC;";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testCustomer(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsCustomer.ID, MsCustomer.ClientID, MsCustomer.Name, MsCustomer.HandphoneNumber, MsCustomer.Address, MsCustomer.Gender
        FROM MsCustomer
        ORDER BY Name ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function testPayment(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT MsPayment.ID, MsPayment.ClientID, MsPayment.PaymentCash, MsPayment.PaymentCash, MsPayment.PaymentCredit, MsPayment.PaymentDebit, MsPayment.PaymentQRIS, MsPayment.PaymentTransfer, MsPayment.PaymentEWallet
        FROM MsPayment
        ORDER BY ID DESC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    /* START: PRODUCT */
    public function getCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, Name 
        from MsClient
        ORDER BY Name ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function getAllProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	$getAuth = $this->validateAuth($request->_s);
        $query = "SELECT * 
                    FROM (
                            SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
                                    IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
                                    (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
                                    IFNULL((SELECT SUM(Qty) FROM TR_ORDER_PRODUCT WHERE ProductID=p.ID),0) ItemSold,
                                    p.Stock,
                                    IFNULL(d.DiscountType,0) DiscountType,
                                    IFNULL(d.Discount,0) Discount,
				    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty
                                FROM MS_PRODUCT p
                                    JOIN MS_BRANCH b ON b.ID = p.BranchID
                                    LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
                                WHERE p.Status = 1 
                                    AND b.Status = 1
                                    AND p.Stock > 0
                                    {definedFilter}
                                ORDER BY p.Name ASC
                        ) TEMP
                    UNION 
                    SELECT * 
                    FROM (
                            SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
                                    IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
                                    (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
                                    IFNULL((SELECT SUM(Qty) FROM TR_ORDER_PRODUCT WHERE ProductID=p.ID),0) ItemSold,
                                    p.Stock,
                                    IFNULL(d.DiscountType,0) DiscountType,
                                    IFNULL(d.Discount,0) Discount,
				    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty

                                FROM MS_PRODUCT p
                                    JOIN MS_BRANCH b ON b.ID = p.BranchID
                                    LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
                                WHERE p.Status = 1 
                                    AND b.Status = 1
                                    AND p.Stock = 0
                                    {definedFilter}
                                ORDER BY p.Name ASC
                        ) TEMP";
        $definedFilter = "AND 1=1";
        if ($request->BranchID!="") $definedFilter .= " AND b.ID = '".$request->BranchID."'";
        if ($request->CatID!="") $definedFilter .= " AND p.ID IN (SELECT ProductID FROM MS_PRODUCT_CATEGORY WHERE CategoryID = '".$request->CatID."')";
        if ($request->Keyword!="") $definedFilter .= " AND p.Name LIKE '%".str_replace("'","",$request->Keyword)."%'";
        $query = str_replace("{definedFilter}",$definedFilter,$query);
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }
    public function getProductDetail(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	$getAuth = $this->validateAuth($request->_s);
        $query = "SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
                    IFNULL((SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MinOrder ASC LIMIT 0,1),0) Price,
                    IFNULL((SELECT MIN(MinOrder) FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID),0) MinOrder,
                    p.Description,
                    IFNULL(d.DiscountType,0) DiscountType,
                    IFNULL(d.Discount,0) Discount,
                    p.Stock,
		    IFNULL((SELECT SUM(Qty) FROM TR_CART WHERE ProductID=p.ID ".(isset($getAuth['CustomerID']) ? " AND CustomerID='".$getAuth['CustomerID']."'" : "")." ),0) Qty
                FROM MS_PRODUCT p
                    JOIN MS_BRANCH b ON b.ID = p.BranchID
                    LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
                WHERE p.Status = 1 
                    AND b.Status = 1
                    AND p.ID = ?";
        $data = DB::select($query,[$request->_i]);
        $return['data'] = $data[0];
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }
    public function getProductImage(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=? ORDER BY IsMain DESC";
        $return['data'] = DB::select($query,[$request->_i]);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }
    public function getProductPrice(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, MinOrder, MaxOrder, Price
                    FROM MS_PRODUCT_PRICE 
                    WHERE ProductID=?
                    ORDER BY MinOrder ASC";
        $return['data'] = DB::select($query,[$request->ProductID]);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }



    /* START : PROFILE */
    public function getUser(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT Name, Phone, Email 
                    FROM MS_CUSTOMER c
                    WHERE ID=?";
            $data = DB::select($query,[$getAuth['CustomerID']]);
            $return['data'] = $data[0];
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    public function doUpdateDeviceID(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT c.UserID FROM MS_CUSTOMER c JOIN MS_USER u ON u.ID = c.UserID WHERE c.ID = ?";
            $data = DB::select($query,[$getAuth['CustomerID']]);
            if ($data) {
                        $query = "UPDATE MS_CUSTOMER
                                    SET DeviceID=?,
                                        ModifiedDate=NOW(),
                                        ModifiedBy=?
                                    WHERE ID=?";
                        DB::update($query, [
                            $request->deviceID,
                            $getAuth["UserID"],
                            $getAuth['CustomerID']
                        ]);
            }
        } else $return = array('status'=>false,'message'=>"Oops! sepertinya kamu belum Login");
        return response()->json($return, 200);
    }

    public function doUpdateUser(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT c.UserID, u.RegisterFrom FROM MS_CUSTOMER c JOIN MS_USER u ON u.ID = c.UserID WHERE c.ID = ?";
            $data = DB::select($query,[$getAuth['CustomerID']]);
            if ($data) {
                if ($data[0]->RegisterFrom == 'google') 
                {
                    $return['false'] = false;
                    $return['message'] = "Tidak dapat mengubah informasi untuk User Daftar dari Google";
                }
                else
                {
                    $query = "SELECT u.ID 
                                FROM MS_USER u 
                                    JOIN MS_CUSTOMER c ON c.UserID=u.ID 
                                WHERE c.IsB2B=0 
                                    AND ((UPPER(c.Phone) = UPPER(?) AND Phone != '') OR UPPER(c.Email) = UPPER(?)) 
                                    AND c.ID != ?";
                    $checkUser = DB::select($query,[$request->txtFrmPhone,$request->txtFrmEmail,$getAuth['CustomerID']]);
                    if ($checkUser) {
                        $return['status'] = false;
                        $return['message'] = "Email atau No. Telepon ini sudah terdaftar sebelumnya";
                    } else {
                        $query = "UPDATE MS_CUSTOMER
                                    SET Name=?,
                                        Phone=?,
                                        Email=?,
                                        ModifiedDate=NOW(),
                                        ModifiedBy=?
                                    WHERE ID=?";
                        DB::update($query, [
                            $request->txtFrmName,
                            $request->txtFrmPhone,
                            $request->txtFrmEmail,
                            $getAuth["UserID"],
                            $getAuth['CustomerID']
                        ]);

                        $query = "UPDATE MS_USER
                                    SET Email=?,
                                        ModifiedDate=NOW(),
                                        ModifiedBy=?
                                    WHERE ID=?";
                        DB::update($query, [
                            $request->txtFrmEmail,
                            $getAuth["UserID"],
                            $getAuth["UserID"]
                        ]);
                        $return['message'] = "Data berhasil disimpan!";
                        $return['callback'] = "doHandlerUpdateUser()";
                    }
                }
            }

            
        } else $return = array('status'=>false,'message'=>"Oops! sepertinya kamu belum Login");
        return response()->json($return, 200);
    }
    public function getUserAddress(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT a.ID, 
                            a.Name,
                            a.Phone,
                            a.StateID,
                            a.CityID,
                            a.DistrictID,
                            st.Field2 StateName,
                            ct.Field2 CityName,
                            dt.Field2 DistrictName,
                            a.PostalCode,
                            a.Address,
                            a.IsDefault
                        FROM MS_CUSTOMER_ADDRESS a
                            JOIN MS_REFERENCES st ON st.ID = a.StateID
                            JOIN MS_REFERENCES ct ON ct.ID = a.CityID
                            JOIN MS_REFERENCES dt ON dt.ID = a.DistrictID
                        WHERE a.CustomerID=?
                        ORDER BY a.IsDefault DESC";
            $return['data'] = DB::select($query,[$getAuth['CustomerID']]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Kamu perlu login menampilkan Daftar Alamat");
        return response()->json($return, 200);
    }
    public function doSaveAddress(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if (intval($request->chkDefaultAddress) > 0) {
                $query = "UPDATE MS_CUSTOMER_ADDRESS SET IsDefault = 0 WHERE CustomerID=?";
                DB::update($query, [$getAuth['CustomerID']]);
            }
            if ($request->hdnAction == "add") {
                $query = "INSERT INTO MS_CUSTOMER_ADDRESS
                        (ID, CustomerID, Name, Phone, StateID, CityID, DistrictID, PostalCode, Address, IsDefault, CreatedDate, CreatedBy)
                        VALUES
                        (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
                DB::insert($query, [
                    $getAuth['CustomerID'],
                    $request->txtAddressName,
                    $request->txtFrmPhone,
                    $request->SelFrmState,
                    $request->SelFrmCity,
                    $request->SelFrmDistrict,
                    $request->txtPostalCode,
                    $request->txtAddressDetail,
                    intval($request->chkDefaultAddress),
                    $getAuth["UserID"]
                ]);
            } else {
                $query = "UPDATE MS_CUSTOMER_ADDRESS
                            SET Name=?,
                                Phone=?,
                                StateID=?,
                                CityID=?,
                                DistrictID=?,
                                PostalCode=?,
                                Address=?,
                                IsDefault=?,
                                ModifiedDate=NOW(),
                                ModifiedBy=?
                            WHERE ID=?";
                DB::update($query, [
                    $request->txtAddressName,
                    $request->txtFrmPhone,
                    $request->SelFrmState,
                    $request->SelFrmCity,
                    $request->SelFrmDistrict,
                    $request->txtPostalCode,
                    $request->txtAddressDetail,
                    intval($request->chkDefaultAddress),
                    $getAuth['UserID'],
                    $request->hdnFrmID
                ]);
            }
            $return['message'] = "Data berhasil disimpan";
            $return['callback'] = "doHandlerSaveAddress();";
        } else $return = array('status'=>false,'message'=>"Oops! sepertinya kamu belum Login");
        return response()->json($return, 200);
    }
    public function doRemoveAddress(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "DELETE FROM MS_CUSTOMER_ADDRESS WHERE ID=?";
            DB::delete($query, [$request->hdnFrmID]);
            $return['message'] = "Alamat berhasil dihapus";
            $return['callback'] = "doHandlerRemoveAddress()";
        } else $return = array('status'=>false,'message'=>"Oops! sepertinya kamu belum Login");
        return response()->json($return, 200);
    }
    public function doSetPrimaryAddress(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "UPDATE MS_CUSTOMER_ADDRESS SET IsDefault = 0 WHERE CustomerID=?";
            DB::update($query, [$getAuth['CustomerID']]);
            $query = "UPDATE MS_CUSTOMER_ADDRESS
                        SET IsDefault=1,
                            ModifiedDate=NOW(),
                            ModifiedBy=?
                        WHERE ID=?";
            DB::update($query, [
                $getAuth['UserID'],
                $request->hdnFrmID
            ]);
            $return['message'] = "Alamat utama berhasil diganti";
            if ($request->_cb) {
                $return['callback'] = "doHandleSetPrimaryAddress('".$request->_cb."')";
            }
        } else $return = array('status'=>false,'message'=>"Oops! sepertinya kamu belum Login");
        return response()->json($return, 200);
    }

    

    /* START : CART */
    public function getCart(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT p.ID ProductID, p.Name Product, b.ID BranchID, b.Name Branch,
                            IFNULL(
                                (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID AND c.Qty BETWEEN MinOrder AND MaxOrder LIMIT 0,1),
                                IFNULL(
                                    (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MaxOrder DESC LIMIT 0,1),
                                    0
                                )
                            ) Price,
                            (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
                            IFNULL(d.DiscountType,0) DiscountType,
                            IFNULL(d.Discount,0) Discount,
				p.Stock,
                            c.Qty,
                            c.Notes
                        FROM TR_CART c
                            JOIN MS_PRODUCT p ON p.ID = c.ProductID
                            JOIN MS_BRANCH b ON b.ID = p.BranchID
                            LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
                        WHERE p.Status = 1 
                            AND b.Status = 1
                            AND c.CustomerID = ?
                    ORDER BY b.Name ASC, p.Name ASC";
            $return['data'] = DB::select($query,[$getAuth['CustomerID']]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    public function doCalculateDelivery(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	$id = $request->addressId;
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT DistrictID
                        FROM MS_CUSTOMER_ADDRESS a
                        WHERE CustomerID=?
                        ORDER BY IsDefault DESC";
            $addr = DB::select($query,[$getAuth['CustomerID']]);
            if ($addr) {
                $addr = $addr[0];
                $query = "SELECT b.Branch,
                                IFNULL((SELECT Fee FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) Fee,
                                IFNULL((SELECT 1 FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) IsFound
                            FROM (
                                    SELECT DISTINCT b.DistrictID, b.Name Branch
                                    FROM TR_CART c
                                        JOIN MS_PRODUCT p ON p.ID = c.ProductID
                                        JOIN MS_BRANCH b ON b.ID = p.BranchID
                                    WHERE p.Status = 1 
                                        AND b.Status = 1
                                        AND c.CustomerID = ?
                                ) b";
                $return['data'] = DB::select($query,[$addr->DistrictID,$addr->DistrictID,$getAuth['CustomerID']]);
                $return['query'] = $query;
                if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
            }
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }

    public function doCalculateDeliveryNew(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	$id = $request->addressId;
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT DistrictID
                        FROM MS_CUSTOMER_ADDRESS a
                        WHERE ID=?
                        ORDER BY IsDefault DESC";
            $addr = DB::select($query,[$id]);
            if ($addr) {
                $addr = $addr[0];
                $query = "SELECT b.Branch,
                                IFNULL((SELECT Fee FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) Fee,
                                IFNULL((SELECT 1 FROM MS_DELIVERYCOST WHERE FromDistrictID=b.DistrictID AND ToDistrictID=? LIMIT 0,1),0) IsFound
                            FROM (
                                    SELECT DISTINCT b.DistrictID, b.Name Branch
                                    FROM TR_CART c
                                        JOIN MS_PRODUCT p ON p.ID = c.ProductID
                                        JOIN MS_BRANCH b ON b.ID = p.BranchID
                                    WHERE p.Status = 1 
                                        AND b.Status = 1
                                        AND c.CustomerID = ?
                                ) b";
                $return['data'] = DB::select($query,[$addr->DistrictID,$addr->DistrictID,$getAuth['CustomerID']]);
                $return['query'] = $addr;
                if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
            }
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }


    public function doSaveCart(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        $isValid = true;
        if ($getAuth['status']) {
            $query = "SELECT ID, Qty FROM TR_CART WHERE CustomerID=? AND ProductID=?";
            $data = DB::select($query,[$getAuth['CustomerID'],$request->ProductID]);

            $query = "SELECT ID, Name, Stock FROM MS_PRODUCT WHERE ID=?";
            $product = DB::select($query,[$request->ProductID]);
            if ($data) {
                if ($request->Qty == 0) {
                    $query = "DELETE FROM TR_CART WHERE ID=?";
                    DB::delete($query, [$data[0]->ID]);
                    //$return['message'] = "Produk berhasil dihapus";
                } else {
                    if ($request->Qty <= $product[0]->Stock) {
                        $query = "UPDATE TR_CART
                                    SET Qty=?,
                                        Notes=?,
                                        ModifiedDate=NOW()
                                    WHERE ID=?";
                        DB::update($query, [
                            $request->Source == "product" ? (intval($data[0]->Qty) + 1) : $request->Qty,
                            $request->Notes,
                            $data[0]->ID
                        ]);
                        //$return['message'] = "Keranjang berhasil dirubah";
                    } else {
                        $isValid = false;
                        $return['message'] = "Stock hanya tersedia ".$product[0]->Stock;
                    }
                }
            } else {
                if ($request->Qty <= $product[0]->Stock) {
                    $query = "INSERT INTO TR_CART
                                    (ID, CustomerID, ProductID, Qty, Notes, CreatedDate)
                                VALUES
                                    (UUID(), ?, ?, ?, ?, NOW())";
                    DB::insert($query, [
                        $getAuth['CustomerID'],
                        $request->ProductID,
                        $request->Qty,
                        $request->Notes
                    ]);
                    //$return['message'] = "Produk berhasil ditambahkan";
                } else {
                    $isValid = false;
                    $return['message'] = "Stock hanya tersedia ".$product[0]->Stock;
                }
            }
            $return['callback'] = "doHandlerSaveCart('".$request->Source."','".$isValid."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }

    public function doUpdateCart(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        $isValid = true;
        if ($getAuth['status']) {
            $i=0;
            $return['message'] = "";
            foreach ($request->ProductID as $key => $value) {
                if ($isValid)
                {
                    $query = "SELECT ID, Name, Stock FROM MS_PRODUCT WHERE ID=?";
                    $product = DB::select($query,[$request->ProductID[$i]]);
                    if ($request->Qty[$i] <= $product[0]->Stock) {
                        $query = "UPDATE TR_CART
                                    SET Qty=?,
                                        Notes=?,
                                        ModifiedDate=NOW()
                                    WHERE ProductID=?
                                        AND CustomerID=?";
                        DB::update($query, [
                            $request->Qty[$i],
                            $request->Notes[$i],
                            $request->ProductID[$i],
                            $getAuth['CustomerID']
                        ]);
                    } else {
                        $isValid = false;
                        $return['message'] = "Stok ". $product[0]->Name ." tidak cukup";
                    }
                }
                $i++;
            }
            $return['callback'] = "doHandlerSaveCartNew('".$isValid."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    
    public function getPaymentMethod(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT Field1 Category, Field2 ID, Field3 ImagePath FROM MS_REFERENCES WHERE Type='PaymentMethod' AND Status='1' ORDER BY Field1 ASC, Field2 ASC";
            $return['data'] = DB::select($query);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    public function doPay(Request $request) {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $isValid = true;

            $itemParams = [];
            $subTotal = 0;
            $query = "SELECT b.ID BranchID, 
                            p.ID ProductID,
                            p.Code,
                            p.Name,
                            IFNULL(
                                (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID AND c.Qty BETWEEN MinOrder AND MaxOrder LIMIT 0,1),
                                IFNULL(
                                    (SELECT Price FROM MS_PRODUCT_PRICE WHERE ProductID=p.ID ORDER BY MaxOrder DESC LIMIT 0,1),
                                    0
                                )
                            ) Price,
                            IFNULL(d.DiscountType,0) DiscountType,
                            IFNULL(d.Discount,0) Discount,
                            p.Stock,
                            c.Qty,
                            c.Notes
                        FROM TR_CART c
                            JOIN MS_PRODUCT p ON p.ID = c.ProductID
                            JOIN MS_BRANCH b ON b.ID = p.BranchID
                            LEFT JOIN MS_DISCOUNT d ON (d.Status=1 AND CURDATE() BETWEEN d.StartDate AND d.EndDate AND d.ProductID=p.ID)
                        WHERE p.Status = 1 
                            AND b.Status = 1
                            AND c.CustomerID = ?
                    ORDER BY b.Name ASC, p.Name ASC";
            $cartData = DB::select($query,[$getAuth['CustomerID']]);
            foreach ($cartData as $cart) {
                if ($cart->Qty > $cart->Stock) $isValid = false;
                if ($isValid) {
                    $price = $cart->DiscountType == 0 ? $cart->Price : ($cart->DiscountType == 1 ? ($cart->Price - $cart->Discount) : ($cart->Price - (($cart->Price * $cart->Discount)/100)));
                    $subTotal += ($price * $cart->Qty);
                    $arrData = array(
                        'id' => $cart->Code,
                        'price' => $price,
                        'quantity' => $cart->Qty,
                        'name' => $cart->Name
                    );
                    array_push($itemParams,$arrData);
                }
            }
            if ($isValid)
            {
                $query = "SELECT Name, Phone, Email FROM MS_CUSTOMER WHERE ID=?";
                $custData = DB::select($query,[$getAuth['CustomerID']])[0];
    
                $query = "SELECT a.ID, 
                                a.Name,
                                a.Phone,
                                st.Field2 StateName,
                                ct.Field2 CityName,
                                dt.Field2 DistrictName,
                                a.DistrictID,
                                a.PostalCode,
                                a.Address
                            FROM MS_CUSTOMER_ADDRESS a
                                JOIN MS_REFERENCES st ON st.ID = a.StateID
                                JOIN MS_REFERENCES ct ON ct.ID = a.CityID
                                JOIN MS_REFERENCES dt ON dt.ID = a.DistrictID
                            WHERE a.CustomerID=?
                            ORDER BY a.IsDefault DESC";
                $addressData = DB::select($query,[$getAuth['CustomerID']])[0];
    
                $query = "SELECT Field1,Field2,Field3 FROM MS_REFERENCES WHERE Field2 LIKE '%".$request->paymentMethod."%' AND Type = 'PaymentMethod'";
                $paymentData = DB::select($query)[0];
    
                
                $params = null;
                $query = "SELECT d.Fee, b.BranchID, b.Branch
                            FROM MS_DELIVERYCOST d
                            JOIN (
                                SELECT DISTINCT b.DistrictID, b.ID BranchID, b.Name Branch
                                FROM TR_CART c
                                    JOIN MS_PRODUCT p ON p.ID = c.ProductID
                                    JOIN MS_BRANCH b ON b.ID = p.BranchID
                                WHERE p.Status = 1
                                    AND b.Status = 1
                                    AND c.CustomerID = ?
                            ) b ON d.FromDistrictID = b.DistrictID
                            WHERE d.ToDistrictID = ?";
                $deliveryData = DB::select($query,[$getAuth['CustomerID'],$addressData->DistrictID]);
                foreach ($deliveryData as $item) {
                    $subTotal += $item->Fee;
                    $arrData = array(
                        'id' => $item->BranchID,
                        'price' => $item->Fee,
                        'quantity' => 1,
                        'name' => 'Ongkir '.$item->Branch
                    );
                    array_push($itemParams,$arrData);
                }
    
                $params = [];
                $PaymentID = "INV/".date("Ymd")."/".$this->randomString(11);
                if ($paymentData->Field1 == "bank_transfer") {
                    $params = array(
                        'payment_type' => $paymentData->Field1,
                        'transaction_details' => array(
                            'order_id' => $PaymentID,
                            'gross_amount' => $subTotal,
                        ),
                        'customer_details' => array(
                            'email' => $custData->Email,
                            'first_name' => $custData->Name,
                            'last_name' => '',
                            'phone' => $custData->Phone
                        ),
                        'item_details' => $itemParams,
                        'bank_transfer' => array(
                            'bank' => $request->paymentMethod,
                        ),
                    );
                }
                if ($paymentData->Field1 == "cstore") {
                    $params = array(
                        'payment_type' => $paymentData->Field1,
                        'transaction_details' => array(
                            'order_id' => $PaymentID,
                            'gross_amount' => $subTotal,
                        ),
                        'customer_details' => array(
                            'email' => $custData->Email,
                            'first_name' => $custData->Name,
                            'last_name' => '',
                            'phone' => $custData->Phone
                        ),
                        'item_details' => $itemParams,
                        'cstore' => array(
                            'store' => $request->paymentMethod,
                        ),
                    );
                }
                if ($paymentData->Field1 == "gopay") {
                    $params = array(
                        'payment_type' => $paymentData->Field1,
                        'transaction_details' => array(
                            'order_id' => $PaymentID,
                            'gross_amount' => $subTotal,
                        ),
                        'customer_details' => array(
                            'email' => $custData->Email,
                            'first_name' => $custData->Name,
                            'last_name' => '',
                            'phone' => $custData->Phone
                        ),
                        'item_details' => $itemParams,
                        'gopay' => array(
                            'enable_callback' => false
                        ),
                    );
                }
		if ($paymentData->Field1 == "echannel") {
                    $params = array(
                        'payment_type' => $paymentData->Field1,
                        'transaction_details' => array(
                            'order_id' => $PaymentID,
                            'gross_amount' => $subTotal,
                        ),
                        'customer_details' => array(
                            'email' => $custData->Email,
                            'first_name' => $custData->Name,
                            'last_name' => '',
                            'phone' => $custData->Phone
                        ),
                        'item_details' => $itemParams,
                        'echannel' => array(
                            'bill_info1' => 'Payment',
			    'bill_info2' => $PaymentID
                        ),
                    );
                }


                $isPaymentValid = false;
                $referenceNumber = "";
                $followupNumber = "";
                $callbackUrl = ""; 
                if ($paymentData->Field1 == "direct") {
                    $isPaymentValid = true;
                    if (explode("|",$paymentData->Field2)[0] == "transfer") $followupNumber = "BCA a/n TJO FELIANA: 2388820999";
                }
                else
                {
                    //$serverKey = base64_encode('SB-Mid-server-kO99X1M9McX_o8aq2G5XQWOS:');
                    $serverKey = base64_encode('Mid-server-I0gfGfjWQb_FcQOZ5Z-7iOFl:');
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        //CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/charge",
                        CURLOPT_URL => "https://api.midtrans.com/v2/charge",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode($params),
                        CURLOPT_HTTPHEADER => array(
                            "Accept: application/json",
                            "Content-Type: application/json",
                            "Authorization: Basic ".$serverKey
                        ),
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($response);
                    
                    if (isset($response->status_code)) {
                        if ($response->status_code != 201) {
                            $return['status'] = false;
                            $return['message'] = $response->status_message;
                        } else {
                            $isPaymentValid = true;
                            $referenceNumber = $response->transaction_id; 
                            $followupNumber = ($paymentData->Field1 == "bank_transfer" ? (explode("|",$paymentData->Field2)[0] == "permata" ? $response->permata_va_number : $response->va_numbers[0]->va_number) : ($paymentData->Field1 == "cstore" ? $response->payment_code : ($paymentData->Field1 == "echannel" ? $response->bill_key : "")));
                            $callbackUrl = $paymentData->Field1 == "gopay" ? $response->actions[1]->url : "";
                        }
                    } else {
                        $return['midtrans'] = $response;
                        $return['status'] = false;
                        $return['message'] = "Error processing data to Midtrans";
                    }
                }
                
                if ($isPaymentValid) {
                    $query = "SELECT DATE_ADD(NOW(), INTERVAL 1 DAY) ExpiredDate, DATE_ADD(NOW(), INTERVAL 30 MINUTE) ExpiredDateGopay";
                    if ($paymentData->Field1 == "gopay") $ExpiredDate = DB::select($query)[0]->ExpiredDateGopay;
                    else $ExpiredDate = DB::select($query)[0]->ExpiredDate;
                    $query = "INSERT INTO TR_ORDER_PAYMENT
                                    (ID, TransactionID, ExpiredDate, PaymentMethodCategory, PaymentMethod, ReferenceID, GopayDeepLink, GrossAmount, IsPaid, IsCancelled, CreatedDate)
                                VALUES
                                    (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW())";
                    DB::insert($query, [
                        $PaymentID,
                        $referenceNumber,
                        $ExpiredDate,
                        $paymentData->Field1,
                        $request->paymentMethod,
                        $followupNumber,
                        $callbackUrl,
                        $subTotal
                    ]);
                    $grossAmount = $subTotal;
    
                    $query = "INSERT INTO TR_ORDER_ADDRESS
                                    (ID, PaymentID, Name, Phone, StateName, CityName, DistrictName, PostalCode, Address)
                                VALUES
                                    (UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                    DB::insert($query, [
                        $PaymentID,
                        $custData->Name,
                        $addressData->Phone,
                        $addressData->StateName,
                        $addressData->CityName,
                        $addressData->DistrictName,
                        $addressData->PostalCode,
                        $addressData->Address
                    ]);
                    
                    foreach ($deliveryData as $item) {
                        $query = "SELECT UUID() GenID";
                        $ID = DB::select($query)[0]->GenID;
                        $OrderID = 'ORDER-'.date("Ymd").'-'.$this->randomString(10);
                        $subTotal = 0;
                        foreach ($cartData as $cart) {
                            if ($cart->BranchID == $item->BranchID) {
                                $price = $cart->DiscountType == 0 ? $cart->Price : ($cart->DiscountType == 1 ? ($cart->Price - $cart->Discount) : ($cart->Price - (($cart->Price * $cart->Discount)/100)));
                                $subTotal += ($price * $cart->Qty);
                            }
                        }
                        $query = "INSERT INTO TR_ORDER
                                        (ID, BranchID, CustomerID, PaymentID, IsB2B, 
                                        OrderNumber, SubTotal, DeliveryFee, Total, 
                                        Status, CreatedDate, CreatedBy)
                                    VALUES
                                        (?, ?, ?, ?, ?,
                                        ?, ?, ?, ?, 
                                        ?, NOW(), ?)";
                        DB::insert($query, [
                            $ID,
                            $item->BranchID,
                            $getAuth['CustomerID'],
                            $PaymentID,
                            0,
                            $OrderID,
                            $subTotal,
                            $item->Fee,
                            ($subTotal + $item->Fee),
                            1,
                            'SYSTEM'
                        ]);
        
                        foreach ($cartData as $cart) {
                            if ($cart->BranchID == $item->BranchID) {
                                $discount = $cart->DiscountType == 0 ? 0 : ($cart->DiscountType == 1 ? $cart->Discount : ($cart->Price * $cart->Discount)/100);
                                $query = "INSERT INTO TR_ORDER_PRODUCT
                                                (ID, OrderID, ProductID, Qty, SourcePrice, DiscountPrice, ItemPrice, Notes)
                                            VALUES
                                                (UUID(), ?, ?, ?, ?, ?, ?, ?)";
                                DB::insert($query, [
                                    $ID,
                                    $cart->ProductID,
                                    $cart->Qty,
                                    $cart->Price,
                                    $discount,
                                    $cart->Price - $discount,
                                    $cart->Notes
                                ]);
    
                                $query = "UPDATE MS_PRODUCT
                                            SET Stock=(Stock-".$cart->Qty.")
                                            WHERE ID=?";
                                DB::update($query, [
                                    $cart->ProductID
                                ]);
                            }
                        }
                    }
    
                    $query = "DELETE FROM TR_CART WHERE CustomerID=?";
                    DB::delete($query, [$getAuth['CustomerID']]);
    
                    $return['status'] = true;
                    $return['data'] = array(
                        'PaymentMethodCategory' => $paymentData->Field1,
                        'PaymentMethodLogo' => $paymentData->Field3,
                        'PaymentMethod' => explode("|",$paymentData->Field2)[1],
                        'ExpiredDate' => $ExpiredDate,
                        'ReferenceID' => $followupNumber,
                        'GrossAmount' => $grossAmount,
                        'GoPayDeepLink' => $callbackUrl
                    );
                    $return['callback'] = "onCompleteDoPay(e.data)";
                }
            }
            else $return = array('status'=>false,'message'=>"Yahh kamu kalah cepat, sudah keburu dibeli orang");
        } 
        return response()->json($return, 200);
    }
    public function getMidtransNotification(Request $request)
    {
        $return = array('status'=>true,'message'=>"");
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;
        //$serverKey = "SB-Mid-server-kO99X1M9McX_o8aq2G5XQWOS";
        $serverKey = "Mid-server-I0gfGfjWQb_FcQOZ5Z-7iOFl";
        if ($request->signature_key == openssl_digest($orderId.$statusCode.$grossAmount.$serverKey, 'sha512')) {
            if ($request->transaction_status == "settlement" || $request->transaction_status == "capture") {
                $query = "UPDATE TR_ORDER SET Status=2, ModifiedBy='Midtrans', ModifiedDate=NOW() WHERE PaymentID=?";
                DB::update($query, [$request->order_id]);

                $query = "UPDATE TR_ORDER_PAYMENT SET IsPaid=1, PaidDate=? WHERE ID=?";
                DB::update($query, [$request->settlement_time, $request->order_id]);
            }
        }
        return response()->json($return, 200);
    }



    /* START : CHAT */
    public function getChatList(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT b.ID, 
                            b.Name,
                            IFNULL((SELECT SUBSTR(Message,1,100) FROM TR_CHAT_MESSAGE WHERE CustomerID=? AND BranchID=b.ID Order BY CreatedDate DESC LIMIT 0,1),'') LastMessage,
                            (SELECT COUNT(ID) FROM TR_CHAT_MESSAGE WHERE CustomerID=? AND BranchID=b.ID AND IsReadByCustomer=0) UnreadMessage
                        FROM MS_BRANCH b
                        WHERE b.Status = 1";
            $return['data'] = DB::select($query,[$getAuth['CustomerID'],$getAuth['CustomerID']]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    public function getChatDetail(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "UPDATE TR_CHAT_MESSAGE SET IsReadByCustomer=1 WHERE BranchID=? AND CustomerID=?";
            DB::update($query, [$request->BranchID, $getAuth['CustomerID']]);

            $query = "SELECT ID, Message, IsReadByBranch, IsReadByCustomer,
                            CASE WHEN (CreatedBy=?) THEN 0 ELSE 1 END IsReply
                        FROM TR_CHAT_MESSAGE
                        WHERE CustomerID=?
                            AND BranchID=?
                            AND Status=1
                        ORDER BY CreatedDate ASC";
            $return['data'] = DB::select($query,[$getAuth['UserID'],$getAuth['CustomerID'],$request->BranchID]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    public function doSaveMessage(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "INSERT INTO TR_CHAT_MESSAGE
                            (ID, CustomerID, BranchID, Message, IsReadByBranch, IsReadByCustomer, Status, CreatedDate, CreatedBy)
                        VALUES
                            (UUID(), ?, ?, ?, 0, 1, 1, NOW(), ?)";
            DB::insert($query, [
                $getAuth['CustomerID'],
                $request->BranchID,
                $request->Message,
                $getAuth['UserID']
            ]);
            $return['callback'] = "reloadChatMessage()";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }



    /* START: Transaction */
    public function getUnpaidTransaction(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ord.ID OrderID, opy.ID, opy.PaymentMethodCategory, r.Field2 PaymentMethod, 
                            r.Field3 PaymentLogo, opy.ReferenceID, opy.GopayDeepLink, opy.GrossAmount, opy.IsPaid, opy.ExpiredDate,
                            CASE WHEN (NOW() >= opy.ExpiredDate) THEN 1 ELSE 0 END IsExpired
                        FROM TR_ORDER_PAYMENT opy
                        JOIN TR_ORDER ord ON ord.PaymentID=opy.ID
                            LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
                        WHERE ord.CustomerID=?
                            AND opy.IsCancelled=0
                            AND ord.Status=1
                            AND ord.IsB2B = 0
                        ORDER BY ord.CreatedDate DESC";
            $data = DB::select($query,[$getAuth['CustomerID']]);
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
            $return['data'] = DB::select($query,[$getAuth['CustomerID']]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    public function getTransaction(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT o.PaymentID ID, o.CreatedDate, o.Total GrossAmount,
                    (SELECT p.Name FROM MS_PRODUCT p JOIN TR_ORDER_PRODUCT op ON p.ID=op.ProductID WHERE op.OrderID=o.ID ORDER BY op.ProductID ASC LIMIT 0,1)Product,
                    (SELECT b.Name FROM MS_BRANCH b JOIN MS_PRODUCT p ON p.BranchID=b.ID JOIN TR_ORDER_PRODUCT op ON p.ID=op.ProductID WHERE op.OrderID=o.ID ORDER BY op.ProductID ASC LIMIT 0,1) Branch,
                    (SELECT ImagePath FROM MS_PRODUCT_IMAGE pi JOIN TR_ORDER_PRODUCT op ON pi.ProductID=op.ProductID WHERE pi.IsMain=1 AND op.OrderID=o.ID ORDER BY op.ProductID LIMIT 0,1) ImagePath, o.Status,
                    IFNULL(CASE WHEN (SELECT SUM(op.Qty) FROM TR_ORDER_PRODUCT op WHERE op.OrderID=o.ID) > 1 THEN ((SELECT SUM(op.Qty) FROM TR_ORDER_PRODUCT op WHERE op.OrderID=o.ID)-1) ELSE 1 END,0) TotalItem
                    FROM TR_ORDER o
                    WHERE o.CustomerID=?
                        AND o.Status=?
                        AND o.IsB2B = 0
                    ORDER BY o.CreatedDate DESC";
            $return['data'] = DB::select($query,[$getAuth['CustomerID'],$request->Status]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }
    public function getTransactionDetail(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT opy.ID, opy.CreatedDate, opy.IsPaid, opy.PaidDate, opy.GrossAmount, opy.PaymentMethodCategory, r.Field2 PaymentMethod, opy.GrossAmount, 
                            oad.Name, oad.Phone, oad.StateName, oad.CityName, oad.DistrictName, oad.PostalCode, oad.Address
                        FROM TR_ORDER_PAYMENT opy
                            JOIN TR_ORDER_ADDRESS oad ON oad.PaymentID=opy.ID
                            LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
                        WHERE opy.ID=?";
            $paymentData = DB::select($query,[$request->ID])[0];
            $query = "SELECT o.ID, o.OrderNumber, b.Name Branch, b.ID BranchID, o.SubTotal, o.DeliveryFee, o.Discount, o.ShippingDate, o.TrackingNumber, o.ShippingMethod, o.Status,
                            p.Name Product, op.Qty, op.Notes, o.CancelledReason, (SELECT ImagePath FROM MS_PRODUCT_IMAGE WHERE ProductID=p.ID AND IsMain=1 LIMIT 0,1) ImagePath,
                            op.SourcePrice, op.ItemPrice, (IFNULL(op.DiscountPrice,0) * op.Qty) SubDiscount, (IFNULL(op.SourcePrice,0) * op.Qty) SubTotal
                        FROM TR_ORDER o
                            JOIN MS_BRANCH b ON b.ID=o.BranchID
                            JOIN TR_ORDER_PRODUCT op ON op.OrderID=o.ID
                            JOIN MS_PRODUCT p ON p.ID=op.ProductID
                        WHERE o.PaymentID=?
                        ORDER BY b.Name, p.Name ASC";
            $orderData = DB::select($query,[$request->ID]);
            $return['data'] = array('ID' => urlencode($paymentData->ID), 'paymentData' => $paymentData, 'orderData' => $orderData);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"",'callback'=>"doHandlerNotAuthorized()");
        return response()->json($return, 200);
    }



    /* START : HELP */
    public function getHelpList(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT Field1 ID, Field2 Title, Field3 Content
                    FROM MS_REFERENCES
                    WHERE Status = 1
                        AND Type = 'FAQ'
                    ORDER BY Field1 ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function printOrder(Request $request)
    {
        $result = '
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link href="https://ellafroze.com/assets/favicon.ico" rel="shortcut icon" />
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
                <title>Ella Froze - Invoice</title>
            </head>
            <body style="font-size:7pt">
                <div class="col-lg-8 mx-auto p-3 py-md-5">
                    <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
                        <div class="col-xs-12">
                            <img src="https://ellafroze.com/assets/img/logo.png" height="100px" />
                        </div>
                    </header>
                    <main>{dataObject}</main>
                    <footer class="pt-5 my-5 text-muted">
                        Dicetak pada: '.date("Y-m-d").'
                    </footer>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
            </body>
        </html>';
        if ($request->i) {
            $query = "SELECT opy.ID, opy.CreatedDate, opy.IsPaid, opy.PaidDate, opy.GrossAmount, opy.PaymentMethodCategory, r.Field2 PaymentMethod, opy.GrossAmount, 
                            oad.Name, oad.Phone, oad.StateName, oad.CityName, oad.DistrictName, oad.PostalCode, oad.Address
                        FROM TR_ORDER_PAYMENT opy
                            JOIN TR_ORDER_ADDRESS oad ON oad.PaymentID=opy.ID
                            LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
                        WHERE opy.ID=?";
            $paymentData = DB::select($query,[urldecode($request->i)])[0];
            $query = "SELECT o.ID, o.OrderNumber, b.Name Branch, b.ID BranchID, o.SubTotal, o.DeliveryFee, o.Discount, o.ShippingDate, o.TrackingNumber, o.ShippingMethod, o.Status,
                            p.Name Product, op.Qty, op.Notes, o.CancelledReason,
                            op.SourcePrice, op.ItemPrice, (IFNULL(op.DiscountPrice,0) * op.Qty) SubDiscount, (IFNULL(op.SourcePrice,0) * op.Qty) SubTotal
                        FROM TR_ORDER o
                            JOIN MS_BRANCH b ON b.ID=o.BranchID
                            JOIN TR_ORDER_PRODUCT op ON op.OrderID=o.ID
                            JOIN MS_PRODUCT p ON p.ID=op.ProductID
                        WHERE o.PaymentID=?
                        ORDER BY b.Name, p.Name ASC";
            $orderData = DB::select($query,[urldecode($request->i)]);


            if ($paymentData) {
                $arrData = '
                <table>
                <tbody>
                    <tr>
                        <td width="150px">No. Invoice</td>
                        <td>: <b>'.$paymentData->ID.'<b></td>
                    </tr>
                    <tr>
                        <td>Tgl. Pesanan</td>
                        <td>: '.$paymentData->CreatedDate.'</td>
                    </tr>
                    <tr>
                        <td valign="top">Pengiriman</td>
                        <td>: '.$paymentData->Name.'<br />&nbsp;&nbsp;'.$paymentData->StateName.', '.$paymentData->CityName.', '.$paymentData->DistrictName.'<br />&nbsp;&nbsp;'.$paymentData->Address.'<br />&nbsp;&nbsp;'.$paymentData->PostalCode.'<br />&nbsp;&nbsp;'.$paymentData->Phone.'</td>
                    </tr>
                </tbody>
                </table>
                <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Harga Barang</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>';
                $subTotal = 0;
                $deliveryFee = 0;
                $totalDiscount = 0;
                $total = 0;
                foreach($orderData as $item) {
                    $arrData .= '<tr>
                                    <td>'.$item->Product.'</td>
                                    <td>'.number_format($item->Qty).'</td>
                                    <td>'.($item->SourcePrice != $item->ItemPrice ? '<small><s>Rp ' .number_format($item->SourcePrice). '</s></small> Rp '.number_format($item->ItemPrice) : 'Rp '.number_format($item->ItemPrice)).'</td>
                                    <td>Rp '.number_format($item->ItemPrice * $item->Qty).'</td>
                                </tr>';
                    $arrData .= '<tr><td colspan="3">Catatan: '.$item->Notes.'</td></tr>';

                    $subTotal += $item->SubTotal;
                    $deliveryFee = $item->DeliveryFee;
                    $totalDiscount += $item->SubDiscount;
                }
                $arrData .= '   
                <tr>
                    <th scope="col" colspan="3">Subtotal Harga Barang</th>
                    <th scope="col">Rp '.number_format($subTotal).'</th>
                </tr>
                <tr>
                    <th scope="col" colspan="3">Total Discount</th>
                    <th scope="col">- Rp '.number_format($totalDiscount).'</th>
                </tr>
                <tr>
                    <th scope="col" colspan="3">Ongkos Kirim</th>
                    <th scope="col">Rp '.number_format($deliveryFee).'</th>
                </tr>
                <tr>
                    <th scope="col" colspan="3"><b>Total Bayar</b></th>
                    <th scope="col"><b>Rp '.number_format(($subTotal + $deliveryFee) - $totalDiscount).'</b></th>
                </tr>
                </tbody>
                </table>';
                $result = str_replace("{dataObject}",$arrData,$result);
            } else {
                //$result = "Not Authorized";
            }
        } else {
            //$result = "Not Authorized";
        }
        return response($result);
    }

    /* START : ARTICLE */
    public function getArticle(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, Type, Title, ImageUrl, Contents
                    FROM MS_ARTICLE
                    WHERE Status = 1
                        AND Type = ?
                    ORDER BY CreatedDate DESC";
        $return['data'] = DB::select($query,[$request->Type]);
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function getArticleDetail(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID, Type, Title, ImageUrl, Contents
                    FROM MS_ARTICLE
                    WHERE Status = 1
                        AND ID = ? ";
        $return['data'] = DB::select($query,[$request->ID])[0];
        if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function viewArticleDetail(Request $request)
    {
	$query = "SELECT ID, Type, Title, ImageUrl, Contents
                    FROM MS_ARTICLE
                    WHERE Status = 1
                        AND ID = ? ";
        $articleData = DB::select($query,[urldecode($request->i)])[0];

        $result = '
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link href="https://ellafroze.com/assets/favicon.ico" rel="shortcut icon" />
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
                <title>Ella Froze - '.$articleData->Title.'</title>
            </head>
            <body style="font-size:7pt">
                <div class="col-lg-8 mx-auto p-3 py-md-5">
		    <img src="https://ellafroze.com/api/uploaded/article/'.$articleData->ImageUrl.'" style="width: 100%;height: auto;" />
		    <br /><br /><br /><br />
		    <h3>'.$articleData->Title.'</h3>
                    <main style="font-size:12pt">'.$articleData->Contents.'</main>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
            </body>
        </html>';
        return response($result);
    }

}