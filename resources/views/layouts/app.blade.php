<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Greenaxe CRM')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #fafbfc;
            display: flex;
            height: 100vh;
            color: #1f2937;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 28px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .sidebar-logo {
            font-size: 22px;
            font-weight: 800;
            color: #16a34a;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 24px 0;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-title {
            padding: 12px 24px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.5px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }

        .sidebar-nav a.active {
            background: rgba(22, 163, 74, 0.15);
            color: #4ade80;
            border-left-color: #16a34a;
        }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .logout-btn {
            width: 100%;
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(239, 68, 68, 0.5);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Header */
        header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
        }

        header h1 {
            font-size: 24px;
            color: #1f2937;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-size: 13px;
        }

        .header-user-name {
            color: #1f2937;
            font-weight: 600;
        }

        .header-user-email {
            color: #6b7280;
            font-size: 12px;
            margin-top: 2px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        /* Page Content */
        .page-content {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Common Styles */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary {
            background: #16a34a;
            color: white;
        }

        .btn-primary:hover {
            background: #15803d;
        }

        .btn-secondary {
            background: white;
            color: #1f2937;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #ecfdf5;
            color: #15803d;
            border-color: #16a34a;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-color: #dc2626;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1f2937;
            font-weight: 600;
            font-size: 13px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            transition: all 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="datetime-local"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 240px;
            }

            .main-content {
                margin-left: 240px;
            }

            .page-content {
                padding: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">Greenaxe</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Głównie</div>
                <a href="{{ route('dashboard') }}" class="@if(Route::currentRouteName() === 'dashboard') active @endif">Pulpit</a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">CRM</div>
                <a href="{{ route('clients.index') }}" class="@if(strpos(Route::currentRouteName(), 'clients') !== false) active @endif">Klienci</a>
                <a href="{{ route('garden-projects.index') }}" class="@if(strpos(Route::currentRouteName(), 'garden-projects') !== false) active @endif">Projekty</a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">Sprzedaż</div>
                <a href="#" style="color: #6b7280;">Oferty (Soon)</a>
                <a href="#" style="color: #6b7280;">Szacunki (Soon)</a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Wyloguj się</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header>
            <h1>@yield('page-title', 'Pulpit')</h1>
            <div class="header-user">
                <div class="header-user-info">
                    <div class="header-user-name">{{ Auth::user()->name }}</div>
                    <div class="header-user-email">{{ Auth::user()->email }}</div>
                </div>
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <strong>Błąd:</strong> {{ $errors->first() }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
