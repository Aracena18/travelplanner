<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trips</title>
    <!-- Use the same Bootstrap version throughout your project -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* TripIt-inspired header design */
        nav.navbar {
            background-color: #0d1b2a;
            padding: 0.8rem 1rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand1 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #f1f1f1;
            letter-spacing: 1px;
        }
        .navbar-nav .nav-item .nav-link {
            color: #f1f1f1 !important;
            font-size: 1rem;
            padding: 0.8rem 1.2rem;
            font-weight: 500;
        }
        .navbar-nav .nav-item .nav-link:hover {
            background-color: #0056b3;
            border-radius: 4px;
        }
        .navbar-nav .nav-item .nav-link.active {
            background-color: #004080;
            border-radius: 4px;
        }
        .navbar-collapse {
            justify-content: flex-end;
        }
        .search-bar {
            border-radius: 50px;
            padding: 0.5rem 1rem;
            width: 250px;
            margin-top: 20px;
        }
        .dropdown-menu {
            background-color: #ffffff;
            border-radius: 8px;
        }
        .dropdown-item {
            color: #333;
        }
        .dropdown-item:hover {
            background-color: #f1f1f1;
        }
        .profile-dropdown {
            display: flex;
            align-items: center;
        }
        .profile-dropdown img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Brand Name -->
            <h1 class="navbar-brand1" onclick="window.location.href='homepage.php'" style="cursor:pointer;">My Trips</h1>

            <!-- Search Bar (Optional) -->
            <form class="d-flex ms-3" method="GET" action="homepage.php">
                <input class="search-bar" type="search" name="query" placeholder="Search your trips..." aria-label="Search" value="<?= $_GET['query'] ?? '' ?>">
            </form>

            <!-- Navbar Toggler for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Upcoming Trips</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="finished_trips.php">Finished Trips</a>
                    </li>
                </ul>

                <!-- User Profile Dropdown -->
                <ul class="navbar-nav ms-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle profile-dropdown" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/assets/images/user-placeholder.png" alt="User Avatar">
                            Welcome, <?= $_SESSION['name'] ?? 'User' ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="homepage.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap Bundle includes Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize any dropdowns (this is usually handled automatically,
        // but this code ensures initialization if needed)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dropdown-toggle').forEach(function(dropdownToggleEl) {
                new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
