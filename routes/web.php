<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadRecipientController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::middleware(['guest'])->group(function () {
    Route::get('/login',[AuthController::class,'index'])->name('login.index');
    Route::post('/login',[AuthController::class,'login'])->name('login.post');
    Route::post('/login/recaptcha',[AuthController::class,'verifyRecaptcha'])->name('login.recaptcha');
    Route::post('/login/otp',[AuthController::class,'verifyOtp'])->name('login.otp');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    Route::get('/',[DashboardController::class,'index'])->name('dashboard.index');

    //Access Management
    //Role
    Route::post('/role/data',[RoleController::class,'getData'])->name('role.data');
    Route::resource('/role', RoleController::class);
    //User
    Route::post('/user/data',[UserController::class,'getData'])->name('user.data');
    Route::post('/user/update-password',[UserController::class,'updatePassword'])->name('user.password');
    Route::resource('/user',UserController::class);

    Route::post('/upload-recipient/data', [UploadRecipientController::class,'getData'])->name('upload-recipient.data');
    Route::post('/upload-recipient/{id}/cancel', [UploadRecipientController::class,'cancel'])->name('upload-recipient.cancel');
    Route::post('/upload-recipient/{id}/data', [UploadRecipientController::class,'getDetailData'])->name('upload-recipient.detail_data');
    Route::post('/upload-recipient/{id}/approve', [UploadRecipientController::class,'approve'])->name('upload-recipient.approve');
    Route::get('/upload-recipient/download', [UploadRecipientController::class,'download'])->name('upload-recipient.download');
    Route::resource('/upload-recipient', UploadRecipientController::class)->only(['index','show','store','create']);
  
});

