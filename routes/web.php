<?php

use App\Livewire\TaskManager;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/', TaskManager::class);
