@extends('layouts.app')

@section('title', 'Редактировать блюдо')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Редактировать блюдо</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('dishes.update', $dish) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $dish->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $dish->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Цена (₽) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="999999.99" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $dish->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cooking_time" class="form-label">Время приготовления (мин.) <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="1440" 
                                       class="form-control @error('cooking_time') is-invalid @enderror" 
                                       id="cooking_time" name="cooking_time" value="{{ old('cooking_time', $dish->cooking_time) }}" required>
                                @error('cooking_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Категория <span class="text-danger">*</span></label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Выберите категорию</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $dish->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Изображение</label>
                            @if($dish->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $dish->image) }}" alt="{{ $dish->name }}" 
                                         class="img-thumbnail" style="max-height: 200px;">
                                    <p class="text-muted small">Текущее изображение</p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Максимальный размер: 2MB. Форматы: JPEG, PNG, JPG, GIF</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_available" 
                                       name="is_available" value="1" 
                                       {{ old('is_available', $dish->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    Блюдо доступно
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dishes.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

