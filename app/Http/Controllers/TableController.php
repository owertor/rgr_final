<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Table::withCount('orders');
        
        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%");
            });
        }
        
        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Сортировка
        $sortBy = $request->get('sort_by', 'number');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $tables = $query->paginate(15);
        
        return view('tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|integer|min:1|unique:tables,number',
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:free,occupied,reserved',
        ]);

        Table::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Столик успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        $table->load(['orders' => function($query) {
            $query->latest()->limit(10);
        }, 'orders.orderItems.dish']);
        
        $currentOrder = $table->getCurrentOrder();
        
        return view('tables.show', compact('table', 'currentOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        return view('tables.edit', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'number' => 'required|integer|min:1|unique:tables,number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:free,occupied,reserved',
        ]);

        $table->update($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Столик успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        // Проверяем, есть ли активные заказы
        $activeOrders = $table->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->count();
            
        if ($activeOrders > 0) {
            return back()->with('error', 'Невозможно удалить столик, так как на нем есть активные заказы!');
        }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Столик успешно удален!');
    }

    /**
     * Update table status
     */
    public function updateStatus(Request $request, Table $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:free,occupied,reserved',
        ]);

        $table->update(['status' => $validated['status']]);

        return back()->with('success', 'Статус столика обновлен!');
    }
}
