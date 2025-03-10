<?php
// filepath: c:\xampp\htdocs\travelplanner-master\update_sub_plan.php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['sub_plan_id'];
    $type = $_POST['sub_plan_type'];
    $name = $_POST['sub_plan_name'];
    $start_date = $_POST['sub_plan_start_date'];
    $end_date = $_POST['sub_plan_end_date'];
    $start_time = $_POST['sub_plan_start_time'];
    $end_time = $_POST['sub_plan_end_time'];
    $address = $_POST['sub_plan_address'];
    $website = $_POST['sub_plan_website'];
    $email = $_POST['sub_plan_email'];
    $phone = $_POST['sub_plan_phone'];
    $cost = $_POST['sub_plan_cost'];

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
        $stmt = $pdo->prepare("UPDATE $table SET name = ?, start_date = ?, end_date = ?, start_time = ?, end_time = ?, address = ?, website = ?, email = ?, phone = ?, cost = ? WHERE id = ?");
        $stmt->execute([$name, $start_date, $end_date, $start_time, $end_time, $address, $website, $email, $phone, $cost, $id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid sub plan type.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>