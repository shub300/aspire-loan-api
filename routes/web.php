<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('pinfo', function () {
    phpinfo();
});

Route::get('clear-cache', function () {
    $exitCode = \Illuminate\Support\Facades\Artisan::call('cache:clear');
    $exitCodeView = \Illuminate\Support\Facades\Artisan::call('view:clear');
    $exitCodeRoute = \Illuminate\Support\Facades\Artisan::call('route:clear');
    $exitCodeConfig = \Illuminate\Support\Facades\Artisan::call('config:clear');
    dd($exitCode, $exitCodeView, $exitCodeRoute, $exitCodeConfig);
});

Route::get('/', function () {
    return view('welcome');
});
