<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // First check admin table
    $admin_sql = "SELECT * FROM admins WHERE email = '$email'";
    $admin_result = $conn->query($admin_sql);

    if ($admin_result->num_rows > 0) {
        $admin = $admin_result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['is_admin'] = true;
            header("Location: ../admin/dashboard.php");
            exit();
        }
    }

    // If not admin, check regular users
    $user_sql = "SELECT * FROM users WHERE email = '$email'";
    $user_result = $conn->query($user_sql);

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['is_admin'] = false;
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelPlanner - Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/travelplanner-master/css/auth.css">
</head>

<body>
    <div class="bg-elements">
        <div class="bg-element element-1"></div>
        <div class="bg-element element-2"></div>
        <div class="bg-element element-3"></div>
    </div>

    <div class="current-time">
        <i class="fas fa-clock"></i> <?php echo date('Y-m-d H:i:s'); ?>
    </div>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h1>Welcome Explorer!</h1>
                <p>Sign in to discover amazing destinations and plan your perfect journey</p>
            </div>
            
            <form action="" method="POST">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <div class="input-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input"
                            placeholder="name@example.com"
                            required
                            autocomplete="email"
                        >
                        <i class="input-icon fas fa-envelope"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <i class="input-icon fas fa-lock"></i>
                    </div>
                </div>

                <div class="buttons-container">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                    
                    <a href="register.php" class="btn btn-secondary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </div>

                <div class="footer-link">
                    <a href="forgot-password.php">
                        <i class="fas fa-key"></i> Forgot password?
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>