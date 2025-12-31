<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DeviceController::class, 'index'])->name('devices.index');
Route::get('/devices', [DeviceController::class, 'index']);
