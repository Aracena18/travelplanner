<?php
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch finished trips
$stmt = $pdo->prepare("SELECT t.*, l.name as location_name FROM trips t JOIN locations l ON t.destination = l.id WHERE t.user_id = ? AND t.end_date < ? ORDER BY t.start_date ASC");
$stmt->execute([$user_id, date('Y-m-d')]);
$finishedTrips = $stmt->fetchAll();

function getHotelImage($hotelName)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT image_name FROM hotels WHERE name = ?");
    $stmt->execute([$hotelName]);
    $image_name = $stmt->fetchColumn();
    return $image_name ? "assets/images/" . $image_name : 'assets/images/default-hotel.jpg';
}
?>

<div class="container mt-5">
    <h1>Finished Trips</h1>
    <?php if (count($finishedTrips) > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($finishedTrips as $trip): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <!-- Hotel Image -->
                        <img src="<?= htmlspecialchars(getHotelImage($trip['hotel'])) ?>" alt="Hotel Image" class="card-img-top"
                            style="height: 150px; object-fit: cover; border-radius: 8px;">

                        <!-- Trip Details -->
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($trip['trip_name']) ?></h5>
                            <p class="card-text">
                                <strong>Destination:</strong> <?= htmlspecialchars($trip['location_name']) ?><br>
                                <strong>Hotel:</strong> <?= htmlspecialchars($trip['hotel']) ?><br>
                                <strong>Adults:</strong> <?= htmlspecialchars($trip['adults_num']) ?><br>
                                <strong>Children:</strong> <?= htmlspecialchars($trip['childs_num']) ?><br>
                                <strong>Start Date:</strong> <?= htmlspecialchars($trip['start_date']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($trip['end_date']) ?><br>
                                <strong>Estimated Cost:</strong> <?= htmlspecialchars('$' . $trip['estimated_cost']) ?>
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-footer d-flex justify-content-between">
                            <a href="edit_trip.php?trip_id=<?= $trip['trip_id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_trip.php?trip_id=<?= $trip['trip_id'] ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this trip?');">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="mt-3">No finished trips found.</p>
    <?php endif; ?>
</div>
</body>

</html>