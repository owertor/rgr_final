@extends('layouts.app')

@section('title', 'Ежедневный отчет')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.daily') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="date" class="form-label">Дата:</label>
                                <input type="date" name="date" class="form-control" 
                                       value="{{ $date }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">Показать</button>
                            </div>
                            <div class="col-md-6 text-end">
                                <label class="form-label">&nbsp;</label>
                                <a href="{{ route('reports.monthly') }}" class="btn btn-secondary">
                                    Месячный отчет
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Всего заказов</h6>
                    <h3>{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Выручка</h6>
                    <h3>{{ number_format($totalRevenue, 2) }} ₽</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Средний чек</h6>
                    <h3>{{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00' }} ₽</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Уникальных столиков</h6>
                    <h3>{{ $tablesStats->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Заказы по статусам</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Статус</th>
                                    <th>Количество</th>
                                    <th>Выручка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordersByStatus as $status => $data)
                                <tr>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                'pending' => 'Ожидает',
                                                'preparing' => 'Готовится',
                                                'ready' => 'Готов',
                                                'served' => 'Подано',
                                                'paid' => 'Оплачено',
                                                'cancelled' => 'Отменено',
                                            ];
                                        @endphp
                                        {{ $statusLabels[$status] ?? $status }}
                                    </td>
                                    <td>{{ $data['count'] }}</td>
                                    <td>{{ number_format($data['revenue'], 2) }} ₽</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Популярные блюда</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Блюдо</th>
                                    <th>Кол-во</th>
                                    <th>Выручка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularDishes as $item)
                                <tr>
                                    <td>{{ $item['dish']->name }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>{{ number_format($item['revenue'], 2) }} ₽</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Нет данных</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Статистика по столикам</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Столик</th>
                                    <th>Количество заказов</th>
                                    <th>Выручка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tablesStats as $stat)
                                <tr>
                                    <td>Столик #{{ $stat['table']->number }}</td>
                                    <td>{{ $stat['orders_count'] }}</td>
                                    <td>{{ number_format($stat['revenue'], 2) }} ₽</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Нет данных</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



