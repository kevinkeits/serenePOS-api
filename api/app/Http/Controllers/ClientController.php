<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ClientController extends Controller
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
    
   // GET CLIENT
   public function get(Request $request)
   {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        $query = "SELECT MsClient.ID id, MsClient.Name name, MsClient.PlanType planType, MsUser.Name userName, MsUser.PhoneNumber phoneNumber, MsOutlet.Address address, MsClient.ImgUrl imgUrl, MsClient.MimeType mimeType
           FROM MsClient
           JOIN MsUser
           ON MsClient.ID = MsUser.ClientID
           JOIN MsOutlet
           ON MsClient.ID = MsOutlet.ClientID
           ORDER BY ID ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
   }    
   // END GET CLIENT

   // POST CLIENT
   public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "add") {
                $base64string = $request->fileData;

                // Split the base64 string to get the MIME type and file data
                $parts = explode(";base64,", $base64string);

                // Extract the file data
                $fileData = base64_decode($parts[1]);

                // Specify the directory where you want to save the file
                $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/client/';

                // Specify the filename
                $fileName = $request->fileName;

                // Specify the full path including the filename
                $filePath = $uploadDirectory . $fileName;

                // // Save the file to the specified directory
                file_put_contents($filePath, $fileData);
                $query = "SELECT UUID() GenID";
                $userID = DB::select($query)[0]->GenID;
                $query = "INSERT INTO MsClient
                        (IsDeleted, UserIn, DateIn, ID, Name, PlanType, ImgUrl, MimeType)
                        VALUES
                        (0, ?, NOW(), ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $userID,
                    $request->clientName,
                    $request->planType,
                    $filePath,
                    $parts[0],
                ]);
                $return['message'] = "Client successfully created.";
            }
            if ($request->action == "edit") {

                $base64string = $request->fileData;

                // Split the base64 string to get the MIME type and file data
                $parts = explode(";base64,", $base64string);

                // Extract the file data
                $fileData = base64_decode($parts[1]);

                // Specify the directory where you want to save the file
                $uploadDirectory = 'C:/xampp/htdocs/serenePOS-api/api/public/uploaded/client/';

                // Specify the filename
                $fileName = $request->fileName;

                // Specify the full path including the filename
                $filePath = $uploadDirectory . $fileName;

                // // Save the file to the specified directory
                file_put_contents($filePath, $fileData);
                $query = "UPDATE MsClient
                SET IsDeleted=0,
                    UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PlanType=?,
                    ImgUrl=?,
                    MimeType=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->clientName,
                    $request->planType,
                    $filePath,
                    $parts[0],
                    $request->id
                ]);
                $return['message'] = "Client successfully modified.";
            }
            if ($request->action == "delete") {
                $query = "UPDATE MsClient SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                DB::update($query, [$getAuth['UserID'],$request->id]);
                $return['message'] = "Client successfully deleted";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST CLIENT
}