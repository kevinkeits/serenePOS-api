<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

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
            $mainQuery = "SELECT ID,Name,Status,CreatedDate
                            FROM MS_ROLE
                            WHERE {definedFilter}
                            ORDER BY CreatedDate DESC";
            $definedFilter = "ID!='SYSTEM'";
            if ($request->_i) {
                $definedFilter = "ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $return['data'] = $data[0];
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getSelected(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        if ($request->_i) {
            $query = "SELECT MenuID FROM MS_ROLE_ACCESS WHERE RoleID = ?";
            $data = DB::select($query,[$request->_i]);
            if ($data) {
                $return['data'] = $data;
                $return['callback'] = "onCompleteFetchSelected(e.data)";
            }
        }
        return response()->json($return, 200);
    }

    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $isProcess = false;
            $i = 0;
            $query = "SELECT ID FROM MS_MENU WHERE Status=1";
            $arrMenu = DB::select($query);
            foreach ($arrMenu as $key => $value) {
                if(isset($_POST['chkFrmMenu_'.$value->ID])) $i++;
            }
            if ($i > 0) {
                if ($request->hdnFrmAction=="add") {
                    $data = DB::select("SELECT UUID() ID");
                    $ID = $data[0]->ID;
                    $query = "INSERT INTO MS_ROLE
                            (ID, Name, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                            VALUES(?, ?, ?, NOW(), ?, NULL, NULL)";
                    DB::insert($query, [
                        $ID,
                        $request->txtFrmName,
                        intval($request->radFrmStatus),
                        $getAuth['UserID']
                    ]);
                    $isProcess = true;
                    $return['message'] = "Data berhasil disimpan!";
                    $return['callback'] = "doReloadTable()";
                }
                if ($request->hdnFrmAction=="edit") {
                    $ID = $request->hdnFrmID;
                    if ($request->hdnFrmID!="SYSTEM") {
                        $query = "UPDATE MS_ROLE
                                SET Name=?, 
                                Status=?, 
                                ModifiedDate=NOW(), 
                                ModifiedBy=?
                                WHERE ID=?";
                        DB::update($query, [
                            $request->txtFrmName,
                            intval($request->radFrmStatus),
                            $getAuth['UserID'],
                            $request->hdnFrmID
                        ]);
                        $isProcess = true;
                        $return['message'] = "Data berhasil disimpan!";
                        $return['callback'] = "doReloadTable()";
                    } else {
                        $return['status'] = false;
                        $return['message'] = "Not Authorized";
                    }
                }
                if ($isProcess) {
                    $query = "DELETE FROM MS_ROLE_ACCESS WHERE RoleID = ?";
                    DB::delete($query, [$request->hdnFrmID]);
                    foreach ($arrMenu as $key => $value) {
                        if(isset($_POST['chkFrmMenu_'.$value->ID])) {
                            $query = "INSERT INTO MS_ROLE_ACCESS
                                        (ID, RoleID, MenuID)
                                    VALUES(UUID(), ?, ?)";
                            DB::insert($query, [$ID,$value->ID,]);
                        }
                    }
                }
            } else {
                $return['status'] = false;
                $return['message'] = "Mohon pilih satu menu atau lebih untuk peran ini";
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doDelete(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->_i!="SYSTEM") {
                $query = "DELETE FROM MS_ROLE WHERE ID = ?";
                DB::delete($query, [$request->_i]);
                $query = "DELETE FROM MS_ROLE_ACCESS WHERE RoleID = ?";
                DB::delete($query, [$request->_i]);
                $return['message'] = "Data berhasil dihapus";
                $return['callback'] = "doReloadTable()";
            } else {
                $return['status'] = false;
                $return['message'] = "Not Authorized";
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doGenerate()
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $query = "SELECT ID FROM MS_ROLE WHERE ID='SYSTEM'";
        $isExist = DB::select($query);
        if (!$isExist) {
            $query = "INSERT INTO MS_ROLE
                    (ID, Name, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                    VALUES(?, ?, ?, NOW(), ?, NULL, NULL)";
            DB::insert($query, [
                'SYSTEM',
                'System Administrator',
                1,
                'SYSTEM'
            ]);
            $i = 0;
            $query = "SELECT ID FROM MS_MENU WHERE Status=1";
            $arrMenu = DB::select($query);
            foreach ($arrMenu as $key => $value) {  
                $query = "INSERT INTO MS_ROLE_ACCESS
                            (ID, RoleID, MenuID)
                        VALUES(UUID(), ?, ?)";
                DB::insert($query, ['SYSTEM',$value->ID]);
            }
            $return['message'] = "Data berhasil disimpan!";
        } else {
            $return['status'] = false;
            $return['message'] = "Data sudah ada";
        }
        return response()->json($return, 200);
    }

}