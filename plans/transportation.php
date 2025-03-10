<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/plans/transportation.php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

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
    $address = $_POST['address'];
    $location_name = $_POST['location_name'];
    $phone = $_POST['phone'] ?? null;
    $website = $_POST['website'] ?? null;
    $email = $_POST['email'] ?? null;
    $vehicle_info = $_POST['vehicle_info'];
    $vehicle_description = $_POST['vehicle_description'] ?? null;
    $number_of_passengers = $_POST['number_of_passengers'];
    $transportation_cost = $_POST['transportation_cost'];
    $cost = $_POST['cost'];

    // Insert transportation details into the database
    $stmt = $pdo->prepare("INSERT INTO transportation (trip_id, departure_date, departure_time, arrival_date, arrival_time, address, location_name, phone, website, email, vehicle_info, vehicle_description, number_of_passengers, transportation_cost, cost, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$trip_id, $departure_date, $departure_time, $arrival_date, $arrival_time, $address, $location_name, $phone, $website, $email, $vehicle_info, $vehicle_description, $number_of_passengers, $transportation_cost, $cost]);

    header("Location: ../edit_trip.php?trip_id=$trip_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transportation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Transportation</h1>
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
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="location_name" class="form-label">Location Name</label>
                <input type="text" id="location_name" name="location_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control">
            </div>
            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="url" id="website" name="website" class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="vehicle_info" class="form-label">Vehicle Info</label>
                <input type="text" id="vehicle_info" name="vehicle_info" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="vehicle_description" class="form-label">Vehicle Description</label>
                <textarea id="vehicle_description" name="vehicle_description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="number_of_passengers" class="form-label">Number of Passengers</label>
                <input type="number" id="number_of_passengers" name="number_of_passengers" class="form-control"
                    required>
            </div>
            <div class="mb-3">
                <label for="transportation_cost" class="form-label">Transportation Cost</label>
                <input type="number" step="0.01" id="transportation_cost" name="transportation_cost"
                    class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Cost</label>
                <input type="number" step="0.01" id="cost" name="cost" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Transportation</button>
                <a href="../edit_trip.php?trip_id=<?= $trip_id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>