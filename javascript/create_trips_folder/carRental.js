// src/carRental.js

import { calculateEstimatedCost } from '/travelplanner-master/javascript/create_trips_folder/cost.js';
import { pendingReservations, updatePendingReservationsField } from '/travelplanner-master/javascript/create_trips_folder/reservations.js';

document.addEventListener('DOMContentLoaded', function() {
  const searchCarsBtn = document.getElementById('searchCars');
  const carsListContainer = document.getElementById('carsList');
  const carLoadingIndicator = document.getElementById('carLoading');

  // Sample car data (replace with an actual API call if needed)
  const sampleCars = [
    {
      id: 'car1',
      name: 'Toyota Camry',
      type: 'economy',
      image: '/travelplanner-master/assets/images/cars/camry.jpg',
      seats: '5',
      bags: '4',
      transmission: 'Automatic',
      ac: 'A/C',
      price: 45,
      company: {
        name: 'Enterprise',
        logo: '/travelplanner-master/assets/images/companies/enterprise.png'
      }
    },
    {
      id: 'car2',
      name: 'BMW 5 Series',
      type: 'luxury',
      image: '/travelplanner-master/assets/images/cars/bmw.jpg',
      seats: '5',
      bags: '4',
      transmission: 'Automatic',
      ac: 'A/C',
      price: 85,
      company: {
        name: 'Hertz',
        logo: '/travelplanner-master/assets/images/companies/hertz.png'
      }
    },
    // Add more sample cars as needed.
  ];

  searchCarsBtn.addEventListener('click', async function() {
    const pickupLocation = document.getElementById('pickupLocation').value;
    const dropoffLocation = document.getElementById('dropoffLocation').value;
    const pickupDateTime = document.getElementById('pickupDateTime').value;
    const dropoffDateTime = document.getElementById('dropoffDateTime').value;
    const selectedType = document.querySelector('input[name="carType"]:checked').value;

    if (!pickupLocation || !dropoffLocation || !pickupDateTime || !dropoffDateTime) {
      alert('Please fill in all required fields');
      return;
    }

    // Show loading indicator
    carLoadingIndicator.classList.remove('d-none');
    carsListContainer.innerHTML = '';

    try {
      // Simulate an API call delay.
      await new Promise(resolve => setTimeout(resolve, 1500));

      // Filter cars based on the selected type.
      const filteredCars = selectedType === 'all'
        ? sampleCars
        : sampleCars.filter(car => car.type === selectedType);

      // Render each car.
      filteredCars.forEach(car => {
        const carCard = document.createElement('div');
        carCard.className = 'car-card';
        carCard.innerHTML = `
          <div class="car-image">
            <img src="${car.image}" alt="${car.name}">
            <span class="car-type-badge">${car.type}</span>
          </div>
          <div class="car-details">
            <h5 class="car-name">${car.name}</h5>
            <div class="car-features">
              <span class="feature"><i class="fas fa-users"></i> ${car.seats} seats</span>
              <span class="feature"><i class="fas fa-suitcase"></i> ${car.bags} bags</span>
              <span class="feature"><i class="fas fa-cog"></i> ${car.transmission}</span>
              <span class="feature"><i class="fas fa-snowflake"></i> ${car.ac}</span>
            </div>
            <div class="rental-company">
              <img src="${car.company.logo}" alt="${car.company.name}" class="company-logo">
              <span class="company-name">${car.company.name}</span>
            </div>
            <div class="price-section">
              <div class="price-details">
                <span class="price-amount">$${car.price}</span>
                <span class="price-period">per day</span>
              </div>
              <button class="btn btn-primary select-car-btn" data-car-id="${car.id}">Select</button>
            </div>
          </div>
        `;

        // Bind select button event.
        carCard.querySelector('.select-car-btn').addEventListener('click', function() {
          const carId = this.dataset.carId;
          if (pendingReservations.rentalCars.some(c => c.car_id === carId)) {
            alert('This car is already added to your reservation.');
            return;
          }

          const carData = {
            car_id: carId,
            name: car.name,
            type: car.type,
            company: car.company.name,
            price: car.price,
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

    } catch (error) {
      console.error('Error fetching cars:', error);
      alert('An error occurred while fetching available cars. Please try again.');
    } finally {
      carLoadingIndicator.classList.add('d-none');
    }
  });
});

// Global map variables
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

    // Force immediate resize and log result
    console.log('Invalidating map size...');
    rentalMap.invalidateSize(true);

    // Initialize map features
    console.log('Initializing map features...');
    initializeMapFeatures();
    
    console.log('Map initialization completed successfully');
  } catch (error) {
    console.error('Error during map initialization:', error);
    console.log('Map container state:', mapContainer.innerHTML);
  }
}

function initializeMapFeatures() {
  // Initialize geocoding provider
  const provider = new GeoSearch.OpenStreetMapProvider();

  // Initialize search controls
  initializeLocationSearch('pickup', provider);
  initializeLocationSearch('dropoff', provider);

  // Map click handler
  rentalMap.on('click', function(e) {
    if (activeLocationType) {
      setLocation(e.latlng, activeLocationType);
    }
  });

  // Initialize location picker buttons
  initializeLocationPickers();
}

function initializeLocationPickers() {
  document.querySelectorAll('.btn-pick-map').forEach(btn => {
    btn.addEventListener('click', function() {
      const type = this.dataset.type;
      activeLocationType = activeLocationType === type ? null : type;

      // Update button states
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

// Update modal event listeners with debugging
const carRentalModal = document.getElementById('carRentalModal');
if (carRentalModal) {
  carRentalModal.addEventListener('show.bs.modal', function() {
    console.log('Modal show event triggered');
    cleanupMap();
  });

  carRentalModal.addEventListener('shown.bs.modal', function() {
    console.log('Modal shown event triggered');
    console.log('Modal visibility:', window.getComputedStyle(carRentalModal).display);
    
    // Add a small delay and check container size
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

// Improved cleanup function with debugging
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
              setLocation({ lat: result.y, lng: result.x }, type);
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

  // Hide results when clicking outside
  document.addEventListener('click', function(e) {
    if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
      resultsContainer.style.display = 'none';
    }
  });
}

async function setLocation(latlng, type) {
  const input = document.getElementById(`${type}Location`);

  // Remove existing marker
  if (type === 'pickup' && pickupMarker) {
    pickupMarker.remove();
  }
  if (type === 'dropoff' && dropoffMarker) {
    dropoffMarker.remove();
  }

  // Create new marker
  const newMarker = L.marker(latlng, {
    icon: L.divIcon({
      className: `location-marker ${type}`,
      html: `<i class="fas fa-map-marker-alt"></i>`,
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    })
  }).addTo(rentalMap);

  // Store marker reference
  if (type === 'pickup') {
    pickupMarker = newMarker;
  } else {
    dropoffMarker = newMarker;
  }

  // Update input with reverse geocoded address
  const address = await reverseGeocode(latlng);
  input.value = address;

  // If both markers exist, draw route
  if (pickupMarker && dropoffMarker) {
    drawRoute(pickupMarker.getLatLng(), dropoffMarker.getLatLng());
  }
}

// Add these new functions for route handling
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
      
      // Draw the route line
      routeLayer = L.geoJSON(route.geometry, {
        style: {
          color: '#2563eb',
          weight: 4,
          opacity: 0.8,
          lineCap: 'round',
          lineJoin: 'round',
          dashArray: '8,12'
        }
      }).addTo(rentalMap);

      // Fit map to show the entire route
      rentalMap.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });

      // Update route info panel
      updateRouteInfo(route);
    }
  } catch (error) {
    console.error('Error drawing route:', error);
  }
}

function updateRouteInfo(route) {
  const routeInfoPanel = document.querySelector('.route-info-panel');
  const distance = (route.distance / 1000).toFixed(1); // Convert to km
  const duration = Math.round(route.duration / 60); // Convert to minutes

  routeInfoPanel.innerHTML = `
    <h5 class="mb-3">Route Information</h5>
    <div class="route-stats">
      <div class="route-stat-item">
        <div class="stat-value">${distance} km</div>
        <div class="stat-label">Distance</div>
      </div>
      <div class="route-stat-item">
        <div class="stat-value">${duration} min</div>
        <div class="stat-label">Est. Drive Time</div>
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
