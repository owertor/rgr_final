@extends('layouts.app')

@section('title', 'Категории')

@section('actions')
    @if(auth()->user()->isAdmin())
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Добавить категорию
        </a>
    @endif
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('categories.index') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Поиск по названию или описанию..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Поиск</button>
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Сбросить</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Описание</th>
                                    <th>Количество блюд</th>
                                    <th>Дата создания</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td>{{ Str::limit($category->description ?? '—', 100) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $category->dishes_count }}</span>
                                    </td>
                                    <td>{{ $category->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Категории не найдены</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

