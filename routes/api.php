<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login',[App\Http\Controllers\Api\AuthController::class,'login'])->name('api.login')->middleware('guest');
Route::post('/auth/logout',[App\Http\Controllers\Api\AuthController::class,'logout'])->name('api.logout')->middleware(['auth:api']);

Route::get('/user',[App\Http\Controllers\Auth\UserController::class,'user'])->middleware(['auth:api','scopes:identity'])->name('api.user');
Route::get('/accounts/{id?}',[App\Http\Controllers\Api\AuthController::class,'accounts'])->middleware(['auth:api','scopes:identity','otp'])->name('api.user.accounts');

Route::middleware(['auth:api','scopes:user-setting','otp','encrypt'])->group(function(){
    Route::match(['get','post'],'/user/update',[App\Http\Controllers\Auth\UserController::class,'update'])->name('api.user.update');
    Route::match(['get','post'],'/user/password',[App\Http\Controllers\Auth\UserController::class,'password'])->name('api.user.password');
    Route::match(['get','post','delete'],'/user/otp',[App\Http\Controllers\Auth\UserController::class,'otp'])->name('api.user.otp');
    Route::match(['get','delete'],'/user/webauthn/{id?}',[App\Http\Controllers\Auth\UserController::class,'webauthn'])->name('api.user.webauthn');
    Route::match(['get','delete'],'/user/tokens/{id?}',[App\Http\Controllers\Auth\UserController::class,'tokens'])->name('api.user.tokens');
});

Route::post('/2fa',[App\Http\Controllers\Api\AuthController::class,'otp'])->middleware('auth:api')->name('api.2fa');