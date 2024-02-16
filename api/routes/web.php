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
    $router->get('get',  ['uses' => 'ProductController@get']);
    $router->get('getVariant',  ['uses' => 'ProductController@getVariant']);
    $router->get('getVariantOption',  ['uses' => 'ProductController@getVariantOption']);
    $router->post('doSave',  ['uses' => 'ProductController@doSave']);
    $router->post('doSaveVariant',  ['uses' => 'ProductController@doSaveVariant']);
    $router->post('doSaveVariantOption',  ['uses' => 'ProductController@doSaveVariantOption']);
});

$router->group(['prefix' => 'variant'], function () use ($router) {
    $router->get('get',  ['uses' => 'VariantController@get']);
    $router->get('getOption',  ['uses' => 'VariantController@getOption']);
    $router->post('doSave',  ['uses' => 'VariantController@doSave']);
    $router->post('doSaveOption',  ['uses' => 'VariantController@doSaveOption']);
});

$router->group(['prefix' => 'transaction'], function () use ($router) {
    $router->get('get',  ['uses' => 'TransactionController@get']);
    $router->get('getHistory',  ['uses' => 'TransactionController@getHistory']);
    $router->post('doSave',  ['uses' => 'TransactionController@doSave']);
    $router->post('doSaveProduct',  ['uses' => 'TransactionController@doSaveProduct']);
    $router->post('doSaveProductVariant',  ['uses' => 'TransactionController@doSaveProductVariant']);
});

$router->group(['prefix' => 'category'], function () use ($router) {
    $router->get('get',  ['uses' => 'CategoryController@get']);
    $router->post('doSave',  ['uses' => 'CategoryController@doSave']);
});

$router->group(['prefix' => 'client'], function () use ($router) {
    $router->get('get',  ['uses' => 'ClientController@get']);
    $router->post('doSave',  ['uses' => 'ClientController@doSave']);
});

$router->group(['prefix' => 'outlet'], function () use ($router) {
    $router->get('get',  ['uses' => 'OutletController@get']);
    $router->post('doSave',  ['uses' => 'OutletController@doSave']);
});

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('get',  ['uses' => 'UserController@get']);
    $router->get('getDetail',  ['uses' => 'UserController@getDetail']);
});

$router->group(['prefix' => 'payment'], function () use ($router) {
    $router->get('get',  ['uses' => 'PaymentController@get']);
    $router->post('doSave',  ['uses' => 'PaymentController@doSave']);
});

$router->group(['prefix' => 'customer'], function () use ($router) {
    $router->get('get',  ['uses' => 'CustomerController@get']);
    $router->post('doSave',  ['uses' => 'CustomerController@doSave']);
});
