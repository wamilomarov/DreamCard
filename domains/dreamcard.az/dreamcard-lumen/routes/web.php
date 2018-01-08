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
$router->post('/admin/logout', ['middleware' => ['auth', 'admin'], 'uses' => 'AdminController@logout']);

$router->post('/users/login', 'UserController@login');
$router->post('/users/logout',['middleware' => ['auth', 'user'], 'uses' => 'UserController@logout']);

$router->post('/users/register', 'UserController@create');
$router->post('/users/update', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@update']);
$router->get('/users/delete/{id}', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@delete']);
$router->get('/users', ['middleware' => ['auth', 'admin'], 'uses' => 'UserController@getUsers']);
$router->get('/users/{id}', ['middleware' => ['auth'], 'uses' => 'UserController@get']);
$router->post('/users/forgot_password', 'UserController@forgotPassword');
$router->post('/users/reset_password', 'UserController@resetPassword');
$router->get('/users/favorite/partners/{category_id}', 'UserController@favoritePartners');
$router->get('/users/favorite/categories', 'UserController@favoriteCategories');
$router->post('/users/favorite/partner', 'UserController@addFavoritePartner');

$router->post('/categories', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@create']);
$router->post('/categories/update', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@update']);
$router->get('/categories/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@delete']);
$router->get('/categories/disable/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@disable']);
$router->get('/categories/restore/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@restore']);
$router->get('/categories', ['middleware' => ['auth'], 'uses' => 'CategoryController@getCategories']);
$router->get('/categories/{id}', ['middleware' => ['auth'], 'uses' => 'CategoryController@get']);

$router->post('/partners', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@create']);
$router->post('/partners/update', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@update']);
$router->get('/partners/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@delete']);
$router->get('/partners/disable/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@disable']);
$router->get('/partners/restore/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@restore']);
$router->get('/partners', ['middleware' => ['auth'], 'uses' => 'PartnerController@getPartners']);
$router->get('/partners/{id}', 'PartnerController@get');


$router->post('/departments', 'DepartmentController@create');
$router->post('/departments/update', 'DepartmentController@update');
$router->get('/departments/delete/{id}', 'DepartmentController@delete');
$router->get('/departments/disable/{id}', 'DepartmentController@disable');
$router->get('/departments/restore/{id}', 'DepartmentController@restore');
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
$router->get('/packages', ['middleware' => ['auth'], 'uses' => 'PackageController@getPackages']);
$router->get('/packages/{id}', 'PackageController@get');

$router->post('/news','NewsController@create');
$router->post('/news/update','NewsController@update');
$router->get('/news/delete/{id}', 'NewsController@delete');
$router->get('/news','NewsController@getNews');
$router->get('/news/{id}', 'NewsController@get');


$router->post('/campaign', 'CampaignController@create');
$router->post('/campaign/update', 'CampaignController@update');
$router->get('/campaign/delete/{id}', 'CampaignController@delete');
$router->get('/campaigns', 'CampaignController@getCampaign');
$router->get('/campaigns/{id}', 'CampaignController@get');

