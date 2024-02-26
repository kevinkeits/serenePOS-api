<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
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

    // GET USER
    public function get(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
                if ($request->ID) {
                    $query = "  SELECT MsUser.ID id, MsClient.ImgUrl imgUrl, MsClient.Name storeName, MsUser.Name name, MsUser.PhoneNumber phoneNumber, MsOutlet.ID outletID, MsOutlet.Name outletName, MsOutlet.IsPrimary isPrimary, MsOutlet.Address address
                                    FROM MsUser
                                    JOIN MsClient
                                    ON MsUser.ClientID = MsClient.ID
                                    JOIN MsOutlet
                                    ON MsOutlet.ID = MsUser.OutletID
                                    WHERE MsUser.ID = ?";
                    $details = DB::select($query,[$request->ID])[0];

                    $query = "  SELECT MsOutlet.ID id, MsOutlet.Name outletName, MsOutlet.PhoneNumber phoneNumber, MsOutlet.Address address, MsOutlet.ProvinceID provinceID, MsOutlet.DistrictID districtID, MsOutlet.SubDistrictID subDistrictID, MsOutlet.PostalCode postalCode
                                    FROM MsOutlet
                                    JOIN MsUser
                                    ON MsUser.OutletID = MsOutlet.ID
                                    WHERE MsUser.ID = ?
                                    ORDER BY MsOutlet.Name ASC" ;
                    $outletDetails = DB::select($query,[$request->ID]);

                    $return['data'] = array('details'=>$details, 'outletDetails'=>$outletDetails);
            } else {
                    $query = "  SELECT MsUser.ID id, MsUser.Name name, MsUser.Email email, MsUser.PhoneNumber phoneNumber, MsOutlet.Name outletName, MsClient.ImgUrl imgUrl, MsClient.MimeType mimeType
                                    FROM MsUser
                                    JOIN MsOutlet
                                    ON MsOutlet.ID = MsUser.OutletID
                                    JOIN MsClient
                                    ON MsClient.ID = MsUser.ClientID
                                    WHERE MsUser.ClientID = ?
                                    ORDER BY MsUser.Name ASC";
                    $data = DB::select($query, [$getAuth['ClientID']]);
                    if ($data) $return['data'] = $data;
                }
            } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }
    // END GET USER
    
    // POST ACCOUNT
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
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
        
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->Action == "add") {
                if ($isValid) {
                    $key = $this->randomString(10);
                    $encrypt = $this->strEncrypt($key,$request->Password);
                    $query = "SELECT UUID() GenID";
                    $UserID = DB::select($query)[0]->GenID;
                    $query = "INSERT INTO MsUser
                                (IsDeleted, UserIn, DateIn, ID, ClientID, OutletID, RegisterFrom, Name, PhoneNumber, Email, Password, Salt, IVssl, Tagssl)
                                VALUES
                                (0, ?, NOW(), ?, ?, ?, 'App', ?, ?, ?, ?, ?, ?, ?)";
                    DB::insert($query, [
                        $getAuth['UserID'],
                        $UserID,
                        $getAuth['ClientID'],
                        $request->OutletID,

                        $request->Name,
                        $request->PhoneNumber,
                        $request->Email,
                        base64_encode($encrypt['result']),
                        base64_encode($key),
                        base64_encode($encrypt['iv']),
                        base64_encode($encrypt['tag']),
                    ]);
                    $isValid = true;
                    $return['message'] = "User successfully created.";
                }
            }
            $return['message'] = $_message;

            if ($request->Action == "edit") {
                if ($isValid = true) {
                    $query = "UPDATE MsUser
                    SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        PhoneNumber=?,
                        Password=?,
                        OutletID=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->Name,
                        $request->PhoneNumber,
                        $request->Password,
                        $request->OutletID,
                        $request->ID
                    ]);
                    $isValid = true;
                    $return['message'] = "User successfully modified.";
                }
            }
            $return['status'] = $isValid;
            return response()->json($return, 200);

            if ($request->Action == "delete") {
                $query = "DELETE FROM MsUser
                WHERE ID=?";
                DB::delete($query, [$request->ID]);
                $return['message'] = "User successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
}