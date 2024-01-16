<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    private function validateAuth($Token) {
        $return = array('status'=>false,'UserID'=>"");
        $query = "SELECT u.ID, u.AccountType, b.BranchID
                    FROM MS_USER u
                        JOIN TR_SESSION s ON s.UserID = u.ID 
                        LEFT JOIN MS_BRANCH_ADMIN b ON b.UserID = u.ID
                    WHERE s.Token=?
                        AND s.LogoutDate IS NULL";
        $checkAuth = DB::select($query,[$Token]);
        if ($checkAuth) {
            $data = $checkAuth[0];
            $arrBranch = "";
            if ($data->BranchID) {
                foreach ($checkAuth as $key => $value) {
                    if ($arrBranch != "") $arrBranch .= ",";
                    $arrBranch .= "'".$value->BranchID."'";
                }
            }
            $query = "UPDATE TR_SESSION SET LastActive=NOW() WHERE Token=?";
            DB::update($query,[$Token]);
            $return = array(
                'status' => true,
                'UserID' => $data->ID,
                'AccountType' => $data->AccountType,
                'BranchAuth' => $arrBranch
            );
        }
        return $return;
    }

    public function getAll(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "  SELECT
                                b.ID,
                                b.Name,
                                b.Address,
                                b.DistrictID,
                                b.CityID,
                                b.StateID,
                                ds.Field2 as DistrictName,
                                ct.Field2 as CityName,
                                st.Field2 as StateName,
                                b.Phone,
                                b.WA,
                                b.FB,
                                b.IG,
                                b.Status
                            FROM MS_BRANCH b
                                LEFT JOIN MS_REFERENCES ds ON ds.Type = 'DistrictID' AND ds.ID = b.DistrictID
                                LEFT JOIN MS_REFERENCES ct ON ct.Type = 'CityID' AND ct.ID = b.CityID
                                LEFT JOIN MS_REFERENCES st ON st.Type = 'StateID' AND st.ID = b.StateID
                            WHERE {definedFilter}
                            ORDER BY b.Name ASC";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "b.ID IN (".$getAuth['BranchAuth'].")";
            $return['message'] = $getAuth['BranchAuth'];
            if ($request->_i) {
                $definedFilter = "b.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT    a.UserID ID
                                FROM    MS_BRANCH_ADMIN a
                                    LEFT JOIN MS_USER u ON u.ID = a.UserID
                                WHERE a.BranchID = ?
                                ORDER BY  u.FullName ASC";
                    $selBranch = DB::select($query,[$request->_i]);
                    $arrData = [];
                    if ($selBranch) {
                        foreach ($selBranch as $key => $value) {
                            array_push($arrData,$value->ID);
                        }
                    }
                    $return['data'] = array('header'=>$data[0], 'selBranch'=>$arrData);
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
                $query = "SELECT ID FROM MS_BRANCH WHERE Name=?";
                $isExist = DB::select($query, [$request->txtFrmName]);
                if (!$isExist) {
                    $query = "SELECT UUID() GenID";
                    $ID = DB::select($query)[0]->GenID;
                    $query = "INSERT INTO MS_BRANCH (ID, Name, Address, DistrictID, CityID, StateID, Phone, WA, FB, IG, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
                    DB::insert($query, [
                        $ID,
                        $request->txtFrmName,
                        $request->txtFrmAlamat,
                        $request->SelFrmDistrict,
                        $request->SelFrmCity,
                        $request->SelFrmState,
                        $request->txtFrmPhone,
                        $request->txtFrmWA,
                        $request->txtFrmFB,
                        $request->txtFrmIG,
                        $request->radFrmStatus==1 ? 1 : 0,
                        $getAuth['UserID']
                    ]);
                    if ($request->selFrmAdminBranch) {
						if (count($request->selFrmAdminBranch) > 0) {
							foreach ($request->selFrmAdminBranch as $key => $value) {
								$query = "INSERT INTO MS_BRANCH_ADMIN (ID, BranchID, UserID) 
								VALUES (UUID(), ?, ?)";
								DB::insert($query, [
									$ID,
									$value
								]);
							}
						}
					}
                    
                    $return['message'] = "Data berhasil tersimpan!";
                    $return['callback'] = "doReloadTable()";
                } else {
                    $return['status'] = false;
                    $return['message'] = "Nama sudah terdaftar";
                }
            }
            if ($request->hdnFrmAction=="edit") {
				$query = "SELECT ID FROM MS_BRANCH WHERE Name=? AND ID!=?";
                $isExist = DB::select($query, [$request->txtFrmName,$request->hdnFrmID]);
                if (!$isExist) {
					$ID = $request->hdnFrmID;
					$query = "UPDATE MS_BRANCH
							SET Name=?, 
                                Address=?,
                                StateID=?,
                                CityID=?,
                                DistrictID=?,
                                Phone=?,
                                WA=?,
                                FB=?,
                                IG=?,
                                Status=?,
                                ModifiedDate=NOW(), 
                                ModifiedBy=?
							WHERE ID=?";
					DB::update($query, [
						$request->txtFrmName,
						$request->txtFrmAlamat,
						$request->SelFrmState,
						$request->SelFrmCity,
						$request->SelFrmDistrict,
						$request->txtFrmPhone,
						$request->txtFrmWA,
						$request->txtFrmFB,
						$request->txtFrmIG,
						$request->radFrmStatus==1 ? 1 : 0,
						$getAuth['UserID'],
						$request->hdnFrmID
					]);
					$query = "DELETE FROM MS_BRANCH_ADMIN WHERE BranchID=?";
					DB::delete($query, [$request->hdnFrmID]);
					if ($request->selFrmAdminBranch) {
						if (count($request->selFrmAdminBranch) > 0) {
							foreach ($request->selFrmAdminBranch as $key => $value) {
								$query = "INSERT INTO MS_BRANCH_ADMIN (ID, BranchID, UserID) 
								VALUES (UUID(), ?, ?)";
								DB::insert($query, [
									$request->hdnFrmID,
									$value
								]);
							}
						}
					}
					$return['message'] = "Data berhasil tersimpan!";
					$return['callback'] = "doReloadTable()";
				
				} else {
					$return['status'] = false;
                    $return['message'] = "Nama sudah terdaftar";
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
            $query = "SELECT ID FROM MS_BRANCH WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                $query = "DELETE FROM MS_BRANCH WHERE ID=?";
                DB::delete($query, [$request->_i]);
                $query = "DELETE FROM MS_BRANCH_ADMIN WHERE BranchID=?";
                DB::delete($query, [$request->_i]);
                $return['message'] = "Data berhasil dihapus!";
                $return['callback'] = "doReloadTable()";
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

}