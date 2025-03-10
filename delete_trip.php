<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if (!isset($_GET['trip_id']) || !is_numeric($_GET['trip_id'])) {
    die("Invalid trip ID");
}

$trip_id = $_GET['id'];

// Verify the trip exists before deleting
$stmt = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ? AND user_id = ?");
$stmt->execute([$trip_id, $_SESSION['user_id']]);
$trip = $stmt->fetch();

if (!$trip) {
    die("No matching record found.");
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Delete related records in child tables
    $tables = ['car_rental', 'activity', 'concert', 'flights', 'meeting', 'restaurant', 'transportation'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE trip_id = ?");
        $stmt->execute([$trip_id]);
    }

    // Delete the trip
    $stmt = $pdo->prepare("DELETE FROM trips WHERE trip_id = ? AND user_id = ?");
    $stmt->execute([$trip_id, $_SESSION['user_id']]);

    // Commit transaction
    $pdo->commit();

    // Redirect to index
    header('Location: index.php');
    exit;
} catch (Exception $e) {
    // Rollback transaction in case of error
    $pdo->rollBack();
    die("Failed to delete record: " . $e->getMessage());
}
