<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/plans/car_rental.php
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
    $rental_agency = $_POST['rental_agency'];
    $start_date = $_POST['start_date'];
    $start_time = $_POST['start_time'];
    $end_date = $_POST['end_date'];
    $end_time = $_POST['end_time'];
    $car_type = $_POST['car_type'];
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];
    $cost = $_POST['cost'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $phone = $_POST['phone'];

    // Insert car rental details into the database
    $stmt = $pdo->prepare("INSERT INTO car_rental (trip_id, rental_agency, start_date, start_time, end_date, end_time, car_type, pickup_location, dropoff_location, cost, email, website, phone, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$trip_id, $rental_agency, $start_date, $start_time, $end_date, $end_time, $car_type, $pickup_location, $dropoff_location, $cost, $email, $website, $phone]);

    header("Location: ../edit_trip.php?trip_id=$trip_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Car Rental</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="mb-3">
                <label for="rental_agency" class="form-label">Rental Agency</label>
                <input type="text" id="rental_agency" name="rental_agency" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="time" id="start_time" name="start_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" id="end_time" name="end_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="car_type" class="form-label">Car Type</label>
                <input type="text" id="car_type" name="car_type" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="pickup_location" class="form-label">Pickup Location</label>
                <input type="text" id="pickup_location" name="pickup_location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="dropoff_location" class="form-label">Dropoff Location</label>
                <input type="text" id="dropoff_location" name="dropoff_location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Cost $</label>
                <input type="number" step="0.01" id="cost" name="cost" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="url" id="website" name="website" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="tel" id="phone" name="phone" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Car Rental</button>
                <a href="../edit_trip.php?trip_id=<?= $trip_id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>