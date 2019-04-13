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

/** @var $router Laravel\Lumen\Routing\Router */

$router->group(['middleware' => 'auth:api'], function () use ($router) {
    $router->get('/', function () {
        return config('app.name');
    });
});

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->get('{id: [0-9]+}', 'UserController@get');
    $router->get('/', 'UserController@search');
    $router->post('/', 'UserController@create');
    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->put('{id: [0-9]+}', 'UserController@update');
        $router->delete('{id: [0-9]+}', 'UserController@delete');
    });
});

$router->group(['prefix' => 'tickets'], function () use ($router) {
    $router->get('{id: [0-9]+}/history', 'TicketLogController@history');
    $router->get('{id: [0-9]+}', 'TicketController@get');
    $router->get('/', 'TicketController@search');
    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->post('/', 'TicketController@create');
        $router->put('{id: [0-9]+}', 'TicketController@update');
        $router->delete('{id: [0-9]+}', 'TicketController@delete');
    });
});
