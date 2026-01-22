@extends('layouts.app')

@section('title', 'Столик #' . $table->number)

@section('actions')
    @if(auth()->user()->isAdmin())
        <a href="{{ route('tables.edit', $table) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Редактировать
        </a>
    @endif
    <a href="{{ route('tables.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Информация о столике</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Номер:</dt>
                        <dd class="col-sm-7"><strong>#{{ $table->number }}</strong></dd>

                        <dt class="col-sm-5">Вместимость:</dt>
                        <dd class="col-sm-7">{{ $table->capacity }} мест</dd>

                        <dt class="col-sm-5">Статус:</dt>
                        <dd class="col-sm-7">
                            @if($table->status == 'free')
                                <span class="badge bg-success">Свободен</span>
                            @elseif($table->status == 'occupied')
                                <span class="badge bg-danger">Занят</span>
                            @else
                                <span class="badge bg-warning">Зарезервирован</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Всего заказов:</dt>
                        <dd class="col-sm-7">{{ $table->orders->count() }}</dd>
                    </dl>

                    @if($currentOrder)
                    <div class="mt-3">
                        <h6>Текущий заказ:</h6>
                        <div class="alert alert-info">
                            <strong>Заказ #{{ $currentOrder->id }}</strong><br>
                            Статус: <span class="badge bg-{{ $currentOrder->getStatusColor() }}">
                                {{ $currentOrder->getStatusText() }}
                            </span><br>
                            <a href="{{ route('orders.show', $currentOrder) }}" class="btn btn-sm btn-primary mt-2">
                                Подробнее
                            </a>
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <form action="{{ route('tables.update-status', $table) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="mb-2">
                                <label for="status" class="form-label">Изменить статус:</label>
                                <select name="status" class="form-control" id="status">
                                    <option value="free" {{ $table->status == 'free' ? 'selected' : '' }}>Свободен</option>
                                    <option value="occupied" {{ $table->status == 'occupied' ? 'selected' : '' }}>Занят</option>
                                    <option value="reserved" {{ $table->status == 'reserved' ? 'selected' : '' }}>Зарезервирован</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Обновить статус</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>История заказов</h5>
                </div>
                <div class="card-body">
                    @if($table->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID заказа</th>
                                    <th>Дата</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($table->orders->take(10) as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} ₽</td>
                                    <td>
                                        <span class="badge bg-{{ $order->getStatusColor() }}">
                                            {{ $order->getStatusText() }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">На этом столике еще не было заказов</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

