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
    $router->post('doRegister',  ['uses' => 'AuthController@doRegister']);
    $router->post('doLogin',  ['uses' => 'AuthController@doLogin']);
    $router->post('doLogout',  ['uses' => 'AuthController@doLogout']);
    $router->get('doAuth',  ['uses' => 'AuthController@doAuth']);
    $router->post('doReset',  ['uses' => 'AuthController@doReset']);
});

$router->group(['prefix' => 'product'], function () use ($router) {
    $router->get('getProduct',  ['uses' => 'ProductController@getProduct']);
    $router->get('getProductVariant',  ['uses' => 'ProductController@getProductVariant']);
    $router->get('getProductVariantOption',  ['uses' => 'ProductController@getProductVariantOption']);
    $router->post('doSaveProduct',  ['uses' => 'ProductController@doSaveProduct']);
    $router->post('doSaveProductVariant',  ['uses' => 'ProductController@doSaveProductVariant']);
    $router->post('doSaveProductVariantOption',  ['uses' => 'ProductController@doSaveProductVariantOption']);
});

$router->group(['prefix' => 'variant'], function () use ($router) {
    $router->get('getVariant',  ['uses' => 'VariantController@getVariant']);
    $router->get('getVariantOption',  ['uses' => 'VariantController@getVariantOption']);
    $router->post('doSaveVariant',  ['uses' => 'VariantController@doSaveVariant']);
    $router->post('doSaveVariantOption',  ['uses' => 'VariantController@doSaveVariantOption']);
});

$router->group(['prefix' => 'transaction'], function () use ($router) {
    $router->get('getTransaction',  ['uses' => 'TransactionController@getTransaction']);
    $router->get('getTransactionHistory',  ['uses' => 'TransactionController@getTransactionHistory']);
    $router->post('doSaveTransaction',  ['uses' => 'TransactionController@doSaveTransaction']);
    $router->post('doSaveTransactionProduct',  ['uses' => 'TransactionController@doSaveTransactionProduct']);
    $router->post('doSaveTransactionProductVariant',  ['uses' => 'TransactionController@doSaveTransactionProductVariant']);
});

$router->group(['prefix' => 'category'], function () use ($router) {
    $router->get('getCategory',  ['uses' => 'CategoryController@getCategory']);
    $router->post('doSaveCategory',  ['uses' => 'CategoryController@doSaveCategory']);
});

$router->group(['prefix' => 'client'], function () use ($router) {
    $router->get('getClient',  ['uses' => 'ClientController@getClient']);
    $router->post('doSaveClient',  ['uses' => 'ClientController@doSaveClient']);
});

$router->group(['prefix' => 'outlet'], function () use ($router) {
    $router->get('getOutlet',  ['uses' => 'OutletController@getOutlet']);
    $router->post('doSaveOutlet',  ['uses' => 'OutletController@doSaveOutlet']);
});

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('getUser',  ['uses' => 'UserController@getUser']);
    $router->get('getUserDetail',  ['uses' => 'UserController@getUserDetail']);
});

$router->group(['prefix' => 'payment'], function () use ($router) {
    $router->get('getPayment',  ['uses' => 'PaymentController@getPayment']);
    $router->post('doSavePayment',  ['uses' => 'PaymentController@doSavePayment']);
});

$router->group(['prefix' => 'customer'], function () use ($router) {
    $router->get('getCustomer',  ['uses' => 'CustomerController@getPayment']);
    $router->post('doSaveCustomer',  ['uses' => 'CustomerController@doSaveCustomer']);
});
