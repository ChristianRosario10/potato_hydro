<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;

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

Route::get('/', function () {
    return redirect()->route('sensor.dashboard');
});

Route::get('/sensor-data', [SensorDataController::class, 'dashboard'])->name('sensor.dashboard');
Route::get('/api/sensor-data', [SensorDataController::class, 'latest']);
Route::post('/api/sensor-data', [SensorDataController::class, 'store']);
