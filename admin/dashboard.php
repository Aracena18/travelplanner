<?php
session_start();
require_once '../db.php';
require_once 'auth_middleware.php';
require_once 'admin_layout.php';

// Check admin authentication
checkAdminAuth();

// Get some basic statistics with error checking
$users_count = 0;
$trips_count = 0;

$users_result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($users_result) {
    $users_count = $users_result->fetch_assoc()['count'];
} else {
    echo "Error getting users count: " . $conn->error;
}

$trips_result = $conn->query("SELECT COUNT(*) as count FROM trips");
if ($trips_result) {
    $trips_count = $trips_result->fetch_assoc()['count'];
} else {
    echo "Error getting trips count: " . $conn->error;
}

$hotels_query = "SELECT * FROM hotels ORDER BY created_at DESC LIMIT 10";
$hotels_result = $conn->query($hotels_query);

// Add debugging information
echo "<!-- Debug Info: -->";
echo "<!-- Database connected: " . ($conn ? "Yes" : "No") . " -->";
echo "<!-- Users count: $users_count -->";
echo "<!-- Trips count: $trips_count -->";

// Fetch locations
$sql = "SELECT id, name FROM locations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            background: #f8f9fa;
        }

        .admin_page {
            display: flex;
        }

        #content {
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 2rem;
        }

        .card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body>
    <section class="admin_page">
        <div class="container mt-4" id="content">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h2>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-4"><?php echo $users_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Trips</h5>
                            <p class="card-text display-4"><?php echo $trips_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hotelbooking mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Hotel Bookings</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addHotelModal">
                        <i class="fas fa-plus"></i> Add Hotel
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Hotel Name</th>
                                        <th>Image Name</th>
                                        <th>Stars</th>
                                        <th>Location Id</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Price/Night</th>
                                        <th>Available Rooms</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="hotelsTableBody">
                                    <?php if ($hotels_result && $hotels_result->num_rows > 0): ?>
                                        <?php while ($hotel = $hotels_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($hotel['name']); ?></td>
                                                <td><?php echo htmlspecialchars($hotel['image_name']); ?></td>
                                                <td><?php echo htmlspecialchars($hotel['stars']); ?></td>
                                                <td><?php echo htmlspecialchars($hotel['location_id']); ?></td>
                                                <td><?php echo htmlspecialchars($hotel['latitude']); ?></td>
                                                <td><?php echo htmlspecialchars($hotel['longitude']); ?></td>
                                                <td>$<?php echo number_format($hotel['price'], 2); ?></td>
                                                <td><?php echo $hotel['available_rooms']; ?></td>
                                                <td>
                                                    <span
                                                        class="badge bg-<?php echo $hotel['status'] === 'active' ? 'success' : 'warning'; ?>">
                                                        <?php echo ucfirst($hotel['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#editHotelModal<?php echo $hotel['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="deleteHotel(<?php echo $hotel['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No hotels found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update the Add Hotel Modal form -->
            <div class="modal fade" id="addHotelModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Hotel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="addHotelForm" onsubmit=" return submitHotelForm(event)">
                            <div class="modal-body">
                                <div id="formAlert" class="alert" style="display: none;"></div>
                                <div class="mb-3">
                                    <label for="hotelName" class="form-label">Hotel Name</label>
                                    <input type="text" class="form-control" id="hotelName" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="image_name" class="form-label">Image Name</label>
                                    <input type="text" class="form-control" id="image_name" name="image_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stars" class="form-label">Stars</label>
                                    <input type="text" class="form-control" id="stars" name="stars" required>
                                </div>
                                <div class="mb-3">
                                    <label for="location_id" class="form-label">Location</label>
                                    <select class="form-select" id="location_id" name="location_id" required>
                                        <option value="">Select a location</option>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row["id"] . '">' . htmlspecialchars($row["name"]) . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">No locations available</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php $conn->close(); ?>
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude" required>
                                </div>
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude" required>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price per Night</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="rooms" class="form-label">Available Rooms</label>
                                    <input type="number" class="form-control" id="rooms" name="rooms" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Hotel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function submitHotelForm(event) {
            event.preventDefault();

            const form = document.getElementById('addHotelForm');
            const formAlert = document.getElementById('formAlert');
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            try {
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                submitButton.disabled = true;
                formAlert.style.display = 'none';

                const formData = new FormData(form);

                const response = await fetch('add_hotel.php', {
                    method: 'POST',
                    body: formData
                });

                const responseText = await response.text(); // Get the raw response text
                console.log('Raw response:', responseText); // Log the raw response

                // Check if the response is valid JSON
                let data;
                try {
                    data = JSON.parse(responseText); // Parse the JSON response
                } catch (e) {
                    console.error('Invalid JSON response:', responseText);
                    throw new Error('Invalid JSON response from server');
                }

                if (!data.success) {
                    throw new Error(data.message || 'Failed to add hotel');
                }

                // Success handling
                formAlert.className = 'alert alert-success';
                formAlert.textContent = data.message;
                formAlert.style.display = 'block';

                // Reset form and refresh table
                form.reset();
                await refreshHotelsTable();

                // Close modal after success
                const modal = bootstrap.Modal.getInstance(document.getElementById('addHotelModal'));
                if (modal) {
                    setTimeout(() => modal.hide(), 1500);
                }

            } catch (error) {
                console.error('Operation failed:', error);
                formAlert.className = 'alert alert-danger';
                formAlert.textContent = error.message;
                formAlert.style.display = 'block';
            } finally {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }

            return false;
        }


        // Add this helper function to refresh the hotels table
        async function refreshHotelsTable() {
            try {
                const response = await fetch('get_hotels.php');
                if (!response.ok) throw new Error('Failed to fetch hotels');

                const data = await response.json();
                if (!data.success) throw new Error(data.message);

                const tableBody = document.querySelector('#hotelsTableBody');
                if (!data.data.length) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hotels found</td></tr>';
                    return;
                }

                tableBody.innerHTML = data.data.map(hotel => `
            <tr>
                <td>${escapeHtml(hotel.name || '')}</td>
                <td>${escapeHtml(hotel.image_name || '')}</td>
                <td>${escapeHtml(hotel.stars ? hotel.stars.toString() : '')}</td>
                <td>${escapeHtml(hotel.location_id ? hotel.location_id.toString() : '')}</td>
                <td>${escapeHtml(hotel.latitude ? hotel.latitude.toString() : '')}</td>
                <td>${escapeHtml(hotel.longitude ? hotel.longitude.toString() : '')}</td>
                <td>$${hotel.price ? parseFloat(hotel.price).toFixed(2) : ''}</td>
                <td>${escapeHtml(hotel.available_rooms ? hotel.available_rooms.toString() : '')}</td>
                <td>
                    <span class="badge bg-${hotel.status === 'active' ? 'success' : 'warning'}">
                        ${escapeHtml(hotel.status || '')}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editHotel(${hotel.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteHotel(${hotel.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

            } catch (error) {
                console.error('Failed to refresh table:', error);
                alert('Error refreshing hotel list: ' + error.message);
            }
        }

        // Helper function to prevent XSS
        function escapeHtml(unsafe) {
            if (typeof unsafe !== 'string') {
                return '';
            }
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Function to delete a hotel
        function deleteHotel(hotelId) {
            if (confirm('Are you sure you want to delete this hotel?')) {
                fetch('delete_hotel.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: hotelId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error deleting hotel');
                        }
                    });
            }
        }

        // Fetch locations and populate the dropdown
        document.addEventListener('DOMContentLoaded', function() {
            fetchLocations();
        });

        function fetchLocations() {
            fetch('/api/locations') // Update this URL to your actual API endpoint
                .then(response => response.json())
                .then(data => populateLocationDropdown(data))
                .catch(error => console.error('Error fetching locations:', error));
        }

        function populateLocationDropdown(locations) {
            const locationSelect = document.getElementById('location_id');
            locations.forEach(location => {
                const option = document.createElement('option');
                option.value = location.location_id; // Assuming `location_id` is the primary key
                option.textContent = location.location_name; // Assuming `location_name` holds the display name
                locationSelect.appendChild(option);
            });
        }

        // Add this to the bottom of dashboard.php before </body>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the hotels link
            const hotelsLink = document.querySelector('a[href="hotels.php"]');

            // Prevent default navigation
            hotelsLink.addEventListener('click', function(e) {
                hotelsLink.classList.add('active');
            });
        });
    </script>
</body>

</html>