<?php
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('products\create');
});

//Route::get('/products/create',[ProductController::class,'create'])->name('products.create');

Route::resource('products',ProductController::class);