<?php

use Src\Route;

Route::add('GET', '/statistic', [Controller\SiteController::class, 'statistic'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/signup', [Controller\UserController::class, 'signup'])
    ->middleware('auth','admin');
Route::add(['GET', 'POST'], '/', [Controller\SiteController::class, 'list'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/login', [Controller\UserController::class, 'login']);
Route::add('GET', '/logout', [Controller\UserController::class, 'logout']);
Route::add(['GET', 'POST'], '/add_building', [Controller\SiteController::class, 'addBuilding']);
Route::add(['GET', 'POST'], '/edit_building', [Controller\SiteController::class, 'editBuilding']);

