<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Exports\GeneralExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
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

	public function getTransactionReport(Request $request)
	{
		ini_set('memory_limit', '-1');
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
			$filename = 'Transaction_Report';
			if ($request->branchID != "") {
				$mainQuery = "SELECT Name FROM MS_BRANCH WHERE ID=?";
				$branchData = DB::select($mainQuery,[$request->branchID]);
				$filename = 'Transaction_Report_'.$branchData[0]->Name;
			}

            $mainQuery = "SELECT o.IsB2B, o.PaymentID, o.OrderNumber, o.CreatedDate, b.Name Branch, c.Name Customer, c.Phone, p.Name Product, op.Qty, o.SubTotal, o.DeliveryFee, o.Total, o.Status, opy.IsPaid, opy.PaymentMethod, op.ItemPrice, op.DiscountPrice, op.SourcePrice
							FROM TR_ORDER o
									JOIN MS_BRANCH b ON b.ID=o.BranchID
									JOIN MS_CUSTOMER c ON c.ID=o.CustomerID
									JOIN TR_ORDER_PRODUCT op ON op.OrderID=o.ID
									JOIN MS_PRODUCT p ON p.ID=op.ProductID
									JOIN TR_ORDER_PAYMENT opy ON opy.ID=o.PaymentID
							WHERE o.CreatedDate BETWEEN ? AND ?";
							if ($request->selFrmBranch != "") $mainQuery .= " AND o.BranchID = '".$request->selFrmBranch."'";
							if ($request->selFrmStatusOrder != "") $mainQuery .= " AND o.Status = '".$request->selFrmStatusOrder."'";
							if ($request->selFrmStatusPayment != "") $mainQuery .= " AND opy.IsPaid = '".$request->selFrmStatusPayment."'";
			$data = DB::select($mainQuery,[$request->txtFrmStartDate, $request->txtFrmEndDate]);
            
            $arrData = [];
            $arrHeader = array(
                "NO",
				"CABANG",
				"NO. PESANAN",
                "TGL. PESANAN",
                "PELANGGAN",
                "NO. TELEPON PELANGGAN",
				"PRODUK",
				"JUMLAH PESAN",
				"HARGA AWAL",
				"DISCOUNT",
				"HARGA FINAL",
				"SUB TOTAL",
				"ONGKOS KIRIM",
				"TOTAL BAYAR",
				"METODE PEMBAYARAN",
				"STATUS PESANAN",
				"STATUS BAYAR"
            );
            array_push($arrData,$arrHeader);
			$i=1;
            foreach ($data as $key => $value) {
				$status = "Belum Bayar";
				if ($value->Status == 2) $status = "Dikonfirmasi";
				if ($value->Status == 3) $status = "Dalam Pengiriman";
				if ($value->Status == 4) $status = "Selesai";
				if ($value->Status == 5) $status = "Batal";
                $rows = array(
                    $i,
					$value->Branch,
                    $value->PaymentID,
                    $value->CreatedDate,
                    $value->Customer,
					$value->Phone,
					$value->Product,
					$value->Qty,
					$value->IsB2B == 1 ? $value->SourcePrice : ($value->SourcePrice * $value->Qty),
					$value->DiscountPrice,
					$value->ItemPrice,
					(isset($data[$i]) ? ($data[$i-1]->PaymentID != $data[$i]->PaymentID ? $value->SubTotal : 0) : 0),
					(isset($data[$i]) ? ($data[$i-1]->PaymentID != $data[$i]->PaymentID ? $value->DeliveryFee : 0) : 0),
					(isset($data[$i]) ? ($data[$i-1]->PaymentID != $data[$i]->PaymentID ? $value->Total : 0) : 0),
					(isset($data[$i]) ? ($data[$i-1]->PaymentID != $data[$i]->PaymentID ? $value->PaymentMethod : '') : ''),
					(isset($data[$i]) ? ($data[$i-1]->PaymentID != $data[$i]->PaymentID ? $status : '') : ''),
					(isset($data[$i]) ? ($data[$i-1]->PaymentID != $data[$i]->PaymentID ? ($value->IsPaid == 1 ? "Sudah Bayar" : "Belum Bayar") : '') : ''),
                );
                array_push($arrData,$rows);
				$i++;
            }
            return Excel::download(new GeneralExport([$arrData]), $filename.'_'.time().'.xlsx');
        } else {
			return response()->json($return, 200);
		}
   	}

   	public function getCustomerReport(Request $request)
	{
		ini_set('memory_limit', '-1');
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "SELECT c.Name, c.Phone, c.Email, st.Field2 State, ct.Field2 City, dt.Field2 District, ca.Address, ca.PostalCode
						FROM MS_CUSTOMER c
							JOIN MS_CUSTOMER_ADDRESS ca ON ca.CustomerID=c.ID
							JOIN MS_REFERENCES st ON st.ID=ca.StateID
							JOIN MS_REFERENCES ct ON ct.ID=ca.CityID
							JOIN MS_REFERENCES dt ON dt.ID=ca.DistrictID
						ORDER BY Name ASC";
			$data = DB::select($mainQuery);
       
            $filename = 'Customer_Report';
            $arrData = [];
            $arrHeader = array(
                "NO",
                "NAME",
                "PHONE",
                "EMAIL",
				"ALAMAT",
				"PROVINSI",
				"KOTA/KABUPATEN",
				"KECAMATAN",
				"KODE POS"
            );
            array_push($arrData,$arrHeader);
			$i=1;
            foreach ($data as $key => $value) {
                $rows = array(
                    $i,
                    $value->Name,
                    $value->Phone,
                    $value->Email,
					$value->Address,
					$value->State,
					$value->City,
					$value->District,
					$value->PostalCode
                );
                array_push($arrData,$rows);
				$i++;
            }
            return Excel::download(new GeneralExport([$arrData]), $filename.'_'.time().'.xlsx');
        } else {
			return response()->json($return, 200);
		}
   	}

   	public function getStockReport(Request $request)
	{
		ini_set('memory_limit', '-1');
        $return = array('status'=>true,'message'=>"",'data'=>array(),'callback'=>"");
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
			$mainQuery = "SELECT Name FROM MS_BRANCH WHERE ID=?";
			$branchData = DB::select($mainQuery,[$request->branchID]);

            $mainQuery = "SELECT Code,Name,Weight,Stock FROM MS_PRODUCT WHERE BranchID=? ORDER BY Name ASC";
			$data = DB::select($mainQuery,[$request->branchID]);
       
            $filename = 'Stock_Report_'.$branchData[0]->Name;
            $arrData = [];
            $arrHeader = array(
                "NO",
				"SKU",
                "PRODUK",
                "CABANG",
                "BERAT",
				"STOK"
            );
            array_push($arrData,$arrHeader);
			$i=1;
            foreach ($data as $key => $value) {
                $rows = array(
                    $i,
					$value->Code,
                    $value->Name,
                    $branchData[0]->Name,
                    $value->Weight,
					$value->Stock,
                );
                array_push($arrData,$rows);
				$i++;
            }
            return Excel::download(new GeneralExport([$arrData]), $filename.'_'.time().'.xlsx');
        } else {
			return response()->json($return, 200);
		}
   	}


}