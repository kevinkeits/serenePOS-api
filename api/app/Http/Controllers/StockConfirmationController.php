<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class StockConfirmationController extends Controller
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
                            WHERE   r.CreatedBy = ?
                                    AND r.RequestStatus IN (3,4,5)
                                    {definedFilter}
                            ORDER BY r.CreatedDate ASC";
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
                $query = "SELECT RequestStatus,ID FROM TR_STOCKREQUEST WHERE ID=? AND RequestStatus IN (3,4,5)";
                $isExist = DB::select($query, [$request->hdnFrmID]);
                if ($isExist) {
                    $query = "UPDATE TR_STOCKREQUEST
                                SET RequestStatus=5,
                                    ReceivedDate=NOW(),
                                    ReceivedBy=?,
                                    ModifiedDate=NOW(), 
                                    ModifiedBy=?
                                WHERE ID=?";
                    DB::update($query, [
                        $getAuth['UserID'],
                        $getAuth['UserID'],
                        $request->hdnFrmID
                    ]);

                    $query = "SELECT p.ID ProductID, p.Name Product, op.Qty
                                FROM TR_STOCKREQUEST_PRODUCT op
                                    JOIN MS_PRODUCT p ON p.ID=op.ProductID
                                WHERE op.StockRequestID=?
                                ORDER BY p.Name ASC";
                    $subData = DB::select($query,[$request->hdnFrmID]);
                    foreach ($subData as $key => $value) {
                        $query = "UPDATE MS_PRODUCT
                                    SET Stock=(Stock+".$value->Qty.")
                                    WHERE ID=?";
                        DB::update($query, [
                            $value->ProductID
                        ]);
                    }
                    
                    $return['message'] = "Permintaan berhasil dikonfirmasi";
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