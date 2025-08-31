<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            min-width: 260px;
            max-width: 260px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .sidebar>* {
            position: relative;
            z-index: 2;
        }

        .sidebar-header {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h4 {
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .sidebar-header small {
            opacity: 0.8;
            font-size: 12px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 15px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px;
        }

        .logout-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 12px 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ff6b6b !important;
            border-color: #ff6b6b;
        }

        .content {
            flex: 1;
            padding: 30px;
            background-color: #f8f9fa;
        }

        .content-header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .content-header h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .breadcrumb-item {
            color: #6c757d;
        }

        .breadcrumb-item.active {
            color: #667eea;
        }

        .main-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            min-height: 500px;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stats-card.primary {
            border-left-color: #667eea;
        }

        .stats-card.success {
            border-left-color: #51cf66;
        }

        .stats-card.warning {
            border-left-color: #ffd43b;
        }

        .stats-card.danger {
            border-left-color: #ff6b6b;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .stats-icon.primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .stats-icon.success {
            background: linear-gradient(45deg, #51cf66, #40c057);
        }

        .stats-icon.warning {
            background: linear-gradient(45deg, #ffd43b, #ffc107);
        }

        .stats-icon.danger {
            background: linear-gradient(45deg, #ff6b6b, #fa5252);
        }
        .badge-counter {
            position: absolute;
            top: 18px;
            right: 59px;
            background-color: #e74a3b !important;
            color: white;
            border-radius: 50%;
            font-size: 0.65rem;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .nav-item {
            position: relative;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-width: 70px;
                max-width: 70px;
            }

            .sidebar-header h4,
            .sidebar-header small,
            .nav-link span {
                display: none;
            }

            .nav-link {
                justify-content: center;
            }

            .nav-link i {
                margin-right: 0;
            }

            .logout-section {
                position: relative;
                bottom: auto;
            }
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-shield-alt"></i> Admin</h4>
            <small>Control Panel</small>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link @yield('dashboard-active')">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.user.index') }}" class="nav-link @yield('users-active')">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.booking.index') }}" class="nav-link @yield('booking-active')">
                    <i class="fas fa-chart-bar"></i>
                    <span>Booking</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{route('admin.room.index')}}" class="nav-link @yield('room-active')">
                    <i class="fas fa-cog"></i>
                    <span>Room</span>
                </a>
            </div>
               <div class="nav-item">
                <a href="{{route('admin.bank.accounts.index')}}" class="nav-link @yield('banks-active')">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Bank Accounts</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{route('admin.reset')}}" class="nav-link @yield('change-password-active')">
                    <i class="fas fa-cog"></i>
                    <span>Change password</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{route('admin.notifications.index')}}" class="nav-link @yield('notifications-active')">
                    <i class="fas fa-bell"></i>
                    <span>Contact</span>
                    @php
                        $unreadCount = \App\Models\Notification::unread()->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge badge-danger badge-counter">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>
            </div>
            {{-- <div class="nav-item">
                <a href="#" class="nav-link @yield('reports-active')">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
            </div> --}}
            <div class="nav-item">
                <a href="{{ route('admin.logout') }}" class="logout-link">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        {{-- <div class="logout-section">
            <a href="#" class="logout-link"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="#" method="POST" style="display: none;">
                <!-- CSRF token would go here -->
            </form>
        </div> --}}
    </div>

    <!-- Main Content -->
    <div class="content" style="overflow-y:auto;">
        <div class="content-header">
            <h2><i class="@yield('page-icon', 'fas fa-tachometer-alt') me-2"></i>@yield('page-title', 'Dashboard')</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>
