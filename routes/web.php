<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/shared', [DashboardController::class, 'shared'])->name('shared');  // Add this line
    Route::post('/upload', [FileController::class, 'upload'])->name('upload');
    Route::get('/users/search', [FileController::class, 'searchUsers'])->name('users.search');
    Route::post('/files/{file}/share', [FileController::class, 'share'])->name('files.share');
    Route::get('/files/{file}/permissions', [FileController::class, 'getPermissions'])->name('files.permissions');
    Route::match(['get', 'post'], '/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
