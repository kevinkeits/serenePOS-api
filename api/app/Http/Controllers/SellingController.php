<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class SellingController extends Controller
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
            $mainQuery = "SELECT o.ID, o.OrderNumber, o.PaymentID InvoiceID, o.CreatedDate TransactionDate, c.Name Customer, b.Name Branch, o.Total, o.DeliveryFee, o.Status,
                                oa.Phone, oa.StateName, oa.CityName, oa.DistrictName, oa.PostalCode, oa.Address,
                                o.ShippingMethod, o.TrackingNumber, o.CancelledReason, op.PaymentMethod
                            FROM TR_ORDER o
                                JOIN MS_CUSTOMER c ON c.ID=o.CustomerID
                                JOIN MS_BRANCH b ON b.ID=o.BranchID
                                JOIN TR_ORDER_ADDRESS oa ON oa.PaymentID=o.PaymentID
                                LEFT JOIN TR_ORDER_PAYMENT op ON op.ID=o.PaymentID
                            WHERE {definedFilter}
                                    AND o.IsB2B = 0
                            ORDER BY o.CreatedDate DESC";
            $definedFilter = "1=1";
            if ($request->startDate) $definedFilter.= " AND o.CreatedDate > '".$request->startDate."'";
            if ($request->endDate) $definedFilter.= " AND DATE_ADD(o.CreatedDate, INTERVAL -1 DAY) <= '".$request->endDate."'";
            if ($getAuth['BranchAuth'] != "") $definedFilter .= " AND o.BranchID IN (".$getAuth['BranchAuth'].")";
            if ($request->_i) {
                $definedFilter = "o.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT p.Name Product, op.Qty, op.Notes
                                FROM TR_ORDER_PRODUCT op
                                    JOIN MS_PRODUCT p ON p.ID=op.ProductID
                                WHERE op.OrderID=?
                                ORDER BY p.Name ASC";
                    $subData = DB::select($query,[$request->_i]);
                    $return['data']['orderData'] = $data[0];
                    $return['data']['orderItem'] = $subData;
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
            $query = "SELECT ID, Status, TrackingNumber, ShippingMethod, ShippingDate, ConfirmedDate, CompletedDate, CancelledDate, CancelledReason FROM TR_ORDER WHERE ID=?";
            $data = DB::select($query, [$request->hdnFrmID]);
            if ($data) {
                $data = $data[0];
                $query = "UPDATE TR_ORDER
                            SET TrackingNumber=?, 
                                ShippingMethod=?,
                                ShippingDate=?, 
                                ConfirmedDate=?, 
                                CompletedDate=?, 
                                CancelledDate=?,
                                CancelledReason=?,
                                Status=?,
                                ModifiedDate=NOW(), 
                                ModifiedBy=?
                            WHERE ID=?";
                DB::update($query, [
                    ($request->hdnFrmStatus == 3 ? $request->txtFrmTrackingNumber : $data->TrackingNumber),
                    ($request->hdnFrmStatus == 3 ? $request->txtFrmCourier : $data->ShippingMethod),
                    ($request->hdnFrmStatus == 3 ? ($data->ShippingDate == NULL ? date("Y-m-d H:i:s") : $data->ShippingDate) : $data->ShippingDate),
                    ($request->hdnFrmStatus == 2 ? ($data->ConfirmedDate == NULL ? date("Y-m-d H:i:s") : $data->ConfirmedDate) : $data->ConfirmedDate),
                    ($request->hdnFrmStatus == 4 ? ($data->CompletedDate == NULL ? date("Y-m-d H:i:s") : $data->CompletedDate) : $data->CompletedDate),
                    ($request->hdnFrmStatus == 5 ? ($data->CancelledDate == NULL ? date("Y-m-d H:i:s") : $data->CancelledDate) : $data->CancelledDate),
                    ($request->hdnFrmStatus == 5 ? $request->txtFrmCancelledReason : (intval($request->hdnFrmStatus) == 0 ? $request->txtFrmCancelledReason : $data->CancelledReason)),
                    intval($request->hdnFrmStatus) == 0 ? 5 : $request->hdnFrmStatus,
                    $getAuth['UserID'],
                    $request->hdnFrmID
                ]);

                if ($request->hdnFrmStatus == 5 && $data->Status != 5) {
                    $query = "SELECT ProductID, Qty FROM TR_ORDER_PRODUCT WHERE OrderID=?";
                    $product = DB::select($query, [$data->ID]);
                    foreach ($product as $key => $value) {
                        $query = "UPDATE MS_PRODUCT
                                    SET Stock=(Stock+".$value->Qty.")
                                    WHERE ID=?";
                        DB::update($query, [
                            $value->ProductID
                        ]);
                    } 
                }
                $return['message'] = "Data berhasil disimpan!";
                $return['callback'] = "doReloadTable()";
            }
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
                    <header class="d-flex align-items-center mb-1 border-bottom border-dark">
                        <!--<div class="col-xs-6">
                            <img src="https://ellafroze.com/assets/img/logo.png" height="60px" />
                        </div>-->
                        <div class="col-xs-6">{branchObject}</div>
                    </header>
                    <main>{dataObject}</main>
                    <!--<footer class="pt-5 my-5 text-muted text-center">
                        Terima kasih telah berbelanja di Ella Froze<br />** Seafood Praktis, Higienis &amp; Bergizi **
                    </footer>-->
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
            </body>
        </html>';
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->_i) {
                $query = "SELECT o.ID, o.PaymentID InvoiceID, o.CreatedDate TransactionDate, c.Name Customer, b.Name Branch, o.Total, o.DeliveryFee, o.Status,
                                oa.Phone, oa.StateName, oa.CityName, oa.DistrictName, oa.PostalCode, oa.Address,
                                o.ShippingMethod, o.TrackingNumber, o.CancelledReason, o.OrderNumber,
                                
                                st.Field2 BranchStateName,
                                ct.Field2 BranchCityName,
                                dt.Field2 BranchDistrictName,
                                b.Address BranchAddress
                            FROM TR_ORDER o
                                JOIN MS_CUSTOMER c ON c.ID=o.CustomerID
                                JOIN MS_BRANCH b ON b.ID=o.BranchID
                                JOIN TR_ORDER_ADDRESS oa ON oa.PaymentID=o.PaymentID
                                JOIN MS_REFERENCES st ON st.ID = b.StateID
                                JOIN MS_REFERENCES ct ON ct.ID = b.CityID
                                JOIN MS_REFERENCES dt ON dt.ID = b.DistrictID
                            WHERE o.ID = ?";
                if ($getAuth['BranchAuth'] != "") $query .= " AND o.BranchID IN (".$getAuth['BranchAuth'].")";
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $data = $data[0];
                    $query = "SELECT p.Name Product, op.Qty, op.Notes
                                FROM TR_ORDER_PRODUCT op
                                    JOIN MS_PRODUCT p ON p.ID=op.ProductID
                                WHERE op.OrderID=?
                                ORDER BY p.Name ASC";
                    $subData = DB::select($query,[$request->_i]);
                    //$branchData = $data->Branch.'<br />'.$data->BranchStateName.', '.$data->BranchCityName.', '.$data->BranchDistrictName.'<br />'.$data->BranchAddress;
                    $branchData = "<h3>Ellafroze ".$data->Branch.'</h3>';
                    $arrData = '
                    <table class="border-bottom border-dark d-flex align-items-center" >
                    <tbody>
                        <tr>
                            <td>'.$data->OrderNumber.'</td>
                        </tr>
                        <tr>
                            <td><strong>'.$data->Customer.'</strong></td>
                        </tr>
                        <tr>
                            <td><strong>'.$data->Phone.'</strong></td>
                        </tr>
                        <tr>
                            <td>'.$data->StateName.', '.$data->CityName.', '.$data->DistrictName.'<br />'.$data->Address.'<br />'.$data->PostalCode.'</td>
                        </tr>
                    </tbody>
                    </table>
                    <table class="table mt-1">
                    <tbody>';
                    foreach($subData as $item) {
                        $arrData .= '<tr class="border-bottom border-dark">
                                        <td style="80%">'.$item->Product.'<br />Catatan:<br />'.$item->Notes.'</td>
                                        <td style="20%">x'.number_format($item->Qty).'</td>
                                    </tr>';
                    }
                    $arrData .= '   
                    </tbody>
                    </table>
                    <table class="table mt-3">
                    <tbody>
                        <tr class="border-bottom border-dark">
                            <td style="width:50%"><center>Disiapkan Oleh</center></td>
                            <td style="width:50%"><center>Dicek Oleh</center></td>
                        </tr>
                        <tr class="border-bottom border-dark">
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                    </tbody>
                    </table>';
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