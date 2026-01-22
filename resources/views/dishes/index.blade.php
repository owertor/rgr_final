@extends('layouts.app')

@section('title', 'Блюда')

@section('actions')
    @if(auth()->user()->isAdmin())
        <a href="{{ route('dishes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Добавить блюдо
        </a>
    @endif
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('dishes.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Поиск по названию..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="category_id" class="form-control">
                                    <option value="">Все категории</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_available" class="form-control">
                                    <option value="">Все</option>
                                    <option value="1" {{ request('is_available') == '1' ? 'selected' : '' }}>Доступны</option>
                                    <option value="0" {{ request('is_available') == '0' ? 'selected' : '' }}>Недоступны</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Фильтровать</button>
                                <a href="{{ route('dishes.index') }}" class="btn btn-secondary">Сбросить</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($dishes as $dish)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                @if($dish->image)
                    <img src="{{ asset('storage/' . $dish->image) }}" class="card-img-top" alt="{{ $dish->name }}" style="height: 200px; object-fit: cover;">
                @else
                    <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $dish->name }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($dish->description ?? '—', 80) }}</p>
                    <div class="mb-2">
                        <span class="badge bg-primary">{{ number_format($dish->price, 0) }} ₽</span>
                        <span class="badge bg-info">{{ $dish->cooking_time }} мин.</span>
                        @if($dish->category)
                            <span class="badge bg-secondary">{{ $dish->category->name }}</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        @if($dish->is_available)
                            <span class="badge bg-success">Доступно</span>
                        @else
                            <span class="badge bg-danger">Недоступно</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('dishes.show', $dish) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('dishes.edit', $dish) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('dishes.destroy', $dish) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Вы уверены, что хотите удалить это блюдо?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12">
            <div class="alert alert-info">Блюда не найдены</div>
        </div>
        @endforelse
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-center mt-3">
                {{ $dishes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

