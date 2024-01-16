<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class SettingController extends Controller
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
            $mainQuery = "  SELECT  ID,
                                    Type,
                                    Field1,
                                    Field2,
                                    Field3,
                                    Status
                            FROM MS_REFERENCES
                            WHERE {definedFilter}
                            ORDER BY Type, Field1 ASC";
            $definedFilter = "1=1";
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
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doSave(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->hdnFrmAction=="add") {
                $query = "SELECT ID FROM MS_REFERENCES WHERE Type=? AND Field1=? AND Field2=?";
                $isExist = DB::select($query, [$request->txtFrmType, $request->txtFrmField1, $request->txtFrmField2]);
                if (!$isExist) {
                    $query = "INSERT INTO MS_REFERENCES
                                    (ID, Type, Field1, Field2, Field3, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                                VALUES(UUID(), ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
                    DB::insert($query, [
                        $request->txtFrmType,
                        $request->txtFrmField1,
                        $request->txtFrmField2,
                        $request->txtFrmField3,
                        intval($request->radFrmStatus),
                        $getAuth['UserID']
                    ]);
                    $return['message'] = "Data berhasil disimpan!";
                    $return['callback'] = "doReloadTable()";
                } else {
                    $return['status'] = false;
                    $return['message'] = "Tipe, Field1 dan Field2 sudah terdaftar";
                }
            }
            if ($request->hdnFrmAction=="edit") {
                /*$query = "SELECT ID FROM MS_REFERENCES WHERE Type=? AND Field1=? AND Field2=?";
                $isExist = DB::select($query, [$request->txtFrmType, $request->txtFrmField1, $request->txtFrmField2]);
                if (!$isExist) {*/
                    $query = "UPDATE MS_REFERENCES
                                SET Type=?, 
                                    Field1=?, 
                                    Field2=?, 
                                    Field3=?, 
                                    Status=?,
                                    ModifiedDate=NOW(), 
                                    ModifiedBy=?
                                WHERE ID=?";
                    DB::update($query, [
                        $request->txtFrmType,
                        $request->txtFrmField1,
                        $request->txtFrmField2,
                        $request->txtFrmField3,
                        intval($request->radFrmStatus),
                        $getAuth['UserID'],
                        $request->hdnFrmID
                    ]);
                    $return['message'] = "Data berhasil disimpan!";
                    $return['callback'] = "doReloadTable()";
                /*} else {
                    $return['status'] = false;
                    $return['message'] = "Tipe, Field1 dan Field2 sudah terdaftar";
                }*/
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doDelete(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID FROM MS_REFERENCES WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                $query = "DELETE FROM MS_REFERENCES WHERE ID=?";
                DB::delete($query, [$request->_i]);
                $return['message'] = "Data berhasil dihapus!";
                $return['callback'] = "doReloadTable()";
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

}