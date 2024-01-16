<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Exports\GeneralExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    private function randomString($length) {
		$characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
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

    public function GetTransaction(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT DATE_FORMAT(o.CreatedDate, '%Y-%m-%d') TransDate, o.BranchID, b.Name Branch, SUM(Total) TotalSales
                        FROM TR_ORDER o
                            JOIN MS_BRANCH b ON b.ID = o.BranchID
                        WHERE Month(o.CreatedDate) = MONTH(NOW())
                            AND YEAR(o.CreatedDate) = YEAR(NOW())
                            AND {definedFilter}
                        GROUP BY o.CreatedDate, o.BranchID, b.Name
                        ORDER BY o.CreatedDate ASC";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "o.BranchID IN (".$getAuth['BranchAuth'].")";
            $query = str_replace("{definedFilter}",$definedFilter,$query);
            $transData = DB::select($query);

            $branchList = array();
            $dataList = array();
            foreach ($transData as $key => $value) {
                if (!in_array($value->Branch, $branchList)) {
                    array_push($branchList, $value->Branch);
                    array_push($dataList, array('name' => $value->Branch, 'value' => ""));
                }
            }

            $dateList = array();
            $totalDays = cal_days_in_month(CAL_GREGORIAN, date('m'),date('Y'));
            foreach ($dataList as $key => $value) {
                for ($i=1; $i<=$totalDays; $i++) { 
                    $refDate = date('Y')."-".date('m')."-".(strlen($i)==1 ? "0".$i : $i);
                    array_push($dateList, $refDate);
                    $isFound = false;
                    foreach ($transData as $keys => $values) {
                        if ($value['name'] == $values->Branch && $values->TransDate == $refDate) {
                            $dataList[$key]['value'] .= ($dataList[$key]['value'] == "" ? "" : ",") . str_replace(".00","",$values->TotalSales);
                            $isFound = true;
                        } 
                    }
                    if (!$isFound) $dataList[$key]['value'] .= ($dataList[$key]['value'] == "" ? "" : ",") . "0";
                }
            }

            $return['data'] = array(
                'dateList' => $dateList,
                'dataList' => $dataList
            );

            $return['callback'] ="onCompleteFetchTransaction(e.data)";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function GetProductSales(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT o.BranchID, b.Name Branch, p.Name Product, SUM(op.Qty) TotalSales
                        FROM TR_ORDER_PRODUCT op
                            JOIN MS_PRODUCT p ON p.ID = op.ProductID
                            JOIN TR_ORDER o ON o.ID = op.OrderID
                            JOIN MS_BRANCH b ON b.ID = o.BranchID
                        WHERE Month(o.CreatedDate) = MONTH(NOW())
                            AND YEAR(o.CreatedDate) = YEAR(NOW())
                            AND {definedFilter}
                        GROUP BY o.BranchID, b.Name, p.Name
                        ORDER BY TotalSales DESC, p.Name ASC
                        LIMIT 0,10";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "o.BranchID IN (".$getAuth['BranchAuth'].")";
            $query = str_replace("{definedFilter}",$definedFilter,$query);
            $return['data'] = DB::select($query);
            $return['callback'] ="onCompleteFetchProduct(e.data)";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function GetNotification(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT COUNT(ID) Total FROM TR_STOCKREQUEST WHERE RequestStatus = '2'";
            $stockApproval = DB::select($query)[0]->Total;

            $query = "SELECT COUNT(ID) Total
                        FROM TR_ORDER 
                        WHERE Status = '2'
                            AND NOW() > DATE_ADD(ConfirmedDate, INTERVAL 3 DAY)
                            AND {definedFilter}";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "BranchID IN (".$getAuth['BranchAuth'].")";
            $query = str_replace("{definedFilter}",$definedFilter,$query);
            $confirmedTransaction = DB::select($query)[0]->Total;

            $query = "SELECT COUNT(ID) Total
                        FROM TR_ORDER
                        WHERE Status = '1'
                            AND NOW() > DATE_ADD(CreatedDate, INTERVAL 30 DAY)
                            AND IsB2B = '1'
                            AND {definedFilter}";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "BranchID IN (".$getAuth['BranchAuth'].")";
            $query = str_replace("{definedFilter}",$definedFilter,$query);
            $waitingPayment = DB::select($query)[0]->Total;

            $query = "SELECT COUNT(DISTINCT CustomerID) Total
                        FROM TR_CHAT_MESSAGE
                        WHERE IsReadByBranch = '0'
                            AND {definedFilter}";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "BranchID IN (".$getAuth['BranchAuth'].")";
            $query = str_replace("{definedFilter}",$definedFilter,$query);
            $unreadMessage = DB::select($query)[0]->Total;

            $return['callback'] ="onCompleteFetchNotification(e.data)";
            $return['data'] = array('stockApproval' => intval($stockApproval), 'confirmedTransaction' => intval($confirmedTransaction), 'waitingPayment' => intval($waitingPayment), 'unreadMessage' => intval($unreadMessage));
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }
}