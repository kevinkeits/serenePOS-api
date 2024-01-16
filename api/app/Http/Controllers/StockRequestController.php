<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class StockRequestController extends Controller
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

    public function getProduct(Request $request)
	{
		$return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
		$getAuth = $this->validateAuth($request->_s);
		if ($getAuth['status']) {
            $query = "SELECT	ID,
                                Code,
                                Name
                    FROM	  	MS_PRODUCT
                    WHERE       BranchID=?
                                AND Status=1
                    ORDER BY    Name ASC";
            $return['data'] = DB::select($query,[$request->retailID]);
			if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."','".$request->_i."')";
		} else $return = array('status'=>false,'message'=>"Not Authorized");
		return response()->json($return, 200);
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
            if ($request->hdnFrmAction=="add") {
                $RequestID = 'REQ-'.date("Ymd").'-'.$this->randomString(10);
                $query = "INSERT INTO TR_STOCKREQUEST
                                (ID, BranchID, RequestStatus, CreatedDate, CreatedBy)
                            VALUES(?, ?, ?, NOW(), ?)";
                DB::insert($query, [
                    $RequestID,
                    $request->selFrmBranch,
                    $request->hdnIsDraft=="T" ? 1 : 2,
                    $getAuth['UserID']
                ]);

                if ($request->hdnIsDraft == "F") {
                    $query = "UPDATE TR_STOCKREQUEST
                                SET SentOn=NOW()
                                WHERE ID=?";
                    DB::update($query, [$RequestID]);
                }

                $i=0;
                foreach ($request->product as $key => $value) {
                    $query = "INSERT INTO TR_STOCKREQUEST_PRODUCT
                                    (ID, StockRequestID, ProductID, Qty)
                                VALUES
                                    (UUID(), ?, ?, ?)";
                    DB::insert($query, [
                        $RequestID,
                        $request->product[$i],
                        $request->qty[$i]
                    ]);
                    $i++;
                }
                $return['message'] = $request->hdnIsDraft == "T" ? "Data berhasil tersimpan!" : "Permintaan berhasil dikirim";
                $return['callback'] = "doReloadTable()";
            }
            if ($request->hdnFrmAction=="edit") {
                $query = "SELECT RequestStatus,ID FROM TR_STOCKREQUEST WHERE ID=?";
                $isExist = DB::select($query, [$request->hdnFrmID]);
                if ($isExist) {
                    $query = "UPDATE TR_STOCKREQUEST
                                SET BranchID=?,
                                    RequestStatus=?,
                                    ModifiedDate=NOW(), 
                                    ModifiedBy=?
                                WHERE ID=?";
                    DB::update($query, [
                        $request->selFrmBranch,
                        $request->hdnIsDraft=="F" ? 2 : 1,
                        $getAuth['UserID'],
                        $request->hdnFrmID
                    ]);
                    
                    if ($request->hdnIsDraft == "F") {
                        $query = "UPDATE TR_STOCKREQUEST
                                    SET SentOn=NOW()
                                    WHERE ID=?";
                        DB::update($query, [$request->hdnFrmID]);
                    }
    
                    $query = "DELETE FROM TR_STOCKREQUEST_PRODUCT WHERE StockRequestID=?";
                    DB::delete($query, [$request->hdnFrmID]);

                    $i=0;
                    foreach ($request->product as $key => $value) {
                        $query = "INSERT INTO TR_STOCKREQUEST_PRODUCT
                                        (ID, StockRequestID, ProductID, Qty)
                                    VALUES
                                        (UUID(), ?, ?, ?)";
                        DB::insert($query, [
                            $request->hdnFrmID,
                            $request->product[$i],
                            $request->qty[$i]
                        ]);
                        $i++;
                    }
                    $return['message'] = $request->hdnIsDraft == "T" ? "Data berhasil tersimpan!" : "Sukses terkirim ke Approver";
                    $return['callback'] = "doReloadTable()";
                } else {
                    $return['status'] = false;
                    $return['message'] = "Not Authorized";
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
            $query = "SELECT RequestStatus,ID FROM TR_STOCKREQUEST WHERE ID=?";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {
                if ($isExist[0]->RequestStatus == "1") {
                    $query = "DELETE FROM TR_STOCKREQUEST WHERE ID=?";
                    DB::delete($query, [$isExist[0]->ID]);
                    $query = "DELETE FROM TR_STOCKREQUEST_PRODUCT WHERE StockRequestID=?";
                    DB::delete($query, [$isExist[0]->ID]);
                    
                    $return['message'] = "Data berhasil dihapus!";
                    $return['callback'] = "doReloadTable()";
                }
            } else $return = array('status'=>false,'message'=>"Not Authorized");
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function printOrder(Request $request)
    {
        $result = '
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link href="https://ellafroze.com/assets/favicon.ico" rel="shortcut icon" />
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
                <title>Ella Froze - Cetak Pesanan</title>
            </head>
            <body style="font-size:10pt">
                <div class="col-lg-8 mx-auto p-3 py-md-5">
                    <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
                        <div class="col-xs-6">
                            <img src="https://ellafroze.com/assets/img/logo.png" height="100px"/>
                        </div>
                        <div class="col-xs-6" style="padding-left: 20px;">{branchObject}</div>
                    </header>
                    <main>{dataObject}</main>
                    <footer class="pt-5 my-5 text-muted">
                        Dicetak pada: '.date("Y-m-d").'
                    </footer>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
            </body>
        </html>';
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->_i) {
                $query = "SELECT  r.ID,
                                    r.BranchID,
                                    b.Name Branch,
                                    st.Field2 BranchStateName,
                                    ct.Field2 BranchCityName,
                                    dt.Field2 BranchDistrictName,
                                    b.Address BranchAddress,
                                    
                                    r.CreatedDate,
                                    ua.FullName ApprovedBy,
                                    ur.FullName ReceivedBy,
                                    uc.FullName CreatedBy,
                                    r.RequestStatus
                            FROM    TR_STOCKREQUEST r
                                    JOIN MS_BRANCH b ON b.ID = r.BranchID
                                    JOIN MS_REFERENCES st ON st.ID = b.StateID
                                    JOIN MS_REFERENCES ct ON ct.ID = b.CityID
                                    JOIN MS_REFERENCES dt ON dt.ID = b.DistrictID
                                    
                                    LEFT JOIN MS_USER ua ON ua.ID = r.ApprovedBy
                                    LEFT JOIN MS_USER ur ON ur.ID = r.ReceivedBy
                                    LEFT JOIN MS_USER uc ON uc.ID = r.CreatedBy
                            WHERE   r.ID = ? ";
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $data = $data[0];
                    $query = "SELECT p.ID ProductID, p.Name Product, op.Qty
                                FROM TR_STOCKREQUEST_PRODUCT op
                                    JOIN MS_PRODUCT p ON p.ID=op.ProductID
                                WHERE op.StockRequestID=?
                                ORDER BY p.Name ASC";
                    $subData = DB::select($query,[$request->_i]);
                    $branchData = $data->Branch.'<br />'.$data->BranchStateName.', '.$data->BranchCityName.', '.$data->BranchDistrictName.'<br />'.$data->BranchAddress;
                    $arrData = '
                    <table>
                    <tbody>
                        <tr>
                            <td width="150px">No. Permintaan</td>
                            <td>: '.$data->ID.'</td>
                        </tr>
                        <tr>
                            <td>Tgl. Permintaan</td>
                            <td>: '.$data->CreatedDate.'</td>
                        </tr>
                        <tr>
                            <td>Pembuat</td>
                            <td>: '.$data->CreatedBy.'</td>
                        </tr>
                        <tr>
                            <td>Disetujui Oleh:</td>
                            <td>: '.$data->ApprovedBy.'</td>
                        </tr>
                    </tbody>
                    </table>
                    <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($subData as $item) {
                        $arrData .= '<tr>
                                        <td>'.$item->Product.'</td>
                                        <td>'.number_format($item->Qty).'</td>
                                    </tr>';
                    }
                    $arrData .= '   
                    </tbody>
                    </table>
                    
                    <p>*) Pastikan jumlah stock yang diterima sesuai dengan permintaan penambahan stock.</p>';
                    
                    $result = str_replace("{branchObject}",$branchData,$result);
                    $result = str_replace("{dataObject}",$arrData,$result);
                } else {
                    //$result = "Not Authorized";
                }
            } else {
                //$result = "Not Authorized";
            }
        } else {
            //$result = "Not Authorized";
        }
        return response($result);
    }

}