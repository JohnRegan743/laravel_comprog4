<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom Black & White Theme -->
    <style>
        :root {
            --bs-primary: #000000;
            --bs-primary-rgb: 0,0,0;
            --bs-secondary: #6c757d;
            --bs-secondary-rgb: 108,117,125;
            --bs-success: #212529;
            --bs-success-rgb: 33,37,41;
            --bs-info: #000000;
            --bs-info-rgb: 0,0,0;
            --bs-warning: #000000;
            --bs-warning-rgb: 0,0,0;
            --bs-danger: #000000;
            --bs-danger-rgb: 0,0,0;
            --bs-light: #ffffff;
            --bs-light-rgb: 255,255,255;
            --bs-dark: #000000;
            --bs-dark-rgb: 0,0,0;
            --bs-body-color: #000000;
            --bs-body-bg: #ffffff;
            --bs-border-color: #dee2e6;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #000000;
        }
        
        .navbar {
            background-color: #000000 !important;
            border-bottom: 1px solid #333333;
        }
        
        .navbar-brand {
            color: #ffffff !important;
            font-weight: 600;
        }
        
        .navbar-brand:hover {
            color: #cccccc !important;
        }
        
        .navbar-nav .nav-link {
            color: #ffffff !important;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: #cccccc !important;
        }
        
        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            font-weight: 500;
        }
        
        .dropdown-menu {
            background-color: #000000;
            border: 1px solid #333333;
        }
        
        .dropdown-item {
            color: #ffffff !important;
        }
        
        .dropdown-item:hover {
            background-color: #333333;
            color: #ffffff !important;
        }
        
        .btn-primary {
            background-color: #000000;
            border-color: #000000;
            color: #ffffff;
        }
        
        .btn-primary:hover {
            background-color: #333333;
            border-color: #333333;
            color: #ffffff;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #ffffff;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            color: #ffffff;
        }
        
        .card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            color: #000000;
        }
        
        .table {
            color: #000000;
        }
        
        .table thead th {
            border-bottom: 2px solid #000000;
            color: #000000;
            font-weight: 600;
        }
        
        .form-control {
            background-color: #ffffff;
            border: 1px solid #ced4da;
            color: #000000;
        }
        
        .form-control:focus {
            background-color: #ffffff;
            border-color: #000000;
            color: #000000;
            box-shadow: 0 0 0 0.2rem rgba(0,0,0,0.25);
        }
        
        .form-label {
            color: #000000;
            font-weight: 500;
        }
        
        .alert {
            border: none;
        }
        
        .alert-success {
            background-color: #f8f9fa;
            color: #000000;
            border-left: 4px solid #000000;
        }
        
        .alert-danger {
            background-color: #f8f9fa;
            color: #000000;
            border-left: 4px solid #000000;
        }
        
        .alert-info {
            background-color: #f8f9fa;
            color: #000000;
            border-left: 4px solid #000000;
        }
        
        .alert-warning {
            background-color: #f8f9fa;
            color: #000000;
            border-left: 4px solid #000000;
        }
        
        .badge {
            font-weight: 500;
        }
        
        .img-thumbnail {
            border: 1px solid #dee2e6;
        }
        
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #000000;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            color: #000000;
        }
        
        .carousel-indicators [data-bs-target] {
            background-color: #000000;
        }
        
        .page-link {
            color: #000000;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
        }
        
        .page-link:hover {
            color: #000000;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .page-item.active .page-link {
            color: #ffffff;
            background-color: #000000;
            border-color: #000000;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .text-primary {
            color: #000000 !important;
        }
        
        .border {
            border-color: #dee2e6 !important;
        }
        
        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075) !important;
        }
        
        .main-content {
            min-height: calc(100vh - 56px);
            background-color: #ffffff;
        }
        
        .footer {
            background-color: #000000;
            color: #ffffff;
            padding: 20px 0;
            margin-top: auto;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(8px);
            animation: fadeInUp 0.4s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('styles')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('products.index') }}">
                                    <i class="fas fa-box"></i> Products
                                </a>
                            </li>
                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('users.index') }}">
                                        <i class="fas fa-users"></i> Users
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('users.profile') }}">
                                        <i class="fas fa-user"></i> Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="main-content py-4">
            @yield('content')
        </main>
        
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-uppercase fw-bold mb-3">{{ config('app.name', 'OfficeOne Store') }}</h6>
                        <p class="small">Professional inventory management system for modern businesses.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-uppercase fw-bold mb-3">Quick Links</h6>
                        <ul class="list-unstyled small">
                            <li><a href="{{ route('products.index') }}" class="text-white text-decoration-none">Products</a></li>
                            <li><a href="{{ route('users.profile') }}" class="text-white text-decoration-none">Profile</a></li>
                        </ul>
                    </div>
                </div>
                <hr class="border-secondary">
                <div class="text-center small">
                    &copy; {{ date('Y') }} {{ config('app.name', 'OfficeOne Store') }}. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
