<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dish::with('category');
        
        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Фильтр по категории
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Фильтр по доступности
        if ($request->has('is_available')) {
            $query->where('is_available', $request->is_available == '1');
        }
        
        // Сортировка
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $dishes = $query->paginate(12);
        $categories = Category::all();
        
        return view('dishes.index', compact('dishes', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('dishes.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'category_id' => 'required|exists:categories,id',
            'cooking_time' => 'required|integer|min:1|max:1440',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        // Загрузка изображения
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('dishes', 'public');
        }

        $validated['is_available'] = $request->has('is_available');

        Dish::create($validated);

        return redirect()->route('dishes.index')
            ->with('success', 'Блюдо успешно создано!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dish $dish)
    {
        $dish->load('category', 'orders');
        return view('dishes.show', compact('dish'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dish $dish)
    {
        $categories = Category::all();
        return view('dishes.edit', compact('dish', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dish $dish)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'category_id' => 'required|exists:categories,id',
            'cooking_time' => 'required|integer|min:1|max:1440',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        // Загрузка нового изображения
        if ($request->hasFile('image')) {
            // Удаляем старое изображение
            if ($dish->image) {
                Storage::disk('public')->delete($dish->image);
            }
            $validated['image'] = $request->file('image')->store('dishes', 'public');
        }

        $validated['is_available'] = $request->has('is_available');

        $dish->update($validated);

        return redirect()->route('dishes.index')
            ->with('success', 'Блюдо успешно обновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dish $dish)
    {
        // Проверяем, есть ли заказы с этим блюдом
        if ($dish->orders()->count() > 0) {
            return back()->with('error', 'Невозможно удалить блюдо, так как оно используется в заказах!');
        }

        // Удаляем изображение
        if ($dish->image) {
            Storage::disk('public')->delete($dish->image);
        }

        $dish->delete();

        return redirect()->route('dishes.index')
            ->with('success', 'Блюдо успешно удалено!');
    }
}
