<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Volt::route('/login', 'auth.login')->name('login');
Volt::route('/register', 'auth.register')->name('register');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/categories', function() {
        return view('livewire.categories');
    })->name('categories');

    Route::get('/transactions', function() {
        return view('livewire.transactions');
    })->name('transactions');
});
