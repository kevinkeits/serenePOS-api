<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
                                    Name,
                                    ImagePath,
                                    Status
                            FROM    MS_CATEGORY
                            WHERE {definedFilter}
                            ORDER BY Name ASC";
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
                $query = "SELECT UUID() GenID";
                $ID = DB::select($query)[0]->GenID;
                $query = "SELECT ID FROM MS_CATEGORY WHERE Name=?";
                $isExist = DB::select($query, [$request->txtFrmName]);
                if (!$isExist) {
                    if ($request->hdnAttached == "") {
						$return['status'] = false;
						$return['message'] = "Mohon untuk pilih gambar terlebih dahulu!";
					} else {
                        $query = "INSERT INTO MS_CATEGORY
                                    (ID, Name, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                                VALUES(?, ?, ?, NOW(), ?, NULL, NULL)";
                        DB::insert($query, [
                            $ID,
                            $request->txtFrmName,
                            $request->radFrmStatus==1 ? 1 : 0,
                            $getAuth['UserID']
                        ]);

                        $return['status'] = true;
                        $return['message'] = "Data berhasil tersimpan!";
                        $return['callback'] = "execUpload('".$ID."')";
                    }
                } else {
                    $return['status'] = false;
                    $return['message'] = "Nama sudah terdaftar";
                }
            }
            if ($request->hdnFrmAction=="edit") {
                $query = "SELECT ID FROM MS_CATEGORY WHERE Name=? AND ID!=?";
                $isExist = DB::select($query, [$request->txtFrmCode, $request->hdnFrmID]);
                if (!$isExist) {
                    if ($request->hdnAttached == "") {
                        $query = "SELECT ImagePath FROM MS_CATEGORY WHERE ID=?";
                        $isExist = DB::select($query, [$request->hdnFrmID]);
                        if ($isExist[0]->ImagePath != '' && $isExist[0]->ImagePath != null) {
                            if (file_exists('uploaded/category/'.$isExist[0]->ImagePath)) {
                                $query = "UPDATE MS_CATEGORY
                                            SET Name=?, 
                                                Status=?,
                                                ModifiedDate=NOW(), 
                                                ModifiedBy=?
                                            WHERE ID=?";
                                DB::update($query, [
                                    $request->txtFrmName,
                                    $request->radFrmStatus==1 ? 1 : 0,
                                    $getAuth['UserID'],
                                    $request->hdnFrmID
                                ]);
                                $return['status'] = true;
                                $return['message'] = "Data berhasil tersimpan!";
                                $return['callback'] = "doReloadTable()";
                            }
                        } else {
                            $return['status'] = false;
						    $return['message'] = "Mohon untuk pilih gambar terlebih dahulu!";
                        }
                    } else {
                        $query = "UPDATE MS_CATEGORY
                                    SET Name=?, 
                                        Status=?,
                                        ModifiedDate=NOW(), 
                                        ModifiedBy=?
                                    WHERE ID=?";
                        DB::update($query, [
                            $request->txtFrmName,
                            $request->radFrmStatus==1 ? 1 : 0,
                            $getAuth['UserID'],
                            $request->hdnFrmID
                        ]);
                        $return['status'] = true;
                        $return['callback'] = "execUpload('".$request->hdnFrmID."')";
                    }
                } else {
                    $return['status'] = false;
                    $return['message'] = "Nama sudah terdaftar";
                }
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doUpload(Request $request)
    {
        $return = array('status'=>false,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->file('inpFile')->isValid()) {
                try
                {
                    $query = "SELECT ImagePath FROM MS_CATEGORY WHERE ID=? AND (ImagePath <> '' AND ImagePath is not null)";
                    $isExist = DB::select($query, [$request->_data1]);
                    if ($isExist) {
                        if(file_exists(base_path('public/uploaded/category').$isExist[0]->ImagePath)) {
                            unlink(base_path('public/uploaded/category').$isExist[0]->ImagePath);
                        }
                    }
                    $tempFile = 'temp-'.time().'.'.$request->file('inpFile')->getClientOriginalExtension();
                    $request->file('inpFile')->move(base_path('public/uploaded/category'), $tempFile);
                    $query = "UPDATE MS_CATEGORY
                                SET ImagePath=?
                                WHERE ID=?";
                    DB::update($query, [
                        $tempFile,
                        $request->_data1
                    ]);
                    $return['status'] = true;
                    $return['message'] = "Data berhasil tersimpan!";
                    $return['callback'] = "doReloadTable()";
                } catch(Exception $e) {
                    $return['status'] = true;
                    $return['message'] = $e->getMessage();
                }
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doDelete(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID, ImagePath FROM MS_CATEGORY WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                try
                {
                    if ($isExist[0]->ImagePath != '' && $isExist[0]->ImagePath != null) {
                        if (file_exists(base_path('public/uploaded/category').$isExist[0]->ImagePath)) {
                            unlink(base_path('public/uploaded/category').$isExist[0]->ImagePath);
                        }
                    }
                    $query = "DELETE FROM MS_CATEGORY WHERE ID=?";
                    DB::delete($query, [$request->_i]);
                    $return['message'] = "Data berhasil dihapus!";
                    $return['callback'] = "doReloadTable()";
                } catch(Exception $e) {
                    $return['status'] = false;
                    $return['message'] = $e->getMessage();
                }
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doDeletePic(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID, ImagePath FROM MS_CATEGORY WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                try
                {
                    if ($isExist[0]->ImagePath != '' && $isExist[0]->ImagePath != null) {
                        if (file_exists(base_path('public/uploaded/category').$isExist[0]->ImagePath)) {
                            unlink(base_path('public/uploaded/category').$isExist[0]->ImagePath);
                        }
                    }
                    $query = "UPDATE MS_CATEGORY SET ImagePath = '', ModifiedDate = now(), ModifiedBy = ? WHERE ID=?";
                    DB::delete($query, [$getAuth['UserID'], $request->_i]);
                    $return['message'] = "Data berhasil dihapus!";
                    $return['callback'] = "doReloadTable()";
                } catch(Exception $e) {
                    $return['status'] = false;
                    $return['message'] = $e->getMessage();
                }
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }
    

}