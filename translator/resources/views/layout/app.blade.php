<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OneSolution - All-in-One Document Processing')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        @media (max-width: 991.98px) {
            .sidebar-container { display: none !important; }
        }
        @media (min-width: 992px) {
            .offcanvas-lg { position: static; transform: none; visibility: visible !important; background: none; border: none; }
            .offcanvas-lg .offcanvas-body { padding: 0; }
        }
    </style>
</head>

<body class="bg-light">
    @include('partials.header')

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (Offcanvas for mobile, static for desktop) -->
            <div class="col-lg-3 d-none d-lg-block sidebar-container">
                @include('partials.sidebar')
            </div>
            <div class="offcanvas offcanvas-start offcanvas-lg d-lg-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sidebarOffcanvasLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    @include('partials.sidebar')
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 content-container">
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials.footer')
</body>
</html>
