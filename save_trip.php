<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/api/save_trip.php
include '../db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Debugging: Log the received data
    error_log(print_r($data, true));

    if (!isset($data['trip_id'])) {
        echo json_encode(['success' => false, 'message' => 'Trip ID is missing.']);
        exit;
    }
    $trip_id = $data['trip_id'];

    // Check if the trip_id exists in the trips table
    $stmt = $pdo->prepare("SELECT trip_id FROM trips WHERE trip_id = ?");
    $stmt->execute([$trip_id]);
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Trip ID not found.']);
        exit;
    }

    // Check for required fields (add your own logic here)
    // For example, check if the trip has a name and destination
    $stmt = $pdo->prepare("SELECT trip_name, destination FROM trips WHERE trip_id = ?");
    $stmt->execute([$trip_id]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($trip['trip_name']) || empty($trip['destination'])) {
        echo json_encode(['success' => false, 'message' => 'Trip name or destination is missing.']);
        exit;
    }

    // Save the trip (you can add your own logic here)
    // For example, update the trip's last modified date
    $stmt = $pdo->prepare("UPDATE trips SET last_modified = NOW() WHERE trip_id = ?");
    $stmt->execute([$trip_id]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
