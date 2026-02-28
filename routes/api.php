<?php

use App\Http\Controllers\Api\EventQrController;
use App\Http\Controllers\Api\MyEventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ===== CONTROLLERS =====
use App\Http\Controllers\Api\PublicEventController;
use App\Http\Controllers\Api\EventRegistrationController;
use App\Http\Controllers\Api\EventAttendanceController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\UserAuthController;

// ===== DEFAULT =====
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ===== PUBLIC EVENT ROUTES =====
Route::get('/events',        [PublicEventController::class, 'index']);
Route::get('/events/{slug}', [PublicEventController::class, 'show']);
Route::get('/sliders',       [PublicEventController::class, 'sliders']);

// ===== AUTH USER (MAHASISWA) =====
Route::post('/mahasiswa/login',    [UserAuthController::class, 'login']);
Route::post('/mahasiswa/register', [UserAuthController::class, 'register']);

// ===== PROTECTED =====
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mahasiswa/me',     [UserAuthController::class, 'me']);
    Route::post('/mahasiswa/logout',[UserAuthController::class, 'logout']);

    Route::post('/events/{slug}/register',   [EventRegistrationController::class, 'store']);
    Route::post('/events/{slug}/attendance', [EventAttendanceController::class, 'store']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/certificate/{slug}', [CertificateController::class, 'show']);
});


Route::middleware('auth:sanctum')->post('/events/{slug}/generate-qr', [EventQrController::class, 'generate']);
Route::middleware('auth:sanctum')->post('/attendance/scan', [EventAttendanceController::class, 'store']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mahasiswa/my-events', [MyEventController::class, 'index']);
});


use App\Http\Controllers\Api\InfoApiController;

Route::get('/infos', [InfoApiController::class, 'index']);
Route::get('/infos/{id}', [InfoApiController::class, 'show']);


use App\Http\Controllers\Api\DocumentationApiController;

Route::get('/documentation', [DocumentationApiController::class, 'index']);
