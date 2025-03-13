<?php
include 'db.php';
session_start();

// Set JSON content type header early
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Define API credentials
$api_key = 'x-rapidapi-key: a4f936d18fmsh72c62428617d573p1b6010jsn36884d1c931d'; // Replace with your actual API key
$api_url = 'https://api.example.com/flights/search-one-way'; // Replace with the actual API endpoint

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $trip_id = $_GET['trip_id'] ?? '';
        
        if (empty($trip_id)) {
            throw new Exception('Trip ID is required');
        }
        
        if (isset($_GET['type']) && $_GET['type'] === 'reservations') {
            // Fetch all reservations for the trip
            $stmt = $pdo->prepare("
                SELECT 
                    r.id,
                    r.reservation_type AS type,
                    NULL AS title,
                    NULL AS description,
                    NULL AS status,
                    NULL AS location,
                    NULL AS confirmation_number,
                    r.departure_datetime AS start_time,
                    r.arrival_datetime AS end_time,
                    r.cost,
                    r.created_at,
                    CASE WHEN r.reservation_type = 'flight' THEN r.provider_name ELSE NULL END AS airline,
                    CASE WHEN r.reservation_type = 'flight' THEN r.departure_city ELSE NULL END AS departure_city,
                    CASE WHEN r.reservation_type = 'flight' THEN r.arrival_city ELSE NULL END AS arrival_city
                FROM reservations r
                WHERE r.trip_id = ?
                ORDER BY r.departure_datetime ASC
            ");
            
            $stmt->execute([$trip_id]);
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'reservations' => $reservations
            ]);
            exit;
        }
        
        // Flight search with real API request
        $departure = $_GET['departure'] ?? '';
        $arrival = $_GET['arrival'] ?? '';
        $date = $_GET['date'] ?? '';

        if (empty($departure) || empty($arrival) || empty($date)) {
            throw new Exception('Departure, Arrival, and Date are required.');
        }

        // API request to Flights Scraper
        $query_params = http_build_query([
            'departure' => $departure,
            'arrival' => $arrival,
            'date' => $date
        ]);

        $ch = curl_init("$api_url?$query_params");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_key",
            "Accept: application/json"
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            throw new Exception('Failed to fetch flight data.');
        }

        $flights = json_decode($response, true);
        echo json_encode(['success' => true, 'flights' => $flights]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle flight selection
        $flightData = json_decode(file_get_contents('php://input'), true);
        
        if (isset($flightData['action']) && $flightData['action'] === 'select_flight') {
            $trip_id = $flightData['trip_id'];
            $flight_details = $flightData['flight_details'];
            
            // Store flight reservation
            $stmt = $pdo->prepare("
                INSERT INTO reservations (
                    trip_id, 
                    reservation_type,
                    provider_name,
                    departure_city,
                    arrival_city,
                    departure_datetime,
                    arrival_datetime,
                    cost
                ) VALUES (?, 'flight', ?, ?, ?, ?, ?, ?)
            ");
            
            try {
                $stmt->execute([
                    $trip_id,
                    $flight_details['airline'],
                    $flight_details['departure_city'],
                    $flight_details['arrival_city'],
                    $flight_details['departure_datetime'],
                    $flight_details['arrival_datetime'],
                    $flight_details['cost']
                ]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Flight selected successfully',
                    'reservation_id' => $pdo->lastInsertId()
                ]);
            } catch (PDOException $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to save flight reservation'
                ]);
            }
            exit;
        }
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
