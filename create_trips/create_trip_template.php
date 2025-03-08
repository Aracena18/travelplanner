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
          <div class="form-section">
            <h3 class="section-title mb-4">
              <i class="fas fa-hotel section-icon"></i>
              Select Your Accommodation
            </h3>
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
                      <button type="button" class="btn btn-light">
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