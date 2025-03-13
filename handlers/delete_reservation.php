<?php
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $id = $_POST['id'] ?? null;
    $type = $_POST['type'] ?? null;

    if (!$id || !$type) {
        throw new Exception('Missing required parameters');
    }

    // Define the table and ID column based on type
    switch ($type) {
        case 'flight':
            $table = 'flights';
            $idColumn = 'flight_id';
            break;        
        case 'car':
            $table = 'car_rentals';
            $idColumn = 'rental_id'; // Update this to match your car_rentals table's ID column
            break;
        default:
            throw new Exception('Invalid reservation type');
    }

    $stmt = $pdo->prepare("DELETE FROM $table WHERE $idColumn = ?");
    $success = $stmt->execute([$id]);

    if (!$success) {
        throw new Exception('Failed to delete reservation');
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
