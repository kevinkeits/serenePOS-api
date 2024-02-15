<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

	private function numberFormat($dec, $comma='.', $thousand=',') {
		return number_format($dec,0,$comma,$thousand);
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

    public function getTest()
    {
        
        $query = "SELECT NOW() DATE";
        $data = DB::select($query);
        
        echo $data[0]->DATE;
        echo '<br />';
        echo date("Y-m-d H:i:s");
        echo '<br />';
        echo date_default_timezone_get();
    }
	
    public function getMenu(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT m.ID,m.Name,m.ParentID
                        FROM MS_MENU m
                        LEFT JOIN MS_MENU mp ON mp.ID=m.ParentID
                        WHERE m.Status=1 
                        ORDER BY m.Sequence ASC, mp.Sequence ASC";
            $return['data'] = DB::select($query);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        $return['data'] = DB::select($query);
        return response()->json($return, 200);
    }

    public function getRole(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID,Name 
                        FROM MS_ROLE 
                        WHERE Status=1 
                            AND ID!='SYSTEM' 
                        ORDER BY Name ASC";
            $return['data'] = DB::select($query);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getMaster(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID,CONCAT('[',Field1,'] ',Field2) Name 
                        FROM MS_REFERENCES 
                        WHERE Status=1 
                            AND Type=? 
                        ORDER BY Field1 ASC";
            $return['data'] = DB::select($query,[$request->type]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getState(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        //$getAuth = $this->validateAuth($request->_s);
        //if ($getAuth['status']) {
            $query = "SELECT ID,Field2 Name 
                        FROM MS_REFERENCES 
                        WHERE Status=1 
                                AND Type='StateID' 
                        ORDER BY Field2 ASC";
            $return['data'] = DB::select($query);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        //} else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getCity(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        //$getAuth = $this->validateAuth($request->_s);
        //if ($getAuth['status']) {
            $query = "SELECT ID,Field2 Name 
                        FROM MS_REFERENCES 
                        WHERE Status=1 
                            AND Type='CityID' 
                            AND Field3=? 
                        ORDER BY Field2 ASC";
            $return['data'] = DB::select($query,[$request->stateID]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        //} else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getDistrict(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        //$getAuth = $this->validateAuth($request->_s);
        //if ($getAuth['status']) {
            $query = "SELECT ID,Field2 Name 
                        FROM MS_REFERENCES 
                        WHERE Status=1 
                                AND Type='DistrictID' 
                                AND Field3=? 
                        ORDER BY Field2 ASC";
            $return['data'] = DB::select($query,[$request->cityID]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        //} else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getAllUser(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID, FullName Name
                        FROM MS_USER
                        WHERE ID!='SYSTEM'
                            AND RoleID IS NOT NULL
                        ORDER BY FullName ASC";
            $return['data'] = DB::select($query);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getAllBranch(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT ID,Name
                        FROM MS_BRANCH
                        WHERE Status = 1
                            AND {definedFilter}
                        ORDER BY Name ASC";
            $definedFilter = "1=1";
            if ($getAuth['BranchAuth'] != "") $definedFilter = "ID IN (".$getAuth['BranchAuth'].")";
            $query = str_replace("{definedFilter}",$definedFilter,$query);
            $return['data'] = DB::select($query);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getCategory(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT    ID,
                                Name
                        FROM      MS_CATEGORY
                        ORDER BY  Name ASC";
            $return['data'] = DB::select($query,[$request->_p]);
            if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }
}