<?php
require '../db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            echo ("register successfully");
            header('location: login.php');
            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelPlanner - Register</title>
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
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Join Our Community!</h1>
                <p>Create an account to start planning your next adventure</p>
            </div>

            <form action="" method="POST">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <div class="input-group">
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input"
                            placeholder="Enter your full name"
                            required
                        >
                        <i class="input-icon fas fa-user"></i>
                    </div>
                </div>

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
                        >
                        <i class="input-icon fas fa-lock"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="form-input"
                            placeholder="••••••••"
                            required
                        >
                        <i class="input-icon fas fa-lock-keyhole"></i>
                    </div>
                </div>

                <div class="buttons-container">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                    
                    <a href="login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>

                <div class="footer-link">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>