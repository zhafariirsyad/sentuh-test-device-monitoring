<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::post('/devices/connect',[DeviceController::class, 'connect']);
Route::post('/devices/ping', [DeviceController::class, 'ping']);
Route::post('/devices/disconnect', [DeviceController::class, 'disconnect']);
