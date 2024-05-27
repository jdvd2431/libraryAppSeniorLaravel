<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;

// Ruta para el inicio de sesión
Route::post('/login', [LoginController::class, 'login'])->name('login');

// Ruta para obtener información del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para la gestión de libros
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::post('/books', [BookController::class, 'store']);
Route::put('/books/{id}', [BookController::class, 'update']);
Route::delete('/books/{id}', [BookController::class, 'destroy']);

// Rutas para la gestión de usuarios
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Rutas para la gestión de categorías
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

//libro populares
Route::get('/statistics/popular-books', [StatisticsController::class, 'popularBooks']);
//usuario mas activos
Route::get('/statistics/active-users', [StatisticsController::class, 'activeUsers']);
//categoria de libros mas popular
Route::get('/statistics/loans-by-category', [StatisticsController::class, 'loansByCategory']);

Route::get('/statistics/users-with-overdue-loans', [StatisticsController::class, 'usersWithOverdueLoans']);
Route::get('/statistics/loan-trends', [StatisticsController::class, 'loanTrends']);
Route::get('/statistics/average-loan-duration', [StatisticsController::class, 'averageLoanDuration']);
//Registro de prestamo de libro
Route::post('/loans', [LoanController::class, 'store']);

