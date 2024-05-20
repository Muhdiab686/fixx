<?php

use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Worker\WorkerController;
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
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::group(["middleware" => "admin"], function () {
        Route::post('addWorker', [AdminController::class, 'AddWorker']);
        Route::delete('deleteWorker', [AdminController::class, 'DeleteWorker']);
        Route::post('addelectrical', [AdminController::class, 'AddElectrical']);
        Route::post('updateRequestByAdmin', [AdminController::class,'updateRequestByAdmin']);
        Route::get('show_qr', [AdminController::class,'show_qr']);
    });
    Route::group(["middleware" => "worker"], function () {
        Route::post("updateRequestByWorker", [WorkerController::class,"updateRequestByWorker"]);
    });
    Route::group(["middleware" => "user"], function () {
        Route::post("storeRequestByUser", [UserController::class,"storeRequestByUser"]);
    });
    Route::get('showTeam', [AdminController::class, 'Show_Team']);
    Route::get('showWorker', [AdminController::class, 'Show_Worker']);
    

});
