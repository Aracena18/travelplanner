<?php
session_start();
ob_start();

require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$searchQuery = $_GET['query'] ?? '';

try {
    // Determine if a search query is present
    if (!empty($searchQuery)) {
        $stmt = $pdo->prepare(
            "SELECT t.*, l.name as location_name FROM trips t
            JOIN locations l ON t.destination = l.id
            WHERE t.user_id = ? 
            AND (t.trip_name LIKE ? OR l.name LIKE ? OR t.hotel LIKE ?) 
            ORDER BY t.start_date ASC"
        );
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->execute([$user_id, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare(
            "SELECT t.*, l.name as location_name FROM trips t
            JOIN locations l ON t.destination = l.id
            WHERE t.user_id = ? AND t.end_date >= ? 
            ORDER BY t.start_date ASC"
        );
        $stmt->execute([$user_id, date('Y-m-d')]);
    }

    $upcomingTrips = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching trips: " . $e->getMessage());
}

// Fetch destinations and hotels from the database
$destinations = [];
$stmt = $pdo->query("SELECT DISTINCT h.location_id, l.name as location_name FROM hotels h JOIN locations l ON h.location_id = l.id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $location_id = $row['location_id'];
    $location_name = $row['location_name'];
    $destinations[$location_id] = ['name' => $location_name, 'hotels' => []];

    $hotel_stmt = $pdo->prepare("SELECT * FROM hotels WHERE location_id = ?");
    $hotel_stmt->execute([$location_id]);
    while ($hotel_row = $hotel_stmt->fetch(PDO::FETCH_ASSOC)) {
        $destinations[$location_id]['hotels'][] = $hotel_row;
    }
}

function getHotelImage($destination, $hotelName)
{
    global $destinations;
    if (isset($destinations[$destination])) {
        foreach ($destinations[$destination]['hotels'] as $hotel) {
            if ($hotel['name'] === $hotelName) {
                return "assets/images/" . $hotel['image_name'];
            }
        }
    }
    return "assets/images/hotel.png";
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Trips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --background-color: #f8f9fa;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .search-container {
            max-width: 600px;
            margin: 2rem auto;
        }

        .search-input {
            border-radius: 30px;
            padding: 0.8rem 1.5rem;
            border: 2px solid #eee;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            position: relative;
            max-width: 340px;
            margin: 0 auto;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .delete-btn:hover {
            background: #dc3545;
            color: white;
        }

        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .trip-info {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.2rem;
        }

        .destination-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(52, 152, 219, 0.9);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success {
            background-color: var(--secondary-color);
            border: none;
        }

        .btn-success:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: #f39c12;
            border: none;
            color: white;
        }

        .btn-danger {
            background-color: var(--accent-color);
            border: none;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 50px 50px;
        }

        .filter-container {
            margin-bottom: 2rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="container">
            <h1 class="text-center display-4 mb-4">
                <?= !empty($searchQuery) ? "Search Results for: \"" . htmlspecialchars($searchQuery) . "\"" : "My Travel Adventures" ?>
            </h1>
            
            <div class="search-container">
                <form method="GET" action="" class="d-flex">
                    <input type="text" name="query" class="form-control search-input" 
                           placeholder="Search your trips..." value="<?= htmlspecialchars($searchQuery) ?>">
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="filter-container">
                <button class="btn btn-outline-secondary me-2">All Trips</button>
                <button class="btn btn-outline-secondary me-2">Upcoming</button>
                <button class="btn btn-outline-secondary">Past</button>
            </div>
            <form id="create-trip-form" method="POST" action="/travelplanner-master/create_trips/create_trip.php">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i>Plan New Adventure
                </button>
            </form>
        </div>

        <?php if (count($upcomingTrips) > 0): ?>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($upcomingTrips as $trip): ?>
                    <div class="col animate-in">
                        <div class="card h-100 shadow-sm" onclick="window.location.href='edit_trip.php?trip_id=<?= $trip['trip_id'] ?>'">
                            <span class="destination-badge">
                                <?= htmlspecialchars($trip['location_name']) ?>
                            </span>
                            <button class="delete-btn" onclick="deleteTrip(event, <?= $trip['trip_id'] ?>)">
                                <i class="fas fa-times"></i>
                            </button>
                            <img src="<?= htmlspecialchars(getHotelImage($trip['destination'], $trip['hotel'])) ?>"
                                 alt="Hotel Image" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($trip['trip_name']) ?></h5>
                                <div class="trip-info">
                                    <i class="fas fa-hotel me-2"></i><?= htmlspecialchars($trip['hotel']) ?>
                                </div>
                                <div class="trip-info">
                                    <i class="fas fa-users me-2"></i><?= htmlspecialchars($trip['adults_num'] + $trip['childs_num']) ?> Guests
                                </div>
                                <div class="trip-info">
                                    <i class="fas fa-calendar me-2"></i><?= date('M d', strtotime($trip['start_date'])) ?> - <?= date('M d, Y', strtotime($trip['end_date'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <img src="assets/images/empty-state.svg" alt="No trips" style="width: 200px; margin-bottom: 2rem;">
                <h3><?= $searchQuery ? "No trips match your search query." : "No upcoming trips found." ?></h3>
                <p class="text-muted">Start planning your next adventure!</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling and animation triggers
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.animate-in');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });

        function deleteTrip(event, tripId) {
            event.stopPropagation();
            if (confirm('Are you sure you want to delete this trip?')) {
                window.location.href = `delete_trip.php?trip_id=${tripId}`;
            }
        }
    </script>
</body>

</html>