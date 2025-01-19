<?php

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PetController::class, 'index'])->name('pet.list');
Route::get('/create', [PetController::class, 'create'])->name('pet.create');
Route::post('/pet/store', [PetController::class, 'store'])->name('pet.store');
Route::get('/pet/{petId}', [PetController::class, 'show'])->name('pet.show');
Route::put('/pet/{petId}', [PetController::class, 'update'])->name('pet.update');
Route::delete('/pet/{petId}', [PetController::class, 'destroy'])->name('pet.destroy');
