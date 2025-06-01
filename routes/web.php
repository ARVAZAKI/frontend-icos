<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/', function(){ 
      return redirect()->route('login.view');
});
Route::get('/login', [AuthController::class, "index"])->name('login.view');
Route::post('/login', [AuthController::class, "login"])->name('login');
Route::get('/token', [AuthController::class, "showtoken"])->name('token');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['api.auth']], function () {
      Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
      Route::post('/branch', [BranchController::class, 'store'])->name('branch.store');
      Route::get('/branch/{id}', [BranchController::class, 'show'])->name('branch.show');
      Route::put('/branch/{id}', [BranchController::class, 'update'])->name('branch.update');
      Route::delete('/branch', [BranchController::class, 'destroy'])->name('branch.destroy');

      Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
      Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
      Route::put('/category', [CategoryController::class, 'update'])->name('category.update');
      Route::delete('/category', [CategoryController::class, 'destroy'])->name('category.destroy');

      Route::get('/product', [ProductController::class, 'index'])->name('product.index');
      Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
      Route::post('/product', [ProductController::class, 'store'])->name('product.store');
      Route::put('/product/{id}', [ProductController::class, 'update'])->name('product.update');
      Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
});