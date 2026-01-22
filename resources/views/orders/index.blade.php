@extends('layouts.app')

@section('title', 'Заказы')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Заказы</h1>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Новый заказ
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('orders.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Поиск по ID или столику..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Все статусы</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Ожидает</option>
                                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Готовится</option>
                                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Готов</option>
                                    <option value="served" {{ request('status') == 'served' ? 'selected' : '' }}>Подано</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Оплачено</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Фильтровать</button>
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Сбросить</a>
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
                        <table class="table table-hover data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Столик</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                    <th>Ожидаемое время</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>Столик {{ $order->table->number }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} руб.</td>
                                    <td>
                                        <span class="badge bg-{{ $order->getStatusColor() }}">
                                            {{ $order->getStatusText() }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        @if($order->estimated_completion_time)
                                            {{ $order->estimated_completion_time->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('orders.invoice', $order) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection