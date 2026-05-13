<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;



// Rute User Area
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/event/1', [EventController::class,'show'])->name('events.show');
Route::get('/checkout', [EventController::class,'checkout'])->name('checkout');
Route::get('/my-ticket', [EventController::class, 'ticket'])->name('ticket');

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    // Catatan: Dashboard & Login Auth di kemudian hari akan menempati blok ini juga
    Route::resource('events', EventAdminController::class);

    Route::get('/', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/transactions', [DashboardController::class,'indexTransaction'])->name('transactions.index');
    // dan seterusnya...
});