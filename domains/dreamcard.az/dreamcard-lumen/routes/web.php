<?php

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

//PAYMENT API

$router->post('/pay', 'UserController@pay');
$router->get('/pay', 'UserController@getPaymentForm');
$router->get('/payment_callback', 'UserController@paymentCallback');
$router->get('/payment_error', 'UserController@paymentError');

//MILLION
$router->get('/million/check', 'UserController@millionCheckId');

$router->post('/admin/login', 'AdminController@login');
$router->post('/admin/register', 'AdminController@create');
$router->post('/admin/logout', 'AdminController@logout');

$router->post('/users/login', 'UserController@login');
$router->post('/users/logout',['middleware' => ['auth'], 'uses' => 'UserController@logout']);

$router->post('/users/register', 'UserController@create');
$router->post('/users/update', 'UserController@update');
$router->get('/users/delete/{id}', 'UserController@delete');
$router->get('/users', 'UserController@getUsers');
$router->get('/users/{id}', 'UserController@get');
$router->post('/users/forgot_password', 'UserController@forgotPassword');
$router->post('/users/reset_password', 'UserController@resetPassword');

$router->post('/categories', 'CategoryController@create');
$router->post('/categories/update', 'CategoryController@update');
$router->get('/categories/delete/{id}', 'CategoryController@delete');
$router->get('/categories/disable/{id}', 'CategoryController@disable');
$router->get('/categories', 'CategoryController@getCategories');
$router->get('/categories/{id}', 'CategoryController@get');

$router->post('/partners', 'PartnerController@create');
$router->post('/partners/update', 'PartnerController@update');
$router->get('/partners/delete/{id}', 'PartnerController@delete');
$router->get('/partners/disable/{id}', 'PartnerController@disable');
$router->get('/partners', 'PartnerController@getPartners');
$router->get('/partners/{id}', 'PartnerController@get');

$router->post('/departments', 'DepartmentController@create');
$router->post('/departments/update', 'DepartmentController@update');
$router->get('/departments/delete/{id}', 'DepartmentController@delete');
$router->get('/departments/disable/{id}', 'DepartmentController@disable');
$router->get('/departments', 'DepartmentController@getDepartments');
$router->get('/departments/{id}', 'DepartmentController@get');

$router->post('/cards', 'CardController@create');
$router->post('/cards/update', 'CardController@update');
$router->get('/cards/delete/{id}', 'CardController@delete');
$router->get('/cards', 'CardController@getCards');
$router->post('/cards/upgrade', 'CardController@upgrade');
$router->get('/cards/request_qr', 'CardController@requestQr');
$router->get('/cards/{id}', 'CardController@get');


$router->post('/packages', 'PackageController@create');
$router->post('/packages/update', 'PackageController@update');
$router->get('/packages/delete/{id}', 'PackageController@delete');
$router->get('/packages', ['middleware' => ['auth', 'department'], 'uses' => 'PackageController@getPackages']);
$router->get('/packages/{id}', 'PackageController@get');

$router->post('/news','NewsController@create');
$router->post('/news','NewsController@update');
$router->get('/news/delete/{id}', 'NewsController@delete');
$router->get('/news','NewsController@getNews');
$router->get('/news/{id}', 'NewsController@get');





