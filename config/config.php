<?php
$host = 'localhost'; // Database host
$username = 'root'; // Database username (default for XAMPP)
$password = ''; // Database password (default is empty for XAMPP)
$db = 'trip'; // Your database name

$conn = new mysqli(hostname: $host, username: $username, password: $password, database: $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", username: $username, password: $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
