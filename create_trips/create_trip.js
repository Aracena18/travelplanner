// ------------------------------
// Helper function to fetch flight location ID via searchDestination endpoint
// ------------------------------

//Location: /traveplanner-master/create_trips/create_trip.js

async function getFlightLocation(city, languagecode = 'en-us') {
    const url = `https://booking-com15.p.rapidapi.com/api/v1/flights/searchDestination?query=${encodeURIComponent(city)}&languagecode=${languagecode}`;
    const options = {
      method: 'GET',
      headers: {
        'x-rapidapi-key': 'a4f936d18fmsh72c62428617d573p1b6010jsn36884d1c931d',
        'x-rapidapi-host': 'booking-com15.p.rapidapi.com'
      }
    };
    try {
      const response = await fetch(url, options);
      if (!response.ok) {
        throw new Error(`Search destination request failed: ${response.status}`);
      }
      const result = await response.json();
      console.log('Search destination result for', city, result);
      if (result.data && result.data.length > 0) {
        // Pick the best match – you might refine this logic.
        const match = result.data.find(item =>
          item.name.toLowerCase().includes(city.toLowerCase())
        ) || result.data[0];
        return match.id; // "id" is used as the location identifier (e.g. "BOM.AIRPORT")
      }
      return null;
    } catch (error) {
      console.error('Error in getFlightLocation:', error);
      return null;
    }
  }
    
  // ------------------------------
  // Helper function to format duration in seconds to "X hr Y min"
  // ------------------------------
  function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${hours} hr ${minutes} min`;
  }
    
  // ------------------------------
  // Existing Hotel Functions (unchanged)
  // ------------------------------
  const destinationSelect = document.getElementById('destination');
  const hotelCardsContainer = document.getElementById('hotel-cards');
    
  function updateTripName() {
    const tripNameInput = document.getElementById('trip_name');
    const selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
    if (selectedOption) {
      tripNameInput.value = selectedOption.textContent.trim();
    }
  }
    
  function loadHotelsForDestination(destination) {
    hotelCardsContainer.style.opacity = '0';
    setTimeout(() => {
      hotelCardsContainer.innerHTML = '';
      if (hotels[destination]) {
        hotels[destination].hotels.forEach((hotel, index) => {
          const isActive = index === 0 ? 'active' : '';
          const card = `
            <div class="carousel-item ${isActive}" data-hotel="${hotel.name}" data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}">
              <div class="card hotel-card">
                <div class="hotel-image-wrapper">
                  <img src="/travelplanner-master/assets/images/${hotel.image_name}" class="card-img-top hotel-image" alt="${hotel.name}">
                  <div class="hotel-image-overlay">
                    <div class="hotel-features">
                      <span class="feature-tag"><i class="fas fa-wifi"></i> Free WiFi</span>
                      <span class="feature-tag"><i class="fas fa-swimming-pool"></i> Pool</span>
                      <span class="feature-tag"><i class="fas fa-parking"></i> Parking</span>
                    </div>
                  </div>
                  <div class="hotel-rating">
                    ${Array(hotel.stars).fill('<i class="fas fa-star"></i>').join('')}
                  </div>
                </div>
                <div class="card-body">
                  <h5 class="card-title text-truncate">${hotel.name}</h5>
                  <div class="price-tag">
                    <span class="price-label">Per Night</span>
                    <span class="price-amount">$${hotel.price}</span>
                  </div>
                </div>
              </div>
            </div>`;
          hotelCardsContainer.innerHTML += card;
        });
        updateSelectedHotel();
        centerCarousel();
      }
      hotelCardsContainer.style.opacity = '1';
    }, 300);
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
    
  function populateAllHotelsModal(destination) {
    const allHotelsGrid = document.getElementById('allHotelsGrid');
    allHotelsGrid.innerHTML = '';
    if (hotels[destination]) {
      hotels[destination].hotels.forEach((hotel) => {
        const card = `
          <div class="col-md-6 col-lg-4">
            <div class="card hotel-card" data-hotel="${hotel.name}" data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}">
              <div class="hotel-image-wrapper">
                <img src="/travelplanner-master/assets/images/${hotel.image_name}" class="card-img-top hotel-image" alt="${hotel.name}">
                <div class="hotel-rating">
                  ${Array(hotel.stars).fill('<i class="fas fa-star"></i>').join('')}
                </div>
              </div>
              <div class="card-body">
                <h5 class="card-title">${hotel.name}</h5>
                <div class="price-tag">
                  <span class="price-label">Per Night</span>
                  <span class="price-amount">$${hotel.price}</span>
                </div>
              </div>
            </div>
          </div>`;
        allHotelsGrid.innerHTML += card;
      });
      document.querySelectorAll('#allHotelsGrid .hotel-card').forEach(card => {
        card.addEventListener('click', function() {
          const hotelName = this.dataset.hotel;
          const latitude = this.dataset.latitude;
          const longitude = this.dataset.longitude;
          document.getElementById('hotel').value = hotelName;
          updateMapMarker(latitude, longitude);
          const carouselItems = document.querySelectorAll('#hotel-cards .carousel-item');
          carouselItems.forEach(item => {
            if (item.dataset.hotel === hotelName) {
              item.classList.add('active');
            } else {
              item.classList.remove('active');
            }
          });
          document.querySelectorAll('#allHotelsGrid .hotel-card').forEach(c => {
            c.classList.remove('selected');
          });
          this.classList.add('selected');
          setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('allHotelsModal')).hide();
          }, 500);
          calculateEstimatedCost();
          centerCarousel();
        });
      });
    }
  }
    
  destinationSelect.addEventListener('change', function(e) {
    updateTripName();
    loadHotelsForDestination(e.target.value);
    populateAllHotelsModal(e.target.value);
  });
    
  // Carousel controls
  document.querySelector('.carousel-control-next').addEventListener('click', function() {
    const items = document.querySelectorAll('#hotel-cards .carousel-item');
    if (!items.length) return;
    let activeIndex = Array.from(items).findIndex(item => item.classList.contains('active'));
    items[activeIndex].classList.remove('active');
    let nextIndex = (activeIndex + 1) % items.length;
    items[nextIndex].classList.add('active');
    updateSelectedHotel();
    centerCarousel();
  });
    
  document.querySelector('.carousel-control-prev').addEventListener('click', function() {
    const items = document.querySelectorAll('#hotel-cards .carousel-item');
    if (!items.length) return;
    let activeIndex = Array.from(items).findIndex(item => item.classList.contains('active'));
    items[activeIndex].classList.remove('active');
    let prevIndex = (activeIndex - 1 + items.length) % items.length;
    items[prevIndex].classList.add('active');
    updateSelectedHotel();
    centerCarousel();
  });
    
  function centerCarousel() {
    const carouselContainer = document.querySelector('.hotel-carousel');
    const carouselInner = document.querySelector('.carousel-inner');
    const activeItem = document.querySelector('#hotel-cards .carousel-item.active');
    if (activeItem && carouselContainer && carouselInner) {
      const containerRect = carouselContainer.getBoundingClientRect();
      const activeRect = activeItem.getBoundingClientRect();
      const offset = (containerRect.left + containerRect.width / 2) - (activeRect.left + activeRect.width / 2);
      carouselInner.style.transform = `translateX(${offset}px)`;
    }
  }
    
  // ------------------------------
  // Map Initialization (unchanged)
  // ------------------------------
  const map = L.map('map', { zoomControl: false }).setView([0, 0], 2);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
  L.control.zoom({ position: 'topright' }).addTo(map);
    
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
    
  // ------------------------------
  // Cost Calculation and Form Validation (unchanged)
  // ------------------------------
  function calculateEstimatedCost() {
    const adultsNum = parseInt(document.getElementById('adults_num').value) || 0;
    const childsNum = parseInt(document.getElementById('childs_num').value) || 0;
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
    const hotelCost = activeHotelItem ? parseFloat(activeHotelItem.querySelector('.price-amount').textContent.replace('$', '')) : 0;
    const numberOfNights = Math.max((endDate - startDate) / (1000 * 60 * 60 * 24), 1);
    const totalPeople = adultsNum + childsNum;
    const roomsNeeded = Math.ceil(totalPeople / 2);
    let estimatedCost = (hotelCost * roomsNeeded * numberOfNights);
    activities.forEach(activity => {
      estimatedCost += parseFloat(activity.cost);
    });
    const costDisplay = document.getElementById('estimated-cost-display');
    costDisplay.style.transform = 'scale(1.1)';
    setTimeout(() => { costDisplay.style.transform = 'scale(1)'; }, 200);
    costDisplay.textContent = `Estimated Cost: $${estimatedCost.toFixed(2)}`;
  }
    
  ['adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateEstimatedCost);
  });
  document.getElementById('hotel-carousel').addEventListener('click', calculateEstimatedCost);
  document.querySelectorAll('.form-control, .form-select').forEach(input => {
    input.addEventListener('input', function() {
      if (this.value) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
      } else {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      }
    });
  });
    
  // Initial load
  updateTripName();
  calculateEstimatedCost();
  loadHotelsForDestination(destinationSelect.value);
    
  function redirectTo(page) {
    window.location.href = `${page}?trip_id=${tripId}`;
  }
    
  // ------------------------------
  // Choices.js Initialization (unchanged)
  // ------------------------------
  document.addEventListener('DOMContentLoaded', function () {
    const choices = new Choices('#destination', {
      searchEnabled: true,
      itemSelectText: '',
      shouldSort: false,
      position: 'bottom',
      searchPlaceholderValue: 'Search for a destination...',
      classNames: { containerOuter: 'choices destination-choices' },
      callbackOnInit: function() {
        const choicesInput = document.querySelector('.choices__input');
        if (choicesInput) {
          choicesInput.setAttribute('aria-label', 'Search for a destination');
        }
      }
    });
  });
    
  // ------------------------------
  // Pending Reservations and Helper Functions (unchanged)
  // ------------------------------
  const pendingReservations = {
    flights: [],
    lodging: [],
    rentalCars: [],
    restaurants: [],
    attachments: [],
    others: []
  };
    
  function updatePendingReservationsField() {
    document.getElementById('pendingReservations').value = JSON.stringify(pendingReservations);
  }
    
  function isFlightAlreadyAdded(flightId) {
    return pendingReservations.flights.some(flight => flight.flight_id === flightId);
  }
    
  // Flight booking: open flight modal and pre-fill arrival city
  document.querySelector('button[type="button"].btn-light i.fas.fa-plane').parentElement.addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('flightModal'));
    modal.show();
    const destinationSelect = document.getElementById('destination');
    const selectedDestination = destinationSelect.options[destinationSelect.selectedIndex].text;
    document.getElementById('arrivalCity').value = selectedDestination;
  });
    
  // ------------------------------
  // Updated Flight Search Integration using booking-com15 API (with airline logos)
  // ------------------------------
  document.getElementById('searchFlights').addEventListener('click', async function() {
    const departureCity = document.getElementById('departureCity').value.trim();
    const arrivalCity = document.getElementById('arrivalCity').value.trim();
    const departDate = document.getElementById('flightDepartureDate').value; // Expected format: YYYY-MM-DD
    const returnDate = document.getElementById('flightReturnDate').value; // Optional
    
    if (!departureCity || !arrivalCity || !departDate) {
      alert('Please enter departure, arrival cities and a departure date');
      return;
    }
    
    // Read number of adults and children from form
    const adults = document.getElementById('adults_num').value || 1;
    const childs = document.getElementById('childs_num').value || 0;
    const childrenParam = (parseInt(childs) > 0) ? Array(parseInt(childs)).fill(10).join(',') : '0';
    
    // Show loading indicator
    document.getElementById('flightLoading').classList.remove('d-none');
    
    try {
      // Retrieve location IDs via searchDestination endpoint
      const fromId = await getFlightLocation(departureCity);
      const toId = await getFlightLocation(arrivalCity);
      
      if (!fromId || !toId) {
        throw new Error('Could not retrieve location IDs for one or both cities');
      }
      
      // Build the flight search URL with new query parameters:
      let searchUrl = `https://booking-com15.p.rapidapi.com/api/v1/flights/searchFlights?fromId=${encodeURIComponent(fromId)}&toId=${encodeURIComponent(toId)}&departDate=${departDate}`;
      if (returnDate) {
        searchUrl += `&returnDate=${returnDate}`;
      }
      searchUrl += `&pageNo=1&adults=${adults}&children=${encodeURIComponent(childrenParam)}&sort=BEST&cabinClass=ECONOMY&currency_code=USD`;
      
      const options = {
        method: 'GET',
        headers: {
          'x-rapidapi-key': 'a4f936d18fmsh72c62428617d573p1b6010jsn36884d1c931d',
          'x-rapidapi-host': 'booking-com15.p.rapidapi.com'
        }
      };
      
      const response = await fetch(searchUrl, options);
      if (!response.ok) {
        throw new Error(`Flight search request failed: ${response.status}`);
      }
      const result = await response.json();
      console.log('Flight search response:', result);
      
      // Extract flight offers from the response.
      const flightOffers = (result.data && result.data.flightOffers) || [];
      console.log('Number of flight offers:', flightOffers.length);
      
      // Map each flight offer to your UI structure.
      const flights = flightOffers.map((flight, index) => {
        const id = flight.token || `F${index+1}`;
        const segment = flight.segments[0];
        const leg = segment.legs[0];
        // Extract airline name and logo from carriersData.
        const airline = (leg.carriersData && leg.carriersData[0] && leg.carriersData[0].name) || 'Unknown Airline';
        const airlineLogo = (leg.carriersData && leg.carriersData[0] && leg.carriersData[0].logo) || '';
        const duration = leg.totalTime ? formatDuration(leg.totalTime) : 'N/A';
        const type = (leg.flightStops && leg.flightStops.length === 0) ? 'direct' : 'withStops';
        
        let price = 0;
        if (flight.travellerPrices && Array.isArray(flight.travellerPrices)) {
          const adultPriceEntry = flight.travellerPrices.find(tp => tp.travellerType === 'ADULT');
          if (adultPriceEntry?.travellerPriceBreakdown?.total) {
            price = adultPriceEntry.travellerPriceBreakdown.total.units;
          }
        }
        if (!price && flight.unifiedPriceBreakdown?.price) {
          price = flight.unifiedPriceBreakdown.price.units;
        }
        
        return {
          id,
          airline,
          airlineLogo,
          departure: departureCity,
          arrival: arrivalCity,
          departureTime: segment.departureTime || '00:00',
          arrivalTime: segment.arrivalTime || '00:00',
          duration,
          type,
          price
        };
      });
      
      // Update the flights list UI (hiding the flight token from the user).
      const flightsList = document.getElementById('flightsList');
      flightsList.innerHTML = flights.map(flight => `
        <div class="card flight-card mb-3" data-flight-id="${flight.id}" data-flight-type="${flight.type}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="airline-info d-flex align-items-center">
                        ${flight.airlineLogo ? `<img src="${flight.airlineLogo}" alt="${flight.airline} Logo" style="max-height:30px; margin-right:8px;">` : ''}
                        <h5 class="mb-0">${flight.airline}</h5>
                    </div>
                    <div class="flight-price">
                        <h4 class="mb-0">$${flight.price}</h4>
                    </div>
                </div>
                <div class="flight-details mt-3">
                    <div class="d-flex justify-content-between">
                        <div class="departure">
                            <div class="time">${flight.departureTime}</div>
                            <div class="city">${flight.departure}</div>
                        </div>
                        <div class="flight-duration d-flex flex-column align-items-center">
                            <div class="duration-time">${flight.duration}</div>
                            <div class="flight-line">
                                <i class="fas fa-plane"></i>
                            </div>
                            <div class="flight-type">${flight.type === 'direct' ? 'Direct Flight' : 'With Stops'}</div>
                        </div>
                        <div class="arrival text-end">
                            <div class="time">${flight.arrivalTime}</div>
                            <div class="city">${flight.arrival}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      `).join('');
      
      document.querySelectorAll('.flight-card').forEach(card => {
        card.addEventListener('click', function() {
          const flightId = this.dataset.flightId;
          if (isFlightAlreadyAdded(flightId)) {
            alert('This flight is already added to your reservation.');
            return;
          }
          const flightData = {
            flight_id: flightId,
            airline: this.querySelector('.airline-info h5').textContent.trim(),
            departure: departureCity,
            arrival: arrivalCity,
            departureTime: this.querySelector('.departure .time').textContent.trim(),
            arrivalTime: this.querySelector('.arrival .time').textContent.trim(),
            duration: this.querySelector('.flight-duration .duration-time').textContent.trim(),
            price: parseFloat(this.querySelector('.flight-price h4').textContent.replace('$', ''))
          };
          pendingReservations.flights.push(flightData);
          updatePendingReservationsField();
          this.classList.add('selected');
          setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('flightModal')).hide();
            calculateEstimatedCost();
          }, 500);
        });
      });
      
    } catch (error) {
      console.error('Error fetching flights:', error);
      alert('An error occurred while fetching flights. Please try again.');
    } finally {
      document.getElementById('flightLoading').classList.add('d-none');
    }
  });

// Car Rental Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
  const searchCarsBtn = document.getElementById('searchCars');
  const carsListContainer = document.getElementById('carsList');
  const carLoadingIndicator = document.getElementById('carLoading');

  // Sample car data (replace with actual API call)
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
    // Add more sample cars as needed
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
      // Simulate API call delay
      await new Promise(resolve => setTimeout(resolve, 1500));

      // Filter cars based on selected type
      const filteredCars = selectedType === 'all' 
        ? sampleCars 
        : sampleCars.filter(car => car.type === selectedType);

      // Render cars
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

        // Add click handler for the select button
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

          // Close modal
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
