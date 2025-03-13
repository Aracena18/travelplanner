<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Trip</title>
  <!-- External CSS libraries -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="create_trip.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Choices.js CSS for searchable dropdown -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <link rel="stylesheet" href="selected-flights.css">

  <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.css" />
  <link rel="stylesheet" href="/travelplanner-master/css/car_rental.css">
  
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.umd.js"></script>

</head>

<body>
  <div class="layout-container">
    <div class="planning-section">
      <div class="header-banner">
        <h1 class="display-4">
          <i class="fas fa-globe-americas me-3"></i>
          Plan Your Dream Journey
        </h1>
        <p class="lead mb-0">Create unforgettable memories with our travel planner</p>
      </div>

      <div class="progress-bar-container">
        <div class="progress-steps">
          <div class="step-indicator step-active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Destination</div>
          </div>
          <div class="step-indicator" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Accommodation</div>
          </div>
          <div class="step-indicator" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Details</div>
          </div>
          <div class="step-indicator" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Review</div>
          </div>
        </div>
      </div>

      <form method="POST" class="trip-form">
        <div class="form-step active" data-step="1">
          <!-- Hidden Trip Name field, auto-populated from the selected destination -->
          <input type="hidden" id="trip_name" name="trip_name">
          <div class="row g-3">
            <div class="col-12">
              <div class="destination-select-container">
                <label for="destination" class="form-label">
                  <i class="fas fa-map-marker-alt me-2"></i>Choose Your Destination
                </label>
                <select id="destination" name="destination" class="form-select" required>
                  <option value="">Select a destination...</option>
                  <?php foreach ($destinations as $location_id => $location): ?>
                    <option value="<?= $location_id ?>"> <?= htmlspecialchars($location['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="button" class="btn btn-primary next-step">Continue</button>
          </div>
        </div>

        <div class="form-step" data-step="2">
          <div class="accommodation-section">
            <div class="accommodation-header">
              <div>
                <h3 class="accommodation-title">
                  <i class="fas fa-hotel section-icon"></i>
                  Select Your Accommodation
                </h3>
                <p class="hotel-selection-info">Choose from our curated selection of premium hotels</p>
              </div>
              <button type="button" class="see-all-btn" data-bs-toggle="modal" data-bs-target="#allHotelsModal">
                View All Hotels <i class="fas fa-arrow-right"></i>
              </button>
            </div>

            <div class="hotel-filters">
              <button type="button" class="filter-button active">All</button>
              <button type="button" class="filter-button">
                <i class="fas fa-star"></i> 5 Star
              </button>
              <button type="button" class="filter-button">
                <i class="fas fa-dollar-sign"></i> Best Value
              </button>
              <button type="button" class="filter-button">
                <i class="fas fa-map-marker-alt"></i> Near Center
              </button>
            </div>

            <div id="hotel-carousel" class="carousel slide mb-4" data-bs-ride="false">
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
            <input type="hidden" id="hotel" name="hotel">
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary prev-step">Back</button>
            <button type="button" class="btn btn-primary next-step">Continue</button>
          </div>
        </div>

        <div class="form-step" data-step="3">
          <div class="form-section">
            <h3 class="section-title mb-4">
              <i class="fas fa-users section-icon"></i>
              Travel Details
            </h3>
            <div class="row g-4">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" id="adults_num" name="adults_num" class="form-control"
                    required>
                  <label for="adults_num">Number of Adults</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" id="childs_num" name="childs_num" class="form-control"
                    required>
                  <label for="childs_num">Number of Children</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="date" id="start_date" name="start_date" class="form-control" required>
                  <label for="start_date">Start Date</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="date" id="end_date" name="end_date" class="form-control" required>
                  <label for="end_date">End Date</label>
                </div>
              </div>

              <!-- Enhanced Reservations Section -->
              <div class="col-12">
                <div class="reservations-summary">
                  <!-- Flights Section -->
                  <div id="selectedFlightsContainer" class="selected-reservations mb-4" style="display: none;">
                    <div class="reservation-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">
                        <i class="fas fa-plane text-primary me-2"></i>Flight Reservations
                      </h5>
                      <span class="reservation-count text-muted"></span>
                    </div>
                    <div id="selectedFlightsList" class="selected-items-list">
                      <!-- Flights will be populated here -->
                    </div>
                  </div>

                  <!-- Car Rentals Section -->
                  <div id="selectedCarsContainer" class="selected-reservations mb-4" style="display: none;">
                    <div class="reservation-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">
                        <i class="fas fa-car text-primary me-2"></i>Car Rentals
                      </h5>
                      <span class="reservation-count text-muted"></span>
                    </div>
                    <div id="selectedCarsList" class="selected-items-list">
                      <!-- Car rentals will be populated here -->
                    </div>
                  </div>
                </div>
              </div>

              <!-- Reservations and Attachments -->
              <div class="col-12 mt-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="card-title">Reservations and attachments</h5>
                    <div class="d-flex flex-wrap gap-2">
                    <button type="button" id="flightBtn" class="btn btn-light">
                      <i class="fas fa-plane"></i> Flight
                    </button>

                      <button type="button" class="btn btn-light">
                        <i class="fas fa-hotel"></i> Lodging
                      </button>
                      <button type="button" id="carRentalBtn" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#carRentalModal">
                        <i class="fas fa-car"></i> Rental car
                      </button>
                      <button type="button" class="btn btn-light">
                        <i class="fas fa-utensils"></i> Restaurant
                      </button>
                      <button type="button" class="btn btn-light">
                        <i class="fas fa-paperclip"></i> Attachment
                      </button>
                      <button type="button" class="btn btn-light">
                        <i class="fas fa-ellipsis-h"></i> Other
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Budgeting -->
                <div class="card mb-3">
                  <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title mb-0">Budgeting</h5>
                    </div>
                    <div>
                      <span class="fs-5 fw-bold">$0.00</span>
                      <a href="#" class="ms-3 text-decoration-none">View details</a>
                    </div>
                  </div>
                </div>

                <!-- Notes -->
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Notes</h5>
                    <textarea class="form-control" rows="4" name="notes"
                      placeholder="Write or paste anything here: how to get around, tips and tricks"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary prev-step">Back</button>
            <button type="button" class="btn btn-primary next-step">Review</button>
          </div>
        </div>

         <!--Hidden input for Reservations -->
        <input type="hidden" id="pendingReservations" name="pendingReservations">

        <div class="form-step" data-step="4">
          <div class="trip-summary">
            <h3>Trip Summary</h3>
            <div class="estimated-cost text-center">
              <h4 class="mb-0">Estimated Trip Cost</h4>
              <div class="fs-2 fw-bold" id="estimated-cost-display">$0.00</div>
            </div>
            <div id="summary-content" class="mt-4">
              <!-- Will be populated via JavaScript -->
            </div>
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary prev-step">Back</button>
            <button type="submit" class="btn btn-create">
              <i class="fas fa-plane-departure me-2"></i>Confirm Booking
            </button>
          </div>
        </div>
      </form>
    </div>
    <div class="map-section">
      <div id="map"></div>
    </div>
  </div>

  <!-- All Hotels Modal -->
  <div class="modal fade" id="allHotelsModal" tabindex="-1" aria-labelledby="allHotelsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="allHotelsModalLabel">All Available Hotels</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4" id="allHotelsGrid">
            <!-- Hotels will be dynamically populated here -->
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Flight Booking Modal -->
<div class="modal fade" id="flightModal" tabindex="-1" aria-labelledby="flightModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h4 class="modal-title fw-bold" id="flightModalLabel">
          <i class="fas fa-plane-departure text-primary me-2"></i>Find Your Perfect Flight
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="flight-search-container">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="departureCity" placeholder="From"
                       list="departureCities" autocomplete="off">
                <label for="departureCity">
                  <i class="fas fa-plane-departure text-primary me-2"></i>From
                </label>
              </div>
              <datalist id="departureCities">
                <!-- Will be populated dynamically -->
              </datalist>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="arrivalCity" placeholder="To"
                       list="arrivalCities" autocomplete="off">
                <label for="arrivalCity">
                  <i class="fas fa-plane-arrival text-primary me-2"></i>To
                </label>
              </div>
              <datalist id="arrivalCities">
                <!-- Will be populated dynamically -->
              </datalist>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="flightDepartureDate">
                <label for="flightDepartureDate">
                  <i class="far fa-calendar-alt text-primary me-2"></i>Departure Date
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="flightReturnDate">
                <label for="flightReturnDate">
                  <i class="far fa-calendar-alt text-primary me-2"></i>Return Date (Optional)
                </label>
              </div>
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2" 
                      id="searchFlights">
                <i class="fas fa-search"></i>
                <span>Search Flights</span>
              </button>
            </div>
          </div>
        </div>

        <div class="flight-filters">
          <button class="filter-chip active" data-filter="all">All Flights</button>
          <button class="filter-chip" data-filter="direct">Direct Only</button>
          <button class="filter-chip" data-filter="cheapest">Best Price</button>
          <button class="filter-chip" data-filter="fastest">Fastest</button>
          <button class="filter-chip" data-filter="recommended">Recommended</button>
        </div>

        <!-- Loading State -->
        <div id="flightLoading" class="text-center py-5 d-none">
          <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted mt-3">Searching for the best flights...</p>
        </div>

        <!-- Error State -->
        <div id="flightError" class="alert alert-danger d-none" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>
          <span class="error-message"></span>
        </div>

        <!-- Results Container -->
        <div id="flightsList" class="flight-results mt-4">
          <!-- Flight cards will be populated here -->
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Flight Card Template -->
<template id="flightCardTemplate">
  <div class="flight-card">
    <div class="flight-header">
      <div class="airline-info">
        <img src="" alt="" class="airline-logo">
        <div>
          <h5 class="airline-name mb-0"></h5>
          <small class="text-muted flight-number"></small>
          <div class="price-comparison"></div>
        </div>
      </div>
      <div class="price-section">
        <button class="price-alert" title="Set Price Alert">
          <i class="far fa-bell"></i>
        </button>
        <span class="price-amount"></span>
        <small class="price-label">per person</small>
      </div>
    </div>
    
    <div class="flight-details">
      <div class="flight-route">
        <div class="route-point departure">
          <div class="city-code"></div>
          <div class="city-name"></div>
          <div class="time"></div>
          <div class="date"></div>
        </div>
        <div class="flight-path">
          <div class="duration-bar">
            <div class="duration-progress"></div>
            <div class="layover-indicator"></div>
          </div>
          <i class="fas fa-plane path-icon"></i>
        </div>
        <div class="route-point arrival">
          <div class="city-code"></div>
          <div class="city-name"></div>
          <div class="time"></div>
          <div class="date"></div>
        </div>
      </div>
      
      <div class="flight-features">
        <span class="feature-tag baggage-info">
          <i class="fas fa-suitcase"></i>
          <span></span>
        </span>
        <span class="feature-tag seat-info">
          <i class="fas fa-chair"></i>
          <span></span>
        </span>
        <span class="feature-tag meal-info">
          <i class="fas fa-utensils"></i>
          <span></span>
        </span>
      </div>
    </div>

    <div class="flight-info">
      <div class="info-item">
        <i class="fas fa-clock"></i>
        <span class="duration"></span>
      </div>
      <div class="info-item">
        <i class="fas fa-exchange-alt"></i>
        <span class="stops"></span>
      </div>
      <button class="flight-details-toggle">
        <span>Show Details</span>
        <i class="fas fa-chevron-down"></i>
      </button>
    </div>

    <div class="flight-details-expand">
      <!-- Expanded flight details will be populated here -->
    </div>

    <button class="select-flight-btn">
      <i class="fas fa-check me-2"></i>
      Select This Flight
    </button>
  </div>
</template>

<!-- Add Skeleton Loading Template -->
<template id="flightSkeletonTemplate">
  <div class="flight-card skeleton">
    <div class="flight-header">
      <div class="skeleton-loading" style="width: 200px; height: 24px;"></div>
      <div class="skeleton-loading" style="width: 100px; height: 32px;"></div>
    </div>
    <div class="flight-details">
      <div class="skeleton-loading" style="width: 100%; height: 120px;"></div>
    </div>
  </div>
</template>

<!-- Car Rental Modal -->
<div class="modal fade car-rental-modal" id="carRentalModal" tabindex="-1" aria-labelledby="carRentalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="carRentalModalLabel">
          <i class="fas fa-car"></i>
          Find Your Perfect Rental Car
        </h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="search-section">
          <div class="car-search-container">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label"><i class="fas fa-map-marker-alt text-primary me-2"></i>Pickup Location</label>
                <div class="location-search-group">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="pickupLocation" placeholder="Enter pickup location">
                    <label for="pickupLocation">Search location...</label>
                  </div>
                  <button class="btn btn-outline-primary btn-pick-map" data-type="pickup">
                    <i class="fas fa-map-pin"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label"><i class="fas fa-map-marker-alt text-primary me-2"></i>Drop-off Location</label>
                <div class="location-search-group">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="dropoffLocation" placeholder="Enter drop-off location">
                    <label for="dropoffLocation">Search location...</label>
                  </div>
                  <button class="btn btn-outline-primary btn-pick-map" data-type="dropoff">
                    <i class="fas fa-map-pin"></i>
                  </button>
                </div>
              </div>
              
              <div class="col-12">
                <div class="location-map-container">
                  <div id="carRentalMap" class="rental-map"></div>
                  <div class="map-instructions" id="mapInstructions">
                    Use the search boxes or map pins to set your locations
                  </div>
                </div>
                <!-- Add this new route info section -->
                <div class="route-summary mt-3">
                  <div class="route-info-panel">
                    <div class="route-stats-container">
                      <!-- Route stats will be populated here -->
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label"><i class="far fa-calendar-alt text-primary me-2"></i>Pickup Date & Time</label>
                <div class="form-floating">
                  <input type="datetime-local" class="form-control" id="pickupDateTime">
                  <label for="pickupDateTime">Select pickup date and time</label>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label"><i class="far fa-calendar-alt text-primary me-2"></i>Drop-off Date & Time</label>
                <div class="form-floating">
                  <input type="datetime-local" class="form-control" id="dropoffDateTime">
                  <label for="dropoffDateTime">Select drop-off date and time</label>
                </div>
              </div>

              <div class="col-12">
                <div class="car-filters">
                  <input type="radio" class="btn-check" name="carType" id="allCars" value="all" checked>
                  <label class="btn btn-outline-primary" for="allCars">All Cars</label>

                  <input type="radio" class="btn-check" name="carType" id="economy" value="economy">
                  <label class="btn btn-outline-primary" for="economy">Economy</label>

                  <input type="radio" class="btn-check" name="carType" id="luxury" value="luxury">
                  <label class="btn btn-outline-primary" for="luxury">Luxury</label>

                  <input type="radio" class="btn-check" name="carType" id="suv" value="suv">
                  <label class="btn btn-outline-primary" for="suv">SUV</label>
                </div>
              </div>

              <div class="col-12">
                <button class="btn btn-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2" 
                        id="searchCars">
                  <i class="fas fa-search"></i>
                  <span>Search Available Cars</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div id="carLoading" class="text-center py-5 d-none">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted mt-3">Searching for the best cars...</p>
        </div>

        <!-- Results Container -->
        <div id="carsList" class="car-results mt-4">
          <!-- Car cards will be populated here -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Car Card Template -->
<template id="carCardTemplate">
  <div class="car-card">
    <div class="car-image">
      <img src="" alt="Car Image" class="img-fluid">
      <span class="car-type-badge"></span>
    </div>
    <div class="car-details">
      <h5 class="car-name"></h5>
      <div class="car-features">
        <span class="feature"><i class="fas fa-users"></i> <span class="seats"></span></span>
        <span class="feature"><i class="fas fa-suitcase"></i> <span class="bags"></span></span>
        <span class="feature"><i class="fas fa-cog"></i> <span class="transmission"></span></span>
        <span class="feature"><i class="fas fa-snowflake"></i> <span class="ac"></span></span>
      </div>
      <div class="rental-company">
        <img src="" alt="Rental Company Logo" class="company-logo">
        <span class="company-name"></span>
      </div>
      <div class="price-section">
        <div class="price-details">
          <span class="price-amount"></span>
          <span class="price-period">per day</span>
        </div>
        <button class="btn btn-primary select-car-btn">Select</button>
      </div>
    </div>
  </div>
</template>

  <!-- External JS libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <!-- Choices.js JS for searchable dropdown -->
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <!-- Pass PHP variables to JavaScript -->
  <script>
    const hotels = <?php echo json_encode($destinations); ?>;
    const activities = <?php echo json_encode($activities); ?>;
    const tripId = <?php echo json_encode($trip_id); ?>;
  </script>

  <!-- Initialize Choices.js on the destination dropdown and update the hidden trip name -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const destinationElement = document.getElementById('destination');
      const tripNameInput = document.getElementById('trip_name');

      // Initialize Choices.js
      const choices = new Choices(destinationElement, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        placeholderValue: "Select a destination"
      });

      // Function to update the hidden trip name based on the selected destination's text
      function updateTripName() {
        const selectedOption = destinationElement.options[destinationElement.selectedIndex];
        if (selectedOption) {
          tripNameInput.value = selectedOption.text.trim();
        }
      }

      // Set initial trip name value
      updateTripName();

      // Update trip name whenever the destination changes
      destinationElement.addEventListener('change', function() {
        updateTripName();
      });
    });
  </script>

  <script src="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.umd.js"></script>
  <!-- Custom JS file -->
  <script type="module" src="/travelplanner-master/javascript/create_trips_folder/main.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-polylinedecorator/1.7.0/leaflet.polylineDecorator.min.js"></script>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const steps = document.querySelectorAll('.form-step');
      const indicators = document.querySelectorAll('.step-indicator');
      let currentStep = 1;

      function showStep(stepNumber) {
        steps.forEach(step => step.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('step-active'));

        const activeStep = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        const activeIndicator = document.querySelector(`.step-indicator[data-step="${stepNumber}"]`);

        activeStep.classList.add('active');
        activeIndicator.classList.add('step-active');

        // Update progress
        indicators.forEach(indicator => {
          const step = parseInt(indicator.dataset.step);
          if (step < currentStep) {
            indicator.classList.add('step-completed');
          } else {
            indicator.classList.remove('step-completed');
          }
        });
      }

      // Navigation button handlers
      document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', () => {
          if (currentStep < 4) {
            currentStep++;
            showStep(currentStep);
          }
        });
      });

      document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', () => {
          if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
          }
        });
      });
    });
  </script>
</body>

</html>