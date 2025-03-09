<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arc Travel - Your Journey Begins Here</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #475569;
            --accent: #10b981;
            --background: #f8fafc;
            --text: #1e293b;
            --white: #ffffff;
            --transition: all 0.3s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--text);
            background-color: var(--background);
        }

        /* Header Styles */
        .header {
            position: fixed;
            width: 100%;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }

        .header.scrolled {
            background: rgba(0, 0, 0, 0.9);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: var(--white);
            font-size: 1.8rem;
            font-weight: 700;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-link {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: var(--transition);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }

        .btn-outline {
            border: 2px solid var(--white);
            color: var(--white);
        }

        .btn-solid {
            background: var(--white);
            color: var(--text);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url("assets/images/background.jpg") no-repeat center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
        }

        .hero-content {
            max-width: 800px;
            padding: 2rem;
        }

        .hero-title {
            font-size: 4rem;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Destinations Section */
        .destinations {
            padding: 5rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 0 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .destination-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .destination-card:hover {
            transform: translateY(-10px);
        }

        .card-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Mobile Menu */
        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            color: var(--white);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.9);
                padding: 1rem;
                flex-direction: column;
                text-align: center;
            }

            .nav-menu.active {
                display: flex;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .destinations-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <a href="welcome.php" class="logo">Arc Travel</a>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-menu">
                <li><a href="welcome.php" class="nav-link">Home</a></li>
                <li><a href="#destinations" class="nav-link">Destinations</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>
            <div class="auth-buttons">
                <a href="auth/login.php" class="btn btn-outline">Login</a>
                <a href="auth/register.php" class="btn btn-solid">Register</a>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Your Journey Starts Here</h1>
            <p class="hero-subtitle">Discover extraordinary destinations and create unforgettable memories.</p>
            <a href="auth/login.php" class="btn btn-solid">Start Your Journey</a>
        </div>
    </section>

    <section class="destinations" id="destinations">
        <div class="section-title">
            <h2>Explore Our Exclusive Offers</h2>
            <p>We bring you the best destinations at the best prices.</p>
        </div>
        <div class="destinations-grid">
            <div class="destination-card">
                <img src="assets/images/destination1.png" class="card-image" alt="Tokyo">
                <div class="card-content">
                    <h3 class="card-title">Tokyo</h3>
                    <p>Explore the dynamic blend of modernity and tradition in Japan's capital.</p>
                    <a href="auth/login.php" class="btn btn-solid">Discover More <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
            <div class="destination-card">
                <img src="assets/images/destination2.jpeg" class="card-image" alt="Berlin">
                <div class="card-content">
                    <h3 class="card-title">Berlin</h3>
                    <p>Immerse yourself in the innovative culture and rich history of Germany's capital.</p>
                    <a href="auth/login.php" class="btn btn-solid">Discover More <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
            <div class="destination-card">
                <img src="assets/images/destination3.jpg" class="card-image" alt="Bangkok">
                <div class="card-content">
                    <h3 class="card-title">Bangkok</h3>
                    <p>Experience the vibrant mix of cultural heritage and modern city life in Thailand.</p>
                    <a href="auth/login.php" class="btn btn-solid">Discover More <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5" id="about">
        <div class="container">
            <h2 class="text-center text-dark mb-4" data-aos="fade-up">About Us</h2>
            <p class="text-center text-muted" data-aos="fade-up">Crafting unique travel experiences is our passion.</p>
        </div>
    </section>

    <section class="bg-light py-5" id="contact">
        <div class="container">
            <h2 class="text-center text-dark mb-4" data-aos="fade-up">Contact Us</h2>
            <p class="text-center text-muted" data-aos="fade-up">Have a question? Reach out to us to learn more.</p>
        </div>
    </section>

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('.nav-menu');

        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>