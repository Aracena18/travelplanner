<?php
include 'db.php';

if (isset($_GET['location_id'])) {
    $location_id = $_GET['location_id'];
    $stmt = $pdo->prepare("SELECT name, image_name, stars, latitude, longitude, price FROM hotel WHERE location_id = ? AND status = 'active'");
    $stmt->execute([$location_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
