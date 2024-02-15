<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
        $query = "SELECT u.ID, u.AccountType 
                    FROM MS_USER u
                        JOIN TR_SESSION s ON s.UserID = u.ID 
                    WHERE s.Token=?
                        AND s.LogoutDate IS NULL";
        $checkAuth = DB::select($query,[$Token]);
        if ($checkAuth) {
            $data = $checkAuth[0];
            $query = "UPDATE TR_SESSION SET LastActive=NOW() WHERE Token=?";
            DB::update($query,[$Token]);
            $return = array(
                'status' => true,
                'UserID' => $data->ID,
                'AccountType' => $data->AccountType
            );
        }
        return $return;
    }

    public function getAll(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
                $mainQuery = "SELECT    c.ID,
                                        c.Code,
                                        c.Name,
                                        c.Phone,
                                        c.Email,
                                        c.Status,
                                        c.IsB2B,
                                        u.RegisterFrom
                                FROM MS_CUSTOMER c
                                    LEFT JOIN MS_USER u ON u.ID = c.UserID
                                WHERE {definedFilter}
                                ORDER BY c.ID ASC";
            $definedFilter = "c.ID != 'SYSTEM'";
            if ($request->_i) {
                $definedFilter = "c.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $return['data'] = $data[0];
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                if ($getAuth['AccountType']==1) {
                    $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                    $data = DB::select($query);
                } 
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized",'callback'=>"app.auth.notAuthorizedHandler(e.message)");
        return response()->json($return, 200);
    }

    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        $isAuth = false;
        if ($getAuth['status']) {
            $isAuth = true;
            if ($request->hdnFrmAction=="add") {
                $query = "SELECT UUID() GenID";
                $ID = DB::select($query)[0]->GenID;
                $query = "INSERT INTO MS_CUSTOMER
                    (ID, Code, Name, Phone, Email, IsB2B, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
                DB::insert($query, [
                    $ID,
                    $request->txtFrmCode == "" ? $this->randomString(10) : $request->txtFrmCode,
                    $request->txtFrmName,
                    $request->txtFrmPhone,
                    $request->txtFrmEmail,
                    intval($request->hdnIsB2B),
                    intval($request->radFrmStatus),
                    $getAuth['UserID']
                ]);

                $query = "INSERT INTO MS_CUSTOMER_ADDRESS
                    (ID, CustomerID, Name, Phone, StateID, CityID, DistrictID, PostalCode, Address, IsDefault, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                    VALUES
                    (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?, NULL, NULL)";
                DB::insert($query, [
                    $ID,
                    $request->txtAddressName,
                    $request->txtFrmPhone,
                    $request->SelFrmState,
                    $request->SelFrmCity,
                    $request->SelFrmDistrict,
                    $request->txtPostalCode,
                    $request->txtAddressDetail,
                    $getAuth['UserID']
                ]);
                
                $return['message'] = "Data berhasil disimpan!";
                $return['callback'] = intval($request->hdnIsB2B) == 1 ? "loadModal('b2b/form','fetchBranch();doFetchCustomer()')" : "doReloadTable()";
            }
            if ($request->hdnFrmAction=="edit") {
                $query = "SELECT ID, UserID, IsB2B FROM MS_CUSTOMER WHERE ID=?";
                $data = DB::select($query, [$request->hdnFrmID]);
                if ($data) $isAuth = true;
				if ($isAuth) {
                    if (trim($request->txtFrmEmail) == "" && trim($request->txtFrmPhone) == "") 
                    {
                        $return['status'] = false;
                        $return['message'] = "Harus mengisi salah satu dari No. Telepon atau Email";
                    }
                    else
                    {
                        $isValidEmail = true;
                        if ($data[0]->UserID != "" && trim($request->txtFrmEmail) != "") {
                            $query = "SELECT RegisterFrom FROM MS_USER WHERE Email=? AND ID!=?";
                            $mailCheck = DB::select($query,[trim($request->txtFrmEmail), $data[0]->UserID]);
                            if ($mailCheck) 
                            {
                                $isValidEmail = false;
                                $return['status'] = false;
                                $return['message'] = "Email ini sudah digunakan";
                            }
                        }
                        if ($isValidEmail) {
                            $query = "SELECT RegisterFrom FROM MS_USER WHERE ID=?";
                            $dataUser = DB::select($query,[$data[0]->UserID]);
                            if ($dataUser[0]->RegisterFrom == "app")
                            {
                                if (trim($request->txtFrmPassword) != "") {
                                    $key = $this->randomString(10);
                                    $encrypt = $this->strEncrypt($key,$request->txtFrmPassword);
                                    $query = "UPDATE MS_USER
                                                SET Email=?,
                                                    Password=?,
                                                    Salt=?,
                                                    IVssl=?,
                                                    Tagssl=?,
                                                    ModifiedDate=NOW(),
                                                    ModifiedBy=?
                                                WHERE ID=?";
                                    DB::update($query, [
                                        trim($request->txtFrmEmail),
                                        base64_encode($encrypt['result']),
                                        base64_encode($key),
                                        base64_encode($encrypt['iv']),
                                        base64_encode($encrypt['tag']),
                                        $getAuth['UserID'],
                                        $data[0]->UserID
                                    ]);
                                    $return['message'] = "Ganti password berhasil";
                                    $return['callback'] = "doReloadTable()";
                                } else {
                                    $query = "UPDATE MS_USER
                                                SET Email=?,
                                                    ModifiedDate=NOW(),
                                                    ModifiedBy=?
                                                WHERE ID=?";
                                    DB::update($query, [
                                        trim($request->txtFrmEmail),
                                        $getAuth['UserID'],
                                        $data[0]->UserID
                                    ]);
                                }
                            } else {
                                $return['status'] = false;
                                $return['message'] = "Tidak dapat mengganti informasi untuk Customer yang Daftar menggunakan Google";
                            }
                        }
                    }
                    
                    if ($return['status']) {
                        $query = "UPDATE MS_CUSTOMER
                                    SET Name=?,
                                        Phone=?,
                                        Email=?,
                                        Status=?,
                                        ModifiedDate=NOW(),
                                        ModifiedBy=?
                                    WHERE ID=?";
                        DB::update($query, [
                            $request->txtFrmName,
                            $request->txtFrmPhone,
                            trim($request->txtFrmEmail),
                            intval($request->radFrmStatus),
                            $getAuth['UserID'],
                            $request->hdnFrmID
                        ]);
                        $return['message'] = "Data berhasil disimpan!";
                        $return['callback'] = "doReloadTable()";
                    }
				}
            }
        } 
        if (!$isAuth) $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doDelete(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        $isAuth = false;
        if ($getAuth['status']) {
            $isAuth = true;
            $query = "SELECT ID,UserID FROM MS_CUSTOMER WHERE ID=?";
            $data = DB::select($query, [$request->_i]);
            if ($isAuth) {
                $query = "DELETE FROM MS_USER WHERE ID=?";
                DB::delete($query, [$data[0]->UserID]);
                $query = "DELETE FROM MS_CUSTOMER_ADDRESS WHERE CustomerID=?";
                DB::delete($query, [$request->_i]);
                $query = "DELETE FROM MS_CUSTOMER WHERE ID=?";
                DB::delete($query, [$request->_i]);
                $return['message'] = "Data berhasil dihapus!";
                $return['callback'] = "doReloadTable()";
            }
        } 
        if (!$isAuth) $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getAddress(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "
                    SELECT    ID,
                              Name,
                              Phone,
                              StateID,
                              CityID,
                              DistrictID,
                              PostalCode,
                              Address,
                              IsDefault
                    FROM      MS_CUSTOMER_ADDRESS
                    WHERE     CustomerID=?";
            $return['data'] = DB::select($query,[$request->_p]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getAddressDetail(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT    ID,
                              CustomerID,
                              Name,
                              Phone,
                              StateID,
                              CityID,
                              DistrictID,
                              PostalCode,
                              Address,
                              IsDefault
                    FROM      MS_CUSTOMER_ADDRESS
                    WHERE     ID=?";
            $data = DB::select($query,[$request->_addressID]);
            $return['data'] = $data[0];
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doChangePrimary(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        $isAuth = true;
        if ($getAuth['status']) {
            $query = "UPDATE MS_CUSTOMER_ADDRESS
                        SET IsDefault = 0
                        WHERE CustomerID = ?";
            DB::update($query, [
                $request->_customerID
            ]);
            $query = "UPDATE MS_CUSTOMER_ADDRESS
                        SET IsDefault = 1,
                            ModifiedDate=NOW(),
                            ModifiedBy=?
                        WHERE ID=?";
            DB::update($query, [
                $getAuth['UserID'],
                $request->_addressID
            ]);
            $return['callback'] = "fetchAddressDetail('".$request->_customerID."');";
        } 
        if (!$isAuth) $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doRemoveAddress(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        $isAuth = true;
        if ($getAuth['status']) {
            $query = "DELETE FROM MS_CUSTOMER_ADDRESS WHERE ID=?";
            DB::delete($query, [$request->_addressID]);
            $return['callback'] = "fetchAddressDetail('".$request->_customerID."');";
        } 
        if (!$isAuth) $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doSaveAddress(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        $isAuth = false;
        if ($getAuth['status']) {
            $isAuth = true;
            if ($request->hdnFrmAction=="add") {
                $query = "INSERT INTO MS_CUSTOMER_ADDRESS
                    (ID, CustomerID, Name, Phone, StateID, CityID, DistrictID, PostalCode, Address, IsDefault, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                    VALUES
                    (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW(), ?, NULL, NULL)";
                DB::insert($query, [
                    $request->hdnFrmCustID,
                    $request->txtAddressName,
                    $request->txtFrmPhoneDetail,
                    $request->SelFrmState,
                    $request->SelFrmCity,
                    $request->SelFrmDistrict,
                    $request->txtPostalCode,
                    $request->txtAddressDetail,
                    $getAuth['UserID']
                ]);
                $return['callback'] = "loadModal('customer/form','onDetailForm(\'".$request->hdnFrmCustID."\')');";
            }
            if ($request->hdnFrmAction=="edit") {
                $query = "UPDATE MS_CUSTOMER_ADDRESS
                        SET Name=?,
                            Phone=?,
                            StateID=?,
                            CityID=?,
                            DistrictID=?,
                            PostalCode=?,
                            Address=?,
                            ModifiedDate=NOW(),
                            ModifiedBy=?
                        WHERE ID=?";
                DB::insert($query, [
                    $request->txtAddressName,
                    $request->txtFrmPhoneDetail,
                    $request->SelFrmState,
                    $request->SelFrmCity,
                    $request->SelFrmDistrict,
                    $request->txtPostalCode,
                    $request->txtAddressDetail,
                    $getAuth['UserID'],
                    $request->hdnFrmID
                ]);
                $return['callback'] = "loadModal('customer/form','onDetailForm(\'".$request->hdnFrmCustID."\')');";
            }
        } 
        if (!$isAuth) $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }
}