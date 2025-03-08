<?php
// filepath: /c:/xampp/htdocs/travelplanner/edit_trip.php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if (!isset($_GET['trip_id'])) {
    die("Trip ID is missing.");
} else {
    $trip_id = $_GET['trip_id'];
}

// Fetch trip details
$stmt = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ? AND user_id = ?");
$stmt->execute([$trip_id, $_SESSION['user_id']]);
$trip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    die("Trip not found or you do not have permission to edit this trip.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $trip_name = $_POST['trip_name'];
        $destination = $_POST['destination'];
        $hotel = $_POST['hotel'];
        $adults_num = intval($_POST['adults_num']);
        $childs_num = intval($_POST['childs_num']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $estimated_cost = floatval($_POST['estimated_cost']);

        // Update trip with estimated cost
        $stmt = $pdo->prepare("UPDATE trips SET trip_name = ?, destination = ?, hotel = ?, adults_num = ?, childs_num = ?, start_date = ?, end_date = ?, estimated_cost = ? WHERE trip_id = ? AND user_id = ?");
        $stmt->execute([$trip_name, $destination, $hotel, $adults_num, $childs_num, $start_date, $end_date, $estimated_cost, $trip_id, $_SESSION['user_id']]);

        echo json_encode(['success' => true, 'trip_id' => $trip_id]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while saving the trip.']);
    }
    exit;
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

// Fetch activities for the current trip
$activities = [];
$stmt = $pdo->prepare("SELECT * FROM activity WHERE trip_id = ?");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $activities[] = $row;
}

// Fetch sub plans for the current trip
$sub_plans = [];
$sub_plan_types = ['activity', 'car_rental', 'concert', 'flights', 'meeting', 'restaurant', 'transportation'];
foreach ($sub_plan_types as $type) {
    $stmt = $pdo->prepare("SELECT * FROM $type WHERE trip_id = ?");
    $stmt->execute([$trip_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sub_plans[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .carousel-item img {
            max-height: 500px;
            width: auto;
            object-fit: cover;
        }

        .plan-type-icon {
            font-size: 24px;
            margin-right: 8px;
        }

        .form-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 20px;
        }

        .form-footer {
            margin-top: 20px;
        }

        .estimated-cost-display {
            margin-top: 20px;
        }

        #map {
            height: 100%;
            min-height: 400px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="form-container">
                    <h1 class="text-center form-header">Edit Trip</h1>
                    <form id="edit-trip-form">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="trip_name" class="form-label">Trip Name</label>
                                <input type="text" id="trip_name" name="trip_name" class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($trip['trip_name']) ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label for="destination" class="form-label">Destination</label>
                                <select id="destination" name="destination" class="form-select form-select-sm" required>
                                    <?php foreach ($destinations as $location_id => $location): ?>
                                        <option value="<?= $location_id ?>"
                                            <?= $location_id == $trip['destination'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($location['name']) ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div id="hotel-carousel" class="carousel slide mb-3" data-bs-ride="false">
                                <div class="carousel-inner" id="hotel-cards"></div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#hotel-carousel"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#hotel-carousel"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>

                            <input type="hidden" id="hotel" name="hotel"
                                value="<?= htmlspecialchars($trip['hotel']) ?>">

                            <div class="col-md-6">
                                <label for="adults_num" class="form-label">No. of Adults</label>
                                <input type="text" id="adults_num" name="adults_num"
                                    class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($trip['adults_num']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="childs_num" class="form-label">No. of Children</label>
                                <input type="text" id="childs_num" name="childs_num"
                                    class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($trip['childs_num']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" id="start_date" name="start_date"
                                    class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($trip['start_date']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($trip['end_date']) ?>" required>
                            </div>
                        </div>

                        <!-- Estimated Cost Display -->
                        <div class="alert alert-info fs-5 fw-bold text-center estimated-cost-display"
                            id="estimated-cost-display">Estimated Cost:
                            $0.00</div>
                        <input type="hidden" id="estimated_cost" name="estimated_cost" value="0.00">

                        <?php
                        include 'sub_plans_options.php';
                        include 'sub_plans.php';
                        ?>

                        <div class="d-flex justify-content-between form-footer">
                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                            <a href="index.php" class="btn btn-secondary btn-sm">Exit</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div id="map" class="mb-4"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        const destinationSelect = document.getElementById('destination');
        const hotelCardsContainer = document.getElementById('hotel-cards');
        const hotels = <?= json_encode($destinations); ?>;
        const activities = <?= json_encode($activities); ?>;
        const subPlans = <?= json_encode($sub_plans); ?>;

        function loadHotelsForDestination(destination) {
            hotelCardsContainer.innerHTML = '';

            if (hotels[destination]) {
                hotels[destination].hotels.forEach((hotel, index) => {
                    const isActive = hotel.name === '<?= htmlspecialchars($trip['hotel']) ?>' ? 'active' : '';
                    const card = `
                        <div class="carousel-item ${isActive}" data-hotel="${hotel.name}" data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}" data-price="${hotel.price}">
                            <div class="card">
                                <img src="assets/images/${hotel.image_name}" class="card-img-top" alt="${hotel.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${hotel.name}</h5>
                                    <div class="d-flex justify-content-start">
                                        ${'★'.repeat(hotel.stars)}${'☆'.repeat(5 - hotel.stars)}
                                    </div>
                                    <div class="mt-3">
                                        <h5 class="text-success fs-3 fw-bold">$${hotel.price}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    hotelCardsContainer.innerHTML += card;
                });
                updateSelectedHotel();
            }
        }

        function updateSelectedHotel() {
            const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
            if (activeHotelItem) {
                const selectedHotel = activeHotelItem.getAttribute('data-hotel');
                const latitude = activeHotelItem.getAttribute('data-latitude');
                const longitude = activeHotelItem.getAttribute('data-longitude');
                document.getElementById('hotel').value = selectedHotel;
                updateMapMarker(latitude, longitude);
            }
        }

        destinationSelect.addEventListener('change', function() {
            loadHotelsForDestination(this.value);
        });

        document.getElementById('hotel-carousel').addEventListener('slid.bs.carousel', updateSelectedHotel);

        const map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let currentMarker;

        function updateMapMarker(lat, lng) {
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }
            if (lat && lng) {
                currentMarker = L.marker([lat, lng]).addTo(map)
                    .bindPopup("Hotel Location").openPopup();
                map.setView([lat, lng], 10);
            }
        }

        function calculateEstimatedCost() {
            const adultsNum = parseInt(document.getElementById('adults_num').value) || 0;
            const childsNum = parseInt(document.getElementById('childs_num').value) || 0;
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
            const hotelCost = activeHotelItem ? parseFloat(activeHotelItem.getAttribute('data-price')) : 0;

            const numberOfNights = Math.max((endDate - startDate) / (1000 * 60 * 60 * 24), 1);

            // Calculate the number of rooms needed
            const totalPeople = adultsNum + childsNum;
            const roomsNeeded = Math.ceil(totalPeople / 2);

            // Calculate the estimated cost
            let estimatedCost = (hotelCost * roomsNeeded * numberOfNights);

            // Add the cost of activities and other sub plans
            subPlans.forEach(plan => {
                estimatedCost += parseFloat(plan.cost || plan.price || plan.transportation_cost || 0);
            });

            document.getElementById('estimated-cost-display').textContent = `Estimated Cost: $${estimatedCost.toFixed(2)}`;
            document.getElementById('estimated_cost').value = estimatedCost.toFixed(2);
        }

        // Attach event listeners to update the cost dynamically
        ['adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateEstimatedCost);
        });

        document.getElementById('hotel-carousel').addEventListener('slid.bs.carousel', calculateEstimatedCost);

        // Initial calculation on page load
        loadHotelsForDestination(destinationSelect.value);
        calculateEstimatedCost();

        function redirectTo(page) {
            const tripId = <?= json_encode($trip_id); ?>;
            window.location.href = `${page}?trip_id=${tripId}`;
        }

        // Handle form submission with AJAX
        document.getElementById('edit-trip-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            formData.append('trip_id', <?= json_encode($trip_id); ?>);

            fetch('edit_trip.php?trip_id=<?= $trip_id ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Trip updated successfully!');
                    } else {
                        alert('Failed to update the trip: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the trip.');
                });
        });
    </script>
</body>

</html>