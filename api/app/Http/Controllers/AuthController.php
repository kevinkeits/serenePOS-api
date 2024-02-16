<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
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

    public function doRegister(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null);
        $isValid = true;
        $_message = "";
        if (!filter_var($request->Email, FILTER_VALIDATE_EMAIL)) {
            $_message = "Email is not in the correct format";
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
            $query = "INSERT INTO MsUser (IsDeleted, UserIn, DateIn, ID, ClientID, OutletID, RegisterFrom, Name, Email, Password, Salt, IVssl, Tagssl)
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
            $_message = "Registration successful";
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
                        AND RegisterFrom = 'App'";
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
                    $return['message'] = "Login success";
                } else $return['message'] = "[403] Incorrect Username or Password";
            } else $return['message'] = "User is not active";
        } else $return['message'] = "[404] Incorrect Username or Password";
        return response()->json($return, 200);
    }

    public function doLogout(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        if ($header != null) $header = trim(str_replace("Bearer","",$header));
        if ($getAuth['status']) {
            $query = "UPDATE TrSession SET IsLoggedOut=1, DateUp=NOW() WHERE ID=?";
            DB::update($query, [$header]);
            $return['status'] = true;
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    
    public function doReset(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $return['message'] = "User tidak ditemukan";
        $query = "SELECT ID,Email,FullName,RoleID,RegisterFrom,Password,Salt,IVssl,Tagssl
                    FROM MS_USER
                    WHERE UPPER(Email) = UPPER(?)
                            AND Status = 1";
        $isExist = DB::select($query,[$request->txtEmail]);
        if ($isExist) {
            if ($isExist[0]->RegisterFrom == "manual" || $isExist[0]->RegisterFrom == "app") {
                if ($isExist[0]->Email != "") {
                    $key = $this->randomString(10);
                    $newPass = $this->randomString(10);
                    $encrypt = $this->strEncrypt($key,$newPass);
                    $query = "UPDATE MS_USER
                                SET Password=?,
                                    Salt=?,
                                    IVssl=?,
                                    Tagssl=?
                                WHERE ID=?";
                        DB::update($query, [
                            base64_encode($encrypt['result']),
                            base64_encode($key),
                            base64_encode($encrypt['iv']),
                            base64_encode($encrypt['tag']),
                            $isExist[0]->ID
                        ]);
                    $data = array(
                        'name'=>$isExist[0]->FullName,
                        'email'=>$isExist[0]->Email,
                        'newPass'=>$newPass
                    );
                    Mail::send('resetPass', $data, function($message) use ($data) {
                        $message->to($data['email'], $data['name']);
                        $message->subject('Ellafroze.com Reset Akun');
                    });
                    $return = array(
                        'status' => true,
                        'message' => "Berhasil, Harap cek email anda untuk instruksi lebih lanjut",
                        'callback' => ""
                    );
                } else {
                    $return = array(
                        'status' => false,
                        'message' => "Maaf, kamu belum menentukan email sehingga kami tidak bisa melakukan reset password",
                        'callback' => ""
                    );
                }
            } else {
                $return = array(
                    'status' => false,
                    'message' => "Maaf, reset password tidak dapat digunakan pada akun yang Daftar by Google",
                    'callback' => ""
                );
            }
        }
        return response()->json($return, 200);
    }

}