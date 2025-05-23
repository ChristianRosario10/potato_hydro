<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\API\SensorDataController as APISensorDataController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('posts', PostController::class);
Route::apiResource('sensor-data', SensorDataController::class);

// Individual Sensor Data Routes
Route::get('/sensor-readings', [SensorDataController::class, 'getLatest']);
Route::get('/sensor-readings/all', [SensorDataController::class, 'getAll']);
Route::post('/sensor-readings', [SensorDataController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
