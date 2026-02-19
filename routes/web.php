<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::middleware(['guest'])->group(function () {
    Route::get('/login',[AuthController::class,'index'])->name('login.index');
    Route::post('/login',[AuthController::class,'login'])->name('login.post');
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
});

