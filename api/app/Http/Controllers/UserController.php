<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class UserController extends Controller
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
            $mainQuery = "SELECT u.ID,
                                u.AccountType,
                                u.RoleID,
                                ro.Name RoleName,
                                u.UserName,
                                u.Email,
                                u.FullName,
                                u.Status,
                                u.CreatedDate,
                                (SELECT MAX(LastActive) FROM TR_SESSION s WHERE s.UserID=u.ID) LastActive
                            FROM MS_USER u
                                JOIN MS_ROLE ro ON ro.ID = u.RoleID 
                            WHERE {definedFilter}
                            ORDER BY u.CreatedDate DESC";
            $definedFilter = "u.ID!='SYSTEM'";
            if ($request->_i) {
                $definedFilter = "u.ID=?";
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
            if ($request->hdnFrmAction=="add" && $getAuth['AccountType']==1) {
                $isAuth = true;
                $query = "SELECT ID FROM MS_USER WHERE UserName=?";
                $isExist = DB::select($query, [$request->txtFrmUserName]);
                if (!$isExist) {
                    $isValid = true;
                    if (strpos($request->txtFrmEmail, '@') && !filter_var($request->txtFrmEmail, FILTER_VALIDATE_EMAIL)) {
                        $isValid = false;
                        $return['status'] = false;
                        $return['message'] = "Mohon isi dengan Email dengan format yang benar";
                    }
                    if ($isValid) {
                        $key = $this->randomString(10);
                        $encrypt = $this->strEncrypt($key,$request->txtFrmPassword);
                        $query = "INSERT INTO MS_USER
                            (ID, RoleID, UserName, FullName, Email, AccountType,
                            Password, Salt, IVssl, Tagssl, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                            VALUES
                            (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
                        DB::insert($query, [
                            $request->selFrmRole,
                            $request->txtFrmUserName,
                            $request->txtFrmFullName,
                            $request->txtFrmEmail,
                            '1',
                            base64_encode($encrypt['result']),
                            base64_encode($key),
                            base64_encode($encrypt['iv']),
                            base64_encode($encrypt['tag']),
                            intval($request->radFrmStatus),
                            $getAuth['UserID']
                        ]);
                        $return['message'] = "Data berhasil disimpan!";
                        $return['callback'] = "doReloadTable()";
                    }
                } else {
                    $return['status'] = false;
                    $return['message'] = "Username sudah terdaftar";
                }
            }
            if ($request->hdnFrmAction=="edit") {
                if ($request->hdnFrmID!="SYSTEM") {
                    if ($getAuth['AccountType']==1) $isAuth = true;
                    else {
                        $query = "SELECT ID FROM MS_USER WHERE ID=?";
                        $query = DB::select($query, [$request->hdnFrmID]);
                        if ($query) $isAuth = true;
                    }
                    if ($isAuth) {
                        $isValid = true;
                        if (strpos($request->txtFrmEmail, '@') && !filter_var($request->txtFrmEmail, FILTER_VALIDATE_EMAIL)) {
                            $isValid = false;
                            $return['status'] = false;
                            $return['message'] = "Mohon isi dengan Email dengan format yang benar";
                        }
                        if ($isValid) {
                            if (trim($request->txtFrmPassword)!="") {
                                $key = $this->randomString(10);
                                $encrypt = $this->strEncrypt($key,$request->txtFrmPassword);
                                $query = "UPDATE MS_USER
                                            SET RoleID=?,
                                                FullName=?,
                                                Email=?,
                                                Password=?,
                                                Salt=?,
                                                IVssl=?,
                                                Tagssl=?,
                                                Status=?,
                                                ModifiedDate=NOW(),
                                                ModifiedBy=?
                                            WHERE ID=?";
                                DB::update($query, [
                                    $request->selFrmRole,
                                    $request->txtFrmFullName,
                                    $request->txtFrmEmail,
                                    base64_encode($encrypt['result']),
                                    base64_encode($key),
                                    base64_encode($encrypt['iv']),
                                    base64_encode($encrypt['tag']),
                                    intval($request->radFrmStatus),
                                    $getAuth['UserID'],
                                    $request->hdnFrmID
                                ]);
                            } else {
                                $query = "UPDATE MS_USER
                                            SET RoleID=?,
                                                FullName=?,
                                                Email=?,
                                                Status=?,
                                                ModifiedDate=NOW(),
                                                ModifiedBy=?
                                            WHERE ID=?";
                                DB::update($query, [
                                    $request->selFrmRole,
                                    $request->txtFrmFullName,
                                    $request->txtFrmEmail,
                                    intval($request->radFrmStatus),
                                    $getAuth['UserID'],
                                    $request->hdnFrmID
                                ]);
                            }
                            $return['message'] = "Data berhasil disimpan!";
                            $return['callback'] = "doReloadTable()";
                        }
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
            if ($request->_i!="SYSTEM") {
                if ($getAuth['AccountType']==1) $isAuth = true;
                else {
                    $query = "SELECT ID FROM MS_USER WHERE ID=?";
                    $query = DB::select($query, [$request->_i]);
                    if ($query) $isAuth = true;
                }
                if ($isAuth) {
                    $query = "DELETE FROM MS_USER WHERE ID=?";
                    DB::delete($query, [$request->_i]);
                    $return['message'] = "Data berhasil dihapus!";
                    $return['callback'] = "doReloadTable()";
                }
            }
        } 
        if (!$isAuth) $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doGenerate()
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID FROM MS_USER WHERE ID='SYSTEM'";
        $isExist = DB::select($query);
        if (!$isExist) {
            $key = $this->randomString(10);
            $encrypt = $this->strEncrypt($key,'P@ssw0rd');
            $query = "INSERT INTO MS_USER
                (ID, RoleID, UserName, FullName, Email, IDNumber, ContactNumber, AccountType,
                Password, Salt, IVssl, Tagssl, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
            DB::insert($query, [
                'SYSTEM',
                'SYSTEM',
                'root',
                'System Administor',
                '',
                '',
                '',
                1,
                base64_encode($encrypt['result']),
                base64_encode($key),
                base64_encode($encrypt['iv']),
                base64_encode($encrypt['tag']),
                1,
                'SYSTEM'
            ]);
            $return['message'] = "Data berhasil disimpan!";
        } else {
            $return['status'] = false;
            $return['message'] = "Data sudah ada";
        }
        return response()->json($return, 200);
    } 
    
}