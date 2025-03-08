<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arc Travel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">


    <style>
    /* Transparent Header Styling */
    .transparent-header {
        background-color: rgba(0, 0, 0, 0.3);
        /* Transparent black */
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 999;
    }

    .navbar-nav {
        flex-direction: row;
        gap: 1.5rem;
    }

    .nav-link {
        color: white;
        font-size: 1.2rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .nav-link:hover {
        color: #FFD700;
    }

    .background-image {
        background: url("assets/images/background.jpg") no-repeat center center;
        background-size: cover;
        min-height: 100vh;
    }

    body {
        padding-top: 80px;
        /* Avoid overlap with the fixed header */
    }

    .custom-heading {
        color: white;
        font-size: 7rem;
        font-weight: 100;
        font-family: 'Roboto', sans-serif;
    }

    .custom-search {
        border-radius: 200px;
        font-size: 3rem;
        background-color: transparent;
        color: white;
    }

    .custom-button {
        border-radius: 200px;
        font-size: 2rem;
        background-color: orange;
        color: white;
        margin-top: 20px;
    }
    </style>
</head>;

<body class="background-image">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg transparent-header">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Centered Navigation Links -->
            <ul class="navbar-nav mx-auto d-flex flex-row gap-4">
                <li class="nav-item">
                    <a class="nav-link" href="welcome.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#welcome-destinations">Destinations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact Us</a>
                </li>
            </ul>

            <!-- Buttons on the Far Right -->
            <div class="d-flex gap-2">
                <a href="auth/login.php" class="btn btn-secondary">Login</a>
                <a href="auth/register.php" class="btn btn-secondary">Register</a>
            </div>
        </div>
    </nav>
    <section class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <h1 class="custom-heading">Your Journey Starts Here</h1>

        <!-- Journey Button -->
        <div class="d-flex justify-content-center align-items-center mb-3">
            <a href=" auth/login.php" class="btn btn-secondary custom-button">Start Your Journey</a>
        </div>
    </section>
    <section class="bg-light py-5" id="welcome-destinations">
        <div class="container text-center">
            <h2 class="display-5">Explore Our Exclusive Offers</h2>
            <p class="lead text-muted">We bring you the best destinations at the best prices.</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="assets/images/destination1.png" class="card-img-top" alt="Destination 1">
                        <div class="card-body">
                            <h5 class="card-title">Tokyo</h5>
                            <p class="card-text">Explore the famous capital of Japan with it's magnificent modernist
                                cityscape and magnificent Culture</p>
                            <a href="auth/login.php" class="btn btn-primary">Discover More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="assets/images/destination2.jpeg" class="card-img-top" alt="Destination 2">
                        <div class="card-body">
                            <h5 class="card-title">Berlin</h5>
                            <p class="card-text">Marvel at Famous Gothic Architecture in Germany's capital. Live through
                                the mix of medieval fantasy in the comforts of modern lodgings</p>
                            <a href="auth/login.php" class="btn btn-primary">Discover More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="assets/images/destination3.jpg" class="card-img-top" alt="Destination 3">
                        <div class="card-body">
                            <h5 class="card-title">Bangkok</h5>
                            <p class="card-text">Experience vibrant asian city life with endless attractions in
                                Thailand. Home to exotic animals and exotic Culture.</p>
                            <a href="auth/login.php" class="btn btn-primary">Discover More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-light py-5" id="about">
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>