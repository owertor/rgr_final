<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ресторан - Система управления заказами')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        body {
            padding-top: 56px;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            width: 16.666667%; /* 2/12 колонок = ширина сайдбара */
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background: #f8f9fa;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 56px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: #333;
            padding: 0.75rem 1rem;
            border-left: 4px solid transparent;
        }
        .sidebar .nav-link:hover {
            color: #007bff;
            background: #e9ecef;
            border-left: 4px solid #007bff;
        }
        .sidebar .nav-link.active {
            color: #007bff;
            background: #e9ecef;
            border-left: 4px solid #007bff;
        }
        .sidebar .nav-link i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        /* Отступ и ширина для основного контента, чтобы не перекрывался сайдбаром */
        @media (min-width: 768px) {
            main {
                margin-left: 16.666667% !important; /* Компенсируем ширину сайдбара (2/12 колонок) */
                width: calc(100% - 16.666667%) !important; /* Ширина минус ширина сайдбара */
                max-width: none !important;
            }
        }
        /* На мобильных устройствах сайдбар скрыт, отступ не нужен */
        @media (max-width: 767.98px) {
            .sidebar {
                display: none;
            }
            main {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
        /* Убираем горизонтальную прокрутку */
        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }
        .container-fluid {
            max-width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }
        /* Ограничиваем ширину таблиц и контента */
        main .table-responsive {
            max-width: 100%;
            overflow-x: auto;
        }
        main .card {
            max-width: 100%;
            overflow-x: hidden;
        }
        main .row {
            margin-left: 0;
            margin-right: 0;
        }
        main [class*="col-"] {
            padding-left: 15px;
            padding-right: 15px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-cup-hot"></i> Ресторан
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? 'Пользователь' }}
                            @if(auth()->user()->isAdmin())
                                <span class="badge bg-danger ms-1">Админ</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Выйти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Основной контейнер -->
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link @if(Route::currentRouteName() == 'dashboard') active @endif" 
                               href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Дашборд
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(str_contains(Route::currentRouteName(), 'orders')) active @endif" 
                               href="{{ route('orders.index') }}">
                                <i class="bi bi-receipt"></i> Заказы
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(str_contains(Route::currentRouteName(), 'tables')) active @endif" 
                               href="{{ route('tables.index') }}">
                                <i class="bi bi-table"></i> Столики
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(str_contains(Route::currentRouteName(), 'dishes')) active @endif" 
                               href="{{ route('dishes.index') }}">
                                <i class="bi bi-book"></i> Блюда
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(str_contains(Route::currentRouteName(), 'categories')) active @endif" 
                               href="{{ route('categories.index') }}">
                                <i class="bi bi-tags"></i> Категории
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link @if(str_contains(Route::currentRouteName(), 'reports')) active @endif" 
                                   href="{{ route('reports.daily') }}">
                                    <i class="bi bi-bar-chart"></i> Отчеты
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Основное содержимое -->
            <main role="main" class="col-12 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title')</h1>
                    @hasSection('actions')
                        <div class="btn-toolbar mb-2 mb-md-0">
                            @yield('actions')
                        </div>
                    @endif
                </div>

                <!-- Уведомления -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Содержимое страницы -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ru.json'
                },
                pageLength: 10,
                responsive: true
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>