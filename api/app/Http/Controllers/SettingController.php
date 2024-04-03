<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class SettingController extends Controller
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

    // GET TRANSACTION
    public function getSettings(Request $request)
    {
       $return = array('status'=>true,'message'=>"",'data'=>array());
       $header = $request->header('Authorization');
       $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
                {
                    $query = "SELECT  
                                MsClient.Name storeName,

                                MsUser.Name name,
                                MsUser.PhoneNumber phoneNumber,
                                MsUser.Email email,
                                
                                MsOutlet.ID outletID, 
                                MsOutlet.Name outletName,
                                
                                CASE
                                    WHEN MsUser.ImgUrl != '' THEN CONCAT('https://serenepos.temandigital.id/api/uploaded/user/', MsUser.ImgUrl)
                                    ELSE ''
                                END AS accountImage,

                                CASE
                                    WHEN MsClient.ImgUrl != '' THEN CONCAT('https://serenepos.temandigital.id/api/uploaded/client/', MsClient.ImgUrl)
                                    ELSE ''
                                END AS clientImage
                        FROM MsClient
                        JOIN MsUser
                        ON MsClient.ID = MsUser.ClientID
                        JOIN MsOutlet
                        ON MsClient.ID = MsOutlet.ClientID
                        ORDER BY MsClient.ID DESC";
                    $data = DB::select($query, [$getAuth['ClientID']])[0];
                    if ($data) $return['data'] = $data;
                }
           } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }

    // GET OUTLET
    public function getOutlet(Request $request)
    {
       $return = array('status'=>true,'message'=>"",'data'=>array());
       $header = $request->header('Authorization');
       $getAuth = $this->validateAuth($header);
       if ($getAuth['status']) 
       {
            $query = "  SELECT MsOutlet.ID id, MsOutlet.Name outlet, MsOutlet.PhoneNumber phoneNumber, MsOutlet.IsPrimary isPrimary, MsOutlet.Address address, MsOutlet.SubDistrict subDistrict, MsOutlet.PostalCode postalCode
            FROM MsOutlet
            WHERE IsDeleted=0
                AND ClientID = ?
                ORDER BY Name ASC";
            $data = DB::select($query, [$getAuth['ClientID']]);
            if ($data) $return['data'] = $data;
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }
    // END GET SETTING

    // POST ACCOUNT
    public function doSaveAccount(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        
        if ($getAuth['status']) {
            if ($request->action == "edit") {
                if ($request->fileData != "") {

                    $base64string = $request->fileData;
                    $mime = explode(";base64,", $base64string);
                    $mimeType = str_replace('data:', '', $mime[0]);
                    $fileData = base64_decode($mime[1]);
                    // $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/user/';
                    $uploadDirectory = base_path('public/uploaded/user');
                    $fileName = $request->fileName;

                    $filePath = $uploadDirectory . $fileName;
                    file_put_contents($filePath, $fileData);

                    if ($request->Password != "") {
                        $key = $this->randomString(10);
                        $encrypt = $this->strEncrypt($key,$request->Password);

                        $query ="UPDATE MsUser
                        SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        Password=?,
                        Salt=?, 
                        IVssl=?, 
                        Tagssl=?,
                        ImgUrl=?,
                        MimeType=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->userName,
                        base64_encode($encrypt['result']),
                        base64_encode($key),
                        base64_encode($encrypt['iv']),
                        base64_encode($encrypt['tag']),
                        $fileName,
                        $mimeType,
                        $request->id
                    ]);
                    $return['message'] = "Account successfully modified.";
                    } else {
                        $query = "UPDATE MsUser
                        SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        ImgUrl=?,
                        MimeType=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->userName,
                        $fileName,
                        $mimeType,
                        $request->id
                    ]);
                    $return['message'] = "Account successfully modified.";
                    }
                } else {
                    if ($request->Password != "") {
                        $key = $this->randomString(10);
                        $encrypt = $this->strEncrypt($key, $request->Password);

                        $query = "UPDATE MsUser
                        SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        Password=?,
                        Salt=?, 
                        IVssl=?, 
                        Tagssl=?,
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->userName,
                        base64_encode($encrypt['result']),
                        base64_encode($key),
                        base64_encode($encrypt['iv']),
                        base64_encode($encrypt['tag']),
                        $request->id
                    ]);
                    $return['message'] = "Account successfully modified without image.";
                    } else {
                        $query = "UPDATE MsUser
                        SET IsDeleted=0,
                            UserUp=?,
                            DateUp=NOW(),
                            Name=?,
                            WHERE ID=?";
                        DB::update($query, [
                            $getAuth['UserID'],
                            $request->userName,
                            $request->id
                        ]);
                        $return['message'] = "Account successfully modified without image.";
                    }
                }
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST ACCOUNT

    // POST SETTING
    public function doSaveSetting(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "edit") {
                if ($request->fileData != "") {

                    $base64string = $request->fileData;
                    $mime = explode(";base64,", $base64string);
                    $mimeType = str_replace('data:', '', $mime[0]);
                    $fileData = base64_decode($mime[1]);
                    // $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/user/';
                    $uploadDirectory = base_path('public/uploaded/client');
                    $fileName = $request->fileName;

                    $filePath = $uploadDirectory . $fileName;
                    file_put_contents($filePath, $fileData);
    
                    $query = "UPDATE MsClient
                    SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        ImgUrl=?,
                        MimeType=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->userName,
                        $fileName,
                        $mimeType,
                        $request->id
                    ]);
                    $return['message'] = "Account successfully modified.";
                } else {
                    $query = "UPDATE MsClient
                    SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->userName,
                        $request->id
                    ]);
                    $return['message'] = "Account successfully modified without image.";
                }
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST SETTING

    // POST OUTLET
    public function doSaveOutlet(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "edit") {
                $query = "UPDATE MsOutlet
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PhoneNumber=?,
                    Address=?,
                    SubDistrict=?,
                    IsPrimary=?,
                    PostalCode=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->name,
                    $request->phoneNumber,
                    $request->address,
                    $request->subDistrict,
                    $request->isPrimary == "T" ? 1 : 0,
                    $request->postalCode,
                    $request->id
                ]);
                $return['message'] = "Outlet successfully modified.";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST OUTLET
}