<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $trip_id = $_GET['trip_id'] ?? '';
        if (empty($trip_id)) {
            throw new Exception('Trip ID is required');
        }
        
        // Fetch all car rental reservations for the given trip from the car_rentals table.
        $stmt = $pdo->prepare("
            SELECT 
                rental_id AS id,
                trip_id,
                car_id,
                name,
                image,
                transmission,
                mileage,
                fuel_policy,
                number_of_doors,
                small_suitcases,
                big_suitcases,
                number_of_seats,
                air_conditioning,
                car_class,
                company,
                price,
                currency,
                details,
                pickup,
                dropoff,
                pickup_datetime,
                dropoff_datetime,
                created_at
            FROM car_rentals
            WHERE trip_id = ?
            ORDER BY pickup_datetime ASC
        ");
        $stmt->execute([$trip_id]);
        $carReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success'    => true, 
            'rentalCars' => $carReservations
        ]);
        exit;
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
    exit;
}
?>
