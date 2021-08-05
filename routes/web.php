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

$router->group(['prefix' => 'api'], function () use ($router){
    // User routes
    $router->group(['prefix' => 'user'], function () use ($router){
        $router->post('register', 'AuthController@register');
        $router->post('login', 'AuthController@login');
        $router->group(['middleware' => 'auth'] , function () use ($router){
            $router->post('refresh', 'AuthController@refresh');
            $router->post('logout', 'AuthController@logout');
            $router->post('userinfo', 'AuthController@getAuthUser');
        });
    });

    // Client routes
    $router->group(['prefix' => 'clients'], function () use ($router){
        $router->group(['middleware' => 'auth'] , function () use ($router){
            $router->post('addClient', 'ClientController@store');
            $router->get('allClients', 'ClientController@index');
            $router->get('client/{id}', 'ClientController@show');
            $router->put('client/{id}', 'ClientController@update');
            $router->delete('client/{id}', 'ClientController@destroy');
            // Accounting Routes
            $router->post('client/{id}/addNote', 'AccountingController@store');
            $router->get('client/{id}/allNotes', 'AccountingController@index');
            $router->get('client/{id}/note/{noteId}', 'AccountingController@show');
            $router->put('client/{id}/note/{noteId}', 'AccountingController@update');
            $router->delete('client/{id}/note/{noteId}', 'AccountingController@destroy');
        });
    });
});
