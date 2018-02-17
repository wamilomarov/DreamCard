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

$router->get('/test', 'UserController@test');

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
$router->post('/admin/faq', ['middleware' => ['auth', 'admin'], 'uses' => 'AdminController@faqCreate']);
$router->get('/admin/faq/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'AdminController@getFaq']);
$router->post('/admin/faq/update', ['middleware' => ['auth', 'admin'], 'uses' => 'AdminController@faqUpdate']);
$router->get('/admin/faq/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'AdminController@faqDelete']);

$router->get('/cities', ['middleware' => ['auth'], 'uses' => 'UserController@cities']);

$router->post('/users/login', 'UserController@login');
$router->post('/users/facebook/login', 'UserController@fbLogin');
$router->post('/users/google/login', 'UserController@googleLogin');
//$router->post('/users/google/login', 'UserController@gLogin');
$router->post('/users/logout',['middleware' => ['auth', 'user'], 'uses' => 'UserController@logout']);

$router->get('/users/card', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@card']);
$router->post('/users/register', 'UserController@create');
$router->post('/users/update', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@update']);
$router->get('/users/delete/{id}', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@delete']);
$router->get('/users', ['middleware' => ['auth', 'admin'], 'uses' => 'UserController@getUsers']);
$router->get('/users/{id}', ['middleware' => ['auth'], 'uses' => 'UserController@get']);
$router->post('/users/forgot_password', 'UserController@forgotPassword');
$router->post('/users/reset_password', 'UserController@resetPassword');
$router->get('/users/favorite/partners/{category_id}', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@favoritePartners']);
$router->get('/users/favorite/categories', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@favoriteCategories']);
$router->post('/users/favorite/partners', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@addFavoritePartner']);
$router->get('/users/favorite/partners/{partner_id}/delete', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@deleteFavoritePartner']);
$router->post('/users/rate/partners/', ['middleware' => ['auth', 'user'], 'uses' => 'PartnerController@rate']);
$router->get('/users/purchases/history', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@purchasesHistory']);
$router->get('/users/categories/all', ['middleware' => ['auth'], 'uses' => 'UserController@categories']);
$router->get('/users/partners/all', ['middleware' => ['auth'], 'uses' => 'UserController@partners']);


$router->get('/users/card/generate', ['middleware' => ['auth', 'user'], 'uses' => 'UserController@cardGenerate']);



$router->get('/search', ['middleware' => [], 'uses' => 'UserController@search']);

$router->get('/faq', 'UserController@faq');

$router->post('/categories', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@create']);
$router->post('/categories/update', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@update']);
$router->get('/categories/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@delete']);
$router->get('/categories/disable/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@disable']);
$router->get('/categories/restore/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CategoryController@restore']);
$router->get('/categories', 'CategoryController@getCategories');
$router->get('/categories/{id}', ['middleware' => ['auth'], 'uses' => 'CategoryController@get']);
$router->get('/categories/{id}/partners', 'CategoryController@getPartners');

$router->post('/partners', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@create']);
$router->post('/partners/update', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@update']);
$router->get('/partners/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@delete']);
$router->get('/partners/disable/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@disable']);
$router->get('/partners/restore/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@restore']);
$router->get('/partners', 'PartnerController@getPartners');
$router->get('/partners/{id}', 'PartnerController@get');
$router->get('/partners/{id}/campaigns', ['middleware' => ['auth', 'admin'], 'uses' => 'PartnerController@getCampaigns']);


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
$router->post('/cards/upgrade', ['middleware' => ['auth', 'user'], 'uses' => 'CardController@upgrade']);
$router->get('/cards/request_qr', ['middleware' => ['auth', 'user'], 'uses' => 'CardController@requestQr']);
$router->get('/cards/{id}', ['middleware' => ['auth', 'user'], 'uses' => 'CardController@get']);


$router->post('/packages', ['middleware' => ['auth', 'admin'], 'uses' => 'PackageController@create']);
$router->post('/packages/update', ['middleware' => ['auth', 'admin'], 'uses' => 'PackageController@update']);
$router->get('/packages/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PackageController@delete']);
$router->get('/packages', 'PackageController@getPackages');
$router->get('/discounted_packages', 'PackageController@getDiscountedPackages');
$router->get('/packages/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PackageController@get']);
$router->get('/packages/disable/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PackageController@disable']);
$router->get('/packages/restore/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'PackageController@restore']);

$router->post('/news',['middleware' => ['auth', 'admin'], 'uses' => 'NewsController@create']);
$router->post('/news/update',['middleware' => ['auth', 'admin'], 'uses' => 'NewsController@update']);
$router->get('/news/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'NewsController@delete']);
$router->get('/news', 'NewsController@getNews');
$router->get('/news/{id}', 'NewsController@get');


$router->post('/campaigns', ['middleware' => ['auth', 'admin'], 'uses' => 'CampaignController@create']);
$router->post('/campaigns/update', ['middleware' => ['auth', 'admin'], 'uses' => 'CampaignController@update']);
$router->get('/campaigns/delete/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CampaignController@delete']);
$router->get('/campaigns', 'CampaignController@getCampaigns');
$router->get('/campaigns/{id}', ['middleware' => ['auth'], 'uses' => 'CampaignController@get']);
$router->get('/campaigns/disable/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CampaignController@disable']);
$router->get('/campaigns/restore/{id}', ['middleware' => ['auth', 'admin'], 'uses' => 'CampaignController@restore']);

$router->post('/purchase/update', ['middleware' => ['auth'], 'uses' => 'UserController@updatePurchase']);
