<?php

use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;


Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
Route::post('/branch', [BranchController::class, 'store'])->name('branch.store');
Route::get('/branch/{id}', [BranchController::class, 'show'])->name('branch.show');
Route::put('/branch/{id}', [BranchController::class, 'update'])->name('branch.update');
Route::delete('/branch', [BranchController::class, 'destroy'])->name('branch.destroy');
