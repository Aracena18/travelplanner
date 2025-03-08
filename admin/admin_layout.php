<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    aside {
        height: 100vh;
        width: 280px;
        background: #ffffff;
        position: fixed;
        padding: 1.5rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        border-right: 1px solid #eaeaea;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .sidenav-header {
        padding: 0 0.5rem 1.5rem 0.5rem;
        border-bottom: 1px solid #eaeaea;
        margin-bottom: 1.5rem;
    }

    .sidenav-header h4 {
        color: #1a1a1a;
        font-weight: 600;
        margin: 0;
    }

    .sidenav ul {
        list-style: none;
        padding: 0;
    }

    .sidenav ul li {
        margin-bottom: 0.5rem;
    }

    .sidenav ul li a {
        color: #4a5568;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .sidenav ul li a:hover {
        background: #f8f9fa;
        color: #2563eb;
    }

    .sidenav ul li a i {
        margin-right: 12px;
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }

    .sidenav ul li a.active {
        background: #EEF2FF;
        color: #2563eb;
    }

    .sidebar {
        height: 100%;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #111;
        padding-top: 20px;
    }

    .sidebar a {
        padding: 10px 15px;
        text-decoration: none;
        font-size: 18px;
        color: white;
        display: block;
    }

    .sidebar a:hover {
        background-color: #575757;
    }

    .logout {
        margin-top: auto;
    }
    </style>
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <!-- Sidebar -->
    <aside>
        <div>
            <div class="sidenav-header">
                <h4>Admin Panel</h4>
            </div>
            <nav class="sidenav">
                <ul>
                    <li>
                        <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                            <i class="fas fa-th-large"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="hotels.php" class="<?= $current_page == 'hotels.php' ? 'active' : '' ?>">
                            <i class="fas fa-hotel"></i>
                            Hotels
                        </a>
                    </li>
                    <li>
                        <a href="flights.php" class="<?= $current_page == 'flights.php' ? 'active' : '' ?>">
                            <i class="fas fa-plane-departure"></i>
                            Flights
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i>
                            Users
                        </a>
                    </li>
                    <!-- <li>
                        <a href="recommendations.php" class="<?= $current_page == 'recommendations.php' ? 'active' : '' ?>">
                            <i class="fas fa-star"></i>
                            Recommendations
                        </a>
                    </li>
                    <li>
                        <a href="explores.php" class="<?= $current_page == 'explores.php' ? 'active' : '' ?>">
                            <i class="fas fa-compass"></i>
                            Explores
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </li> -->
                </ul>
            </nav>
        </div>
        <div class="logout">
            <a href="../auth/logout.php" class="btn btn-danger btn-block">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main content -->
    <div class="main-content"></div>
</body>

</html>