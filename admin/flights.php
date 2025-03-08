<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/admin/flights.php
include '../db.php';
session_start();

if (!isset($_SESSION['is_admin'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Handle flight deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM flights WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: flights.php');
    exit;
}

// Fetch flights from the database
$flights = [];
$stmt = $pdo->query("SELECT id, cost, airline, departure_date, departure_time, arrival_date, arrival_time FROM flights");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $flights[] = $row;
}

include 'admin_layout.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 280px;
            /* Adjusted to match the sidebar width */
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <!-- Main content -->
    <div class="main-content">
        <h1 class="text-center mb-4">Flights</h1>

        <!-- Flights Table -->
        <div class="container">
            <h2>Manage Flights</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cost</th>
                        <th>Airline</th>
                        <th>Departure Date</th>
                        <th>Departure Time</th>
                        <th>Arrival Date</th>
                        <th>Arrival Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flights as $flight): ?>
                        <tr>
                            <td><?= htmlspecialchars($flight['id']) ?></td>
                            <td><?= htmlspecialchars($flight['cost']) ?></td>
                            <td><?= htmlspecialchars($flight['airline']) ?></td>
                            <td><?= htmlspecialchars($flight['departure_date']) ?></td>
                            <td><?= htmlspecialchars($flight['departure_time']) ?></td>
                            <td><?= htmlspecialchars($flight['arrival_date']) ?></td>
                            <td><?= htmlspecialchars($flight['arrival_time']) ?></td>
                            <td>
                                <a href="edit_flight.php?id=<?= $flight['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="flights.php?delete=<?= $flight['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this flight?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div> <!-- Close main-content div -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>