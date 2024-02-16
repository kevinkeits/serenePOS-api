<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    private function validateAuth($Token)
    {
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
    
    // GET PRODUCT
    public function getProduct(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>array());
        $getAuth = $this->validateAuth($request->_s);
        if ($getAuth['status']) {
            $mainQuery = "  SELECT 
                                    MsProduct.ID,
                                    MsProduct.ClientID,
                                    MsProduct.Name, 
                                    MsProduct.Notes, 
                                    MsProduct.Qty, 
                                    MsProduct.Price, 
                                    MsCategory.ID CategoryID,
                                    MsCategory.Name Category, 
                                    MsProduct.ProductSKU, 
                                    MsProduct.ImgUrl, 
                                    MsProduct.MimeType 
                            FROM    MsProduct
                            JOIN    MsCategory ON MsProduct.CategoryID = MsCategory.ID
                            WHERE   {definedFilter}
                            ORDER BY MsProduct.Name ASC";
                            $definedFilter = "1=1";
            if ($getAuth['ClientID'] != "") $definedFilter = "MsProduct.ClientID = '".$getAuth['ClientID']."'";
            if ($request->_i) {
                $definedFilter = "MsProduct.ID=?";
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query,[$request->_i]);
                if ($data) {
                    $query = "SELECT    MsVariant.ID,
                                        MsVariant.Name,
                                        MsVariant.Type,
                                        MsVariantOption.ID VariantOptionID,
                                        MsVariantOption.Label,
                                        MsVariantOption.Price
                                FROM    MsVariant
                                JOIN    MsVariantProduct on MsVariantProduct.VariantID = MsVariant.ID
                                JOIN    MsVariantOption on MsVariantOption.VariantID = MsVariant.ID
                                WHERE   MsVariantProduct.ProductID = ?
                                ORDER BY  MsVariant.Name ASC";
                    $selVariant = DB::select($query,[$request->_i]);
                    $arrData = [];
                    if ($selVariant) {
                        foreach ($selVariant as $key => $value) {
                            array_push($arrData,$value->ID);
                        }
                    }
                    $return['data'] = array('header'=>$data[0], 'selVariant'=> $selVariant);
                }
            } else {
                $query = str_replace("{definedFilter}",$definedFilter,$mainQuery);
                $data = DB::select($query);
                if ($data) $return['data'] = $data;
            }
        } else $return = array('status'=>false,'message'=>"");
        return response()->json($return, 200);
    }

    public function getProductVariant(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $query = "SELECT MsProductVariant.ID, MsProduct.ID ProductID, MsVariant.ID VariantID, MsVariant.Name
            FROM MsProductVariant
            JOIN MsProduct
            ON MsProduct.ID = MsProductVariant.ProductID
            JOIN MsVariant
            ON MsVariant.ID = MsProductVariant.VariantID
            WHERE MsProductVariant.ClientID = ''
            ORDER BY ID ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }

    public function getProductVariantOption(Request $request)
    {
        $return = array('status'=>true,'message'=>"",'data'=>null);
        $query = "SELECT MsProductVariantOption.ID, MsProduct.ID ProductID, MsVariantOption.ID VariantOptionID, MsVariantOption.Label, MsVariantOption.Price
            FROM MsProductVariantOption
            JOIN MsProduct
            ON MsProduct.ID = MsProductVariantOption.ProductID
            JOIN MsVariantOption
            ON MsVariantOption.ID = MsProductVariantOption.VariantOptionID
            WHERE MsProductVariantOption.ClientID = ''
            ORDER BY ID ASC";
        $return['data'] = DB::select($query);
        if ($request->_cb) $return[''] = $request->_cb."(e.data,'".$request->_p."')";
        return response()->json($return, 200);
    }
    // END GET PRODUCT
}