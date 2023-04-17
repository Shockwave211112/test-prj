<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

Route::post('/password/forgot', [NewPasswordController::class, 'forgot'])->middleware('guest');
Route::post('/password/reset', [NewPasswordController::class, 'reset'])->middleware('guest')->name('password.reset');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

//DISHES
Route::get('/dishes/menu', [DishController::class, 'menu']);
Route::get('/dishes', [DishController::class, 'index']);
Route::get('/dishes/{id}', [DishController::class, 'show']);

//CATEGORIES
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::group(['middleware' => ['auth:sanctum']], function()
{
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/reports', [ReportsController::class, 'index']);
    //USERS
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/{id}/edit', [UserController::class, 'edit']);
    Route::put('/users/{id}/update', [UserController::class, 'update']);
    Route::delete('/users/{id}/delete', [UserController::class, 'destroy']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);

    //CATEGORIES
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit']);
    Route::put('/categories/{id}/update', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}/delete', [CategoryController::class, 'destroy']);

    //DISHES
    Route::post('/dishes', [DishController::class, 'store']);
    Route::get('/dishes/{id}/edit', [DishController::class, 'edit']);
    Route::put('/dishes/{id}/update', [DishController::class, 'update']);
    Route::delete('/dishes/{id}/delete', [DishController::class, 'destroy']);

    //ORDERS
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::get('/orders/{id}/edit', [OrderController::class, 'edit']);
    Route::put('/orders/{id}/edit/updateDish', [OrderController::class, 'updateDish']);
    Route::put('/orders/{id}/edit/{dish_id}/del', [OrderController::class, 'delDish']);
    Route::put('/orders/{id}/update', [OrderController::class, 'update']);

    Route::delete('/orders/{id}/delete', [OrderController::class, 'destroy']);
});
