<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
    
   // GET DASHBOARD
   public function getTodayIncome(Request $request)
   {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);

        $transactionDate = date("Y-m-d");

        $query = "SELECT SUM(TotalPayment) AS todayIncome
                    FROM TrTransaction
                    WHERE IsDeleted=0 AND DATE_FORMAT(TransactionDate, '%Y-%m-%d') = '$transactionDate'";
        $result = DB::select($query);

        if ($result) {
            $todayIncome = $result[0]->todayIncome;
            
            $return['data'] = $todayIncome != null ? $todayIncome : 0;
        } else {
            $return['data'] = 0;
        }

        return response()->json($return, 200);
   }

   public function getTotalIncomeForMonth(Request $request)
    {
        $return = array('status' => true, 'message' => '', 'data' => array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
    
        $transactionDate = date('Y-m');
    
        $query = "SELECT SUM(TotalPayment) AS monthlyIncome
                  FROM TrTransaction
                  WHERE TransactionDate AND DATE_FORMAT(TransactionDate, '%Y-%m') = '$transactionDate'" ;
    
        $result = DB::select($query);
    
        if ($result) {
            $monthlyIncome = $result[0]->monthlyIncome;
            $return['data'] = $monthlyIncome != null ? $monthlyIncome : 0;
        } else {
            $return['data'] = 0;
        }
        return response()->json($return, 200);
    }

    public function getTopSellings(Request $request)
    {
        $return = array('status' => true, 'message' => '', 'data' => array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        
        $query = "SELECT 
                        SUM(TrTransactionProduct.Qty) AS totalQty, TrTransactionProduct.ProductID AS productID,
                        MsProduct.Name AS productName, 
                        CASE MsProduct.ImgUrl WHEN '' THEN '' ELSE (SELECT CONCAT('https://serenepos.temandigital.id/api/uploaded/product/', MsProduct.ImgUrl)) END imgUrl
                    FROM 
                        TrTransactionProduct
                    JOIN 
                        MsProduct ON MsProduct.ID = TrTransactionProduct.ProductID
                    JOIN
                        TrTransaction ON TrTransaction.ID = TrTransactionProduct.TransactionID
                    WHERE TrTransactionProduct.IsDeleted = 0 AND TrTransaction.Status = 1
                    GROUP BY TrTransactionProduct.ProductID, MsProduct.Name,  MsProduct.ImgUrl
                ORDER BY totalQty DESC
                LIMIT 5";
        $result = DB::select($query);
        
        if ($result) {
            $return['data'] = $result;
        } else {
            $return['data'] = null;
        }
        
        return response()->json($return, 200);
    }

    public function getSalesWeekly(Request $request)
    {
        $return = array('status' => true, 'message' => '', 'data' => array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        $query = "SELECT SUM(t.TotalPayment) AS totalPayment, DAYNAME(t.TransactionDate) AS transactionDay
                    FROM TrTransaction t
                    WHERE t.IsDeleted = 0 AND t.TransactionDate >= DATE_ADD(CURDATE(), INTERVAL -6 DAY) AND t.Status = 1
                    GROUP BY DAYNAME(t.TransactionDate)
                            LIMIT 5";
        $result = DB::select($query);
        
        if ($result) {
            $return['data'] = $result;
        } else {
            $return['data'] = null;
        }
        
        return response()->json($return, 200);
    }

    public function getProfitAmount(Request $request)
    {
        $return = array('status' => true, 'message' => '', 'data' => array());
        $header = $request->header('Authorization');
        $getAuth = $this->validateAuth($header);
        
        // Get the start and end dates for this week
        $thisWeekStartDate = date('Y-m-d', strtotime('this week Monday'));
        $thisWeekEndDate = date('Y-m-d', strtotime('this week Sunday'));
         
        // Get the start and end dates for last week
      
        $lastWeekStartDate = date('Y-m-d', strtotime('last week Monday'));
        $lastWeekEndDate = date('Y-m-d', strtotime('last week Sunday'));
        
        $query = "SELECT 
                        (SELECT SUM(TotalPayment) FROM TrTransaction WHERE IsDeleted = 0 AND TransactionDate BETWEEN '$thisWeekStartDate' AND '$thisWeekEndDate') AS thisWeekAmount,
                        (SELECT SUM(TotalPayment) FROM TrTransaction WHERE IsDeleted = 0 AND TransactionDate BETWEEN '$lastWeekStartDate' AND '$lastWeekEndDate') AS lastWeekAmount
                LIMIT 1";
        $result = DB::select($query);
        
        if ($result) {
            // Calculate profit percentage
            $thisWeekAmount = $result[0]->thisWeekAmount ?? 0;
            $lastWeekAmount = $result[0]->lastWeekAmount ?? 0;
            
            $profitPercentage = 0;
            if ($lastWeekAmount != 0) {
                $profitPercentage = ((($thisWeekAmount / $lastWeekAmount) * 100) - 100);
            }
            
            // Add profit percentage to the data
            $result[0]->profitPercentage = $profitPercentage;
            
            $return['data'] = $result[0];
        } else {
            $return['data'] = null;
        }
        
        return response()->json($return, 200);
    }
    // END GET DASHBOARD
}