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

let rentalMap;
let pickupMarker;
let dropoffMarker;
let activeLocationType = null;

function initializeRentalMap() {
  // Set default center to a reasonable location (e.g., New York)
  const defaultCenter = [40.7128, -74.0060];
  
  // Initialize map only if container exists
  const mapContainer = document.getElementById('carRentalMap');
  if (!mapContainer) return;

  // Create map instance
  rentalMap = L.map('carRentalMap').setView(defaultCenter, 13);
  
  // Add tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(rentalMap);

  // Force a resize to ensure map renders correctly in modal
  setTimeout(() => {
    rentalMap.invalidateSize();
  }, 250);

  // Map click handler
  rentalMap.on('click', function(e) {
    if (activeLocationType) {
      setLocation(e.latlng, activeLocationType);
    }
  });

  // Initialize location picker buttons
  document.querySelectorAll('.btn-pick-map').forEach(btn => {
    btn.addEventListener('click', function() {
      const type = this.dataset.type;
      activeLocationType = activeLocationType === type ? null : type;
      
      // Update button states
      document.querySelectorAll('.btn-pick-map').forEach(b => {
        b.classList.toggle('active', b.dataset.type === activeLocationType);
      });
      
      // Update instructions visibility
      const instructions = document.getElementById('mapInstructions');
      if (instructions) {
        instructions.classList.toggle('hidden', !activeLocationType);
        if (activeLocationType) {
          instructions.textContent = `Click on the map to set ${activeLocationType} location`;
        }
      }
    });
  });

  // Initialize Geocoding provider
  const provider = new GeoSearch.OpenStreetMapProvider();

  // Add search functionality to location inputs
  ['pickup', 'dropoff'].forEach(type => {
    const input = document.getElementById(`${type}Location`);
    if (!input) return;

    input.addEventListener('change', async function() {
      try {
        const results = await provider.search({ query: this.value });
        if (results.length > 0) {
          const location = results[0];
          setLocation({ lat: location.y, lng: location.x }, type);
        }
      } catch (error) {
        console.error('Location search error:', error);
      }
    });
  });
}

// Clean up function to avoid memory leaks
function cleanupMap() {
  if (rentalMap) {
    rentalMap.remove();
    rentalMap = null;
    pickupMarker = null;
    dropoffMarker = null;
    activeLocationType = null;
  }
}

// Modal event handlers
document.getElementById('carRentalModal').addEventListener('shown.bs.modal', function() {
  initializeRentalMap();
});

document.getElementById('carRentalModal').addEventListener('hidden.bs.modal', function() {
  cleanupMap();
});

function initializeLocationSearch() {
  const searchInputs = ['pickupLocation', 'dropoffLocation'];
  
  searchInputs.forEach(inputId => {
    const input = document.getElementById(inputId);
    const type = inputId.includes('pickup') ? 'pickup' : 'dropoff';
    
    // Initialize geocoding search
    const searchControl = new GeoSearch.GeoSearchControl({
      provider: new GeoSearch.OpenStreetMapProvider(),
      showMarker: false,
      autoComplete: true,
      autoCompleteDelay: 250,
    });

    input.addEventListener('change', async function() {
      try {
        const results = await searchControl.provider.search({ query: this.value });
        if (results.length > 0) {
          const location = results[0];
          setLocation({ lat: location.y, lng: location.x }, type);
        }
      } catch (error) {
        console.error('Location search error:', error);
      }
    });
  });
}

function setLocation(latlng, type) {
  const marker = type === 'pickup' ? pickupMarker : dropoffMarker;
  const input = document.getElementById(`${type}Location`);
  
  // Remove existing marker if any
  if (marker) {
    marker.remove();
  }

  // Create new marker
  const newMarker = L.marker(latlng, {
    icon: L.divIcon({
      className: `location-marker ${type}`,
      html: `<i class="fas fa-map-marker-alt"></i> ${type === 'pickup' ? 'Pickup' : 'Drop-off'} Location`
    })
  }).addTo(rentalMap);

  // Store marker reference
  if (type === 'pickup') {
    pickupMarker = newMarker;
  } else {
    dropoffMarker = newMarker;
  }

  // Update input field with reverse geocoded address
  reverseGeocode(latlng).then(address => {
    input.value = address;
  });

  // Fit map bounds to show both markers
  if (pickupMarker && dropoffMarker) {
    const bounds = L.latLngBounds([pickupMarker.getLatLng(), dropoffMarker.getLatLng()]);
    rentalMap.fitBounds(bounds, { padding: [50, 50] });
  }
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

// Initialize map when modal opens
document.getElementById('carRentalModal').addEventListener('shown.bs.modal', function() {
  if (!rentalMap) {
    initializeRentalMap();
  }
});
