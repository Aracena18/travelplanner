<?php
// filepath: c:\xampp\htdocs\travelplanner-master\get_sub_plan.php
include 'db.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
    exit;
}

$id = $_GET['id'];
$type = $_GET['type'];

$table = '';
switch ($type) {
    case 'activity':
        $table = 'activity';
        break;
    case 'car_rental':
        $table = 'car_rental';
        break;
    case 'concert':
        $table = 'concert';
        break;
    case 'flight':
        $table = 'flights';
        break;
    case 'meeting':
        $table = 'meeting';
        break;
    case 'restaurant':
        $table = 'restaurant';
        break;
    case 'transportation':
        $table = 'transportation';
        break;
}

if ($table) {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    $sub_plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sub_plan) {
        echo json_encode(['success' => true, 'data' => $sub_plan]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sub plan not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid sub plan type.']);
}
