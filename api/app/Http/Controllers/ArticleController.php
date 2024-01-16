<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ArticleController extends Controller
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
				    CASE WHEN Type = '1' THEN 'Recipe' ELSE 'Article' END TypeDesc,
                                    Title,
                                    ImageUrl,
                                    Contents,
                                    Status
                            FROM MS_ARTICLE
                            WHERE {definedFilter}
                            ORDER BY CreatedBy DESC";
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
		if ($request->hdnAttached == "") {
                    $return['status'] = false;
                    $return['message'] = "Mohon untuk pilih gambar terlebih dahulu!";
                } else {
		    $isValid = true;
                    if ($isValid) {
		    	$query = "SELECT UUID() GenID";
                    	$ID = DB::select($query)[0]->GenID;

                    	$query = "INSERT INTO MS_ARTICLE
                                    (ID, Type, Title, Contents, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                                VALUES(?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
                    	DB::insert($query, [
				$ID,
                        	intval($request->radFrmType),
                        	$request->txtFrmTitle,
                        	$request->txtFrmContent,
                        	intval($request->radFrmStatus),
                        	$getAuth['UserID']
                    	]);
			$return['callback'] = "execUpload('".$ID."')";
                    	$return['message'] = "Data berhasil disimpan!";
		    }
		}
            }
            if ($request->hdnFrmAction=="edit") {
                $isValid = true;
                $isUpload = true;
                if ($isValid) {
			if ($request->hdnAttached == "") {
                        	$isUpload = false;
                        	$query = "SELECT ImageUrl FROM MS_ARTICLE WHERE ID=?";
                        	$isExist = DB::select($query, [$request->hdnFrmID]);
                        	if ($isExist[0]->ImageUrl == '' || $isExist[0]->ImageUrl == NULL) {
                            		$isValid = false;
                            		$return['status'] = false;
			    		$return['message'] = "Mohon untuk pilih gambar terlebih dahulu!";
                        	}
                    	}
		}
		if ($isValid) {
                    $query = "UPDATE MS_ARTICLE 
                                SET Type=?, 
                                    Title=?, 
                                    Contents=?, 
                                    Status=?,
                                    ModifiedDate=NOW(), 
                                    ModifiedBy=?
                                WHERE ID=?";
                    DB::update($query, [
                        intval($request->radFrmType), 
                        $request->txtFrmTitle,
                        $request->txtFrmContent,
                        intval($request->radFrmStatus),
                        $getAuth['UserID'],
                        $request->hdnFrmID
                    ]);

                    if ($isUpload) $return['callback'] = "execUpload('".$request->hdnFrmID."')";
                    else $return['callback'] = "doReloadTable()";
                    $return['message'] = "Data berhasil tersimpan!";
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
                    $query = "SELECT ImageUrl FROM MS_ARTICLE WHERE ID=? AND (ImageUrl <> '' AND ImageUrl is not null)";
                    $isExist = DB::select($query, [$request->_data1]);
                    if ($isExist) {
                        if(file_exists(base_path('public/uploaded/article').$isExist[0]->ImageUrl )) {
                            unlink(base_path('public/uploaded/article').$isExist[0]->ImageUrl );
                        }
                    }
                    $tempFile = 'temp-'.time().'.'.$request->file('inpFile')->getClientOriginalExtension();
                    $request->file('inpFile')->move(base_path('public/uploaded/article'), $tempFile);
                    $query = "UPDATE MS_ARTICLE 
                                SET ImageUrl =?
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
            $query = "SELECT ID FROM MS_ARTICLE WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                $query = "DELETE FROM MS_ARTICLE WHERE ID=?";
                DB::delete($query, [$request->_i]);
                $return['message'] = "Data berhasil dihapus!";
                $return['callback'] = "doReloadTable()";
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

public function doDeletePic(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID, ImageUrl FROM MS_ARTICLE WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                try
                {
                    if ($isExist[0]->ImagePath != '' && $isExist[0]->ImagePath != null) {
                        if (file_exists(base_path('public/uploaded/article').$isExist[0]->ImagePath)) {
                            unlink(base_path('public/uploaded/article').$isExist[0]->ImagePath);
                        }
                    }
                    $query = "UPDATE MS_ARTICLE SET ImageUrl = '', ModifiedDate = now(), ModifiedBy = ? WHERE ID=?";
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