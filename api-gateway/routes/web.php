<?php

use Illuminate\Support\Facades\Route;
use \L5Swagger\Http\Controllers\SwaggerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/documentation', [SwaggerController::class, 'api']);
Route::get('/docs/api-docs.json', [SwaggerController::class, 'docs']);
