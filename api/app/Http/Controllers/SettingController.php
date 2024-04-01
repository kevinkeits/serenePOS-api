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
    
    // GET ACCOUNT
    public function getAccount(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        $query = "SELECT MsClient.ID id, MsUser.Name name, MsUser.Email email, MsOutlet.ID outletID, MsOutlet.Name outletName, CASE MsUser.ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('http://localhost/serenePOS-api/api/public/uploaded/user/', MsUser.ImgUrl)) END imgUrl
        FROM MsClient
        JOIN MsUser
        ON MsClient.ID = MsUser.ClientID
        JOIN MsOutlet
        ON MsClient.ID = MsOutlet.ClientID
        ORDER BY MsClient.ID DESC";
        $return['data'] = DB::select($query);
        return response()->json($return, 200);
    }
    // END GET ACCOUNT

    // GET SETTING
    public function getSetting(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        $query = "SELECT MsClient.ID id, MsClient.Name name, MsUser.Name userName, MsUser.PhoneNumber phoneNumber, MsOutlet.Name outletName, MsOutlet.Address address, CASE MsClient.ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('http://localhost/serenePOS-api/api/public/uploaded/client/', MsClient.ImgUrl)) END imgUrl
        FROM MsClient
        JOIN MsUser
        ON MsClient.ID = MsUser.ClientID
        JOIN MsOutlet
        ON MsClient.ID = MsOutlet.ClientID
        ORDER BY MsClient.ID DESC";
        $return['data'] = DB::select($query);
        return response()->json($return, 200);
    }
    // END GET SETTING

    // GET OUTLET
    public function getOutlet(Request $request)
    {
       $return = array('status'=>true,'message'=>"",'data'=>array());
       $header = $request->header('Authorization');
       $getAuth = $this->validateAuth($header);
       if ($getAuth['status']) {
            if ($request->ID) {
                $query = "  SELECT MsOutlet.ID id, MsOutlet.Name outletName, MsOutlet.PhoneNumber phoneNumber, MsOutlet.IsPrimary isPrimary, MsOutlet.Address address, MsOutlet.ProvinceID provinceID, MsOutlet.DistrictID districtID, MsOutlet.SubDistrictID subDistrictID, MsOutlet.PostalCode postalCode
                            FROM MsOutlet
                            WHERE ID = ?
                                ORDER BY ID ASC";
                $details = DB::select($query,[$request->ID])[0];

                $return['data'] = array('details'=>$details);
            } else {
                $query = "  SELECT MsOutlet.ID id, MsOutlet.Name outlet, MsOutlet.IsPrimary isPrimary, MsOutlet.Address address, MsOutlet.ProvinceID provinceID, MsOutlet.DistrictID districtID, MsOutlet.SubDistrictID subDistrictID, MsOutlet.PostalCode postalCode
                            FROM MsOutlet
                            WHERE IsDeleted=0
                                AND ClientID = ?
                                ORDER BY Name ASC";
                $data = DB::select($query, [$getAuth['ClientID']]);
                if ($data) $return['data'] = $data;
            }
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
                    $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/user/';
                    $fileName = $request->fileName;
    
                    $query = "UPDATE MsUser
                    SET IsDeleted=0,
                        UserUp=?,
                        DateUp=NOW(),
                        Name=?,
                        Password=?,
                        ImgUrl=?,
                        MimeType=?
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->userName,
                        $request->password,
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
                        Password=?,
                        WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $request->userName,
                        $request->password,
                        $request->id
                    ]);
                    $return['message'] = "Account successfully modified without image.";
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
                    $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/user/';
                    $fileName = $request->fileName;
    
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
                 if ($request->fileData != "") {
                     $query = "UPDATE MsOutlet
                     SET IsDeleted=0,
                         UserUp=?,
                         DateUp=NOW(),
                         Name=?,
                         PhoneNumber=?,
                         Address=?,
                         SubDistrictID=?,
                         PostalCode=?
                         WHERE ID=?";
                     DB::update($query, [
                         $getAuth['UserID'],
                         $request->clientName,
                         $request->phoneNumber,
                         $request->address,
                         $request->subDistrict,
                         $request->postalCode,
                         $request->id
                     ]);
                     $return['message'] = "Outlet successfully modified.";
                 }
             }
         } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
         return response()->json($return, 200);
     }
     // END POST OUTLET
}