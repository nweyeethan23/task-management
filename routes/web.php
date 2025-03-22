<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/register', [LoginController::class, 'register'])->name('register');

Route::get('/main', [LoginController::class, 'main'])->name('main.index');
Route::get('/manage_task', [LoginController::class, 'manageTask'])->name('manage.task');


