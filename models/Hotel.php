<?php
// models/Hotel.php

class Hotel {
    public static function getDestinations() {
        global $pdo;
        $destinations = [];
        $stmt = $pdo->query("SELECT DISTINCT h.location_id, l.name as location_name FROM hotels h JOIN locations l ON h.location_id = l.id");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $location_id = $row['location_id'];
            $destinations[$location_id] = [
                'name' => $row['location_name'],
                'hotels' => self::getHotelsByLocation($location_id)
            ];
        }
        return $destinations;
    }

    public static function getHotelsByLocation($location_id) {
        global $pdo;
        $hotels = [];
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE location_id = ?");
        $stmt->execute([$location_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hotels[] = $row;
        }
        return $hotels;
    }
}
