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

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('doLogin',  ['uses' => 'AuthController@doLogin']);
    $router->post('doLogout',  ['uses' => 'AuthController@doLogout']);
    $router->get('doAuth',  ['uses' => 'AuthController@doAuth']);
    $router->post('doReset',  ['uses' => 'AuthController@doReset']);
});

$router->group(['prefix' => 'external'], function () use ($router) {
    $router->post('doAuthGoogle',  ['uses' => 'ExternalController@doAuthGoogle']);
    $router->post('doRegister',  ['uses' => 'ExternalController@doRegister']);
    $router->post('doLogin',  ['uses' => 'ExternalController@doLogin']);
    $router->post('doLogout',  ['uses' => 'ExternalController@doLogout']);
    $router->post('doReset',  ['uses' => 'AuthController@doReset']);
    $router->get('getAll',  ['uses' => 'ExternalController@getAll']);
    $router->get('getPosProduct',  ['uses' => 'ExternalController@getPosProduct']);
    $router->get('getPosProductVariant',  ['uses' => 'ExternalController@getPosProductVariant']);
    $router->get('getPosProductVariantOption',  ['uses' => 'ExternalController@getPosProductVariantOption']);
    $router->get('getPosCategory',  ['uses' => 'ExternalController@getPosCategory']);
    $router->get('getPosVariant',  ['uses' => 'ExternalController@getPosVariant']);
    $router->get('getPosVariantOption',  ['uses' => 'ExternalController@getPosVariantOption']);
    $router->get('getPosTransaction',  ['uses' => 'ExternalController@getPosTransaction']);
    $router->get('getPosTransactionHistory',  ['uses' => 'ExternalController@getPosTransactionHistory']);
    $router->get('getPosTransactionProduct',  ['uses' => 'ExternalController@getPosTransactionProduct']);
    $router->get('getPosTransactionProductVariant',  ['uses' => 'ExternalController@getPosTransactionProductVariant']);
    $router->get('getPosClient',  ['uses' => 'ExternalController@getPosClient']);
    $router->get('getPosOutlet',  ['uses' => 'ExternalController@getPosOutlet']);
    $router->get('getPosCustomer',  ['uses' => 'ExternalController@getPosCustomer']);
    $router->get('getPosUser',  ['uses' => 'ExternalController@getPosUser']);
    $router->get('getPosPayment',  ['uses' => 'ExternalController@getPosPayment']);
    $router->post('doSaveCategory',  ['uses' => 'ExternalController@doSaveCategory']);
    $router->post('doSaveVariant',  ['uses' => 'ExternalController@doSaveVariant']);
    $router->post('doSaveVariantOption',  ['uses' => 'ExternalController@doSaveVariantOption']);
    $router->post('doSaveProduct',  ['uses' => 'ExternalController@doSaveProduct']);
    $router->post('doSaveProductVariant',  ['uses' => 'ExternalController@doSaveProductVariant']);
    $router->post('doSaveProductVariantOption',  ['uses' => 'ExternalController@doSaveProductVariantOption']);
    $router->post('doSaveTransaction',  ['uses' => 'ExternalController@doSaveTransaction']);
    $router->post('doSaveTransactionProduct',  ['uses' => 'ExternalController@doSaveTransactionProduct']);
    $router->post('doSaveTransactionProductVariant',  ['uses' => 'ExternalController@doSaveTransactionProductVariant']);
    $router->post('doSavePayment',  ['uses' => 'ExternalController@doSavePayment']);
    $router->post('doSaveClient',  ['uses' => 'ExternalController@doSaveClient']);
    $router->post('doSaveOutlet',  ['uses' => 'ExternalController@doSaveOutlet']);
    $router->post('doSaveCustomer',  ['uses' => 'ExternalController@doSaveCustomer']);
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