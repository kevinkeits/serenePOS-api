<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class OutletController extends Controller
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

    public function get(Request $request)
    {
       $return = array('status'=>true,'message'=>"",'data'=>array());
       $header = $request->header('Authorization');
       $getAuth = $this->validateAuth($header);
       if ($getAuth['status']) {
            if ($request->ID) {
                $query = "  SELECT MsOutlet.ID id, MsOutlet.Name outletName, MsOutlet.PhoneNumber phoneNumber, MsOutlet.IsPrimary isPrimary, MsOutlet.Address address, MsOutlet.Province province, MsOutlet.District district, MsOutlet.SubDistrict subDistrict, MsOutlet.PostalCode postalCode
                            FROM MsOutlet
                            WHERE ID = ?
                                ORDER BY ID ASC";
                $details = DB::select($query,[$request->ID])[0];

                $return['data'] = array('details'=>$details);
            } else {
                $query = "  SELECT MsOutlet.ID id, MsOutlet.Name outlet, MsOutlet.IsPrimary isPrimary, MsOutlet.Address address, MsOutlet.Province province, MsOutlet.District district, MsOutlet.SubDistrict subDistrict, MsOutlet.PostalCode postalCode
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
    
    // POST OUTLET
    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        if ($getAuth['status']) {
            if ($request->action == "add") {

                $query = "SELECT UUID() GenID";
                $outletID = DB::select($query)[0]->GenID;
                $query = "INSERT INTO MsOutlet
                        (IsDeleted, UserIn, DateIn, ID, ClientID, Name, PhoneNumber, IsPrimary, Address, Province, District, SubDistrict, PostalCode)
                        VALUES
                        (0, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $getAuth['UserID'],
                    $outletID,
                    $getAuth['ClientID'],
                    $request->name,
                    $request->phoneNumber,
                    $request->isPrimary == "T" ? 1 : 0,
                    $request->address,
                    $request->province,
                    $request->district,
                    $request->subDistrict,
                    $request->postalCode
                ]);
                $return['message'] = "Outlet successfully created.";
            } 
            if ($request->action == "edit") {
                $query = "UPDATE MsOutlet
                SET UserUp=?,
                    DateUp=NOW(),
                    Name=?,
                    PhoneNumber=?,
                    IsPrimary=?,
                    Address=?,
                    Province=?,
                    District=?,
                    SubDistrict=?,
                    PostalCode=?
                    WHERE ID=?";
                DB::update($query, [
                    $getAuth['UserID'],
                    $request->name,
                    $request->phoneNumber,
                    $request->isPrimary == "T" ? 1 : 0,
                    $request->address,
                    $request->province,
                    $request->district,
                    $request->subDistrict,
                    $request->postalCode,
                    $request->id
                ]);
                $return['message'] = "Outlet successfully modified.";
            }
            if ($request->action == "delete") {
                $query = "UPDATE MsOutlet SET IsDeleted=1, UserUp=?, DateUp=NOW() WHERE ID=?";
                DB::update($query, [$getAuth['UserID'],$request->id]);
                $return['message'] = "Outlet successfully deleted.";
            }
        } else $return = array('status'=>false,'message'=>"[403] Not Authorized",'data'=>null);
        return response()->json($return, 200);
    }
    // END POST OUTLET
}