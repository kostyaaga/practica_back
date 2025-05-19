<?php

use Src\Route;

Route::add('GET', '/statistic', [Controller\Site::class, 'statistic'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/signup', [Controller\User::class, 'signup'])
    ->middleware('auth','admin');
Route::add(['GET', 'POST'], '/', [Controller\Site::class, 'list'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/login', [Controller\User::class, 'login']);
Route::add('GET', '/logout', [Controller\User::class, 'logout']);
Route::add(['GET', 'POST'], '/add_building', [Controller\Site::class, 'addBuilding']);
Route::add(['GET', 'POST'], '/edit_building', [Controller\Site::class, 'editBuilding']);

