<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class DiscountController extends Controller
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
			$mainQuery = "  SELECT	  	d.ID,
										d.BranchID,
										p.Code ProductCode,
										p.Name ProductName,
										d.ProductID,
										d.StartDate,
										d.EndDate,
										d.Discount,
										d.DiscountType,
										CASE
											WHEN d.DiscountType = '2' THEN CONCAT(d.Discount, '%')
											ELSE d.Discount
										END AS strDiscount,
										d.Status
							FROM		MS_DISCOUNT d
							LEFT JOIN   MS_PRODUCT p on p.ID = d.ProductID
							WHERE {definedFilter}
							ORDER BY d.StartDate DESC, d.EndDate DESC, p.Code ASC, p.Name ASC";
		   $definedFilter = "1=1";
		   if ($request->_i) {
			   $definedFilter = "d.ID=?";
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

		   if (date_create($request->txtFrmEndDate) < date_create($request->txtFrmStartDate)) {
				$return['status'] = false;
				$return['message'] = "Tanggal Berakhir tidak boleh lebih kecil dari Tanggal Mulai";
		   } else {
				if ($request->hdnFrmAction=="add") {
					$query = "  SELECT	ID
								FROM MS_DISCOUNT
								WHERE ProductID=?
									AND	 (
												? BETWEEN startDate AND endDate OR
												? BETWEEN startDate AND endDate
											)
									AND	  Status=1";
					$isExist = DB::select($query, [$request->selFrmProductID,$request->txtFrmStartDate,$request->txtFrmEndDate]);
					if (!$isExist) {
						$query = "INSERT INTO MS_DISCOUNT
										(ID, BranchID, ProductID, StartDate, EndDate, DiscountType, Discount, Status, CreatedDate, CreatedBy, ModifiedDate, ModifiedBy)
									VALUES(UUID(), ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL)";
						DB::insert($query, [
							$request->selFrmBranch,
							$request->selFrmProductID,
							$request->txtFrmStartDate,
							$request->txtFrmEndDate,
							$request->selFrmDiscountType,
							$request->txtFrmDiscount,
							$request->radFrmStatus==1 ? 1 : 0,
							$getAuth['UserID']
						]);
						$return['message'] = "Data berhasil disimpan!";
						$return['callback'] = "doReloadTable()";
					} else {
						$return['status'] = false;
						$return['message'] = "Produk ini sudah ada dalam efektif tanggal diskon yang aktif";
					}
				}
				if ($request->hdnFrmAction=="edit") {
					$query = "  SELECT  ID
								FROM	MS_DISCOUNT
								WHERE   ProductID=?
								AND	 (
											? BETWEEN startDate AND endDate OR
											? BETWEEN startDate AND endDate
										)
								AND	 Status=1
								AND	 ID!=?";
					$isExist = DB::select($query, [$request->selFrmProductID, $request->txtFrmStartDate, $request->txtFrmEndDate, $request->hdnFrmID]);
					if (!$isExist) {
						$query = "UPDATE MS_DISCOUNT
									SET BranchID=?,
										ProductID=?,
										StartDate=?,
										EndDate=?,
										DiscountType=?,
										Discount=?,
										Status=?,
										ModifiedDate=NOW(),
										ModifiedBy=?
									WHERE ID=?";
						DB::update($query, [
							$request->selFrmBranch,
							$request->selFrmProductID,
							$request->txtFrmStartDate,
							$request->txtFrmEndDate,
							$request->selFrmDiscountType,
							$request->txtFrmDiscount,
							$request->radFrmStatus==1 ? 1 : 0,
							$getAuth['UserID'],
							$request->hdnFrmID
						]);
						$return['message'] = "Data has been saved!";
						$return['callback'] = "doReloadTable()";
					} else {
						$return['status'] = false;
						$return['message'] = "Produk ini sudah ada dalam efektif tanggal diskon yang aktif";
					}
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
		   $query = "SELECT ID FROM MS_DISCOUNT WHERE ID=?";
		   $isExist = DB::select($query, [$request->_i]);
		   if ($isExist) {
			   $query = "DELETE FROM MS_DISCOUNT WHERE ID=?";
			   DB::delete($query, [$request->_i]);
			   $return['message'] = "Data has been removed!";
			   $return['callback'] = "doReloadTable()";
		   } else $return = array('status'=>false,'message'=>"Not Authorized");
	   } else $return = array('status'=>false,'message'=>"Not Authorized");
	   return response()->json($return, 200);
   }

   public function getProduct(Request $request)
	{
		$return = array('status'=>true,'message'=>"",'data'=>null,'callback'=>"");
		$getAuth = $this->validateAuth($request->_s);
		if ($getAuth['status']) {
			$query = "
					SELECT		ID,
							  	Code,
							  	Name
					FROM	  	MS_PRODUCT
					WHERE	 	BranchID = ?
					ORDER BY  Name ASC";
			$return['data'] = DB::select($query,[$request->BranchID]);
			if ($request->_cb) $return['callback'] = $request->_cb."(e.data,'".$request->_p."')";
		} else $return = array('status'=>false,'message'=>"Not Authorized");
		return response()->json($return, 200);
	}

}