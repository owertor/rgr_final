@extends('layouts.app')

@section('title', 'Создать столик')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>Создать новый столик</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tables.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="number" class="form-label">Номер столика <span class="text-danger">*</span></label>
                            <input type="number" min="1" class="form-control @error('number') is-invalid @enderror" 
                                   id="number" name="number" value="{{ old('number') }}" required>
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="capacity" class="form-label">Вместимость (количество мест) <span class="text-danger">*</span></label>
                            <input type="number" min="1" max="50" 
                                   class="form-control @error('capacity') is-invalid @enderror" 
                                   id="capacity" name="capacity" value="{{ old('capacity') }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Статус <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="free" {{ old('status') == 'free' ? 'selected' : '' }}>Свободен</option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Занят</option>
                                <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Зарезервирован</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tables.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Создать
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



