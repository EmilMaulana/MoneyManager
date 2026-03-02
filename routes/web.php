<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return response()->json([
        'message' => 'MoneyManager API is running',
        'status' => 'success'
    ]);
});
