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

$router->group(['prefix' => 'external'], function () use ($router) {
    $router->get('getAll',  ['uses' => 'ExternalController@getAll']);

    $router->get('getProduct',  ['uses' => 'ProductController@getProduct']);
    $router->get('getProductVariant',  ['uses' => 'ProductController@getProductVariant']);
    $router->get('getProductVariantOption',  ['uses' => 'ProductController@getProductVariantOption']);

    $router->get('getVariant',  ['uses' => 'VariantController@getVariant']);
    $router->get('getVariantOption',  ['uses' => 'VariantController@getVariantOption']);

    $router->get('getTransaction',  ['uses' => 'TransactionController@getTransaction']);
    $router->get('getTransactionHistory',  ['uses' => 'TransactionController@getTransactionHistory']);

    $router->get('getCategory',  ['uses' => 'ExternalController@getCategory']);
    $router->get('getClient',  ['uses' => 'ExternalController@getClient']);
    $router->get('getOutlet',  ['uses' => 'ExternalController@getOutlet']);
    $router->get('getCustomer',  ['uses' => 'ExternalController@getCustomer']);
    $router->get('getUser',  ['uses' => 'ExternalController@getUser']);
    $router->get('getPayment',  ['uses' => 'ExternalController@getPayment']);

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
    $router->get('getAllProduct',  ['uses' => 'ExternalController@getAllProduct']);
    $router->get('getProductDetail',  ['uses' => 'ExternalController@getProductDetail']);
    $router->get('getProductImage',  ['uses' => 'ExternalController@getProductImage']);
});