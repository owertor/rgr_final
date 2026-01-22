@extends('layouts.app')

@section('title', 'Заказ #' . $order->id)

@section('actions')
    @if(auth()->user()->isAdmin())
        @if($order->status == 'pending')
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Редактировать
            </a>
        @endif
    @endif
    <a href="{{ route('orders.invoice', $order) }}" class="btn btn-info" target="_blank">
        <i class="bi bi-receipt"></i> Счет
    </a>
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Заказ #{{ $order->id }}</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Столик:</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('tables.show', $order->table) }}">
                                Столик #{{ $order->table->number }}
                            </a>
                        </dd>

                        <dt class="col-sm-4">Статус:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $order->getStatusColor() }} fs-6">
                                {{ $order->getStatusText() }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Общая сумма:</dt>
                        <dd class="col-sm-8">
                            <strong class="text-primary fs-5">{{ number_format($order->total_amount, 2) }} ₽</strong>
                        </dd>

                        <dt class="col-sm-4">Дата заказа:</dt>
                        <dd class="col-sm-8">{{ $order->order_date->format('d.m.Y H:i') }}</dd>

                        <dt class="col-sm-4">Ожидаемое время готовности:</dt>
                        <dd class="col-sm-8">
                            @if($order->estimated_completion_time)
                                {{ $order->estimated_completion_time->format('d.m.Y H:i') }}
                            @else
                                —
                            @endif
                        </dd>

                        @if($order->notes)
                        <dt class="col-sm-4">Примечания:</dt>
                        <dd class="col-sm-8">{{ $order->notes }}</dd>
                        @endif

                        <dt class="col-sm-4">Официант:</dt>
                        <dd class="col-sm-8">{{ $order->user->name ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Блюда в заказе</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Блюдо</th>
                                    <th>Количество</th>
                                    <th>Цена за единицу</th>
                                    <th>Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->dish->name }}</strong><br>
                                        <small class="text-muted">{{ $item->dish->category->name }}</small>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }} ₽</td>
                                    <td><strong>{{ number_format($item->quantity * $item->price, 2) }} ₽</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Итого:</th>
                                    <th>{{ number_format($order->total_amount, 2) }} ₽</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Управление заказом</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Изменить статус:</label>
                            <select name="status" class="form-control" id="status">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Ожидает</option>
                                <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Готовится</option>
                                <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Готов</option>
                                <option value="served" {{ $order->status == 'served' ? 'selected' : '' }}>Подано</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Оплачено</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Обновить статус</button>
                    </form>

                    @if(in_array($order->status, ['pending', 'cancelled']))
                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="mt-3" 
                          onsubmit="return confirm('Вы уверены, что хотите удалить этот заказ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Удалить заказ
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

