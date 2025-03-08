<?php
// models/Trip.php

class Trip {
    public static function fetchById($trip_id, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ? AND user_id = ?");
        $stmt->execute([$trip_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($trip_id, $user_id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE trips SET trip_name = ?, destination = ?, hotel = ?, adults_num = ?, childs_num = ?, start_date = ?, end_date = ?, estimated_cost = ? WHERE trip_id = ? AND user_id = ?");
        return $stmt->execute([
            $data['trip_name'],
            $data['destination'],
            $data['hotel'],
            $data['adults_num'],
            $data['childs_num'],
            $data['start_date'],
            $data['end_date'],
            $data['estimated_cost'],
            $trip_id,
            $user_id,
        ]);
    }
}
