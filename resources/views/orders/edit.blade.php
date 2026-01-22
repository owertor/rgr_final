@extends('layouts.app')

@section('title', 'Редактировать заказ #' . $order->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Редактировать заказ #{{ $order->id }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="table_id" class="form-label">Столик <span class="text-danger">*</span></label>
                                <select class="form-control @error('table_id') is-invalid @enderror" 
                                        id="table_id" name="table_id" required>
                                    <option value="">Выберите столик</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" 
                                                {{ old('table_id', $order->table_id) == $table->id ? 'selected' : '' }}>
                                            Столик #{{ $table->number }} ({{ $table->capacity }} мест) - {{ $table->status == 'free' ? 'Свободен' : 'Занят' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('table_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Блюда <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($dishes->groupBy('category.name') as $categoryName => $categoryDishes)
                                <div class="col-md-12 mb-3">
                                    <h6>{{ $categoryName }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Блюдо</th>
                                                    <th>Цена</th>
                                                    <th>Время</th>
                                                    <th>Количество</th>
                                                    <th>Добавить</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($categoryDishes as $dish)
                                                @php
                                                    $existingItem = $order->orderItems->firstWhere('dish_id', $dish->id);
                                                    $currentQty = $existingItem ? $existingItem->quantity : 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $dish->name }}</strong><br>
                                                        <small class="text-muted">{{ Str::limit($dish->description, 50) }}</small>
                                                    </td>
                                                    <td>{{ number_format($dish->price, 2) }} ₽</td>
                                                    <td>{{ $dish->cooking_time }} мин.</td>
                                                    <td>
                                                        <input type="number" min="0" max="99" value="{{ $currentQty }}" 
                                                               class="form-control form-control-sm dish-quantity" 
                                                               data-dish-id="{{ $dish->id }}" 
                                                               data-dish-price="{{ $dish->price }}"
                                                               data-dish-name="{{ $dish->name }}"
                                                               style="width: 80px;">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary add-dish" 
                                                                data-dish-id="{{ $dish->id }}"
                                                                data-dish-price="{{ $dish->price }}"
                                                                data-dish-name="{{ $dish->name }}">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Выбранные блюда:</label>
                            <div id="selectedDishes" class="border p-3 rounded" style="min-height: 100px;">
                                <p class="text-muted">Загрузка...</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Примечания</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedDishes = {};

// Загружаем текущие блюда в заказ
@foreach($order->orderItems as $item)
selectedDishes[{{ $item->dish_id }}] = {
    id: {{ $item->dish_id }},
    name: "{{ $item->dish->name }}",
    price: {{ $item->price }},
    quantity: {{ $item->quantity }}
};
@endforeach

document.querySelectorAll('.add-dish').forEach(button => {
    button.addEventListener('click', function() {
        const dishId = this.dataset.dishId;
        const dishPrice = parseFloat(this.dataset.dishPrice);
        const dishName = this.dataset.dishName;
        const quantityInput = document.querySelector(`.dish-quantity[data-dish-id="${dishId}"]`);
        let quantity = parseInt(quantityInput.value) || 0;
        quantity++;
        quantityInput.value = quantity;
        
        selectedDishes[dishId] = {
            id: dishId,
            name: dishName,
            price: dishPrice,
            quantity: quantity
        };
        
        updateSelectedDishes();
    });
});

document.querySelectorAll('.dish-quantity').forEach(input => {
    input.addEventListener('change', function() {
        const dishId = this.dataset.dishId;
        const quantity = parseInt(this.value) || 0;
        
        if (quantity > 0) {
            selectedDishes[dishId] = {
                id: dishId,
                name: this.dataset.dishName,
                price: parseFloat(this.dataset.dishPrice),
                quantity: quantity
            };
        } else {
            delete selectedDishes[dishId];
        }
        
        updateSelectedDishes();
    });
});

function updateSelectedDishes() {
    const container = document.getElementById('selectedDishes');
    const form = document.getElementById('orderForm');
    
    // Очищаем старые скрытые поля
    document.querySelectorAll('input[name^="dishes"]').forEach(input => input.remove());
    
    if (Object.keys(selectedDishes).length === 0) {
        container.innerHTML = '<p class="text-muted">Нет выбранных блюд</p>';
        return;
    }
    
    let html = '<ul class="list-unstyled mb-0">';
    let total = 0;
    
    Object.values(selectedDishes).forEach((dish, index) => {
        const subtotal = dish.price * dish.quantity;
        total += subtotal;
        
        html += `
            <li class="mb-2 d-flex justify-content-between align-items-center">
                <div>
                    <strong>${dish.name}</strong> x ${dish.quantity} 
                    <span class="text-muted">(${dish.price.toFixed(2)} ₽)</span>
                </div>
                <span class="badge bg-primary">${subtotal.toFixed(2)} ₽</span>
            </li>
        `;
        
        // Добавляем скрытые поля
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = `dishes[${index}][id]`;
        idInput.value = dish.id;
        form.appendChild(idInput);
        
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = `dishes[${index}][quantity]`;
        quantityInput.value = dish.quantity;
        form.appendChild(quantityInput);
    });
    
    html += `<li class="mt-3 pt-2 border-top"><strong>Итого: ${total.toFixed(2)} ₽</strong></li>`;
    html += '</ul>';
    
    container.innerHTML = html;
}

// Инициализация при загрузке
updateSelectedDishes();

document.getElementById('orderForm').addEventListener('submit', function(e) {
    if (Object.keys(selectedDishes).length === 0) {
        e.preventDefault();
        alert('Пожалуйста, выберите хотя бы одно блюдо!');
        return false;
    }
});
</script>
@endpush
@endsection



