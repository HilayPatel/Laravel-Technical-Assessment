<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::get('/', [ImportController::class, 'index'])->name('dashboard');
Route::get('/upload', [ImportController::class, 'upload'])->name('upload');
Route::post('/post-upload', [ImportController::class, 'postUpload'])->name('post-upload');
Route::get('/logs/{id}', [ImportController::class, 'logs'])->name('logs');

// Route::get('/', function () {
//     return view('welcome');
// });
