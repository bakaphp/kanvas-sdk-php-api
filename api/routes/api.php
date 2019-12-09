<?php

use Baka\Router\RouteGroup;
use Baka\Router\Route;

$publicRoutes = [
    Route::get('/')->controller('IndexController'),
    Route::get('/status')->controller('IndexController')->action('status'),
    Route::post('/auth')->controller('AuthController')->action('login'),

];

$privateRoutes = [
    Route::crud('/users')->notVia('post'),

];

$routeGroup = RouteGroup::from($publicRoutes)
                ->defaultNamespace('Gewaer\Api\Controllers')
                ->defaultPrefix('/v1');

$privateRoutesGroup = RouteGroup::from($privateRoutes)
                ->defaultNamespace('Gewaer\Api\Controllers')
                ->addMiddlewares('auth.jwt@before')
                ->defaultPrefix('/v1');

/**
 * @todo look for a better way to handle this
 */
return array_merge(
    $routeGroup->toCollections(), 
    $privateRoutesGroup->toCollections()
);
