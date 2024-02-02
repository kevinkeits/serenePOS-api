<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'redirect'], function () use ($router) {
    $router->get('blog',  ['uses' => 'ExternalController@redirectBlog']);
});


$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('doLogin',  ['uses' => 'AuthController@doLogin']);
    $router->post('doLogout',  ['uses' => 'AuthController@doLogout']);
    $router->get('doAuth',  ['uses' => 'AuthController@doAuth']);
    $router->post('doReset',  ['uses' => 'AuthController@doReset']);
});

$router->group(['prefix' => 'global'], function () use ($router) {
    $router->get('getTest',  ['uses' => 'GlobalController@getTest']);

    $router->get('getMenu',  ['uses' => 'GlobalController@getMenu']);
    $router->get('getRole',  ['uses' => 'GlobalController@getRole']);
    $router->get('getMaster',  ['uses' => 'GlobalController@getMaster']);

    $router->get('getState',  ['uses' => 'GlobalController@getState']);
    $router->get('getCity',  ['uses' => 'GlobalController@getCity']);
    $router->get('getDistrict',  ['uses' => 'GlobalController@getDistrict']);

    $router->get('getAllUser',  ['uses' => 'GlobalController@getAllUser']);
    $router->get('getAllBranch',  ['uses' => 'GlobalController@getAllBranch']);
    $router->get('getCategory',  ['uses' => 'GlobalController@getCategory']);

    $router->get('getDiscountType',  ['uses' => 'GlobalController@getDiscountType']);
});

$router->group(['prefix' => 'dashboard'], function () use ($router) {
    $router->get('getTransaction',  ['uses' => 'DashboardController@GetTransaction']);
    $router->get('getProduct',  ['uses' => 'DashboardController@GetProductSales']);
    $router->get('getNotification',  ['uses' => 'DashboardController@GetNotification']);
});

$router->group(['prefix' => 'settings'], function () use ($router) {
    $router->get('get',  ['uses' => 'SettingController@getAll']);
    $router->post('doSave',  ['uses' => 'SettingController@doSave']);
    $router->post('doDelete',  ['uses' => 'SettingController@doDelete']);
});

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->get('get',  ['uses' => 'UserController@getAll']);
    $router->post('doSave',  ['uses' => 'UserController@doSave']);
    $router->post('doDelete',  ['uses' => 'UserController@doDelete']);
    $router->get('doGenerate',  ['uses' => 'UserController@doGenerate']);
});

$router->group(['prefix' => 'roles'], function () use ($router) {
    $router->get('get',  ['uses' => 'RoleController@getAll']);
    $router->get('getSelected',  ['uses' => 'RoleController@getSelected']);
    $router->post('doSave',  ['uses' => 'RoleController@doSave']);
    $router->post('doDelete',  ['uses' => 'RoleController@doDelete']);
    $router->get('doGenerate',  ['uses' => 'RoleController@doGenerate']);
});

$router->group(['prefix' => 'branch'], function () use ($router) {
    $router->get('get',  ['uses' => 'BranchController@getAll']);
    $router->post('doSave',  ['uses' => 'BranchController@doSave']);
    $router->post('doDelete',  ['uses' => 'BranchController@doDelete']);
    $router->get('getAdminBranch',  ['uses' => 'BranchController@getAdminBranch']);
});

$router->group(['prefix' => 'category'], function () use ($router) {
    $router->get('get',  ['uses' => 'CategoryController@getAll']);
    $router->post('doSave',  ['uses' => 'CategoryController@doSave']);
    $router->post('doUpload',  ['uses' => 'CategoryController@doUpload']);
    $router->post('doDelete',  ['uses' => 'CategoryController@doDelete']);
	$router->post('doDeletePic',  ['uses' => 'CategoryController@doDeletePic']);
});

$router->group(['prefix' => 'product'], function () use ($router) {
    $router->get('get',  ['uses' => 'ProductController@getAll']);
    $router->get('getPriceDetail',  ['uses' => 'ProductController@getPriceDetail']);
    $router->get('getImageProduct',  ['uses' => 'ProductController@getImageProduct']);
    $router->post('doSave',  ['uses' => 'ProductController@doSave']);
    $router->post('doUpload',  ['uses' => 'ProductController@doUpload']);
    $router->post('doFinish',  ['uses' => 'ProductController@doFinish']);
    $router->post('doDelete',  ['uses' => 'ProductController@doDelete']);
    $router->post('doDeletePic',  ['uses' => 'ProductController@doDeletePic']);
    
});

$router->group(['prefix' => 'banner'], function () use ($router) {
    $router->get('get',  ['uses' => 'BannerController@getAll']);
    $router->post('doSave',  ['uses' => 'BannerController@doSave']);
    $router->post('doUpload',  ['uses' => 'BannerController@doUpload']);
    $router->post('doDelete',  ['uses' => 'BannerController@doDelete']);
	$router->post('doDeletePic',  ['uses' => 'BannerController@doDeletePic']);
});

$router->group(['prefix' => 'deliverycost'], function () use ($router) {
    $router->get('get',  ['uses' => 'DeliverycostController@getAll']);
    $router->post('doSave',  ['uses' => 'DeliverycostController@doSave']);
    $router->post('doDelete',  ['uses' => 'DeliverycostController@doDelete']);
});

$router->group(['prefix' => 'discount'], function () use ($router) {
    $router->get('get',  ['uses' => 'DiscountController@getAll']);
    $router->post('doSave',  ['uses' => 'DiscountController@doSave']);
    $router->post('doDelete',  ['uses' => 'DiscountController@doDelete']);
    $router->get('getBranch',  ['uses' => 'DiscountController@getBranch']);
    $router->get('getProduct',  ['uses' => 'DiscountController@getProduct']);
});

$router->group(['prefix' => 'customer'], function () use ($router) {
    $router->get('get',  ['uses' => 'CustomerController@getAll']);
    $router->post('doSave',  ['uses' => 'CustomerController@doSave']);
    $router->post('doDelete',  ['uses' => 'CustomerController@doDelete']);
    $router->get('getAddress',  ['uses' => 'CustomerController@getAddress']);
    $router->get('getAddressDetail',  ['uses' => 'CustomerController@getAddressDetail']);
    $router->post('doChangePrimary',  ['uses' => 'CustomerController@doChangePrimary']);
    $router->post('doRemoveAddress',  ['uses' => 'CustomerController@doRemoveAddress']);
    $router->post('doSaveAddress',  ['uses' => 'CustomerController@doSaveAddress']);
});

$router->group(['prefix' => 'chat'], function () use ($router) {
    $router->get('getListing',  ['uses' => 'ChatController@getListing']);
    $router->get('getMessageDetail',  ['uses' => 'ChatController@getMessageDetail']);
    $router->post('doPost',  ['uses' => 'ChatController@doPost']);
});

$router->group(['prefix' => 'selling'], function () use ($router) {
    $router->get('get',  ['uses' => 'SellingController@getAll']);
    $router->post('doSave',  ['uses' => 'SellingController@doSave']);
    $router->post('doDelete',  ['uses' => 'SellingController@doDelete']);
    $router->get('printOrder',  ['uses' => 'SellingController@printOrder']);
});

$router->group(['prefix' => 'b2b'], function () use ($router) {
    $router->get('get',  ['uses' => 'B2BController@getAll']);
    $router->post('doSave',  ['uses' => 'B2BController@doSave']);
    $router->post('doDelete',  ['uses' => 'B2BController@doDelete']);
    $router->post('doSearchCustomer',  ['uses' => 'B2BController@doSearchCustomer']);
    $router->get('getProduct',  ['uses' => 'B2BController@getProduct']);
    $router->get('printOrder',  ['uses' => 'B2BController@printOrder']);
});

$router->group(['prefix' => 'stockrequest'], function () use ($router) {
    $router->get('get',  ['uses' => 'StockRequestController@getAll']);
    $router->post('doSave',  ['uses' => 'StockRequestController@doSave']);
    $router->post('doDelete',  ['uses' => 'StockRequestController@doDelete']);
    $router->get('getProduct',  ['uses' => 'StockRequestController@getProduct']);
    $router->get('printOrder',  ['uses' => 'StockRequestController@printOrder']);
});

$router->group(['prefix' => 'stockapproval'], function () use ($router) {
    $router->get('get',  ['uses' => 'StockApprovalController@getAll']);
    $router->post('doSave',  ['uses' => 'StockApprovalController@doSave']);
});
$router->group(['prefix' => 'stockconfirmation'], function () use ($router) {
    $router->get('get',  ['uses' => 'StockConfirmationController@getAll']);
    $router->post('doSave',  ['uses' => 'StockConfirmationController@doSave']);
});

$router->group(['prefix' => 'transactionreport'], function () use ($router) {
    $router->get('get',  ['uses' => 'ReportController@getTransactionReport']);
});

$router->group(['prefix' => 'customerreport'], function () use ($router) {
    $router->get('get',  ['uses' => 'ReportController@getCustomerReport']);
});

$router->group(['prefix' => 'stockreport'], function () use ($router) {
    $router->get('get',  ['uses' => 'ReportController@getStockReport']);
});


$router->group(['prefix' => 'external'], function () use ($router) {
    $router->post('doAuthGoogle',  ['uses' => 'ExternalController@doAuthGoogle']);
    $router->post('doRegister',  ['uses' => 'ExternalController@doRegister']);
    $router->post('doLogin',  ['uses' => 'ExternalController@doLogin']);
    $router->post('doLogout',  ['uses' => 'ExternalController@doLogout']);
    $router->post('doReset',  ['uses' => 'AuthController@doReset']);
    $router->get('testProduct',  ['uses' => 'ExternalController@testProduct']);
    $router->get('testProductVariant',  ['uses' => 'ExternalController@testProductVariant']);
    $router->get('testCategory',  ['uses' => 'ExternalController@testCategory']);
    $router->get('testVariant',  ['uses' => 'ExternalController@testVariant']);
    $router->get('testVariantOption',  ['uses' => 'ExternalController@testVariantOption']);
    $router->get('testProductVariantOption',  ['uses' => 'ExternalController@testProductVariantOption']);
    $router->get('testTransaction',  ['uses' => 'ExternalController@testTransaction']);
    $router->get('testTransactionProduct',  ['uses' => 'ExternalController@testTransactionProduct']);
    $router->get('testTransactionProductVariant',  ['uses' => 'ExternalController@testTransactionProductVariant']);
    $router->get('testClient',  ['uses' => 'ExternalController@testClient']);
    $router->get('testOutlet',  ['uses' => 'ExternalController@testOutlet']);
    $router->get('testCustomer',  ['uses' => 'ExternalController@testCustomer']);
    $router->get('testUser',  ['uses' => 'ExternalController@testUser']);
    $router->get('testPayment',  ['uses' => 'ExternalController@testPayment']);
    $router->post('doSaveCategory',  ['uses' => 'ExternalController@doSaveCategory']);
    $router->post('doSaveVariant',  ['uses' => 'ExternalController@doSaveVariant']);
    $router->post('doSaveVariantOption',  ['uses' => 'ExternalController@doSaveVariantOption']);
    $router->get('getBranch',  ['uses' => 'ExternalController@getBranch']);
    $router->get('getBanner',  ['uses' => 'ExternalController@getBanner']);
    $router->get('getDiscount',  ['uses' => 'ExternalController@getDiscount']);
    $router->get('getCategory',  ['uses' => 'ExternalController@getCategory']);
    $router->get('getAllProduct',  ['uses' => 'ExternalController@getAllProduct']);
    $router->get('getProductPrice',  ['uses' => 'ExternalController@getProductPrice']);
    $router->get('getProductDetail',  ['uses' => 'ExternalController@getProductDetail']);
    $router->get('getProductImage',  ['uses' => 'ExternalController@getProductImage']);
    $router->get('getUser',  ['uses' => 'ExternalController@getUser']);
    $router->post('doUpdateUser',  ['uses' => 'ExternalController@doUpdateUser']);
    $router->get('getUserAddress',  ['uses' => 'ExternalController@getUserAddress']);
    $router->post('doSaveAddress',  ['uses' => 'ExternalController@doSaveAddress']);
    $router->post('doRemoveAddress',  ['uses' => 'ExternalController@doRemoveAddress']);
    $router->post('doSetPrimaryAddress',  ['uses' => 'ExternalController@doSetPrimaryAddress']);
    $router->get('getCart',  ['uses' => 'ExternalController@getCart']);
    $router->get('doCalculateDelivery',  ['uses' => 'ExternalController@doCalculateDelivery']);
    $router->post('doSaveCart',  ['uses' => 'ExternalController@doSaveCart']);
    $router->post('doUpdateCart',  ['uses' => 'ExternalController@doUpdateCart']);
    $router->get('getPaymentMethod',  ['uses' => 'ExternalController@getPaymentMethod']);
    $router->post('doPay',  ['uses' => 'ExternalController@doPay']);
    $router->get('getChatList',  ['uses' => 'ExternalController@getChatList']);
    $router->get('getChatDetail',  ['uses' => 'ExternalController@getChatDetail']);
    $router->post('doSaveMessage',  ['uses' => 'ExternalController@doSaveMessage']);
    $router->get('getUnpaidTransaction',  ['uses' => 'ExternalController@getUnpaidTransaction']);
    $router->get('getTransaction',  ['uses' => 'ExternalController@getTransaction']);
    $router->get('getTransactionDetail',  ['uses' => 'ExternalController@getTransactionDetail']);
    $router->get('getHelpList',  ['uses' => 'ExternalController@getHelpList']);
    $router->get('getNotification',  ['uses' => 'ExternalController@getNotification']);
    $router->get('runScheduler',  ['uses' => 'ExternalController@runScheduler']);

    $router->post('midtransHandler',  ['uses' => 'ExternalController@getMidtransNotification']);


$router->get('getArticle',  ['uses' => 'ExternalController@getArticle']);
$router->get('getArticleDetail',  ['uses' => 'ExternalController@getArticleDetail']);
$router->get('doCalculateDeliveryNew',  ['uses' => 'ExternalController@doCalculateDeliveryNew']);
$router->get('getHighestSold',  ['uses' => 'ExternalController@getHighestSold']);
$router->post('doUpdateDeviceID',  ['uses' => 'ExternalController@doUpdateDeviceID']);

});
$router->get('testExternal',  ['uses' => 'ExternalController@test']);
$router->get('invoice',  ['uses' => 'ExternalController@printOrder']);

$router->group(['prefix' => 'article'], function () use ($router) {
    $router->get('get',  ['uses' => 'ArticleController@getAll']);
    $router->post('doSave',  ['uses' => 'ArticleController@doSave']);
    $router->post('doUpload',  ['uses' => 'ArticleController@doUpload']);
    $router->post('doDelete',  ['uses' => 'ArticleController@doDelete']);
	$router->post('doDeletePic',  ['uses' => 'ArticleController@doDeletePic']);
});

