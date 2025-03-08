<?php
include '../db.php';
session_start();

if (!isset($_SESSION['is_admin'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Fetch hotels from the database
$hotels = [];
$stmt = $pdo->query("SELECT h.*, l.name as location_name FROM hotels h JOIN locations l ON h.location_id = l.id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $hotels[] = $row;
}

include 'admin_layout.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels</title>
    <style>
        .main-content {
            margin-left: 260px;
            /* Same as the width of the sidebar */
            padding: 20px;
        }
    </style>
</head>

<body>

    <!-- Main content -->
    <div class="main-content">
        <div class="container mt-5">
            <h1 class="text-center mb-4">Hotels</h1>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($hotels as $hotel): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <!-- Hotel Image -->
                            <img src="../assets/images/<?= htmlspecialchars($hotel['image_name']) ?>" alt="Hotel Image"
                                class="card-img-top">

                            <!-- Hotel Details -->
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($hotel['name']) ?></h5>
                                <p class="card-text">
                                    <strong>Location:</strong> <?= htmlspecialchars($hotel['location_name']) ?><br>
                                    <strong>Stars:</strong> <?= htmlspecialchars($hotel['stars']) ?><br>
                                    <strong>Latitude:</strong> <?= htmlspecialchars($hotel['latitude']) ?><br>
                                    <strong>Longitude:</strong> <?= htmlspecialchars($hotel['longitude']) ?><br>
                                    <strong>Price per Night:</strong> $<?= htmlspecialchars($hotel['price']) ?><br>
                                    <strong>Available Rooms:</strong> <?= htmlspecialchars($hotel['available_rooms']) ?><br>
                                    <strong>Status:</strong> <?= htmlspecialchars($hotel['status']) ?>
                                </p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card-footer d-flex justify-content-between">
                                <a href="edit_hotel.php?id=<?= $hotel['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_hotel.php?id=<?= $hotel['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this hotel?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>