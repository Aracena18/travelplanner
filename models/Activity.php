<?php
// models/Activity.php

class Activity {
    public static function getByTripId($trip_id) {
        global $pdo;
        $activities = [];
        $stmt = $pdo->prepare("SELECT * FROM activity WHERE trip_id = ?");
        $stmt->execute([$trip_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $activities[] = $row;
        }
        return $activities;
    }

    public static function getSubPlansByTripId($trip_id) {
        global $pdo;
        $sub_plans = [];
        $sub_plan_types = ['activity', 'concert','meeting', 'restaurant', 'transportation'];
        foreach ($sub_plan_types as $type) {
            $stmt = $pdo->prepare("SELECT * FROM $type WHERE trip_id = ?");
            $stmt->execute([$trip_id]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $sub_plans[] = $row;
            }
        }
        return $sub_plans;
    }
}
?>