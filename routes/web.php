<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::get('/', [ImportController::class, 'index'])->name('dashboard');
Route::post('/upload', [ImportController::class, 'upload'])->name('upload');

// Route::get('/', function () {
//     return view('welcome');
// });
