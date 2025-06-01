<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ICOS') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
            width: 200px; /* Reduced from 250px */
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            left: 0;
            top: 0;
        }
        
        .sidebar-header {
            padding: 12px 15px; /* Reduced padding */
            background: rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 50px; /* Reduced from 70px */
        }
        
        .sidebar .logo {
            font-size: 1.2rem; /* Smaller font */
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0;
            white-space: nowrap;
        }
        
        .sidebar-nav {
            padding: 0;
            list-style: none;
            margin: 0.5rem 0; /* Reduced margin */
        }
        
        .sidebar-nav li {
            padding: 0;
            margin-bottom: 2px; /* Reduced margin */
        }
        
        .sidebar-nav li.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-nav a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 8px 12px; /* Reduced padding */
            transition: all 0.3s;
            font-size: 0.9rem; /* Smaller font */
        }
        
        .sidebar-nav a:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-nav a i {
            width: 18px; /* Smaller width */
            text-align: center;
            margin-right: 8px; /* Reduced margin */
            font-size: 14px; /* Smaller icons */
        }
        
        .sidebar-nav a span {
            white-space: nowrap;
        }
        
        .toggle-btn {
            position: fixed;
            left: 200px; /* Adjusted for new sidebar width */
            top: 5px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            padding: 8px; /* Smaller padding */
            cursor: pointer;
            z-index: 1001;
            transition: all 0.3s;
            font-size: 14px; /* Smaller icon */
        }
        
        body.collapsed .toggle-btn {
            left: 60px; /* Adjusted for collapsed sidebar */
        }
        
        body.collapsed .sidebar {
            width: 60px; /* Smaller when collapsed */
        }
        
        body.collapsed .sidebar .logo-text,
        body.collapsed .sidebar .user-name,
        body.collapsed .sidebar-nav a span {
            display: none;
        }
        
        body.collapsed .sidebar-header {
            justify-content: center;
            padding: 12px 5px;
        }
        
        body.collapsed .sidebar-nav a {
            justify-content: center;
            padding: 12px 5px;
        }
        
        body.collapsed .sidebar-nav a i {
            margin-right: 0;
            font-size: 16px;
        }
        
        .divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0.5rem 0; /* Reduced margin */
        }
        
        /* Main content adjustments */
        .main-content {
            transition: all 0.3s;
            margin-left: 200px; /* Adjusted for new sidebar width */
            padding: 15px; /* Reduced padding */
        }
        
        body.collapsed .main-content {
            margin-left: 60px; /* Adjusted for collapsed sidebar */
        }
        
        /* Fully responsive handling */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                transform: translateX(0);
            }
            
            .sidebar .logo-text,
            .sidebar-nav a span {
                display: none;
            }
            
            .sidebar-header {
                justify-content: center;
                padding: 12px 5px;
            }
            
            .sidebar-nav a {
                justify-content: center;
                padding: 12px 5px;
            }
            
            .sidebar-nav a i {
                margin-right: 0;
            }
            
            .toggle-btn {
                left: 60px;
            }
            
            .main-content {
                margin-left: 60px;
                padding: 10px;
            }
            
            /* Expanded state for mobile */
            body.mobile-expanded .sidebar {
                width: 200px;
                z-index: 1050;
            }
            
            body.mobile-expanded .toggle-btn {
                left: 200px;
            }
            
            body.mobile-expanded .sidebar .logo-text,
            body.mobile-expanded .sidebar-nav a span {
                display: inline-block;
            }
            
            body.mobile-expanded .sidebar-header {
                justify-content: flex-start;
                padding: 12px 15px;
            }
            
            body.mobile-expanded .sidebar-nav a {
                justify-content: flex-start;
                padding: 8px 12px;
            }
            
            body.mobile-expanded .sidebar-nav a i {
                margin-right: 8px;
            }
            
            /* Optional overlay for mobile when sidebar is expanded */
            body.mobile-expanded:before {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 375px) {
            .main-content {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <button class="toggle-btn" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="sidebar-header">
            <i class="fas fa-coffee me-2"></i>
            <h3 class="logo mb-0"><span class="logo-text">ICOS</span></h3>
        </div>
        
        <ul class="sidebar-nav">
            <li>
                <a href="{{route('branch.index')}}">
                    <i class="fas fa-location"></i>
                    <span>Cabang</span>
                </a>
            </li>
            <li>
                <a href="/category">
                	<i class="fas fa-bars"></i>
                   <span>Category</span>
                </a>
            </li>
            <li>
                <a href="/product">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="/dashboard">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="/dashboard">
                    <i class="fas fa-table"></i>
                    <span>Tables</span>
                </a>
            </li>
           <li class="mt-3">
    <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap and jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Script -->
    @yield('script')
    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('#sidebarToggle').on('click', function() {
                if ($(window).width() <= 768) {
                    // For mobile devices
                    $('body').toggleClass('mobile-expanded');
                } else {
                    // For desktop devices
                    $('body').toggleClass('collapsed');
                }
            });
            
            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() > 768) {
                    // Remove mobile specific classes on larger screens
                    $('body').removeClass('mobile-expanded');
                } else {
                    // Remove desktop specific classes on smaller screens
                    $('body').removeClass('collapsed');
                }
            });
            
            // Close sidebar when clicking outside (for mobile)
            $(document).on('click', function(e) {
                if ($(window).width() <= 768 && $('body').hasClass('mobile-expanded')) {
                    const $sidebar = $('.sidebar');
                    const $toggleBtn = $('#sidebarToggle');
                    
                    if (!$sidebar.is(e.target) && $sidebar.has(e.target).length === 0 && 
                        !$toggleBtn.is(e.target) && $toggleBtn.has(e.target).length === 0) {
                        $('body').removeClass('mobile-expanded');
                    }
                }
            });
        });
    </script>
</body>
</html>