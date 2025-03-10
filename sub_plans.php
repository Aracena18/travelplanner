<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/sub_plans.php
include 'db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Get the trip_id from the URL
if (!isset($_GET['trip_id'])) {
    header('Location: create_trip.php');
    exit;
} else {
    $trip_id = $_GET['trip_id'];
}

// Handle deletion of sub plans
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = $_GET['delete'];
    $type = $_GET['type'];

    $table = '';
    switch ($type) {
        case 'activity':
            $table = 'activity';
            break;
        case 'car_rental':
            $table = 'car_rental';
            break;
        case 'concert':
            $table = 'concert';
            break;
        case 'flight':
            $table = 'flights';
            break;
        case 'meeting':
            $table = 'meeting';
            break;
        case 'restaurant':
            $table = 'restaurant';
            break;
        case 'transportation':
            $table = 'transportation';
            break;
    }

    if ($table) {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: edit_trip.php?trip_id=$trip_id");
        exit;
    }
}

// Initialize total cost
$total_cost = 0;

// Fetch activities for the current trip
$activities = [];
$stmt = $pdo->prepare("SELECT * FROM activity WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $activities[] = $row;
    $total_cost += $row['cost'];
}

// Fetch car rentals for the current trip
$car_rentals = [];
$stmt = $pdo->prepare("SELECT * FROM car_rental WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $car_rentals[] = $row;
    $total_cost += $row['cost'];
}

// Fetch concerts for the current trip
$concerts = [];
$stmt = $pdo->prepare("SELECT * FROM concert WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $concerts[] = $row;
    $total_cost += $row['cost'];
}

// Fetch flights for the current trip
$flights = [];
$stmt = $pdo->prepare("SELECT * FROM flights WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $flights[] = $row;
    $total_cost += $row['cost'];
}

// Fetch meetings for the current trip
$meetings = [];
$stmt = $pdo->prepare("SELECT * FROM meeting WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $meetings[] = $row;
    $total_cost += $row['cost'];
}

// Fetch restaurants for the current trip
$restaurants = [];
$stmt = $pdo->prepare("SELECT * FROM restaurant WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $restaurants[] = $row;
    $total_cost += $row['price'];
}

// Fetch transportation for the current trip
$transportations = [];
$stmt = $pdo->prepare("SELECT * FROM transportation WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $transportations[] = $row;
    $total_cost += $row['transportation_cost'];
}

function convertTo12HourFormat($time)
{
    $timeParts = explode(':', $time);
    $hours = (int)$timeParts[0];
    $minutes = $timeParts[1];
    $period = $hours >= 12 ? 'PM' : 'AM';
    $hours = $hours % 12;
    $hours = $hours ? $hours : 12; // the hour '0' should be '12'
    return sprintf('%02d:%02d %s', $hours, $minutes, $period);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sub Plans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Sub Plans</h2>

        <?php if (!empty($activities)): ?>
            <h3>Activities</h3>
            <div class="row justify-content-start">
                <?php foreach ($activities as $activity): ?>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?= htmlspecialchars($activity['name']) ?></h5>
                                <p class="card-text">
                                    <strong>Start Date:</strong> <?= htmlspecialchars($activity['start_date']) ?><br>
                                    <strong>End Date:</strong> <?= htmlspecialchars($activity['end_date']) ?><br>
                                    <strong>Start Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($activity['start_time'])) ?><br>
                                    <strong>End Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($activity['end_time'])) ?><br>
                                    <strong>Address:</strong> <?= htmlspecialchars($activity['address']) ?><br>
                                    <strong>Website:</strong> <?= htmlspecialchars($activity['website']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($activity['email']) ?><br>
                                    <strong>Cost:</strong> $<?= htmlspecialchars($activity['cost']) ?>
                                </p>
                                <button class="btn btn-warning btn-sm edit-sub-plan" data-id="<?= $activity['id'] ?>"
                                    data-type="activity">Edit</button>
                                <a href="sub_plans.php?trip_id=<?= $trip_id ?>&delete=<?= $activity['id'] ?>&type=activity"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this activity?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($car_rentals)): ?>
            <h3>Car Rentals</h3>
            <div class="row justify-content-start">
                <?php foreach ($car_rentals as $car_rental): ?>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?= htmlspecialchars($car_rental['rental_agency']) ?></h5>
                                <p class="card-text">
                                    <strong>Start Date:</strong> <?= htmlspecialchars($car_rental['start_date']) ?><br>
                                    <strong>End Date:</strong> <?= htmlspecialchars($car_rental['end_date']) ?><br>
                                    <strong>Start Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($car_rental['start_time'])) ?><br>
                                    <strong>End Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($car_rental['end_time'])) ?><br>
                                    <strong>Website:</strong> <?= htmlspecialchars($car_rental['website']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($car_rental['email']) ?><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars($car_rental['phone']) ?><br>
                                    <strong>Cost:</strong> $<?= htmlspecialchars($car_rental['cost']) ?>
                                </p>
                                <button class="btn btn-warning btn-sm edit-sub-plan" data-id="<?= $car_rental['id'] ?>"
                                    data-type="car_rental">Edit</button>
                                <a href="sub_plans.php?trip_id=<?= $trip_id ?>&delete=<?= $car_rental['id'] ?>&type=car_rental"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this car rental?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($concerts)): ?>
            <h3>Concerts</h3>
            <div class="row justify-content-start">
                <?php foreach ($concerts as $concert): ?>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?= htmlspecialchars($concert['event_name']) ?></h5>
                                <p class="card-text">
                                    <strong>Start Date:</strong> <?= htmlspecialchars($concert['start_date']) ?><br>
                                    <strong>End Date:</strong> <?= htmlspecialchars($concert['end_date']) ?><br>
                                    <strong>Start Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($concert['start_time'])) ?><br>
                                    <strong>End Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($concert['end_time'])) ?><br>
                                    <strong>Venue:</strong> <?= htmlspecialchars($concert['venue']) ?><br>
                                    <strong>Address:</strong> <?= htmlspecialchars($concert['address']) ?><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars($concert['phone']) ?><br>
                                    <strong>Website:</strong> <?= htmlspecialchars($concert['website']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($concert['email']) ?>
                                </p>
                                <button class="btn btn-warning btn-sm edit-sub-plan" data-id="<?= $concert['id'] ?>"
                                    data-type="concert">Edit</button>
                                <a href="sub_plans.php?trip_id=<?= $trip_id ?>&delete=<?= $concert['id'] ?>&type=concert"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this concert?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($flights)): ?>
            <h3>Flights</h3>
            <div class="row justify-content-start">
                <?php foreach ($flights as $flight): ?>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?= htmlspecialchars($flight['airline']) ?></h5>
                                <p class="card-text">
                                    <strong>Flight:</strong> <?= htmlspecialchars($flight['flight']) ?><br>
                                    <strong>Location ID:</strong> <?= htmlspecialchars($flight['location_id']) ?><br>
                                    <strong>Cost:</strong> $<?= htmlspecialchars($flight['cost']) ?><br>
                                    <strong>Departure Date:</strong> <?= htmlspecialchars($flight['departure_date']) ?><br>
                                    <strong>Departure Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($flight['departure_time'])) ?><br>
                                    <strong>Arrival Date:</strong> <?= htmlspecialchars($flight['arrival_date']) ?><br>
                                    <strong>Arrival Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($flight['arrival_time'])) ?>
                                </p>
                                <button class="btn btn-warning btn-sm edit-sub-plan" data-id="<?= $flight['id'] ?>"
                                    data-type="flight">Edit</button>
                                <a href="sub_plans.php?trip_id=<?= $trip_id ?>&delete=<?= $flight['id'] ?>&type=flight"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this flight?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($meetings)): ?>
            <h3>Meetings</h3>
            <div class="row justify-content-start">
                <?php foreach ($meetings as $meeting): ?>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?= htmlspecialchars($meeting['event_name']) ?></h5>
                                <p class="card-text">
                                    <strong>Start Date:</strong> <?= htmlspecialchars($meeting['start_date']) ?><br>
                                    <strong>End Date:</strong> <?= htmlspecialchars($meeting['end_date']) ?><br>
                                    <strong>Start Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($meeting['start_time'])) ?><br>
                                    <strong>End Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($meeting['end_time'])) ?><br>
                                    <strong>Venue:</strong> <?= htmlspecialchars($meeting['venue']) ?><br>
                                    <strong>Address:</strong> <?= htmlspecialchars($meeting['address']) ?><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars($meeting['phone']) ?><br>
                                    <strong>Website:</strong> <?= htmlspecialchars($meeting['website']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($meeting['email']) ?>
                                </p>
                                <button class="btn btn-warning btn-sm edit-sub-plan" data-id="<?= $meeting['id'] ?>"
                                    data-type="meeting">Edit</button>
                                <a href="sub_plans.php?trip_id=<?= $trip_id ?>&delete=<?= $meeting['id'] ?>&type=meeting"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this meeting?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($restaurants)): ?>
            <h3>Restaurants</h3>
            <div class="row justify-content-start">
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?= htmlspecialchars($restaurant['cuisine']) ?></h5>
                                <p class="card-text">
                                    <strong>Start Date:</strong> <?= htmlspecialchars($restaurant['start_date']) ?><br>
                                    <strong>End Date:</strong> <?= htmlspecialchars($restaurant['end_date']) ?><br>
                                    <strong>Start Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($restaurant['start_time'])) ?><br>
                                    <strong>End Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($restaurant['end_time'])) ?><br>
                                    <strong>Address:</strong> <?= htmlspecialchars($restaurant['address']) ?><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars($restaurant['phone']) ?><br>
                                    <strong>Website:</strong> <?= htmlspecialchars($restaurant['website']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($restaurant['email']) ?><br>
                                    <strong>Party Size:</strong> <?= htmlspecialchars($restaurant['party_size']) ?><br>
                                    <strong>Dress Code:</strong> <?= htmlspecialchars($restaurant['dress_code']) ?><br>
                                    <strong>Price:</strong> $<?= htmlspecialchars($restaurant['price']) ?>
                                </p>
                                <button class="btn btn-warning btn-sm edit-sub-plan" data-id="<?= $restaurant['id'] ?>"
                                    data-type="restaurant">Edit</button>
                                <a href="sub_plans.php?trip_id=<?= $trip_id ?>&delete=<?= $restaurant['id'] ?>&type=restaurant"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this restaurant?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($transportations)): ?>
            <h3>Transportations</h3>
            <div class="row justify-content-start">
                <?php foreach ($transportations as $transportation): ?>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?= htmlspecialchars($transportation['vehicle_info']) ?></h5>
                                <p class="card-text">
                                    <strong>Departure Date:</strong>
                                    <?= htmlspecialchars($transportation['departure_date']) ?><br>
                                    <strong>Departure Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($transportation['departure_time'])) ?><br>
                                    <strong>Arrival Date:</strong> <?= htmlspecialchars($transportation['arrival_date']) ?><br>
                                    <strong>Arrival Time:</strong>
                                    <?= htmlspecialchars(convertTo12HourFormat($transportation['arrival_time'])) ?><br>
                                    <strong>Address:</strong> <?= htmlspecialchars($transportation['address']) ?><br>
                                    <strong>Location Name:</strong>
                                    <?= htmlspecialchars($transportation['location_name']) ?><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars($transportation['phone']) ?><br>
                                    <strong>Website:</strong> <?= htmlspecialchars($transportation['website']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($transportation['email']) ?><br>
                                    <strong>Vehicle Description:</strong>
                                    <?= htmlspecialchars($transportation['vehicle_description']) ?><br>
                                    <strong>Number of Passengers:</strong>
                                    <?= htmlspecialchars($transportation['number_of_passengers']) ?><br>
                                    <strong>Transportation Cost:</strong>
                                    $<?= htmlspecialchars($transportation['transportation_cost']) ?>
                                </p>
                                <button class="btn btn-warning btn-sm edit-sub-plan" data-id="<?= $transportation['id'] ?>"
                                    data-type="transportation">Edit</button>
                                <a href="sub_plans.php?trip_id=<?= $trip_id ?>&delete=<?= $transportation['id'] ?>&type=transportation"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this transportation?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Edit Sub Plan Modal -->
        <div class="modal fade" id="editSubPlanModal" tabindex="-1" aria-labelledby="editSubPlanModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSubPlanModalLabel">Edit Sub Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-sub-plan-form">
                            <input type="hidden" id="sub_plan_id" name="sub_plan_id">
                            <input type="hidden" id="sub_plan_type" name="sub_plan_type">
                            <div class="mb-3">
                                <label for="sub_plan_name" class="form-label">Name</label>
                                <input type="text" id="sub_plan_name" name="sub_plan_name" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_start_date" class="form-label">Start Date</label>
                                <input type="date" id="sub_plan_start_date" name="sub_plan_start_date"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_end_date" class="form-label">End Date</label>
                                <input type="date" id="sub_plan_end_date" name="sub_plan_end_date" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_start_time" class="form-label">Start Time</label>
                                <input type="time" id="sub_plan_start_time" name="sub_plan_start_time"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_end_time" class="form-label">End Time</label>
                                <input type="time" id="sub_plan_end_time" name="sub_plan_end_time" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_address" class="form-label">Address</label>
                                <input type="text" id="sub_plan_address" name="sub_plan_address" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_website" class="form-label">Website</label>
                                <input type="url" id="sub_plan_website" name="sub_plan_website" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_email" class="form-label">Email</label>
                                <input type="email" id="sub_plan_email" name="sub_plan_email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_phone" class="form-label">Phone</label>
                                <input type="tel" id="sub_plan_phone" name="sub_plan_phone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="sub_plan_cost" class="form-label">Cost</label>
                                <input type="number" step="0.01" id="sub_plan_cost" name="sub_plan_cost"
                                    class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tripId = <?= json_encode($trip_id); ?>;

        document.querySelectorAll('.edit-sub-plan').forEach(button => {
            button.addEventListener('click', function() {
                const subPlanId = this.getAttribute('data-id');
                const subPlanType = this.getAttribute('data-type');

                // Fetch sub plan details using AJAX
                fetch(`get_sub_plan.php?trip_id=${tripId}&id=${subPlanId}&type=${subPlanType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const subPlan = data.data;
                            // Populate the form with sub plan details
                            document.getElementById('sub_plan_id').value = subPlanId;
                            document.getElementById('sub_plan_type').value = subPlanType;
                            document.getElementById('sub_plan_name').value = subPlan.name;
                            document.getElementById('sub_plan_start_date').value = subPlan.start_date;
                            document.getElementById('sub_plan_end_date').value = subPlan.end_date;
                            document.getElementById('sub_plan_start_time').value = subPlan.start_time;
                            document.getElementById('sub_plan_end_time').value = subPlan.end_time;
                            document.getElementById('sub_plan_address').value = subPlan.address;
                            document.getElementById('sub_plan_website').value = subPlan.website;
                            document.getElementById('sub_plan_email').value = subPlan.email;
                            document.getElementById('sub_plan_phone').value = subPlan.phone;
                            document.getElementById('sub_plan_cost').value = subPlan.cost;

                            // Show the modal
                            const editSubPlanModal = new bootstrap.Modal(document.getElementById(
                                'editSubPlanModal'));
                            editSubPlanModal.show();
                        } else {
                            alert('Failed to fetch sub plan details: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error fetching sub plan details:', error));
            });
        });

        document.getElementById('edit-sub-plan-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            // Update sub plan details using AJAX
            fetch('update_sub_plan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Sub plan updated successfully!');
                        location.reload();
                    } else {
                        alert('Failed to update sub plan: ' + data.message);
                    }
                })
                .catch(error => console.error('Error updating sub plan:', error));
        });
    </script>
</body>

</html>