<nav class="navbar navbar-expand-lg bg-danger text-white p-3 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Left Side: Hamburger for mobile & Logo+Name -->
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                <i class="bi bi-list fs-2"></i>
            </button>
            <a class="navbar-brand text-white fw-bold header-item d-flex align-items-center gap-2 m-0 p-0" href="{{ url('/') }}">
                <img src="{{ asset('images/OneSolution.jpg') }}" alt="OneSolution Logo" style="height: 40px; width: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); background: #fff; padding: 2px;">
                <span class="fs-3 ms-2" style="letter-spacing: 1px; font-weight: 700;">OneSolution</span>
            </a>
        </div>

        <!-- Center: Welcome Message (hidden on xs, shown on sm+) -->
        <div class="text-center flex-grow-1 d-none d-sm-block">
            @auth
                <span class="fw-bold fs-4">Welcome, {{ Auth::user()->name }} 🎉</span>
            @else
                <span class="fw-bold fs-4">Welcome, Guest</span>
            @endauth
        </div>

        <!-- Right Side: Icons with proper gap -->
        <div class="d-flex align-items-center gap-4">
            @auth
                <a href="{{ url('/history') }}" class="text-white header-item fs-5" title="History">
                    <i class="bi bi-clock-history"></i>
                </a>
                <a href="{{ url('/feedback') }}" class="text-white header-item fs-5" title="Feedback">
                    <i class="bi bi-chat-square-text"></i>
                </a>
                <div class="dropdown">
                    <a href="#" class="text-white header-item fs-5 position-relative" id="motivationBell" title="Motivational Reminders">
                        <i class="bi bi-emoji-smile"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning pulse">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </span>
                    </a>
                </div>
                <a href="#" class="text-white header-item fs-5" title="Logout" onclick="confirmLogout()">
                   <i class="bi bi-box-arrow-right"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="text-white header-item fs-5">Login</a>
                <a href="{{ route('register') }}" class="text-white header-item fs-5">Register</a>
            @endauth
        </div>
    </div>
    <!-- Welcome message for xs screens below header -->
    <div class="container-fluid d-block d-sm-none mt-2 text-center">
        @auth
            <span class="fw-bold fs-6">Welcome, {{ Auth::user()->name }} 🎉</span>
        @else
            <span class="fw-bold fs-6">Welcome, Guest</span>
        @endauth
    </div>
</nav>

<!-- Motivational Notification Modal -->
<div class="modal fade" id="motivationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="motivation-icon mb-3">
                    <i class="bi bi-emoji-smile-fill text-warning" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-danger mb-3">Hey, thank you for using our service!</h4>
                <p class="lead mb-4">Let's do something productive! 🚀</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Let's Go!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="text-danger fw-bold fs-4">OneSolution</span>
                </div>
                <div class="mb-3">
                    <i class="bi bi-question-circle-fill text-danger" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-danger mb-3">Confirm Logout</h4>
                <p class="lead mb-4">Are you sure you want to logout?</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitLogout()">Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>

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

/* Pulse Animation for Notification Badge */
.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Motivation Modal Styles */
#motivationModal .modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#motivationModal .modal-body {
    padding: 2rem;
}

.motivation-icon {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-20px);
    }
    60% {
        transform: translateY(-10px);
    }
}

/* Logout Modal Styles */
#logoutModal .modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#logoutModal .modal-body {
    padding: 2rem;
}

#logoutModal .btn {
    padding: 0.5rem 2rem;
    font-weight: 500;
}
</style>

<script>
// Show motivational notification
function showMotivationNotification() {
    const modal = new bootstrap.Modal(document.getElementById('motivationModal'));
    modal.show();
}

// Send motivation email
function sendMotivationEmail() {
    fetch('/api/send-motivation-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Email sent:', data);
    })
    .catch(error => {
        console.error('Error sending email:', error);
    });
}

// Show notification and send email
function showNotificationAndEmail() {
    showMotivationNotification();
    sendMotivationEmail();
}

// Set interval for notifications and emails (5 minutes)
const NOTIFICATION_INTERVAL = 5 * 60 * 1000; // 5 minutes in milliseconds

// Show initial notification after login
document.addEventListener('DOMContentLoaded', function() {
    // Wait for 5 minutes before showing the first notification
    setTimeout(() => {
        showNotificationAndEmail();
        // Then set up the interval for subsequent notifications
        setInterval(showNotificationAndEmail, NOTIFICATION_INTERVAL);
    }, NOTIFICATION_INTERVAL);
});

// Logout functions
function confirmLogout() {
    const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
    modal.show();
}

function submitLogout() {
    document.getElementById('logout-form').submit();
}
</script>

