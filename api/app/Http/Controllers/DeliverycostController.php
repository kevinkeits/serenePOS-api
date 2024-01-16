<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class DeliverycostController extends Controller
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
            $mainQuery = "  SELECT      dc.ID,
                                        df.Field2 DistrictFrom,
                                        dt.Field2 DistrictTo,
                                        sf.ID FromStateID,
                                        cf.ID FromCityID,
                                        df.ID FromDistrictID,
                                        st.ID ToStateID,
                                        ct.ID ToCityID,
                                        dt.ID ToDistrictID,
                                        dc.Fee,
                                        dc.Status
                            FROM        MS_DELIVERYCOST dc
                                LEFT JOIN   MS_REFERENCES sf ON sf.ID = dc.FromStateID AND sf.Type = 'StateID'
                                LEFT JOIN   MS_REFERENCES cf ON cf.ID = dc.FromCityID AND cf.Type = 'CityID'
                                LEFT JOIN   MS_REFERENCES df ON df.ID = dc.FromDistrictID AND df.Type = 'DistrictID'
                                LEFT JOIN   MS_REFERENCES st ON st.ID = dc.ToStateID AND st.Type = 'StateID'
                                LEFT JOIN   MS_REFERENCES ct ON ct.ID = dc.ToCityID AND ct.Type = 'CityID'
                                LEFT JOIN   MS_REFERENCES dt ON dt.ID = dc.ToDistrictID AND dt.Type = 'DistrictID'
                            WHERE {definedFilter}
                            ORDER BY df.Field2, dt.Field2 ASC";
            $definedFilter = "1=1";
            if ($request->_i) {
                $definedFilter = "dc.ID=?";
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
                $query = "SELECT ID FROM MS_DELIVERYCOST WHERE FromDistrictID=? AND ToDistrictID=?";
                $isExist = DB::select($query, [$request->SelFrmDistrictFrom,$request->SelFrmDistrictTo]);
                if (!$isExist) {
                    $query = "INSERT INTO MS_DELIVERYCOST
                                    (ID, FromStateID, FromCityID, FromDistrictID, ToStateID, ToCityID, ToDistrictID, Fee, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
                                VALUES(UUID(), ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
                    DB::insert($query, [
                        $request->SelFrmStateFrom,
                        $request->SelFrmCityFrom,
                        $request->SelFrmDistrictFrom,
                        $request->SelFrmStateTo,
                        $request->SelFrmCityTo,
                        $request->SelFrmDistrictTo,
                        $request->txtFrmCost,
                        $request->radFrmStatus==1 ? 1 : 0,
                        $getAuth['UserID']
                    ]);
                    $return['message'] = "Data berhasil disimpan!";
                    $return['callback'] = "doReloadTable()";
                } else {
                    $return['status'] = false;
                    $return['message'] = "Data yang sama sudah terdaftar";
                }
            }
            if ($request->hdnFrmAction=="edit") {
                $query = "SELECT ID FROM MS_DELIVERYCOST WHERE FromDistrictID=? AND ToDistrictID=? AND ID!=?";
                $isExist = DB::select($query, [$request->SelFrmDistrictFrom, $request->SelFrmDistrictTo, $request->hdnFrmID]);
                if (!$isExist) {
                    $query = "UPDATE MS_DELIVERYCOST
                                SET FromStateID=?,
                                    FromCityID=?,
                                    FromDistrictID=?,
                                    ToStateID=?,
                                    ToCityID=?,
                                    ToDistrictID=?,
                                    Fee=?,
                                    Status=?,
                                    ModifiedDate=NOW(), 
                                    ModifiedBy=?
                                WHERE ID=?";
                    DB::update($query, [
                        $request->SelFrmStateFrom,
                        $request->SelFrmCityFrom,
                        $request->SelFrmDistrictFrom,
                        $request->SelFrmStateTo,
                        $request->SelFrmCityTo,
                        $request->SelFrmDistrictTo,
                        $request->txtFrmCost,
                        $request->radFrmStatus==1 ? 1 : 0,
                        $getAuth['UserID'],
                        $request->hdnFrmID
                    ]);
                    $return['message'] = "Data berhasil disimpan!";
                    $return['callback'] = "doReloadTable()";
                } else {
                    $return['status'] = false;
                    $return['message'] = "Data yang sama sudah terdaftar";
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
            $query = "SELECT ID FROM MS_DELIVERYCOST WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                $query = "DELETE FROM MS_DELIVERYCOST WHERE ID=?";
                DB::delete($query, [$request->_i]);
                $return['message'] = "Data berhasil dihapus";
                $return['callback'] = "doReloadTable()";
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

}