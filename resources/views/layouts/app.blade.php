<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: white;
            transition: all 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-menu {
            padding: 0.5rem 0;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 3px solid #fbbf24;
        }
        .sidebar-menu a i {
            width: 24px;
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background: #f1f5f9;
        }
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-content {
            padding: 1.5rem;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h4 class="fw-bold mb-0">
            <i class="fa-solid fa-store me-2 text-yellow-400"></i> POS SYSTEM
        </h4>
    </div>
    <div class="sidebar-menu">

        {{--  Admin နဲ့ Manager သာလျှင်--}}
        @role('admin','manager')
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            {{ __('messages.dashboard') }}
        </a>
        @endrole

        <a href="{{ route('products.pos') }}" class="{{ request()->routeIs('products.pos') ? 'active' : '' }}">
            <i class="fa-solid fa-cash-register"></i>
            {{ __('messages.pos_counter_short') ?? 'POS Counter' }}
        </a>

        {{-- Admin နဲ့ Manager သာလျှင်--}}
        @role('admin','manager')
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked"></i>
            {{ __('messages.products') }}
        </a>
        @endrole
        <a href="{{ route('sales.history') }}" class="{{ request()->routeIs('sales.history') ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left"></i>
            {{ __('messages.history') }}
        </a>

    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center">
            <h5 class="fw-bold mb-0">@yield('page_title', 'Dashboard')</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex gap-1">
                <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm btn-outline-secondary @if(app()->getLocale() == 'en') active bg-secondary text-white @endif">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'mm') }}" class="btn btn-sm btn-outline-secondary @if(app()->getLocale() == 'mm') active bg-secondary text-white @endif">🇲🇲 MM</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold text-secondary"><i class="fa-solid fa-user-circle me-1"></i> {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.logout_confirm') }}');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger fw-semibold">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> {{ __('messages.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="page-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
