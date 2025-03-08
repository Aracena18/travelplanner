<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/add_activity.php
// Enable error logging
ini_set('log_errors', '1');
// Fix the error log file path by adding a directory separator
ini_set('error_log', __DIR__ . DIRECTORY_SEPARATOR . 'error.log');


// -- Optional: Enable detailed error reporting (development only) --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("DEBUG: User not authenticated. No session user_id found.");
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit;
}

// Include the database connection
include 'db.php';

// Validate required POST fields
if (!isset($_POST['trip_id'], $_POST['activity_name'], $_POST['activity_type'], $_POST['activity_datetime'], $_POST['activity_cost'])) {
    error_log("DEBUG: Missing required POST fields: " . print_r($_POST, true));
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

$trip_id             = $_POST['trip_id'];
$activity_name       = trim($_POST['activity_name']);
$activity_type       = trim($_POST['activity_type']);
$activity_datetime   = $_POST['activity_datetime']; // Will be stored in start_time (DATETIME)
$activity_cost       = floatval($_POST['activity_cost']);
$activity_description= isset($_POST['activity_description']) ? trim($_POST['activity_description']) : '';
$activity_location   = isset($_POST['activity_location']) ? trim($_POST['activity_location']) : '';

// -- Debugging: Log the trip_id and the user_id from the session --
error_log("DEBUG: add_activity - trip_id: " . $trip_id);
error_log("DEBUG: add_activity - session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set'));

// Verify the trip belongs to the current user
$stmt = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ? AND user_id = ?");
$stmt->execute([$trip_id, $_SESSION['user_id']]);
$trip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    // Debugging info if no matching trip is found
    error_log("DEBUG: No matching trip found for trip_id={$trip_id} and user_id={$_SESSION['user_id']}");
    echo json_encode(['success' => false, 'message' => 'Trip not found or permission denied.']);
    exit;
} else {
    // Debugging: Log the trip row
    error_log("DEBUG: Trip found: " . print_r($trip, true));
}

// Insert the new activity into the updated table structure
try {
    $stmt = $pdo->prepare("INSERT INTO activity (trip_id, name, type, start_time, cost, description, location) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Add more detailed logging before execution
    error_log("DEBUG: Attempting to insert activity with data: " . json_encode([
        'trip_id' => $trip_id,
        'name' => $activity_name,
        'type' => $activity_type,
        'start_time' => $activity_datetime,
        'cost' => $activity_cost,
        'description' => $activity_description,
        'location' => $activity_location
    ]));
    
    $stmt->execute([$trip_id, $activity_name, $activity_type, $activity_datetime, $activity_cost, $activity_description, $activity_location]);
    
    // Get the new activity ID
    $activity_id = $pdo->lastInsertId();
    
    // Prepare the new activity data for the response
    $newActivity = [
        'id'          => $activity_id,
        'name'        => $activity_name,
        'type'        => $activity_type,
        'start_time'  => $activity_datetime,
        'cost'        => number_format($activity_cost, 2, '.', ''),
        'description' => $activity_description,
        'location'    => $activity_location
    ];
    
    // Debugging: log success and new activity data
    error_log("DEBUG: Activity inserted successfully with ID {$activity_id}: " . print_r($newActivity, true));
    
    echo json_encode(['success' => true, 'activity' => $newActivity]);
} catch (Exception $e) {
    error_log("ERROR: Exception details - " . $e->getMessage());
    error_log("ERROR: Stack trace - " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'An error occurred while saving the activity.']);
}
?>
