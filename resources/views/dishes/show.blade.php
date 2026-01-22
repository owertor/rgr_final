@extends('layouts.app')

@section('title', $dish->name)

@section('actions')
    @if(auth()->user()->isAdmin())
        <a href="{{ route('dishes.edit', $dish) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Редактировать
        </a>
    @endif
    <a href="{{ route('dishes.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            @if($dish->image)
                <img src="{{ asset('storage/' . $dish->image) }}" alt="{{ $dish->name }}" 
                     class="img-fluid rounded mb-3" style="width: 100%; height: 300px; object-fit: cover;">
            @else
                <div class="bg-secondary d-flex align-items-center justify-content-center rounded mb-3" 
                     style="height: 300px;">
                    <i class="bi bi-image text-white" style="font-size: 4rem;"></i>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $dish->name }}</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $dish->id }}</dd>

                        <dt class="col-sm-4">Название:</dt>
                        <dd class="col-sm-8">{{ $dish->name }}</dd>

                        <dt class="col-sm-4">Описание:</dt>
                        <dd class="col-sm-8">{{ $dish->description ?? '—' }}</dd>

                        <dt class="col-sm-4">Цена:</dt>
                        <dd class="col-sm-8">
                            <strong class="text-primary">{{ number_format($dish->price, 2) }} ₽</strong>
                        </dd>

                        <dt class="col-sm-4">Категория:</dt>
                        <dd class="col-sm-8">
                            @if($dish->category)
                                <a href="{{ route('categories.show', $dish->category) }}">
                                    {{ $dish->category->name }}
                                </a>
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4">Время приготовления:</dt>
                        <dd class="col-sm-8">{{ $dish->cooking_time }} минут</dd>

                        <dt class="col-sm-4">Статус:</dt>
                        <dd class="col-sm-8">
                            @if($dish->is_available)
                                <span class="badge bg-success">Доступно</span>
                            @else
                                <span class="badge bg-danger">Недоступно</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Создано:</dt>
                        <dd class="col-sm-8">{{ $dish->created_at->format('d.m.Y H:i') }}</dd>

                        <dt class="col-sm-4">Обновлено:</dt>
                        <dd class="col-sm-8">{{ $dish->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if($dish->orders->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Заказы с этим блюдом ({{ $dish->orders->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID заказа</th>
                                    <th>Столик</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                    <th>Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dish->orders->take(10) as $order)
                                <tr>
                                    <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                                    <td>Столик {{ $order->table->number }}</td>
                                    <td>{{ $order->pivot->quantity }}</td>
                                    <td>{{ number_format($order->pivot->price, 2) }} ₽</td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
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

