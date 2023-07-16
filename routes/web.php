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
Route::get('/user',[App\Http\Controllers\Auth\UserController::class,'index'])->name('user.index');
Route::post('/user/update',[App\Http\Controllers\Auth\UserController::class,'update'])->name('user.update');
Route::post('/user/password',[App\Http\Controllers\Auth\UserController::class,'password'])->name('user.password');
Route::match(['post','delete'],'/user/otp',[App\Http\Controllers\Auth\UserController::class,'otp'])->name('user.otp');
Route::match(['delete','get'],'/user/webauthn/{id?}',[App\Http\Controllers\Auth\UserController::class,'webauthn'])->name('user.webauthn');

Route::post('/2fa', function () {
    return redirect(URL()->previous());
})->name('2fa')->middleware('otp');

WebAuthn::routes();