<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GalleryController;

Route::controller(LoginRegisterController::class)->group(function(){
    Route::get('/register', 'register')->name('register');
    Route::get('/login', 'login')->name('login');
    Route::post('/store', 'store')->name('store');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');

    Route::get('/users', 'index')->name('users');
    Route::get('/users/{id}', 'edit')->name('user.edit');
    Route::put('/users/{id}', 'update')->name('user.update');
    Route::delete('/users/{id}', 'destroy')->name('user.destroy');
});

Route::get('/sendemail', [SendEmailController::class, 'index'])->name('sendemail');
Route::post('/postemail', [SendEmailController::class, 'store'])->name('postemail');

Storage::disk('local')->put('file.txt', 'Contents');

Route::resource('gallery', GalleryController::class);
