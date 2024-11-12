<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ixaya;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ixaya::class,'index'])->name('index');
Route::get("/products-filter",[ixaya::class,'best_selling_products_by_date'])->name('products.filter');
Route::get("/orders-filter",[ixaya::class,'show_products_with_orders'])->name('orders.filter');
Route::get("/orders-record",[ixaya::class,'orders_record'])->name('orders.record');
Route::get('convert-currency',[ixaya::class,'currency_convert'])->name('convert.currency');
Route::get("/orders-detail/{id}",[ixaya::class, 'order_detail'])->name('orders.detail');