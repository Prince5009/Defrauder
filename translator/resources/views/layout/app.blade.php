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
</head>

<body class="bg-light">
    @include('partials.header')

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (Full Height) -->
            <div class="col-md-3 sidebar-container">
                @include('partials.sidebar')
            </div>

            <!-- Main Content -->
            <div class="col-md-9 content-container">
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials.footer')
</body>
</html>
