@extends('layouts.app')

@section('title', 'Категория: ' . $category->name)

@section('actions')
    @if(auth()->user()->isAdmin())
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Редактировать
        </a>
    @endif
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $category->name }}</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $category->id }}</dd>

                        <dt class="col-sm-3">Название:</dt>
                        <dd class="col-sm-9">{{ $category->name }}</dd>

                        <dt class="col-sm-3">Описание:</dt>
                        <dd class="col-sm-9">{{ $category->description ?? '—' }}</dd>

                        <dt class="col-sm-3">Количество блюд:</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-info">{{ $category->dishes->count() }}</span>
                        </dd>

                        <dt class="col-sm-3">Создано:</dt>
                        <dd class="col-sm-9">{{ $category->created_at->format('d.m.Y H:i') }}</dd>

                        <dt class="col-sm-3">Обновлено:</dt>
                        <dd class="col-sm-9">{{ $category->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if($category->dishes->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Блюда в этой категории</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Цена</th>
                                    <th>Время приготовления</th>
                                    <th>Доступность</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->dishes as $dish)
                                <tr>
                                    <td>{{ $dish->name }}</td>
                                    <td>{{ number_format($dish->price, 2) }} ₽</td>
                                    <td>{{ $dish->cooking_time }} мин.</td>
                                    <td>
                                        @if($dish->is_available)
                                            <span class="badge bg-success">Доступно</span>
                                        @else
                                            <span class="badge bg-danger">Недоступно</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dishes.show', $dish) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

