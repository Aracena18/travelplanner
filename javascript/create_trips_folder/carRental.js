// src/carRental.js

import { calculateEstimatedCost } from '/travelplanner-master/javascript/create_trips_folder/cost.js';
import { pendingReservations, updatePendingReservationsField } from '/travelplanner-master/javascript/create_trips_folder/reservations.js';

document.addEventListener('DOMContentLoaded', function() {
  const searchCarsBtn = document.getElementById('searchCars');
  const carsListContainer = document.getElementById('carsList');
  const carLoadingIndicator = document.getElementById('carLoading');

  searchCarsBtn.addEventListener('click', async function() {
    const pickupLocation = document.getElementById('pickupLocation').value;
    const dropoffLocation = document.getElementById('dropoffLocation').value;
    const pickupDateTime = document.getElementById('pickupDateTime').value; // e.g., "2025-03-14T10:00"
    const dropoffDateTime = document.getElementById('dropoffDateTime').value;
    const selectedType = document.querySelector('input[name="carType"]:checked').value;

    if (!pickupLocation || !dropoffLocation || !pickupDateTime || !dropoffDateTime) {
      alert('Please fill in all required fields');
      return;
    }

    // Retrieve the map markers' coordinates.
    const pickupLatLng = pickupMarker ? pickupMarker.getLatLng() : null;
    const dropoffLatLng = dropoffMarker ? dropoffMarker.getLatLng() : null;
    if (!pickupLatLng || !dropoffLatLng) {
      alert('Please select both pickup and dropoff locations on the map.');
      return;
    }

    // Extract date and time portions.
    const pickupDate = pickupDateTime.substr(0, 10);   // "YYYY-MM-DD"
    const dropoffDate = dropoffDateTime.substr(0, 10);   // "YYYY-MM-DD"
    const pickupTime = pickupDateTime.substr(11, 5);     // "HH:MM"
    const dropoffTime = dropoffDateTime.substr(11, 5);   // "HH:MM"

    // Construct the API URL for searching car rentals.
    const url = `https://booking-com15.p.rapidapi.com/api/v1/cars/searchCarRentals?` +
      `pick_up_latitude=${pickupLatLng.lat}&pick_up_longitude=${pickupLatLng.lng}` +
      `&drop_off_latitude=${dropoffLatLng.lat}&drop_off_longitude=${dropoffLatLng.lng}` +
      `&pick_up_date=${pickupDate}&drop_off_date=${dropoffDate}` +
      `&pick_up_time=${encodeURIComponent(pickupTime)}&drop_off_time=${encodeURIComponent(dropoffTime)}` +
      `&driver_age=30&currency_code=USD`;

    const options = {
      method: 'GET',
      headers: {
        'x-rapidapi-key': 'a4f936d18fmsh72c62428617d573p1b6010jsn36884d1c931d',
        'x-rapidapi-host': 'booking-com15.p.rapidapi.com'
      }
    };

    // Show loading indicator and clear any previous results.
    carLoadingIndicator.classList.remove('d-none');
    carsListContainer.innerHTML = '';

    try {
      const response = await fetch(url, options);
      const resultData = await response.json();
      console.log('API Search Response:', resultData);

      carsListContainer.innerHTML = '';

      if (resultData.status && resultData.data && resultData.data.search_results) {
        // Extract the search_key from the search response for later use.
        const searchKey = resultData.data.search_key;
        const searchResults = resultData.data.search_results;

        searchResults.forEach(resultItem => {
          // Use vehicle_info for proper details.
          const vehicle = resultItem.vehicle_info || {};
          const vehicleId = resultItem.vehicle_id;
          const carName = vehicle.v_name || `Car ${vehicleId}`;
          const carImage = vehicle.image_url || vehicle.image_thumbnail_url || 'default_car_image.jpg';
          const transmission = vehicle.transmission || 'N/A';
          const mileage = vehicle.mileage || 'N/A';
          const seats = vehicle.seats || 'N/A';
          const doors = vehicle.doors || 'N/A';
          const suitcasesSmall = vehicle.suitcases ? vehicle.suitcases.small : 'N/A';
          const suitcasesBig = vehicle.suitcases ? vehicle.suitcases.big : 'N/A';
          
          // Supplier details from supplier_info.
          const supplier = resultItem.supplier_info || {};
          const companyName = supplier.name || 'Unknown';
          const companyLogo = supplier.imageUrl || '';

          // Pricing details.
          const pricing = resultItem.pricing_info || {};
          const price = pricing.price || 'N/A';
          const currency = pricing.currency || 'USD';

          // Build the car card layout.
          const carCard = document.createElement('div');
          carCard.className = 'car-card';
          carCard.innerHTML = `
            <div class="car-image">
              <img src="${carImage}" alt="${carName}">
              <span class="car-type-badge">${transmission}</span>
            </div>
            <div class="car-details">
              <h5 class="car-name">${carName}</h5>
              <div class="car-features">
                <span class="feature"><i class="fas fa-cog"></i> ${transmission}</span>
                <span class="feature"><i class="fas fa-tachometer-alt"></i> ${mileage}</span>
                <span class="feature"><i class="fas fa-users"></i> ${seats} seats</span>
                <span class="feature"><i class="fas fa-door-open"></i> ${doors} doors</span>
              </div>
              <div class="rental-company">
                <img src="${companyLogo}" alt="${companyName}" class="company-logo">
                <span class="company-name">${companyName}</span>
              </div>
              <div class="price-section">
                <div class="price-details">
                  <span class="price-amount">${currency}${price}</span>
                  <span class="price-period">per day</span>
                </div>
                <button class="btn btn-primary select-car-btn" data-car-id="${vehicleId}">Select</button>
              </div>
            </div>
          `;

          // Bind select button event to fetch detailed info.
          carCard.querySelector('.select-car-btn').addEventListener('click', async function() {
            const carId = this.dataset.carId;
            if (pendingReservations.rentalCars.some(c => c.car_id === carId)) {
              alert('This car is already added to your reservation.');
              return;
            }

            // Fetch detailed car info using the vehicleDetails API.
            const carDetails = await fetchCarDetails(carId, searchKey);
            console.log('Vehicle Details:', carDetails);

            // Build the reservation data with detailed info if available.
            const carData = {
              car_id: carId,
              name: carDetails && carDetails.carName ? carDetails.carName : carName,
              image: carDetails && carDetails.carImage ? carDetails.carImage : carImage,
              transmission: carDetails && carDetails.transmission ? carDetails.transmission : transmission,
              mileage: carDetails && carDetails.mileage ? carDetails.mileage : mileage,
              fuelPolicy: carDetails && carDetails.fuelPolicy ? carDetails.fuelPolicy : 'N/A',
              numberOfDoors: carDetails && carDetails.numberOfDoors ? carDetails.numberOfDoors : doors,
              smallSuitcases: carDetails && carDetails.smallSuitcases ? carDetails.smallSuitcases : suitcasesSmall,
              bigSuitcases: carDetails && carDetails.bigSuitcases ? carDetails.bigSuitcases : suitcasesBig,
              numberOfSeats: carDetails && carDetails.numberOfSeats ? carDetails.numberOfSeats : seats,
              airConditioning: carDetails && carDetails.airConditioning ? carDetails.airConditioning : 'N/A',
              carClass: carDetails && carDetails.carClass ? carDetails.carClass : 'N/A',
              company: companyName,
              price: carDetails && carDetails.priceValue ? carDetails.priceValue : price,
              currency: carDetails && carDetails.currency ? carDetails.currency : currency,
              details: carDetails,  // Store all detailed info.
              pickup: pickupLocation,
              dropoff: dropoffLocation,
              pickupDateTime: pickupDateTime,
              dropoffDateTime: dropoffDateTime
            };

            pendingReservations.rentalCars.push(carData);
            updatePendingReservationsField();
            calculateEstimatedCost();

            // Close modal.
            bootstrap.Modal.getInstance(document.getElementById('carRentalModal')).hide();
          });

          carsListContainer.appendChild(carCard);
        });
      } else {
        carsListContainer.innerHTML = `<p>No cars found</p>`;
      }
    } catch (error) {
      console.error('Error fetching car rentals:', error);
      alert('An error occurred while fetching available car rentals. Please try again.');
    } finally {
      carLoadingIndicator.classList.add('d-none');
    }
  });
});

/**
 * Fetch detailed information for a vehicle.
 * @param {string} vehicleId - The vehicle_id from the search result.
 * @param {string} searchKey - The search_key from the search response.
 * @returns {Promise<object|null>} - The extracted vehicle details or null on error.
 */
async function fetchCarDetails(vehicleId, searchKey) {
  const url = `https://booking-com15.p.rapidapi.com/api/v1/cars/vehicleDetails?` +
    `vehicle_id=${vehicleId}&search_key=${encodeURIComponent(searchKey)}` +
    `&units=metric&currency_code=USD&languagecode=en-us`;
  const options = {
    method: 'GET',
    headers: {
      'x-rapidapi-key': 'a4f936d18fmsh72c62428617d573p1b6010jsn36884d1c931d',
      'x-rapidapi-host': 'booking-com15.p.rapidapi.com'
    }
  };

  try {
    const response = await fetch(url, options);
    const detailsData = await response.json();
    console.log('API Details Response:', detailsData);
    if (detailsData.status && detailsData.data) {
      const data = detailsData.data;
      const vehicle = data.vehicle || {};
      const carName = vehicle.makeAndModel || 'Unknown Model';
      const carImage = data.imageUrl || 'default_car_image.jpg';
      const spec = vehicle.specification || {};
      const fuelPolicy = spec.fuelPolicy || 'N/A';
      const mileage = spec.mileage || 'N/A';
      const transmission = spec.transmission || 'N/A';
      const numberOfDoors = spec.numberOfDoors || 'N/A';
      const smallSuitcases = spec.smallSuitcases || 'N/A';
      const bigSuitcases = spec.bigSuitcases || 'N/A';
      const numberOfSeats = spec.numberOfSeats || 'N/A';
      const airConditioning = spec.airConditioning ? 'A/C' : 'No A/C';
      const carClass = spec.carClass || 'N/A';
      const priceValue = data.price && data.price.driveAway ? data.price.driveAway.value : 'N/A';
      const currency = data.price && data.price.driveAway ? data.price.driveAway.currency : 'USD';

      return {
        carName,
        carImage,
        fuelPolicy,
        mileage,
        transmission,
        numberOfDoors,
        smallSuitcases,
        bigSuitcases,
        numberOfSeats,
        airConditioning,
        carClass,
        priceValue,
        currency
      };
    }
    return null;
  } catch (error) {
    console.error('Error fetching car details:', error);
    return null;
  }
}


// --------------------------
// Map & Location functions (unchanged)
// --------------------------

let rentalMap;
let pickupMarker;
let dropoffMarker;
let activeLocationType = null;

function initializeRentalMap() {
  console.log('Starting map initialization...');
  
  const mapContainer = document.getElementById('carRentalMap');
  if (!mapContainer) {
    console.error('Map container not found! ID: carRentalMap');
    return;
  }

  console.log('Map container dimensions:', {
    width: mapContainer.offsetWidth,
    height: mapContainer.offsetHeight,
    visibility: window.getComputedStyle(mapContainer).visibility,
    display: window.getComputedStyle(mapContainer).display
  });

  try {
    console.log('Creating map instance...');
    rentalMap = L.map('carRentalMap', {
      fadeAnimation: false,
      zoomAnimation: false,
      center: [40.7128, -74.0060],
      zoom: 13
    });

    console.log('Adding tile layer...');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19,
      updateWhenIdle: true,
      keepBuffer: 2
    }).addTo(rentalMap);

    console.log('Invalidating map size...');
    rentalMap.invalidateSize(true);

    console.log('Initializing map features...');
    initializeMapFeatures();
    
    console.log('Map initialization completed successfully');
  } catch (error) {
    console.error('Error during map initialization:', error);
    console.log('Map container state:', mapContainer.innerHTML);
  }
}

function initializeMapFeatures() {
  const provider = new GeoSearch.OpenStreetMapProvider();
  initializeLocationSearch('pickup', provider);
  initializeLocationSearch('dropoff', provider);

  rentalMap.on('click', function(e) {
    if (activeLocationType) {
      setLocation(e.latlng, activeLocationType);
    }
  });

  initializeLocationPickers();
}

function initializeLocationPickers() {
  document.querySelectorAll('.btn-pick-map').forEach(btn => {
    btn.addEventListener('click', function() {
      const type = this.dataset.type;
      activeLocationType = activeLocationType === type ? null : type;
      document.querySelectorAll('.btn-pick-map').forEach(b => {
        b.classList.toggle('active', b.dataset.type === activeLocationType);
      });
      updateMapInstructions();
    });
  });
}

function updateMapInstructions() {
  const instructions = document.getElementById('mapInstructions');
  if (instructions) {
    instructions.textContent = activeLocationType ? 
      `Click on the map to set ${activeLocationType} location` : 
      'Use the search boxes or click the map pin buttons to set locations';
    instructions.classList.toggle('hidden', !activeLocationType);
  }
}

const carRentalModal = document.getElementById('carRentalModal');
if (carRentalModal) {
  carRentalModal.addEventListener('show.bs.modal', function() {
    console.log('Modal show event triggered');
    cleanupMap();
  });

  carRentalModal.addEventListener('shown.bs.modal', function() {
    console.log('Modal shown event triggered');
    console.log('Modal visibility:', window.getComputedStyle(carRentalModal).display);
    setTimeout(() => {
      const mapContainer = document.getElementById('carRentalMap');
      console.log('Map container size before initialization:', {
        width: mapContainer.offsetWidth,
        height: mapContainer.offsetHeight
      });
      initializeRentalMap();
    }, 100);
  });

  carRentalModal.addEventListener('hide.bs.modal', function() {
    console.log('Modal hide event triggered');
    cleanupMap();
  });
}

function cleanupMap() {
  if (routeLayer) {
    routeLayer.remove();
    routeLayer = null;
  }
  console.log('Cleaning up map...');
  if (rentalMap) {
    try {
      rentalMap.off();
      rentalMap.remove();
      rentalMap = null;
      pickupMarker = null;
      dropoffMarker = null;
      activeLocationType = null;
      console.log('Map cleanup completed');
    } catch (error) {
      console.error('Error during map cleanup:', error);
    }
  }
}

function initializeLocationSearch(type, provider) {
  const input = document.getElementById(`${type}Location`);
  const resultsContainer = document.createElement('div');
  resultsContainer.className = 'location-search-results';
  resultsContainer.style.display = 'none';
  input.parentNode.appendChild(resultsContainer);

  let searchTimeout;

  input.addEventListener('input', async function() {
    clearTimeout(searchTimeout);
    const query = this.value;
    if (query.length < 3) {
      resultsContainer.style.display = 'none';
      return;
    }
    searchTimeout = setTimeout(async () => {
      try {
        const results = await provider.search({ query });
        resultsContainer.innerHTML = '';
        if (results.length > 0) {
          results.slice(0, 5).forEach(result => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            item.innerHTML = `<i class="fas fa-map-marker-alt"></i>${result.label}`;
            item.addEventListener('click', () => {
              const latlng = { lat: result.y, lng: result.x };
              setLocation(latlng, type);
              input.value = result.label;
              resultsContainer.style.display = 'none';
            });
            resultsContainer.appendChild(item);
          });
          resultsContainer.style.display = 'block';
        }
      } catch (error) {
        console.error('Search error:', error);
      }
    }, 300);
  });

  document.addEventListener('click', function(e) {
    if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
      resultsContainer.style.display = 'none';
    }
  });
}

async function setLocation(latlng, type) {
  const input = document.getElementById(`${type}Location`);
  if (type === 'pickup' && pickupMarker) {
    pickupMarker.remove();
  }
  if (type === 'dropoff' && dropoffMarker) {
    dropoffMarker.remove();
  }
  
  // Create the marker
  const newMarker = L.marker(latlng, {
    icon: L.divIcon({
      className: `location-marker ${type}`,
      html: `<i class="fas fa-map-marker-alt"></i>`,
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    })
  }).addTo(rentalMap);

  // Set the marker reference
  if (type === 'pickup') {
    pickupMarker = newMarker;
  } else {
    dropoffMarker = newMarker;
  }

  // Pan to the new location with animation
  panToLocation(latlng);

  const address = await reverseGeocode(latlng);
  input.value = address;

  if (pickupMarker && dropoffMarker) {
    drawRoute(pickupMarker.getLatLng(), dropoffMarker.getLatLng());
  }
}

// Add this new helper function
function panToLocation(latlng) {
  const zoomLevel = 13; // Adjust this value as needed
  rentalMap.setView(latlng, zoomLevel, {
    animate: true,
    duration: 1, // Animation duration in seconds
    easeLinearity: 0.25
  });
}

let routeLayer = null;

async function drawRoute(start, end) {
  if (routeLayer) {
    routeLayer.remove();
  }
  try {
    const response = await fetch(
      `https://router.project-osrm.org/route/v1/driving/${start.lng},${start.lat};${end.lng},${end.lat}?overview=full&geometries=geojson`
    );
    const data = await response.json();
    if (data.routes && data.routes.length > 0) {
      const route = data.routes[0];
      
      // Add gradient route background
      const routeBackground = L.geoJSON(route.geometry, {
        style: {
          color: '#e2e8f0',
          weight: 6,
          opacity: 1,
          lineCap: 'round',
          lineJoin: 'round'
        }
      }).addTo(rentalMap);

      // Add animated dashed line
      routeLayer = L.geoJSON(route.geometry, {
        style: {
          color: '#2563eb',
          weight: 4,
          opacity: 0.8,
          lineCap: 'round',
          lineJoin: 'round',
          dashArray: '10,15',
          className: 'animated-route'
        }
      }).addTo(rentalMap);

      // Add direction arrows
      const decorator = L.polylineDecorator(routeLayer, {
        patterns: [
          {
            offset: '5%',
            repeat: '10%',
            symbol: L.Symbol.arrowHead({
              pixelSize: 12,
              polygon: false,
              pathOptions: {
                color: '#2563eb',
                fillOpacity: 1,
                weight: 2
              }
            })
          }
        ]
      }).addTo(rentalMap);

      // Add route highlights at endpoints
      const routeHighlights = L.geoJSON(route.geometry, {
        style: {
          color: 'rgba(37, 99, 235, 0.3)',
          weight: 12,
          opacity: 1,
          lineCap: 'round',
          lineJoin: 'round'
        }
      }).addTo(rentalMap);

      rentalMap.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
      updateRouteInfo(route);
    }
  } catch (error) {
    console.error('Error drawing route:', error);
  }
}

function updateRouteInfo(route) {
  const routeStatsContainer = document.querySelector('.route-stats-container');
  const distance = (route.distance / 1000).toFixed(1);
  const duration = Math.round(route.duration / 60);
  const hours = Math.floor(duration / 60);
  const minutes = duration % 60;
  const formattedDuration = hours > 0 
    ? `${hours}h ${minutes}m` 
    : `${minutes}m`;

  routeStatsContainer.innerHTML = `
    <div class="route-stat-box">
      <div class="stat-icon">
        <i class="fas fa-road"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Total Distance</div>
        <div class="stat-value">${distance} km</div>
      </div>
    </div>
    <div class="route-stat-box">
      <div class="stat-icon">
        <i class="fas fa-clock"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Estimated Drive Time</div>
        <div class="stat-value">${formattedDuration}</div>
      </div>
    </div>
  `;
}

async function reverseGeocode(latlng) {
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?lat=${latlng.lat}&lon=${latlng.lng}&format=json`
    );
    const data = await response.json();
    return data.display_name;
  } catch (error) {
    console.error('Reverse geocoding error:', error);
    return `${latlng.lat}, ${latlng.lng}`;
  }
}
