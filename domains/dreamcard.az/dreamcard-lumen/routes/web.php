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

$router->get('foo', function () {
    return 'Hello World';
});

$router->post(
    '/news',
    'NewsController@create'
);
$router->get(
    '/news',
    'NewsController@getNews'
);

$router->get('/news/{id}', 'NewsController@get');

$router->post('/users/register', 'UserController@create');
$router->post('/users/login', 'UserController@login');
$router->post('/users/update', 'UserController@update');
$router->get('/users', 'UserController@getUsers');
$router->get('/users/{id}', 'UserController@get');

$router->post('/categories', 'CategoryController@create');



