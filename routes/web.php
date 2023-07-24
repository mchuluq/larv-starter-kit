<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\WebAuthn;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'verify' => true,
    'register' => true,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/user',[App\Http\Controllers\Auth\UserController::class,'index'])->middleware(['auth','otp'])->name('user.index');
Route::get('/accounts/{id?}',[App\Http\Controllers\Auth\UserController::class,'accounts'])->middleware(['auth','otp'])->name('user.accounts');

Route::middleware(['auth','verified','otp','encrypt'])->group(function(){
    Route::match(['get','post'],'/user/update',[App\Http\Controllers\Auth\UserController::class,'update'])->name('user.update');
    Route::match(['get','post'],'/user/password',[App\Http\Controllers\Auth\UserController::class,'password'])->name('user.password');
    Route::match(['get','post','delete'],'/user/otp',[App\Http\Controllers\Auth\UserController::class,'otp'])->name('user.otp');
    Route::match(['get','delete'],'/user/webauthn/{id?}',[App\Http\Controllers\Auth\UserController::class,'webauthn'])->name('user.webauthn');
    Route::match(['get','delete'],'/user/tokens/{id?}',[App\Http\Controllers\Auth\UserController::class,'tokens'])->name('user.tokens');
});

Route::post('/2fa', function () {
    return redirect(URL()->previous());
})->name('2fa')->middleware('otp');

Route::get('file/{filepath}',[App\Http\Controllers\FileController::class,'file'])->where('filepath', '.*')->name('file.index');

WebAuthn::routes();