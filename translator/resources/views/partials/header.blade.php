<nav class="navbar navbar-expand-lg bg-danger text-white p-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Left Side: Logo and App Name -->
        <a class="navbar-brand text-white fw-bold header-item d-flex align-items-center" href="{{ url('/') }}">
            <span class="fs-4">OneSolution</span>
        </a>

        <!-- Center: Welcome Message -->
        <div class="text-center flex-grow-1">
            @auth
                <span class="fw-bold fs-4">Welcome, {{ Auth::user()->name }} 🎉</span>
            @else
                <span class="fw-bold fs-4">Welcome, Guest</span>
            @endauth
        </div>

        <!-- Right Side: Icons -->
        <div class="d-flex align-items-center">
            @auth
                <a href="{{ url('/history') }}" class="text-white me-3 header-item fs-5" title="History">
                    <i class="bi bi-clock-history"></i>
                </a>
                <a href="{{ url('/downloads') }}" class="text-white me-3 header-item fs-5" title="Downloads">
                    <i class="bi bi-download"></i>
                </a>
                <a href="{{ url('/profile') }}" class="text-white me-3 header-item fs-5" title="Profile">
                    <i class="bi bi-person-circle"></i>
                </a>
                <a href="{{ route('logout') }}" class="text-white header-item fs-5" title="Logout"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                   <i class="bi bi-box-arrow-right"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="text-white me-3 header-item fs-5">Login</a>
                <a href="{{ route('register') }}" class="text-white header-item fs-5">Register</a>
            @endauth
        </div>
    </div>
</nav>

<style>
.header-item {
    transition: opacity 0.3s ease;
    text-decoration: none;
}

.header-item:hover {
    opacity: 0.8;
    text-decoration: none;
    color: white !important;
}

.navbar-brand {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    letter-spacing: 0.5px;
}
</style>
