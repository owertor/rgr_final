<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display daily report
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $dateCarbon = Carbon::parse($date);

        // Статистика за день
        $orders = Order::whereDate('order_date', $dateCarbon)
            ->where('status', '!=', 'cancelled')
            ->with(['table', 'orderItems.dish'])
            ->get();

        // Используем коллекции для обработки данных
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        
        $ordersByStatus = $orders->groupBy('status')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'revenue' => $group->sum('total_amount'),
                ];
            });

        // Популярные блюда (через коллекции)
        $popularDishes = OrderItem::whereHas('order', function($query) use ($dateCarbon) {
                $query->whereDate('order_date', $dateCarbon)
                      ->where('status', '!=', 'cancelled');
            })
            ->select('dish_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('dish_id')
            ->with('dish')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'dish' => $item->dish,
                    'quantity' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                ];
            });

        // Статистика по столикам
        $tablesStats = $orders->groupBy('table_id')
            ->map(function ($group) {
                return [
                    'table' => $group->first()->table,
                    'orders_count' => $group->count(),
                    'revenue' => $group->sum('total_amount'),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return view('reports.daily', compact(
            'date',
            'totalRevenue',
            'totalOrders',
            'ordersByStatus',
            'popularDishes',
            'tablesStats',
            'orders'
        ));
    }

    /**
     * Display monthly report
     */
    public function monthly(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Статистика за месяц
        $orders = Order::whereBetween('order_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->with(['table', 'orderItems.dish'])
            ->get();

        // Используем коллекции
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Статистика по дням
        $dailyStats = $orders->groupBy(function($order) {
                return Carbon::parse($order->order_date)->format('Y-m-d');
            })
            ->map(function ($group) {
                return [
                    'date' => $group->first()->order_date->format('d.m.Y'),
                    'orders_count' => $group->count(),
                    'revenue' => $group->sum('total_amount'),
                ];
            })
            ->sortBy('date')
            ->values();

        // Статистика по статусам
        $ordersByStatus = $orders->groupBy('status')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'revenue' => $group->sum('total_amount'),
                    'percentage' => 0, // будет рассчитано ниже
                ];
            });

        // Процент для каждого статуса
        if ($totalOrders > 0) {
            $ordersByStatus = $ordersByStatus->map(function($item) use ($totalOrders) {
                $item['percentage'] = round(($item['count'] / $totalOrders) * 100, 2);
                return $item;
            });
        }

        // Топ блюд месяца
        $topDishes = OrderItem::whereHas('order', function($query) use ($startDate, $endDate) {
                $query->whereBetween('order_date', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
            })
            ->select('dish_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('dish_id')
            ->with('dish.category')
            ->orderBy('total_revenue', 'desc')
            ->limit(15)
            ->get()
            ->map(function($item) {
                return [
                    'dish' => $item->dish,
                    'quantity' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                ];
            });

        // Статистика по категориям
        $categoryStats = OrderItem::whereHas('order', function($query) use ($startDate, $endDate) {
                $query->whereBetween('order_date', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
            })
            ->with(['dish.category'])
            ->get()
            ->groupBy('dish.category.name')
            ->map(function ($group) {
                return [
                    'category' => $group->first()->dish->category->name,
                    'quantity' => $group->sum('quantity'),
                    'revenue' => $group->sum(function($item) {
                        return $item->quantity * $item->price;
                    }),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return view('reports.monthly', compact(
            'year',
            'month',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'dailyStats',
            'ordersByStatus',
            'topDishes',
            'categoryStats'
        ));
    }
}
