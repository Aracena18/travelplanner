<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/plans/flight.php
include '../db.php';
session_start();

// Get the trip_id from the URL
if (!isset($_GET['trip_id'])) {
    header('Location: ../edit_trip.php');
    exit;
} else {
    $trip_id = $_GET['trip_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departure_date = $_POST['departure_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_date = $_POST['arrival_date'];
    $arrival_time = $_POST['arrival_time'];
    $airline = $_POST['airline'];
    $flight_cost = $_POST['flight_cost'];

    // Insert flight details into the database
    $stmt = $pdo->prepare("INSERT INTO flights (trip_id, departure_date, departure_time, arrival_date, arrival_time, airline, cost, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$trip_id, $departure_date, $departure_time, $arrival_date, $arrival_time, $airline, $flight_cost]);

    header("Location: ../edit_trip.php?trip_id=$trip_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Flight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Flight</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="mb-3">
                <label for="departure_date" class="form-label">Departure Date</label>
                <input type="date" id="departure_date" name="departure_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="departure_time" class="form-label">Departure Time</label>
                <input type="time" id="departure_time" name="departure_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="arrival_date" class="form-label">Arrival Date</label>
                <input type="date" id="arrival_date" name="arrival_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="arrival_time" class="form-label">Arrival Time</label>
                <input type="time" id="arrival_time" name="arrival_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="airline" class="form-label">Airline</label>
                <input type="text" id="airline" name="airline" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="flight_cost" class="form-label">Flight Cost</label>
                <input type="number" step="0.01" id="flight_cost" name="flight_cost" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Flight</button>
                <a href="../edit_trip.php?trip_id=<?= $trip_id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>