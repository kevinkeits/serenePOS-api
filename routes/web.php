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

$router->group(['prefix' => 'external'], function () use ($router) {
    $router->post('doAuthGoogle',  ['uses' => 'ExternalController@doAuthGoogle']);
    $router->post('doRegister',  ['uses' => 'ExternalController@doRegister']);
    $router->post('doLogin',  ['uses' => 'ExternalController@doLogin']);
    $router->post('doLogout',  ['uses' => 'ExternalController@doLogout']);
    $router->post('doReset',  ['uses' => 'AuthController@doReset']);
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

    $router->get('doCalculateDeliveryNew',  ['uses' => 'ExternalController@doCalculateDeliveryNew']);
    $router->get('getHighestSold',  ['uses' => 'ExternalController@getHighestSold']);
    $router->post('doUpdateDeviceID',  ['uses' => 'ExternalController@doUpdateDeviceID']);
});