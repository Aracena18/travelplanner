<?php
function processReservations($pdo, $trip_id, $pending) {
    // List of reservation types to process
    $reservationTypes = ['flights', 'lodging', 'rentalCars', 'restaurants', 'attachments', 'others'];
    
    foreach ($reservationTypes as $type) {
        if (empty($pending[$type]) || !is_array($pending[$type])) {
            continue;
        }
        
        foreach ($pending[$type] as $reservation) {
            switch ($type) {
                case 'flights':
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations 
                        (trip_id, reservation_type, provider_name, flight_number, departure_city, arrival_city, departure_datetime, arrival_datetime, duration, cost)
                        VALUES (?, 'flight', ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $trip_id,
                        $reservation['airline'] ?? '',
                        $reservation['flight_id'] ?? '',
                        $reservation['departure'] ?? '',
                        $reservation['arrival'] ?? '',
                        $reservation['departureTime'] ?? '',
                        $reservation['arrivalTime'] ?? '',
                        $reservation['duration'] ?? '',
                        $reservation['price'] ?? 0.0
                    ]);
                    break;
                    
                case 'lodging':
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations 
                        (trip_id, reservation_type, provider_name, departure_datetime, arrival_datetime, cost)
                        VALUES (?, 'lodging', ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $trip_id,
                        $reservation['hotelName'] ?? '',
                        $reservation['checkInDate'] ?? '',
                        $reservation['checkOutDate'] ?? '',
                        $reservation['price'] ?? 0.0
                    ]);
                    break;
                    
                case 'rentalCars':
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations 
                        (trip_id, reservation_type, provider_name, departure_datetime, arrival_datetime, cost)
                        VALUES (?, 'rental_car', ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $trip_id,
                        $reservation['company'] ?? '',
                        $reservation['pickupDate'] ?? '',
                        $reservation['dropoffDate'] ?? '',
                        $reservation['price'] ?? 0.0
                    ]);
                    break;
                    
                case 'restaurants':
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations 
                        (trip_id, reservation_type, provider_name, departure_datetime, cost)
                        VALUES (?, 'restaurant', ?, ?, ?)
                    ");
                    $stmt->execute([
                        $trip_id,
                        $reservation['restaurantName'] ?? '',
                        $reservation['reservationTime'] ?? '',
                        $reservation['price'] ?? 0.0
                    ]);
                    break;
                    
                case 'attachments':
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations 
                        (trip_id, reservation_type, provider_name, cost)
                        VALUES (?, 'attachment', ?, 0)
                    ");
                    $stmt->execute([
                        $trip_id,
                        $reservation['fileName'] ?? ''
                    ]);
                    break;
                    
                case 'others':
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations 
                        (trip_id, reservation_type, provider_name, cost)
                        VALUES (?, 'other', ?, ?)
                    ");
                    $stmt->execute([
                        $trip_id,
                        $reservation['description'] ?? '',
                        $reservation['price'] ?? 0.0
                    ]);
                    break;
                    
                default:
                    // Optionally handle unknown types
                    break;
            }
        }
    }
}
?>
