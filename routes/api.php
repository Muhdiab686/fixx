<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|AddElectrical
*/
Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
Route::group(["middleware" => ["auth:sanctum"]], function(){
    Route::post('addWorker', [AdminController::class,'AddWorker']);
    Route::post('addelectrical', [AdminController::class, 'AddElectrical']);
});
  