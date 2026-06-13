<?php

use App\Http\Controllers\CharacterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CharacterController::class, 'index'])->name('characters.index');
Route::get('/characters/{id}', [CharacterController::class, 'details'])->name('characters.details')->whereNumber('id');

require __DIR__.'/auth.php';