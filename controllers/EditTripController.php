<?php
// controllers/EditTripController.php
if (!file_exists(__DIR__ . '/../models/Trip.php')) {
    die("Trip.php not found!");
}

include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../models/Trip.php';
include_once __DIR__ . '/../models/Hotel.php';
include_once __DIR__ . '/../models/Activity.php';
include_once __DIR__ . '/../includes/functions.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if (!isset($_GET['trip_id'])) {
    die("Trip ID is missing.");
}
$trip_id = $_GET['trip_id'];

$trip = Trip::fetchById($trip_id, $_SESSION['user_id']);
if (!$trip) {
    die("Trip not found or you do not have permission to edit this trip.");
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'trip_name'      => $_POST['trip_name'],
            'destination'    => $_POST['destination'],
            'hotel'          => $_POST['hotel'],
            'adults_num'     => intval($_POST['adults_num']),
            'childs_num'     => intval($_POST['childs_num']),
            'start_date'     => $_POST['start_date'],
            'end_date'       => $_POST['end_date'],
            'estimated_cost' => floatval($_POST['estimated_cost'])
        ];
        
        if (Trip::update($trip_id, $_SESSION['user_id'], $data)) {
            echo json_encode(['success' => true, 'trip_id' => $trip_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'An error occurred while saving the trip.']);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while saving the trip.']);
    }
    exit;
}

// Fetch additional data for the view
$destinations = Hotel::getDestinations();
$activities   = Activity::getByTripId($trip_id);
$sub_plans    = Activity::getSubPlansByTripId($trip_id);
$activeTab    = !empty($trip['hotel']) ? 'accommodation' : 'overview';

// Pass all variables to the view
include __DIR__ . '/../views/edit_trip_view.php';
