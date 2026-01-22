@extends('layouts.app')

@section('title', 'Дашборд')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Дашборд</h1>
            <p class="text-muted">Панель управления рестораном</p>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Статистика -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Заказов сегодня</h6>
                            <h3 class="mb-0">{{ $totalOrdersToday ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-receipt fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Выручка сегодня</h6>
                            <h3 class="mb-0">{{ number_format($totalRevenueToday ?? 0, 2) }} ₽</h3>
                        </div>
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Ожидают приготовления</h6>
                            <h3 class="mb-0">{{ $pendingOrders ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-clock fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Занято столов</h6>
                            <h3 class="mb-0">{{ $occupiedTables ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-table fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Последние заказы -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Последние заказы</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Столик</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($recentOrders ?? []) as $order)
                                <tr>
                                    <td>#{{ $order->id ?? 'N/A' }}</td>
                                    <td>Столик {{ $order->table->number ?? 'N/A' }}</td>
                                    <td>{{ number_format($order->total_amount ?? 0, 2) }} ₽</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'preparing' => 'info',
                                                'ready' => 'success',
                                                'served' => 'primary',
                                                'paid' => 'secondary',
                                                'cancelled' => 'danger'
                                            ];
                                            $status = $order->status ?? 'pending';
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('H:i') ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Нет заказов</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Популярные блюда -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Популярные блюда</h5>
                </div>
                <div class="card-body">
                    @forelse(($popularDishes ?? []) as $dish)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $dish->name ?? 'Неизвестное блюдо' }}</h6>
                                <small class="text-muted">
                                    {{ $dish->orders_count ?? 0 }} заказов
                                </small>
                            </div>
                            <span class="badge bg-primary">{{ $dish->price ?? 0 }} ₽</span>
                        </div>
                        <div class="progress mt-2" style="height: 5px;">
                            @php
                                $orderCount = $dish->orders_count ?? 0;
                                $width = min($orderCount * 20, 100);
                            @endphp
                            <div class="progress-bar" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">Нет данных о популярных блюдах</p>
                    @endforelse
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Новый заказ
                            </a>
                            <a href="{{ route('tables.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-table"></i> Управление столиками
                            </a>
                        @else
                            <a href="{{ route('tables.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-table"></i> Столики
                            </a>
                        @endif
                        <a href="{{ route('dishes.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-book"></i> Меню блюд
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection