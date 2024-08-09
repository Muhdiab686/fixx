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
        Route::get('showelectrical', [AdminController::class, 'ShowElectrical']);
        Route::post('updateRequestByAdmin', [AdminController::class,'updateRequestByAdmin']);
        Route::get('show_qr', [AdminController::class,'show_qr']);
        Route::get('report', [AdminController::class, 'report']);
        Route::get('finish_report', [AdminController::class, 'finish_report']);
        Route::get('pending_report', [AdminController::class, 'Pending_report']);
        Route::post('schedling', [AdminController::class, 'Schedling']);
        Route::get('show_schedling', [AdminController::class, 'Showschedling']);
        Route::get('show_schedling_not', [AdminController::class, 'Shownotschedling']);
        Route::post('generateStatistics', [AdminController::class, 'GenerateStatistics']);
        Route::post('generateRatio', [AdminController::class, 'GenerateRatio']);
        Route::get("show_rating", [UserController::class, "show_rating"]);

        Route::post('leaverequests/{id}', [AdminController::class, 'handleLeaveRequest']);
    });
    Route::group(["middleware" => "worker"], function () {
        Route::get('showelectrical', [AdminController::class, 'ShowElectrical']);
        Route::get('show_qr', [AdminController::class,'show_qr']);
        Route::post("updateRequestByWorker", [WorkerController::class,"updateRequestByWorker"]);
        Route::get("showRequest", [WorkerController::class, "Show_request"]);
        Route::get("show_rating", [UserController::class, "show_rating"]);
        Route::post('requestleave', [WorkerController::class, 'requestLeave']);
    });
    Route::group(["middleware" => "user"], function () {
        Route::get('showelectrical', [AdminController::class, 'ShowElectrical']);
        Route::post("storeRequestByUser", [UserController::class,"storeRequestByUser"]);
        Route::post("rate_maintenance_team", [UserController::class, "rate_maintenance_team"]);
        Route::get("show_rating", [UserController::class, "show_rating"]);
        Route::delete("destroyrate", [UserController::class, "destroyrate"]);
    });
    Route::get('showTeam', [AdminController::class, 'Show_Team']);
    Route::get('showWorker', [AdminController::class, 'Show_Worker']);
    

});
