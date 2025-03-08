<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $departure = $_GET['departure'] ?? '';
    $arrival = $_GET['arrival'] ?? '';
    
    // Mock flight data - In a real app, this would come from an API or database
    $flights = [
        [
            'id' => 'FL001',
            'airline' => 'Skyways',
            'departure' => $departure,
            'arrival' => $arrival,
            'departureTime' => '08:00',
            'arrivalTime' => '10:30',
            'duration' => '2h 30m',
            'price' => 299.99,
            'type' => 'direct'
        ],
        [
            'id' => 'FL002',
            'airline' => 'AirConnect',
            'departure' => $departure,
            'arrival' => $arrival,
            'departureTime' => '10:15',
            'arrivalTime' => '13:45',
            'duration' => '3h 30m',
            'price' => 249.99,
            'type' => 'direct'
        ],
        // Add more mock flights as needed
    ];
    
    header('Content-Type: application/json');
    echo json_encode(['flights' => $flights]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $trip_id = $data['trip_id'] ?? '';
    $flight_id = $data['flight_id'] ?? '';
    
    if ($trip_id && $flight_id) {
        // In a real app, save the flight selection to the database
        $response = ['success' => true, 'message' => 'Flight booked successfully'];
    } else {
        http_response_code(400);
        $response = ['success' => false, 'message' => 'Invalid request'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}
