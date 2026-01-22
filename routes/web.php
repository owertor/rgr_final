<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Аутентификация
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Защищенные маршруты - доступны всем авторизованным пользователям
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Просмотр доступен всем пользователям (только index и show)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice')->where('order', '[0-9]+');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->where('order', '[0-9]+');
    
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show')->where('category', '[0-9]+');
    
    Route::get('/dishes', [DishController::class, 'index'])->name('dishes.index');
    Route::get('/dishes/{dish}', [DishController::class, 'show'])->name('dishes.show')->where('dish', '[0-9]+');
    
    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::get('/tables/{table}', [TableController::class, 'show'])->name('tables.show')->where('table', '[0-9]+');
    
    // Маршруты только для администраторов (создание, редактирование, удаление)
    // ВАЖНО: Специфичные маршруты (create, edit) должны быть ДО общих маршрутов с параметрами
    Route::middleware([\App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
        // CRUD операции для категорий
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // CRUD операции для блюд
        Route::get('/dishes/create', [DishController::class, 'create'])->name('dishes.create');
        Route::post('/dishes', [DishController::class, 'store'])->name('dishes.store');
        Route::get('/dishes/{dish}/edit', [DishController::class, 'edit'])->name('dishes.edit');
        Route::put('/dishes/{dish}', [DishController::class, 'update'])->name('dishes.update');
        Route::delete('/dishes/{dish}', [DishController::class, 'destroy'])->name('dishes.destroy');
        
        // CRUD операции для столиков
        Route::get('/tables/create', [TableController::class, 'create'])->name('tables.create');
        Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
        Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('tables.edit');
        Route::put('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
        Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
        Route::post('/tables/{table}/update-status', [TableController::class, 'updateStatus'])->name('tables.update-status');
        
        // CRUD операции для заказов
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        
        // Отчеты только для администраторов
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    });
});