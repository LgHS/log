<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\LogController::class, 'index'])->name('index');
Route::get('/{id}', [App\Http\Controllers\LogController::class, 'show'])->name('show');

Route::group(['middleware' => 'keycloak'], function () {
    Route::post('/', [App\Http\Controllers\LogController::class, 'create'])->name('create');
    Route::delete('/{id}', [App\Http\Controllers\LogController::class, 'delete'])->name('delete');
});

