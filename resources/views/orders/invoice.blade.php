<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Счет #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
        .invoice-header {
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="no-print mb-3">
                    <button onclick="window.print()" class="btn btn-primary">Печать</button>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Назад</a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="invoice-header text-center">
                            <h2>РЕСТОРАН</h2>
                            <p class="mb-0">Система учета заказов</p>
                            <p class="mb-0">Телефон: +375 (XX) XXX-XX-XX</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <strong>Счет №:</strong> {{ $order->id }}<br>
                                <strong>Дата:</strong> {{ $order->order_date->format('d.m.Y H:i') }}<br>
                                <strong>Столик:</strong> #{{ $order->table->number }}
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Статус:</strong> 
                                <span class="badge bg-{{ $order->getStatusColor() }}">
                                    {{ $order->getStatusText() }}
                                </span>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>Наименование</th>
                                    <th class="text-center">Кол-во</th>
                                    <th class="text-end">Цена</th>
                                    <th class="text-end">Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->dish->name }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price, 2) }} ₽</td>
                                    <td class="text-end">{{ number_format($item->quantity * $item->price, 2) }} ₽</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">ИТОГО:</th>
                                    <th class="text-end">{{ number_format($order->total_amount, 2) }} ₽</th>
                                </tr>
                            </tfoot>
                        </table>

                        @if($order->notes)
                        <div class="mt-3">
                            <strong>Примечания:</strong>
                            <p>{{ $order->notes }}</p>
                        </div>
                        @endif

                        <div class="mt-4 text-center">
                            <p>Спасибо за посещение!</p>
                            <p class="small text-muted">Время печати: {{ now()->format('d.m.Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



