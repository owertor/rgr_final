@extends('layouts.app')

@section('title', 'Месячный отчет')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.monthly') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="year" class="form-label">Год:</label>
                                <select name="year" class="form-control" id="year" required>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="month" class="form-label">Месяц:</label>
                                <select name="month" class="form-control" id="month" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(null, $m)->locale('ru')->monthName }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">Показать</button>
                            </div>
                            <div class="col-md-4 text-end">
                                <label class="form-label">&nbsp;</label>
                                <a href="{{ route('reports.daily') }}" class="btn btn-secondary">
                                    Ежедневный отчет
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
                    <h3>{{ number_format($averageOrderValue, 2) }} ₽</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Период</h6>
                    <h3>{{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Статистика по дням</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Заказов</th>
                                    <th>Выручка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyStats as $stat)
                                <tr>
                                    <td>{{ $stat['date'] }}</td>
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
                                    <th>%</th>
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
                                    <td>{{ $data['percentage'] }}%</td>
                                    <td>{{ number_format($data['revenue'], 2) }} ₽</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Топ-15 блюд по выручке</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Блюдо</th>
                                    <th>Категория</th>
                                    <th>Кол-во</th>
                                    <th>Выручка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topDishes as $item)
                                <tr>
                                    <td>{{ $item['dish']->name }}</td>
                                    <td>{{ $item['dish']->category->name ?? '—' }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td><strong>{{ number_format($item['revenue'], 2) }} ₽</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Нет данных</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Статистика по категориям</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Категория</th>
                                    <th>Кол-во</th>
                                    <th>Выручка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryStats as $stat)
                                <tr>
                                    <td>{{ $stat['category'] }}</td>
                                    <td>{{ $stat['quantity'] }}</td>
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



