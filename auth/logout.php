<?php
// Start session
session_start();

// Destroy all session data to log the user out
session_unset();  // Remove all session variables
session_destroy();  // Destroy the session

// Redirect to login page after logging out
header('Location: ../welcome.php');
exit;
