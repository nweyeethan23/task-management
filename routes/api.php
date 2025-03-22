<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;

Route::post('user/register', [UserController::class, 'register'])->name('user.register');
Route::post('user/login',[UserController::class,'login'])->name('user.login');

Route::middleware('auth:sanctum')->prefix('user')->group(function(){

    Route::post('logout',[UserController::class,'logout'])->name('user.logout');
    
});

Route::middleware('auth:sanctum')->prefix('tasks')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});

