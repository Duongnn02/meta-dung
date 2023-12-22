<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\MainController;
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
Route::get('/', [MainController::class, 'index'])->name('index');
Route::get('/email', [MainController::class, 'mail'])->name('mail');
Route::post('/checkMail', [MainController::class, 'checkMail'])->name('checkMail');
Route::post('/change-password', [MainController::class, 'changePass'])->name('changePass');
Route::post('/check2fa', [MainController::class, 'check2FA'])->name('check2FA');
