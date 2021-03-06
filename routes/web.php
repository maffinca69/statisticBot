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

$router->post('/telegram-webhook', 'WebhookController@handle');
$router->get('/telegram-webhook', 'WebhookController@handle');
$router->get('/adb5d6f862b9d8af48ffff052b0cfdfda00e9a08bd7d6b76c1f4833c4f0f2494/refresh', 'WebhookController@refresh');
