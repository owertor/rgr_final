<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['table', 'user']);
        
        // Поиск
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('table', function($q) use ($search) {
                      $q->where('number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Фильтрация по статусу
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->paginate(10);
        
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $tables = Table::where('status', 'free')->get();
        $dishes = Dish::where('is_available', true)->with('category')->get();
        
        return view('orders.create', compact('tables', 'dishes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'dishes' => 'required|array|min:1',
            'dishes.*.id' => 'required|exists:dishes,id',
            'dishes.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);
        
        return DB::transaction(function () use ($validated, $request) {
            $order = Order::create([
                'table_id' => $validated['table_id'],
                'user_id' => auth()->id(),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'order_date' => now(),
            ]);
            
            $totalAmount = 0;
            $maxCookingTime = 0;
            
            foreach ($validated['dishes'] as $dishItem) {
                $dish = Dish::find($dishItem['id']);
                $quantity = $dishItem['quantity'];
                
                $order->dishes()->attach($dish->id, [
                    'quantity' => $quantity,
                    'price' => $dish->price,
                ]);
                
                $totalAmount += $dish->price * $quantity;
                $maxCookingTime = max($maxCookingTime, $dish->cooking_time);
            }
            
            $order->update([
                'total_amount' => $totalAmount,
                'estimated_completion_time' => now()->addMinutes($maxCookingTime),
            ]);
            
            // Обновляем статус столика
            Table::find($validated['table_id'])->update(['status' => 'occupied']);
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Заказ успешно создан!');
        });
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,paid,cancelled',
        ]);
        
        $order->update(['status' => $request->status]);
        
        // Если заказ оплачен или отменен, освобождаем столик
        if (in_array($request->status, ['paid', 'cancelled'])) {
            $order->table->update(['status' => 'free']);
        }
        
        return back()->with('success', 'Статус заказа обновлен!');
    }

    public function show(Order $order)
    {
        $order->load(['table', 'orderItems.dish.category', 'user']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if (!in_array($order->status, ['pending'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Можно редактировать только заказы со статусом "Ожидает"!');
        }

        $tables = \App\Models\Table::where('status', 'free')
            ->orWhere('id', $order->table_id)
            ->get();
        $dishes = \App\Models\Dish::where('is_available', true)->with('category')->get();
        
        $order->load('orderItems.dish');
        
        return view('orders.edit', compact('order', 'tables', 'dishes'));
    }

    public function update(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Можно редактировать только заказы со статусом "Ожидает"!');
        }

        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'dishes' => 'required|array|min:1',
            'dishes.*.id' => 'required|exists:dishes,id',
            'dishes.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $request, $order) {
            $order->update([
                'table_id' => $validated['table_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Удаляем старые позиции
            $order->orderItems()->delete();
            
            $totalAmount = 0;
            $maxCookingTime = 0;
            
            foreach ($validated['dishes'] as $dishItem) {
                $dish = \App\Models\Dish::find($dishItem['id']);
                $quantity = $dishItem['quantity'];
                
                $order->dishes()->attach($dish->id, [
                    'quantity' => $quantity,
                    'price' => $dish->price,
                ]);
                
                $totalAmount += $dish->price * $quantity;
                $maxCookingTime = max($maxCookingTime, $dish->cooking_time);
            }
            
            $order->update([
                'total_amount' => $totalAmount,
                'estimated_completion_time' => now()->addMinutes($maxCookingTime),
            ]);
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Заказ успешно обновлен!');
        });
    }

    public function destroy(Order $order)
    {
        if (!in_array($order->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Можно удалять только заказы со статусом "Ожидает" или "Отменено"!');
        }

        // Освобождаем столик
        $order->table->update(['status' => 'free']);
        
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Заказ успешно удален!');
    }

    public function invoice(Order $order)
    {
        $order->load(['table', 'orderItems.dish.category', 'user']);
        return view('orders.invoice', compact('order'));
    }
}
