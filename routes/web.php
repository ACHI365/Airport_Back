<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Log::info(config('mail.mailers.smtp'));  // Log the mail configuration

Route::get('/token', function () {
    return csrf_token();
});
// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email', [AuthController::class, 'verifyEmail']);

Route::get('/get-schedules', [ScheduleController::class, 'listSchedules']);

Route::get('/route/{id}', [RouteController::class, 'getById']);
Route::get('/routes', [RouteController::class, 'getAll']);

Route::get('/schedule/{id}', [ScheduleController::class, 'getById']);
Route::get('/schedules', [ScheduleController::class, 'getAll']);

Route::get('/stop/{id}', [StopController::class, 'getById']);
Route::get('/stops', [StopController::class, 'getAll']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/buy-ticket', [TicketController::class, 'buyTicket']);
    Route::get('/get-user-purchases', [UserController::class, 'listPurchases']);
});

Route::middleware('auth:sanctum', AdminMiddleware::class)->group(function () {
    Route::get('/get-all-purchases', [UserController::class, 'listAllPurchases']);

    Route::post('/bus', [BusController::class, 'create']);
    Route::get('/bus/{id}', [BusController::class, 'getById']);
    Route::get('/buses', [BusController::class, 'getAll']);
    Route::put('/bus/{id}', [BusController::class, 'update']);
    Route::delete('/bus/{id}', [BusController::class, 'delete']);

    Route::post('/route', [RouteController::class, 'create']);
    Route::put('/route/{id}', [RouteController::class, 'update']);
    Route::delete('/route/{id}', [RouteController::class, 'delete']);

    Route::post('/schedule', [ScheduleController::class, 'create']);
    Route::put('/schedule/{id}', [ScheduleController::class, 'update']);
    Route::delete('/schedule/{id}', [ScheduleController::class, 'delete']);

    Route::post('/stop', [StopController::class, 'create']);
    Route::put('/stop/{id}', [StopController::class, 'update']);
    Route::delete('/stop/{id}', [StopController::class, 'delete']);

    Route::post('/user', [UserController::class, 'create']);
    Route::get('/user/{id}', [UserController::class, 'getById']);
    Route::get('/users', [UserController::class, 'getAll']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'delete']);
});
