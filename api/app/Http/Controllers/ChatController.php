<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ChatController extends Controller
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

	public function getListing(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            /*$mainQuery = "	select		distinct
										c.ID as CustomerID,
										c.UserID,
										c.Name,
										c.Phone,
										c.Email,
										cm.BranchID,
										cm.IsReadByBranch,
										cm.Status,
										(select Message from TR_CHAT_MESSAGE where BranchID = cm.BranchID and CustomerID = c.ID order by CreatedDate desc limit 1) as Message,
										(select CreatedDate from TR_CHAT_MESSAGE where BranchID = cm.BranchID and CustomerID = c.ID order by CreatedDate desc limit 1) as CreatedDate
							from		MS_CUSTOMER c
							left join	TR_CHAT_MESSAGE cm on cm.CustomerID = c.ID
							where		{definedFilter}
							order by	createdDate desc";*/
							
			$mainQuery = "SELECT *
							FROM (
								SELECT c.ID CustomerID,
										c.UserID,
										c.Name,
										c.Phone,
										c.Email,
										cm.BranchID,
										b.Name Branch,
										IFNULL((SELECT IsReadByBranch FROM TR_CHAT_MESSAGE WHERE CustomerID=c.ID AND BranchID=cm.BranchID Order BY CreatedDate DESC LIMIT 0,1),0) IsReadByBranch,
										IFNULL((SELECT SUBSTR(Message,1,100) FROM TR_CHAT_MESSAGE WHERE CustomerID=c.ID AND BranchID=cm.BranchID Order BY CreatedDate DESC LIMIT 0,1),'') Message,
										(SELECT CreatedDate FROM TR_CHAT_MESSAGE WHERE CustomerID=c.ID AND BranchID=cm.BranchID Order BY CreatedDate DESC LIMIT 0,1) CreatedDate
								FROM 	MS_CUSTOMER c
									JOIN (SELECT DISTINCT CustomerID, BranchID FROM TR_CHAT_MESSAGE) cm ON cm.CustomerID=c.ID
									JOIN MS_BRANCH b ON b.ID=cm.BranchID
								WHERE	{definedFilter}
							) TEMP
							ORDER BY TEMP.CreatedDate DESC";
            $definedFilter = "1=1";
			if ($getAuth['BranchAuth'] != "") $definedFilter = "cm.BranchID IN (".$getAuth['BranchAuth'].")";
			$query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
			$data = DB::select($query);
			if ($data) {
				$return['data'] = $data;
				$return['callback'] = "onCompleteFetch(e.data)";
			}
           
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

	public function getMessageDetail(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
			
			$query = "select IsReadByBranch from TR_CHAT_MESSAGE where CustomerID=? and BranchID=? and status = 1 order by CreatedDate desc";
			$checkRead = DB::select($query,[$request->CustomerID,$request->BranchID]);
			if ($checkRead[0] !== 1){
				$query = "update TR_CHAT_MESSAGE set IsReadByBranch = 1 where CustomerID=? and BranchID=? and status = 1";
				DB::update($query,[$request->CustomerID,$request->BranchID]);
			}

            $mainQuery = "	select		cm.ID,
										cm.BranchID,
										cm.CustomerID,
										c.UserID,
										c.Name as CustomerName,
										u.Field3 as CustomerPhoto,
										c.Phone as CustomerPhone,
										c.Email as CustomerEmail,
										adm.ID as AdminID,
										adm.FullName as AdminName,
										cm.Message,
										cm.Status,
										cm.CreatedBy,
										cm.CreatedDate,
										cm.IsReadByBranch,
										'".$getAuth['UserID']."' as LoginUserID
							from		TR_CHAT_MESSAGE cm
							left join	MS_CUSTOMER c on c.ID = cm.CustomerID
							left join	MS_USER u on u.ID = c.UserID
							left join	MS_USER adm on adm.ID = cm.CreatedBy and adm.ID != u.ID
							where		{definedFilter}
							order by	createdDate asc";
			$definedFilter = "cm.CustomerID=? and cm.BranchID=?";
			$query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
			$data = DB::select($query,[$request->CustomerID,$request->BranchID]);
			if ($data) {
				$return['data'] = $data;
				$return['callback'] = "onCompleteFetch(e.data)";
			}
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

	public function doPost(Request $request)
   {
	   $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
	   $getAuth = $this->validateAuth($request->_s);
	   if ($getAuth['status']) {
			$query = "INSERT INTO TR_CHAT_MESSAGE
							(ID, CustomerID, BranchID, Message, IsReadByBranch, IsReadByCustomer, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
						VALUES(UUID(), ?, ?, ?, 1, 0, 1, NOW(), ?, NULL, NULL)";
			DB::insert($query, [
				$request->CustomerID,
				$request->BranchID,
				str_replace("\n","<br />",$request->txtChat),
				$getAuth['UserID']
			]);
			$return['callback'] = "showDetailForm('".$request->CustomerID."','".$request->BranchID."')";
	   } else $return = array('status'=>false,'message'=>"Not Authorized");
	   return response()->json($return, 200);
   }

}