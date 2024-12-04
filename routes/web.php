<?php

use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
//use App\Http\Controllers\CategoryController;
//use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
//use App\Http\Controllers\ProductController;
//use App\Http\Controllers\InvoiceController;
//use App\Http\Controllers\SettingController;



//Route::get('/', function () {
  //  return view('welcome');
//});

Route::get('/', function () {
    return view('layouts.master');
});

Route::get('dashboard2', function () {
    return view('dashboard.admin.index');
});

Route::get('/customer', function () {
    return view('admin.customer.add');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
