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
                            INSERT INTO flights 
                            (trip_id, flight_id, airline, airline_logo, departure_city, arrival_city, departure_time, arrival_time, duration, flight_type, cost)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $trip_id,
                            $reservation['flight_id']   ?? '',
                            $reservation['airline']     ?? '',
                            $reservation['airlineLogo'] ?? '',  // This stores the logo picture URL or path.
                            $reservation['departure']   ?? '',
                            $reservation['arrival']     ?? '',
                            $reservation['departureTime'] ?? '',
                            $reservation['arrivalTime']   ?? '',
                            $reservation['duration']    ?? '',
                            $reservation['type']        ?? '',
                            $reservation['price']       ?? 0.0
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
                    INSERT INTO car_rentals 
                    (trip_id, car_id, name, image, transmission, mileage, fuel_policy, number_of_doors, small_suitcases, big_suitcases, number_of_seats, air_conditioning, car_class, company, price, currency, details, pickup, dropoff, pickup_datetime, dropoff_datetime)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $trip_id,
                    $reservation['car_id']         ?? '',
                    $reservation['name']           ?? '',
                    $reservation['image']          ?? '',
                    $reservation['transmission']   ?? '',
                    $reservation['mileage']        ?? 0,
                    $reservation['fuelPolicy']     ?? 'N/A',
                    $reservation['numberOfDoors']  ?? 0,
                    $reservation['smallSuitcases'] ?? 0,
                    $reservation['bigSuitcases']   ?? 0,
                    $reservation['numberOfSeats']  ?? 0,
                    $reservation['airConditioning']?? 'N/A',
                    $reservation['carClass']       ?? 'N/A',
                    $reservation['company']        ?? '',
                    $reservation['price']          ?? 0.0,
                    $reservation['currency']       ?? 'USD',
                    json_encode($reservation['details'] ?? []),
                    $reservation['pickup']         ?? '',
                    $reservation['dropoff']        ?? '',
                    $reservation['pickupDateTime'] ?? '',
                    $reservation['dropoffDateTime']?? ''
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
