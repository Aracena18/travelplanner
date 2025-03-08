<?php
// views/edit_trip_view.php
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Content -->
<div class="layout-container">
    <div class="planning-section">
        <div class="header-banner">
            <h1 class="display-4 fw-bold">
                <i class="fas fa-globe-americas me-3"></i>
                <?= htmlspecialchars($trip['trip_name']) ?>
            </h1>
            <p class="lead">
                <i class="fas fa-map-marker-alt me-2"></i>
                <?= htmlspecialchars($destinations[$trip['destination']]['name']) ?>
            </p>
        </div>

        <div class="content-wrapper">
            <!--
            <ul class="nav nav-pills mb-4" id="tripTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'overview' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#overview">
                        <i class="fas fa-info-circle"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'accommodation' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#accommodation">
                        <i class="fas fa-hotel"></i> Accommodation
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'flights' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#flights">
                        <i class="fas fa-plane"></i> Flights
                        Flights
                    </button>
                </li>
                <!--
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#activities">
                        <i class="fas fa-calendar-alt"></i> Activities
                    </button>
                </li>-->
            </ul>-->

            <div class="tab-content flex-grow-1" id="tripTabContent">
                <!--Overview Tab -->
                <div class="tab-pane fade <?= $activeTab === 'overview' ? 'show active' : '' ?>" id="overview" role="tabpanel">
                    <form id="edit-trip-form" class="needs-validation" novalidate>
                        <div class="progress-steps">
                            <div class="step-indicator step-active" data-step="1">
                                <div class="step-number">1</div>
                                <div class="step-label">Overview</div>
                            </div>
                            <div class="step-indicator" data-step="2">
                                <div class="step-number">2</div>
                                <div class="step-label">Accommodation</div>
                            </div>
                            <div class="step-indicator" data-step="3">
                                <div class="step-number">3</div>
                                <div class="step-label">Activities</div>
                            </div>
                            <div class="step-indicator" data-step="4">
                                <div class="step-number">4</div>
                                <div class="step-label">Summary</div>
                            </div>
                        </div>

                        <div class="form-steps-container">
                            <!-- Step 1: Overview -->
                             <div class="form-step active" data-step="1">
                                <div class="trip-stats row g-4">
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <h4><?= date('j M', strtotime($trip['start_date'])) ?> - <?= date('j M Y', strtotime($trip['end_date'])) ?></h4>
                                            <p class="text-muted">Duration</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <h4><?= $trip['adults_num'] + $trip['childs_num'] ?> Travelers</h4>
                                            <p class="text-muted"><?= $trip['adults_num'] ?> Adults, <?= $trip['childs_num'] ?> Children</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon">
                                                <i class="fas fa-hotel"></i>
                                            </div>
                                            <h4><?= htmlspecialchars($trip['hotel']) ?></h4>
                                            <p class="text-muted">Accommodation</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                            <h4>$<?= number_format($trip['estimated_cost'], 2) ?></h4>
                                            <p class="text-muted">Estimated Cost</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-section mt-4">
                                    <h3><i class="fas fa-info-circle me-2"></i>Trip Details</h3>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Trip Name</label>
                                            <input type="text" class="form-control" name="trip_name" value="<?= htmlspecialchars($trip['trip_name']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Destination</label>
                                            <select class="form-select" name="destination" id="destination" required>
                                                <?php foreach ($destinations as $id => $destination): ?>
                                                    <option value="<?= $id ?>" <?= $trip['destination'] == $id ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($destination['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="start_date" id="start_date" value="<?= htmlspecialchars($trip['start_date']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="end_date" id="end_date" value="<?= htmlspecialchars($trip['end_date']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Number of Adults</label>
                                            <input type="number" class="form-control" name="adults_num" id="adults_num" value="<?= htmlspecialchars($trip['adults_num']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Number of Children</label>
                                            <input type="number" class="form-control" name="childs_num" id="childs_num" value="<?= htmlspecialchars($trip['childs_num']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Estimated Cost</label>
                                            <input type="text" class="form-control" name="estimated_cost" id="estimated_cost" value="<?= htmlspecialchars($trip['estimated_cost']) ?>" readonly>
                                        </div>
                                        <!-- Hidden input to store selected hotel -->
                                        <input type="hidden" id="hotel" name="hotel" value="<?= htmlspecialchars($trip['hotel']) ?>">
                                    </div>
                                    <div id="estimated-cost-display" class="mt-3 fw-bold"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Accommodation Tab -->
                <div class="tab-pane fade <?= $activeTab === 'accommodation' ? 'show active' : '' ?>" id="accommodation" role="tabpanel">
                    <div class="filter-section">
                        <h4 class="mb-3">Filter Hotels</h4>
                        <div class="filter-group">
                            <button class="filter-chip active">All</button>
                            <button class="filter-chip">5 Star</button>
                            <button class="filter-chip">4 Star</button>
                            <button class="filter-chip">3 Star</button>
                            <button class="filter-chip">Pool</button>
                            <button class="filter-chip">Spa</button>
                            <button class="filter-chip">Beach Access</button>
                        </div>
                    </div>

                    <div class="hotel-showcase">
                        <h3 class="section-title mb-4">
                            <i class="fas fa-hotel me-2"></i>Available Hotels
                        </h3>
                        <!-- Carousel structure -->
                        <div id="hotel-carousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner" id="hotel-grid">
                                <!-- Hotel cards will be dynamically generated here -->
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#hotel-carousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#hotel-carousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Flights Tab -->
                <div class="tab-pane fade <?= $activeTab === 'flights' ? 'show active' : '' ?>" id="flights" role="tabpanel">
                    <div class="flights-container">
                        <div class="section-header d-flex justify-content-between align-items-center mb-4">
                            <h3><i class="fas fa-plane me-2"></i>Flight Bookings</h3>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFlightModal">
                                <i class="fas fa-plus me-2"></i>Add Flight
                            </button>
                        </div>

                        <!-- Flight Cards Container -->
                        <div class="flight-cards">
                            <?php if (!empty($flights)): ?>
                                <?php foreach ($flights as $flight): ?>
                                    <div class="flight-card">
                                        <div class="flight-card-header">
                                            <div class="airline-info">
                                                <img src="/travelplanner-master/assets/images/airlines/<?= strtolower($flight['airline']) ?>.png" alt="<?= $flight['airline'] ?>" class="airline-logo">
                                                <span class="airline-name"><?= htmlspecialchars($flight['airline']) ?></span>
                                            </div>
                                            <div class="flight-price">$<?= number_format($flight['price'], 2) ?></div>
                                        </div>
                                        <div class="flight-card-body">
                                            <div class="flight-route">
                                                <div class="departure">
                                                    <div class="city"><?= htmlspecialchars($flight['departure']) ?></div>
                                                    <div class="time"><?= date('H:i', strtotime($flight['departure_time'])) ?></div>
                                                    <div class="date"><?= date('M d, Y', strtotime($flight['departure_time'])) ?></div>
                                                </div>
                                                <div class="flight-line">
                                                    <div class="line"></div>
                                                    <i class="fas fa-plane"></i>
                                                    <div class="duration"><?= $flight['duration'] ?></div>
                                                </div>
                                                <div class="arrival">
                                                    <div class="city"><?= htmlspecialchars($flight['arrival']) ?></div>
                                                    <div class="time"><?= date('H:i', strtotime($flight['arrival_time'])) ?></div>
                                                    <div class="date"><?= date('M d, Y', strtotime($flight['arrival_time'])) ?></div>
                                                </div>
                                            </div>
                                            <div class="flight-details mt-3">
                                                <div class="detail-item">
                                                    <i class="fas fa-suitcase"></i>
                                                    <span>2 bags included</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-utensils"></i>
                                                    <span>Meal included</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-wifi"></i>
                                                    <span>Wi-Fi available</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flight-card-footer">
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteFlight(<?= $flight['id'] ?>)">
                                                <i class="fas fa-trash-alt me-1"></i>Remove
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm" onclick="editFlight(<?= $flight['id'] ?>)">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-flights-message">
                                    <i class="fas fa-plane-slash mb-3"></i>
                                    <h4>No Flights Added Yet</h4>
                                    <p>Start by adding your flight details to your trip.</p>
                                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addFlightModal">
                                        <i class="fas fa-plus me-2"></i>Add Your First Flight
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Activities Tab -->
                <div class="tab-pane fade" id="activities" role="tabpanel">
                    <div class="filter-section">
                        <h4 class="mb-3">Filter Activities</h4>
                        <div class="filter-group">
                            <button class="filter-chip active">All</button>
                            <button class="filter-chip">Tours</button>
                            <button class="filter-chip">Adventure</button>
                            <button class="filter-chip">Cultural</button>
                            <button class="filter-chip">Food & Dining</button>
                            <button class="filter-chip">Entertainment</button>
                        </div>
                    </div>

                    <div class="timeline">
                        <?php foreach ($sub_plans as $plan): ?>
                            <div class="timeline-item" data-activity-id="<?= $plan['id'] ?>">
                                <div class="timeline-icon">
                                    <i class="fas <?= getActivityIcon($plan['type'] ?? 'activity') ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-1"><?= htmlspecialchars($plan['name'] ?? $plan['title'] ?? 'Untitled Activity') ?></h5>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-clock me-2"></i>
                                                <?= date('M j, Y g:i A', strtotime($plan['start_time'] ?? $plan['date'])) ?>
                                            </p>
                                        </div>
                                        <div class="activity-cost">
                                            <span class="badge bg-primary">
                                                $<?= number_format($plan['cost'] ?? $plan['price'] ?? 0, 2) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <p class="mb-3"><?= htmlspecialchars($plan['description'] ?? '') ?></p>
                                    <div class="activity-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editActivity(<?= $plan['id'] ?>)">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteActivity(<?= $plan['id'] ?>)">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Add Activity Button -->
                        <div class="text-center mt-4">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                                <i class="fas fa-plus me-2"></i>Add New Activity
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <div class="sticky-map-container">
            <div id="map-large"></div>
            <div class="map-overlay">
                <div class="selected-destination-info">
                    <h4><?= htmlspecialchars($destinations[$trip['destination']]['name']) ?></h4>
                    <p class="mb-0"><i class="fas fa-hotel me-2"></i><?= htmlspecialchars($trip['hotel']) ?></p>
                    <div class="trip-route mt-3">
                        <div class="route-points">
                            <!-- Dynamic route points will be added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    <button type="button" class="float-button" onclick="enableEditing()" title="Edit Trip">
        <i class="fas fa-edit"></i>
    </button>
    <button type="button" class="float-button success" onclick="shareTrip()" title="Share Trip">
        <i class="fas fa-share-alt"></i>
    </button>
</div>

<!-- Pass data to JavaScript -->
<script>
    window.hotelsData = <?= json_encode($destinations[$trip['destination']]['hotels'] ?? []) ?>;
    window.currentTripId = <?= json_encode($trip['trip_id']); ?>;
</script>

<?php
include __DIR__ . '/../includes/vars.php';
include __DIR__ . '/../includes/footer.php';
?>

<!-- Add Activity Modal -->
<div class="modal fade" id="addActivityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="activity-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Activity Name</label>
                            <input type="text" class="form-control" name="activity_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Activity Type</label>
                            <select class="form-select" name="activity_type" required>
                                <option value="tour">Tour</option>
                                <option value="adventure">Adventure</option>
                                <option value="cultural">Cultural</option>
                                <option value="dining">Food & Dining</option>
                                <option value="entertainment">Entertainment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" name="activity_datetime" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="activity_cost" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="activity_description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="activity_location">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveActivity()">Save Activity</button>
            </div>
        </div>
    </div>
</div>
