<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;



//Route::get('/', function () {
  //  return view('welcome');
//});

Route::get('/', function () {
    return view('layouts.master');
});

Route::get('dashboard2', function () {
    return view('dashboard.admin.index');
});

//Route::get('/customer', function () {
  //  return view('admin.customer.add');
//});
Route::get('/dashboard', [AdminController::class, 'index'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/customer', [CustomerController::class, 'index'])->name('index');
    Route::post('/customer/submit', [CustomerController::class, 'create'])->name('create');
    Route::get('/customer/show', [CustomerController::class, 'show']) -> name('customer.show');
    Route::get('/customer/edit/{id}', [CustomerController::class, 'edit']);
    Route::post('/customer/update', [CustomerController::class, 'update']);
    Route::get('/delete/{id}', [CustomerController::class, 'destroy']);

    Route::get('/customer/{id}', [CustomerController::class, 'dataShow'])->name('customer.dataShow');

});

Route::middleware('auth')->group(function () {
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/category/submit', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/show', [CategoryController::class, 'show']);
    Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::get('/category/{id}', [CategoryController::class, 'dataShow'])->name('category.dataShow');
    Route::post('/category/update', [CategoryController::class, 'update'])->name('category.update');
    Route::get('/delete/category/{id}', [CategoryController::class, 'destroy']);

});

Route::middleware('auth')->group(function () {
    Route::get('/brands', [BrandController::class, 'index'])->name('index');
    Route::post('/brands/submit', [BrandController::class, 'create'])->name('create');
    Route::get('/brands/show', [BrandController::class, 'show']) -> name('brands.show');
    Route::get('/brands/edit/{id}', [BrandController::class, 'edit']);
    Route::post('/brands/update', [BrandController::class, 'update']);
    Route::get('/delete/{id}', [BrandController::class, 'destroy']);

    Route::get('/brands/{id}', [BrandController::class, 'dataShow'])->name('brands.dataShow');

}); 

Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/edit/{id}', [ProductController::class, 'edit']);
    Route::put('/products/update/{id}', [ProductController::class, 'update']);
    Route::delete('/products/delete/{id}', [ProductController::class, 'destroy']);
    
    // In web.php
    Route::get('/products/{id}', [ProductController::class, 'dataShow'])->name('product.dataShow');
    
    });

    Route::get('/productlist', [ProductController::class, 'ProductList'])->name('product')->middleware('auth');

Route::get('/view/salelist/{id}', [InvoiceController::class, 'salelist'])->name('salelist');
Route::get('/delete/invoice/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
Route::get('/customerlist', [CustomerController::class, 'customerlist'])->middleware('auth');
Route::get('/sale', [InvoiceController::class, 'sale'])->name('sale')->middleware('auth');
Route::post('/invoices', [InvoiceController::class, 'submitInvoice'])->name('submitInvoice')->middleware('auth');
Route::get('/invoices/show', [InvoiceController::class, 'index'])->name('invoices.index')->middleware('auth');
Route::get('/setting', [SettingController::class, 'edit'])->middleware('auth');
Route::post('/setting/update', [SettingController::class, 'update'])->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
