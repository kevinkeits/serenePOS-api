<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class StockApprovalController extends Controller
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
            $mainQuery = "  SELECT  r.ID,
                                    r.BranchID,
                                    b.Name Branch,
                                    r.CreatedDate,
                                    ua.FullName ApprovedBy,
                                    ur.FullName ReceivedBy,
                                    uc.FullName CreatedBy,
                                    r.RequestStatus
                            FROM    TR_STOCKREQUEST r
                                    JOIN MS_BRANCH b ON b.ID = r.BranchID
                                    LEFT JOIN MS_USER ua ON ua.ID = r.ApprovedBy
                                    LEFT JOIN MS_USER ur ON ur.ID = r.ReceivedBy
                                    LEFT JOIN MS_USER uc ON uc.ID = r.CreatedBy
                            WHERE   r.RequestStatus != '1'
                                    {definedFilter}
                            ORDER BY r.CreatedDate DESC";
            $definedFilter = " AND 1=1";
            if ($request->_i) {
                $definedFilter = " AND r.ID='".$request->_i."'";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$getAuth['UserID']]);
                if ($data) {
                    $query = "SELECT p.ID ProductID, p.Name Product, op.Qty
                                FROM TR_STOCKREQUEST_PRODUCT op
                                    JOIN MS_PRODUCT p ON p.ID=op.ProductID
                                WHERE op.StockRequestID=?
                                ORDER BY p.Name ASC";
                    $subData = DB::select($query,[$request->_i]);
                    $return['data']['orderData'] = $data[0];
                    $return['data']['orderItem'] = $subData;
                    $return['callback'] = "onCompleteFetch(e.data)";
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$getAuth['UserID']]);
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
            if ($request->hdnFrmAction=="edit") {
                $query = "SELECT RequestStatus,ID FROM TR_STOCKREQUEST WHERE ID=? AND RequestStatus != 1";
                $isExist = DB::select($query, [$request->hdnFrmID]);
                if ($isExist) {
                    $query = "UPDATE TR_STOCKREQUEST
                                SET RequestStatus=?,
                                    ModifiedDate=NOW(), 
                                    ModifiedBy=?
                                WHERE ID=?";
                    DB::update($query, [
                        $request->hdnIsDraft=="F" ? 3 : 2,
                        $getAuth['UserID'],
                        $request->hdnFrmID
                    ]);
                    
                    if ($request->hdnIsDraft == "F") {
                        $query = "UPDATE TR_STOCKREQUEST
                                    SET ApprovedDate=NOW(),
                                        ApprovedBy=?
                                    WHERE ID=?";
                        DB::update($query, [$getAuth['UserID'],$request->hdnFrmID]);
                    }
    
                    $query = "DELETE FROM TR_STOCKREQUEST_PRODUCT WHERE StockRequestID=?";
                    DB::delete($query, [$request->hdnFrmID]);

                    $i=0;
                    foreach ($request->hdnproduct as $key => $value) {
                        $query = "INSERT INTO TR_STOCKREQUEST_PRODUCT
                                        (ID, StockRequestID, ProductID, Qty)
                                    VALUES
                                        (UUID(), ?, ?, ?)";
                        DB::insert($query, [
                            $request->hdnFrmID,
                            $request->hdnproduct[$i],
                            $request->qty[$i]
                        ]);
                        $i++;
                    }
                    $return['message'] = $request->hdnIsDraft == "T" ? "Data berhasil tersimpan!" : "Permintaan berhasil disetujui";
                    $return['callback'] = "doReloadTable()";
                } else {
                    $return['status'] = false;
                    $return['message'] = "Not Authorized";
                }
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

}