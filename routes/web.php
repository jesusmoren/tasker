<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/', function () { return redirect()->route('tasks'); });
    Route::get('/dashboard', function () { return redirect()->route('tasks'); })->name('dashboard');
    Route::get('/tasks', function () { return view('tasks'); })->name('tasks');
    Route::get('/tasks_groups', function () { return view('tasks_groups'); })->name('tasks_groups');
    Route::get('/users_management', function () { return view('users_management'); })->name('users_management');

});
