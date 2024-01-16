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

    public function doLogin(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $return['message'] = "Username atau Password salah!";
        $query = "SELECT ID,FullName,RoleID,Status,Password,Salt,IVssl,Tagssl
                    FROM MS_USER
                    WHERE UPPER(UserName) = UPPER(?) AND RegisterFrom = 'manual'";
        $data = DB::select($query,[$request->txtUserName]);
        if ($data) {
            $data = $data[0];
            if ($data->Status==1) {
                $decrypted = $this->strDecrypt(base64_decode($data->Salt),base64_decode($data->IVssl),base64_decode($data->Tagssl),base64_decode($data->Password));
                if ($decrypted == $request->txtPassword) {
                    $SessionID = base64_encode($this->randomString(64).base64_encode(md5($data->ID).time()));
                    $IP = getenv('REMOTE_ADDR');
                    $query = "INSERT INTO TR_SESSION (ID, UserID, Token, LoginDate, LogoutDate, IPAddress, LastActive)
                                VALUES (UUID(), ?, ?, NOW(), NULL, ?, NOW())";
                    DB::insert($query,[$data->ID,$SessionID,$IP]);

                    $arrBranch = "";
                    $query = "SELECT u.ID, u.AccountType, b.BranchID
                                FROM MS_USER u
                                    JOIN TR_SESSION s ON s.UserID = u.ID 
                                    LEFT JOIN MS_BRANCH_ADMIN b ON b.UserID = u.ID
                                WHERE s.Token=?
                                    AND s.LogoutDate IS NULL";
                    $checkAuth = DB::select($query,[$SessionID]);
                    if ($checkAuth) {
                        $data = $checkAuth[0];
                        if ($data->BranchID) {
                            foreach ($checkAuth as $key => $value) {
                                if ($arrBranch != "") $arrBranch .= ",";
                                $arrBranch .= "'".$value->BranchID."'";
                            }
                        }
                    }
                    $return = array(
                        'status' => true,
                        'message' => "",
                        'data' => array(
                            'Token' => $SessionID,
                            'RememberLogin' => $request->chkRemember ? true : false,
                            'IsBranchAdmin' => $arrBranch == "" ? false : true
                        ),
                        'callback' => "app.auth.loginHandler(e.data)"
                    );
                }
            } else {
                $return['message'] = "User tidak aktif";
            }
        }
        return response()->json($return, 200);
    }

    public function doAuth(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT u.ID,u.FullName,u.AccountType
                FROM TR_SESSION s
                    JOIN MS_USER u ON u.ID = s.UserID  
                    JOIN MS_ROLE r ON r.ID = u.RoleID
                WHERE s.Token = ?
                    AND s.LogoutDate IS NULL";
        $data = DB::select($query,[$request->_s]);
        if ($data) {
            $userData = $data[0];
            $query = "SELECT m.ID,m.Name,m.URL,m.Icon,m.ParentID
                    FROM TR_SESSION s
                        JOIN MS_USER u ON u.ID = s.UserID 
                        JOIN MS_ROLE_ACCESS r ON r.RoleID = u.RoleID 
                        JOIN MS_MENU m ON m.ID = r.MenuID 
                        LEFT JOIN MS_MENU mp ON mp.ID=m.ParentID
                    WHERE s.Token = ?
                        AND s.LogoutDate IS NULL
                    ORDER BY m.Sequence ASC, mp.Sequence ASC";
            $accessMenu = DB::select($query,[$request->_s]);
            $arrData = array(
                'userData' => $data[0],
                'accessMenu' => $accessMenu
            );
            $return = array(
                'status' => true,
                'message' => "",
                'data' => $arrData,
                'callback' => "app.init(e.data)"
            );
        } else $return = array('status'=>false,'message'=>"Not Authorized",'callback'=>"app.auth.notAuthorizedHandler(e.message)");
        return response()->json($return, 200);
    }

    public function doLogout(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $query = "UPDATE TR_SESSION
                    SET LogoutDate = NOW()
                    WHERE Token = ?";
        DB::insert($query,[$request->Token]);
        $return = array(
            'status' => true,
            'message' => "Berhasil logout!",
            'callback' => "app.auth.clearSession()"
        );
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