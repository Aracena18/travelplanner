<?php
// Clear any existing output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set proper headers
header('Content-Type: application/json');

require_once '../db.php';
session_start();

// Check admin authentication
if (!isset($_SESSION['is_admin'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

// Validate required fields
$required_fields = ['name', 'image_name', 'stars', 'location_id', 'latitude', 'longitude', 'price', 'rooms', 'status'];
$missing_fields = array_filter($required_fields, function ($field) {
    return !isset($_POST[$field]) || empty($_POST[$field]);
});

if (!empty($missing_fields)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit();
}

try {
    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $image_name = mysqli_real_escape_string($conn, $_POST['image_name']);
    $stars = intval($_POST['stars']);
    $location_id = intval($_POST['location_id']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    $price = floatval($_POST['price']);
    $rooms = intval($_POST['rooms']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Use prepared statement
    $stmt = $conn->prepare("INSERT INTO hotels (name, image_name, location_id, stars, latitude, longitude, price_per_night, available_rooms, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiddissi", $name, $image_name, $location_id, $stars, $latitude, $longitude, $price, $rooms, $status);

    if ($stmt->execute()) {
        $response = [
            'success' => true,
            'message' => 'Hotel added successfully',
            'hotelId' => $conn->insert_id
        ];
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ];
}

$stmt->close();
$conn->close();

echo json_encode($response);
