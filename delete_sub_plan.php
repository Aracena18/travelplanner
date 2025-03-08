<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $sub_plan_id = $data['id'];
    $sub_plan_type = $data['type'];

    try {
        $stmt = $pdo->prepare("DELETE FROM $sub_plan_type WHERE id = ? AND trip_id = ? AND user_id = ?");
        $stmt->execute([$sub_plan_id, $_SESSION['trip_id'], $_SESSION['user_id']]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the sub plan.']);
    }
}
