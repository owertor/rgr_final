<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Models\Dish;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Получаем данные безопасно
        try {
            $totalOrdersToday = Order::whereDate('created_at', $today)->count();
            $totalRevenueToday = Order::whereDate('created_at', $today)->sum('total_amount') ?? 0;
            $pendingOrders = Order::where('status', 'pending')->count();
            $occupiedTables = Table::where('status', 'occupied')->count();
            
            $recentOrders = Order::with(['table' => function($query) {
                $query->select('id', 'number');
            }])
            ->latest()
            ->limit(5)
            ->get();
            
            // Для популярных блюд, пока просто получаем последние 5
            $popularDishes = Dish::with('category')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($dish) {
                    // Добавляем счетчик заказов для демонстрации
                    $dish->orders_count = rand(1, 20);
                    return $dish;
                });
            
        } catch (\Exception $e) {
            // Если таблицы еще не созданы, возвращаем нулевые значения
            $totalOrdersToday = 0;
            $totalRevenueToday = 0;
            $pendingOrders = 0;
            $occupiedTables = 0;
            $recentOrders = collect();
            $popularDishes = collect();
        }
        
        return view('dashboard', compact(
            'totalOrdersToday',
            'totalRevenueToday',
            'pendingOrders',
            'occupiedTables',
            'recentOrders',
            'popularDishes'
        ));
    }
}