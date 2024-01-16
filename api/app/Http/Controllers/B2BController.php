<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class B2BController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    private function sanitizeString($string) {
        $string = trim($string);
        $string = str_replace("'","",$string);
        return $string;
    }

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

    public function getAll(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "SELECT o.OrderNumber, o.ID, o.PaymentID InvoiceID, o.CreatedDate TransactionDate, c.Name Customer, b.Name Branch, o.Discount, o.SubTotal, o.DeliveryFee, o.Total, o.DeliveryFee, o.Status,
                                oa.Phone, oa.StateName, oa.CityName, oa.DistrictName, oa.PostalCode, oa.Address,
                                o.ShippingMethod, o.TrackingNumber, o.CancelledReason, o.CustomerID, o.BranchID, op.IsPaid,

                                o.TrackingNumber, o.ShippingMethod, o.CancelledReason
                            FROM TR_ORDER o
                                JOIN MS_CUSTOMER c ON c.ID=o.CustomerID
                                JOIN MS_BRANCH b ON b.ID=o.BranchID
                                JOIN TR_ORDER_ADDRESS oa ON oa.PaymentID=o.PaymentID
                                JOIN TR_ORDER_PAYMENT op ON op.ID=o.PaymentID
                            WHERE {definedFilter}
                                    AND o.IsB2B = 1
                            ORDER BY o.CreatedDate DESC";
            $definedFilter = "1=1";
            if ($request->startDate) $definedFilter.= " AND o.CreatedDate > '".$request->startDate."'";
            if ($request->endDate) $definedFilter.= " AND DATE_ADD(o.CreatedDate, INTERVAL -1 DAY) <= '".$request->endDate."'";
            if ($getAuth['BranchAuth'] != "") $definedFilter.= " AND o.BranchID IN (".$getAuth['BranchAuth'].")";
            if ($request->_i) {
                $definedFilter = "o.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT p.Name Product, op.Qty, op.Notes, op.ProductID, op.SourcePrice, op.DiscountPrice, op.ItemPrice
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
            if ($request->hdnFrmAction=="add") {
                $query = "SELECT UUID() GenID";
                $ID = DB::select($query)[0]->GenID;

                $OrderID = 'ORDER-'.date("Ymd").'-'.$this->randomString(10);
                $PaymentID = "INV/".date("Ymd")."/".$this->randomString(11);

                $query = "SELECT Name, Phone, Email FROM MS_CUSTOMER WHERE ID=?";
                //$custData = DB::select($query,[$request->selFrmCustomer])[0];
                $custData = DB::select($query,[$request->txtFrmCustomerID])[0];

                $query = "SELECT a.ID, 
                                a.Name,
                                a.Phone,
                                st.Field2 StateName,
                                ct.Field2 CityName,
                                dt.Field2 DistrictName,
                                a.DistrictID,
                                a.PostalCode,
                                a.Address
                            FROM MS_CUSTOMER_ADDRESS a
                                JOIN MS_REFERENCES st ON st.ID = a.StateID
                                JOIN MS_REFERENCES ct ON ct.ID = a.CityID
                                JOIN MS_REFERENCES dt ON dt.ID = a.DistrictID
                            WHERE a.CustomerID=?
                            ORDER BY a.IsDefault DESC";
                //$addressData = DB::select($query,[$request->selFrmCustomer])[0];
                $addressData = DB::select($query,[$request->txtFrmCustomerID])[0];

                $query = "INSERT INTO TR_ORDER_PAYMENT
                                (ID, TransactionID, ExpiredDate, PaymentMethodCategory, PaymentMethod, ReferenceID, GopayDeepLink, GrossAmount, IsPaid, IsCancelled, CreatedDate)
                            VALUES
                                (?, ?, NOW(), ?, ?, ?, ?, ?, ?, 0, NOW())";
                DB::insert($query, [
                    $PaymentID,
                    '',
                    '',
                    '',
                    '',
                    '',
                    $request->txtFrmTotal,
                    intval($request->selFrmStatusPayment),
                ]);

                $query = "INSERT INTO TR_ORDER_ADDRESS
                                (ID, PaymentID, Name, Phone, StateName, CityName, DistrictName, PostalCode, Address)
                            VALUES
                                (UUID(), ?, ?, ?, ?, ?, ?, ?, ?)";
                DB::insert($query, [
                    $PaymentID,
                    $custData->Name,
                    $addressData->Phone,
                    $addressData->StateName,
                    $addressData->CityName,
                    $addressData->DistrictName,
                    $addressData->PostalCode,
                    $addressData->Address
                ]);
            
                $query = "INSERT INTO TR_ORDER
                            (ID, BranchID, CustomerID, PaymentID, IsB2B, 
                            OrderNumber, SubTotal, DeliveryFee, Discount, Total, 
                            Status, CreatedDate, CreatedBy)
                        VALUES
                            (?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, 
                            ?, NOW(), ?)";
                    DB::insert($query, [
                        $ID,
                        $request->selFrmBranch,
                        $request->txtFrmCustomerID,
                        //$request->selFrmCustomer,
                        $PaymentID,
                        1,
                        $OrderID,
                        intval($request->txtFrmSubTotal),
                        intval($request->txtFrmFee),
                        intval($request->txtFrmDiscount),
                        $request->txtFrmTotal,
                        $request->selFrmOrderStatus,
                        $getAuth['UserID']
                    ]);
                $i=0;
                foreach ($request->product as $key => $value) {
                    $query = "INSERT INTO TR_ORDER_PRODUCT
                                    (ID, OrderID, ProductID, Qty, SourcePrice, DiscountPrice, ItemPrice, Notes)
                                VALUES
                                    (UUID(), ?, ?, ?, ?, ?, ?, ?)";
                    DB::insert($query, [
                        $ID,
                        $request->product[$i],
                        $request->qty[$i],
                        $request->priceOri[$i],
                        $request->discount[$i],
                        $request->price[$i],
                        ''
                    ]);

                    $query = "UPDATE MS_PRODUCT
                                SET Stock=(Stock-".$request->qty[$i].")
                                WHERE ID=?";
                    DB::update($query, [
                        $request->product[$i]
                    ]);

                    $i++;
                }
                $return['message'] = "Data berhasil tersimpan!";
                $return['callback'] = "doReloadTable()";
            }
            if ($request->hdnFrmAction=="edit") {
                $query = "SELECT Status, ID, PaymentID, TrackingNumber, ShippingMethod, ShippingDate, ConfirmedDate, CompletedDate, CancelledDate, CancelledReason FROM TR_ORDER WHERE ID=? AND IsB2B=1";
                $isExist = DB::select($query, [$request->hdnFrmID]);
                if ($isExist) {
                    $query = "UPDATE TR_ORDER
                                SET Status=?,
                                    TrackingNumber=?, 
                                    ShippingMethod=?,
                                    ShippingDate=?, 
                                    ConfirmedDate=?, 
                                    CompletedDate=?, 
                                    CancelledDate=?,
                                    CancelledReason=?,
                                    ModifiedDate=NOW(), 
                                    ModifiedBy=?
                                WHERE ID=?";
                    DB::update($query, [
                        intval($request->selFrmOrderStatus) == 0 ? 5 : $request->selFrmOrderStatus,
                        ($request->selFrmOrderStatus == 3 ? $request->txtFrmTrackingNumber : $isExist[0]->TrackingNumber),
                        ($request->selFrmOrderStatus == 3 ? $request->txtFrmCourier : $isExist[0]->ShippingMethod),
                        ($request->selFrmOrderStatus == 3 ? ($isExist[0]->ShippingDate == NULL ? date("Y-m-d H:i:s") : $isExist[0]->ShippingDate) : $isExist[0]->ShippingDate),
                        ($request->selFrmOrderStatus == 2 ? ($isExist[0]->ConfirmedDate == NULL ? date("Y-m-d H:i:s") : $isExist[0]->ConfirmedDate) : $isExist[0]->ConfirmedDate),
                        ($request->selFrmOrderStatus == 4 ? ($isExist[0]->CompletedDate == NULL ? date("Y-m-d H:i:s") : $isExist[0]->CompletedDate) : $isExist[0]->CompletedDate),
                        ($request->selFrmOrderStatus == 5 ? ($isExist[0]->CancelledDate == NULL ? date("Y-m-d H:i:s") : $isExist[0]->CancelledDate) : $isExist[0]->CancelledDate),
                        ($request->selFrmOrderStatus == 5 ? $request->txtFrmCancelledReason : (intval($request->selFrmOrderStatus) == 0 ? $request->txtFrmCancelledReason : $isExist[0]->CancelledReason)),
                        $getAuth['UserID'],
                        $request->hdnFrmID
                    ]);
                    
                    $query = "UPDATE TR_ORDER_PAYMENT
                                SET IsPaid=?
                                WHERE ID=?";
                    DB::update($query, [
                        intval($request->selFrmStatusPayment),
                        $isExist[0]->PaymentID
                    ]);

                    /*$query = "DELETE FROM TR_ORDER_PRODUCT WHERE OrderID=?";
                    DB::delete($query, [$isExist[0]->ID]);

                    $i=0;
                    foreach ($request->product as $key => $value) {
                        //$price = $cart->DiscountType == 0 ? $cart->Price : ($cart->DiscountType == 1 ? ($cart->Price - $cart->Discount) : ($cart->Price - ($cart->Price * $cart->Discount)));
                        $query = "INSERT INTO TR_ORDER_PRODUCT
                                        (ID, OrderID, ProductID, Qty, ItemPrice, Notes)
                                    VALUES
                                        (UUID(), ?, ?, ?, ?, ?)";
                        DB::insert($query, [
                            $isExist[0]->ID,
                            $request->product[$i],
                            $request->qty[$i],
                            ($request->price[$i]/$request->qty[$i]),
                            ''
                        ]);

                        if ($request->selFrmOrderStatus == 5 && $isExist[0]->Status != 5) {
                            $query = "UPDATE MS_PRODUCT
                                        SET Stock=(Stock+".$request->qty[$i].")
                                        WHERE ID=?";
                            DB::update($query, [
                                $request->product[$i]
                            ]);
                        } 

                        $i++;
                    }
                    */

                    if ($request->selFrmOrderStatus == 5 && $isExist[0]->Status != 5) {
                        $query = "SELECT ProductID, Qty FROM TR_ORDER_PRODUCT WHERE OrderID=?";
                        $data = DB::select($query, [$isExist[0]->ID]);
                        foreach ($data as $key => $value) {
                            $query = "UPDATE MS_PRODUCT
                                        SET Stock=(Stock+".$value->Qty.")
                                        WHERE ID=?";
                            DB::update($query, [
                                $value->ProductID
                            ]);
                        } 
                    }

                    $return['message'] = "Data berhasil tersimpan!";
                    $return['callback'] = "doReloadTable()";
                }
            }
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function doSearchCustomer(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $definedFilter = "";
            if ($request->qName != "") $definedFilter.= " AND Name LIKE '%" .$this->sanitizeString($request->qName). "%' ";
            if ($request->qPhone != "") $definedFilter.= " AND Phone LIKE '%" .$this->sanitizeString($request->qPhone). "%' ";
            $query = "SELECT    ID,
                                Code,
                                Name,
                                Phone
                        FROM    MS_CUSTOMER
                        WHERE   Status=1
                                {definedFilter}
                        ORDER BY  Name ASC";
            $query = str_replace("{definedFilter}",$definedFilter,$query);
            $return['data'] = DB::select($query);
            $return['callback'] = "onCompleteFetchCustomer(e.data)";
        } else $return = array('status'=>false,'message'=>"Not Authorized");
        return response()->json($return, 200);
    }

    public function getProduct(Request $request)
	{
		$return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
		$getAuth = $this->validateAuth($request->_s);
		if ($getAuth['status']) {
            if ($request->qty) {
                $query = "SELECT	p.ID,
                                    p.Code,
                                    p.Name,
                                    pr.Price,
                                    p.Stock
                            FROM	MS_PRODUCT p
                                    JOIN MS_PRODUCT_PRICE pr ON pr.ProductID=p.ID
                            WHERE   p.BranchID=?
                                    AND p.ID=?
                                    AND pr.MinOrder <= ?
                                    AND pr.MaxOrder >= ?
                                    AND Status=1";
                $return['data'] = DB::select($query,[$request->retailID,$request->_i,$request->qty,$request->qty]);
            } else {
                $query = "SELECT	ID,
                                    Code,
                                    Name
                        FROM	  	MS_PRODUCT
                        WHERE       BranchID=?
                                    AND Status=1
                        ORDER BY    Name ASC";
                $return['data'] = DB::select($query,[$request->retailID]);
            }
			if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."','".$request->_i."')";
		} else $return = array('status'=>false,'message'=>"Not Authorized");
		return response()->json($return, 200);
	}

    public function doDelete(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $query = "SELECT Status,ID,PaymentID FROM TR_ORDER WHERE ID=? AND IsB2B=1";
            $isExist = DB::select($query, [$request->_i]);
            if ($isExist) {

                if ($isExist[0]->Status != 5) {
                    $query = "SELECT ProductID, Qty FROM TR_ORDER_PRODUCT WHERE OrderID=?";
                    $data = DB::select($query, [$isExist[0]->ID]);
                    foreach ($data as $key => $value) {
                        $query = "UPDATE MS_PRODUCT
                                    SET Stock=(Stock+".$value->Qty.")
                                    WHERE ID=?";
                        DB::update($query, [
                            $value->ProductID
                        ]);
                    } 
                }

                $query = "DELETE FROM TR_ORDER WHERE ID=?";
                DB::delete($query, [$isExist[0]->PaymentID]);
                $query = "DELETE FROM TR_ORDER_ADDRESS WHERE PaymentID=?";
                DB::delete($query, [$isExist[0]->PaymentID]);
                $query = "DELETE FROM TR_ORDER_PAYMENT WHERE ID=?";
                DB::delete($query, [$isExist[0]->PaymentID]);
                $query = "DELETE FROM TR_ORDER_PRODUCT WHERE OrderID=?";
                DB::delete($query, [$isExist[0]->ID]);
                $return['message'] = "Data berhasil dihapus!";
                $return['callback'] = "doReloadTable()";
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
                    <footer class="pt-5 my-5 text-muted text-center">
                        Terima kasih telah berbelanja di Ella Froze<br />** Seafood Praktis, Higienis &amp; Bergizi **
                    </footer>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
            </body>
        </html>';
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            if ($request->_i) {
                $query = "SELECT o.ID, o.PaymentID InvoiceID, o.CreatedDate TransactionDate, c.Name Customer, b.Name Branch, o.Discount, o.SubTotal, o.Total, o.DeliveryFee, o.Status,
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
                    $query = "SELECT p.Name Product, op.Qty, op.Notes, op.SourcePrice, op.DiscountPrice, op.ItemPrice
                                FROM TR_ORDER_PRODUCT op
                                    JOIN MS_PRODUCT p ON p.ID=op.ProductID
                                WHERE op.OrderID=?
                                ORDER BY p.Name ASC";
                    $subData = DB::select($query,[$request->_i]);
                    $branchData = $data->Branch.'<br />'.$data->BranchStateName.', '.$data->BranchCityName.', '.$data->BranchDistrictName.'<br />'.$data->BranchAddress;
                    $arrData = '
                    <table>
                    <tbody>
                        <tr>
                            <td width="150px">No. Pesanan</td>
                            <td>: '.$data->OrderNumber.'</td>
                        </tr>
                        <tr>
                            <td>Tgl. Pesanan</td>
                            <td>: '.$data->TransactionDate.'</td>
                        </tr>
                        <tr>
                            <td>Penerima</td>
                            <td>: '.$data->Customer.'</td>
                        </tr>
                        <tr>
                            <td>No. Telepon</td>
                            <td>: '.$data->Phone.'</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: '.$data->StateName.', '.$data->CityName.', '.$data->DistrictName.'<br />'.$data->Address.'<br />'.$data->PostalCode.'</td>
                        </tr>
                    </tbody>
                    </table>
                    <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Harga Normal</th>
                            <th scope="col">Discount</th>
                            <th scope="col">Harga Akhir</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($subData as $item) {
                        $arrData .= '<tr>
                                        <td>'.$item->Product.'</td>
                                        <td>'.number_format($item->Qty).'</td>
                                        <td>'.number_format($item->SourcePrice).'</td>
                                        <td>'.number_format($item->DiscountPrice).'</td>
                                        <td>'.number_format($item->ItemPrice).'</td>
                                    </tr>';
                    }
                    $arrData .= '   
                    </tbody>
                    </table>
                    
                    <table style="margin-top:50px">
                    <tbody>
                        <tr>
                            <td width="150px">Sub Total</td>
                            <td>: '.number_format($data->SubTotal).'</td>
                        </tr>
                        <tr>
                            <td width="150px">Discount</td>
                            <td>: '.number_format($data->Discount).'</td>
                        </tr>
                        <tr>
                            <td>Ongkos Kirim</td>
                            <td>: '.number_format($data->DeliveryFee).'</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>: '.number_format($data->Total).'</td>
                        </tr>
                    </tbody>
                    </table>

                    <table style="margin-top:50px">
                    <tbody>
                        <tr>
                            <td width="150px">Bank</td>
                            <td>: PT. BANK CENTRAL ASIA TBK.</td>
                        </tr>
                        <tr>
                            <td>Nama Rekening</td>
                            <td>: TJO FELIANA</td>
                        </tr>
                        <tr>
                            <td>No. Rek</td>
                            <td>: 2388820999</td>
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