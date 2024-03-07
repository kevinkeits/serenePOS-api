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

        $query = "SELECT SUM(PaymentAmount) AS todayIncome
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
    
        $query = "SELECT SUM(PaymentAmount) AS monthlyIncome
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
                        MsProduct.ImgUrl AS imgUrl
                    FROM 
                        TrTransactionProduct
                    JOIN 
                        MsProduct ON MsProduct.ID = TrTransactionProduct.ProductID
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
        
        $query = "SELECT SUM(t.PaymentAmount) AS paymentAmount, DAYNAME(t.TransactionDate) AS transactionDay
                    FROM TrTransaction t
                    WHERE t.TransactionDate >= DATE_ADD(CURDATE(), INTERVAL -6 DAY)
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
    // END GET DASHBOARD
}