@extends('layouts.app')

@section('title', 'Столики')

@section('actions')
    @if(auth()->user()->isAdmin())
        <a href="{{ route('tables.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Добавить столик
        </a>
    @endif
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('tables.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Поиск по номеру..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Все статусы</option>
                                    <option value="free" {{ request('status') == 'free' ? 'selected' : '' }}>Свободен</option>
                                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Занят</option>
                                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Зарезервирован</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Фильтровать</button>
                                <a href="{{ route('tables.index') }}" class="btn btn-secondary">Сбросить</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($tables as $table)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-table fs-1"></i>
                    </div>
                    <h4>Столик #{{ $table->number }}</h4>
                    <p class="text-muted">Вместимость: {{ $table->capacity }} мест</p>
                    
                    <div class="mb-2">
                        @if($table->status == 'free')
                            <span class="badge bg-success">Свободен</span>
                        @elseif($table->status == 'occupied')
                            <span class="badge bg-danger">Занят</span>
                        @else
                            <span class="badge bg-warning">Зарезервирован</span>
                        @endif
                    </div>
                    
                    <p class="text-muted small">Заказов: {{ $table->orders_count }}</p>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tables.show', $table) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Подробнее
                        </a>
                        @if(auth()->user()->isAdmin())
                            <div class="btn-group" role="group">
                                <a href="{{ route('tables.edit', $table) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('tables.destroy', $table) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Вы уверены, что хотите удалить этот столик?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12">
            <div class="alert alert-info">Столики не найдены</div>
        </div>
        @endforelse
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-center mt-3">
                {{ $tables->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

