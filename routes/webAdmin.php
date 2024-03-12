<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\eagreement\Admin\AdminController;

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

Route::get('/', [AdminController::class, 'adminLoginPage']);
Route::post('/admin-login-send-otp',[AdminController::class,'adminLoginSendOtp']);
Route::post('/admin-login-verify-otp',[AdminController::class,'adminLoginVerifyOtp']);
Route::middleware(['verify.admin'])->group(function(){
    Route::get('/dashboard', [AdminController::class, 'adminDashboardPage']);
    Route::get('/agreement-list', [AdminController::class, 'agreementListPage']);
});
Route::post('/logout', [AdminController::class, 'adminLogout']);




