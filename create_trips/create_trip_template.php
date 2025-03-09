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
                      <button type="button" class="btn btn-light">
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
      <div class="modal-header">
        <h5 class="modal-title" id="flightModalLabel">
          <i class="fas fa-plane"></i> Book Your Flight
        </h5>
        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Flight search container includes a data attribute for the API endpoint -->
        <div class="flight-search-container mb-4" data-api-endpoint="https://booking-com15.p.rapidapi.com/api/v1/flights/">

          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="departureCity" placeholder="From">
                <label for="departureCity">Departure City</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="arrivalCity" placeholder="To">
                <label for="arrivalCity">Arrival City</label>
              </div>
            </div>
            <!-- Departure Date -->
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="flightDepartureDate" placeholder="Departure Date">
                <label for="flightDepartureDate">Departure Date</label>
              </div>
            </div>
            <!-- Return Date (optional) -->
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="flightReturnDate" placeholder="Return Date">
                <label for="flightReturnDate">Return Date</label>
              </div>
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100" id="searchFlights">
                <i class="fas fa-search"></i> Search Flights
              </button>
            </div>
          </div>
        </div>
        <!-- Loading indicator -->
        <div id="flightLoading" class="d-none text-center">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        <!-- Error message container -->
        <div id="flightError" class="alert alert-danger d-none" role="alert"></div>
        
        <div class="flight-results">
          <div class="flight-filters mb-3">
            <div class="btn-group" role="group" aria-label="Flight filters">
              <button type="button" class="btn btn-outline-primary active" data-filter="all">All Flights</button>
              <button type="button" class="btn btn-outline-primary" data-filter="direct">Direct Only</button>
              <button type="button" class="btn btn-outline-primary" data-filter="cheapest">Cheapest</button>
              <button type="button" class="btn btn-outline-primary" data-filter="fastest">Fastest</button>
            </div>
          </div>
          <div id="flightsList" class="flight-list">
            <!-- Flight cards will be dynamically populated here -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


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

  <!-- Custom JS file -->
  <script src="create_trip.js"></script>

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