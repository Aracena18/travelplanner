<?php
header('Content-Type: application/json');
require_once '../db.php';
session_start();

try {
    if (!isset($_SESSION['is_admin'])) {
        throw new Exception('Unauthorized access');
    }

    $stmt = $conn->prepare("SELECT * FROM hotels ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = array_map('htmlspecialchars', $row);
    }
    
    echo json_encode(['success' => true, 'data' => $hotels]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>