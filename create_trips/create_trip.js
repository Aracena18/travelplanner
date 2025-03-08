const destinationSelect = document.getElementById('destination');
const hotelCardsContainer = document.getElementById('hotel-cards');

// Updates the hidden trip name input based on the selected destination's text
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

// Function to populate all hotels in the modal
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

        // Add click handlers for hotel selection
        document.querySelectorAll('#allHotelsGrid .hotel-card').forEach(card => {
            card.addEventListener('click', function() {
                const hotelName = this.dataset.hotel;
                const latitude = this.dataset.latitude;
                const longitude = this.dataset.longitude;
                
                // Update selected hotel
                document.getElementById('hotel').value = hotelName;
                updateMapMarker(latitude, longitude);
                
                // Update carousel to show selected hotel
                const carouselItems = document.querySelectorAll('#hotel-cards .carousel-item');
                carouselItems.forEach(item => {
                    if (item.dataset.hotel === hotelName) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
                
                // Visual feedback
                document.querySelectorAll('#allHotelsGrid .hotel-card').forEach(c => {
                    c.classList.remove('selected');
                });
                this.classList.add('selected');
                
                // Close modal after short delay
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('allHotelsModal')).hide();
                }, 500);
                
                calculateEstimatedCost();
                centerCarousel();
            });
        });
    }
}

// Listen for changes on the destination select.
// When using Choices.js the underlying select element still fires a native change event.
destinationSelect.addEventListener('change', function(e) {
    updateTripName();
    loadHotelsForDestination(e.target.value);
    populateAllHotelsModal(e.target.value);
});

// Pure JavaScript carousel controls
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
        // Calculate how much to shift so that the active item's center aligns with the container's center
        const offset = (containerRect.left + containerRect.width / 2) - (activeRect.left + activeRect.width / 2);
        carouselInner.style.transform = `translateX(${offset}px)`;
    }
}

// Initialize the map and add the tile layer
const map = L.map('map', {
    zoomControl: false
}).setView([0, 0], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

L.control.zoom({
    position: 'topright'
}).addTo(map);

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

    // Add animation to cost update
    const costDisplay = document.getElementById('estimated-cost-display');
    costDisplay.style.transform = 'scale(1.1)';
    setTimeout(() => {
        costDisplay.style.transform = 'scale(1)';
    }, 200);
    
    costDisplay.textContent = `Estimated Cost: $${estimatedCost.toFixed(2)}`;
}

['adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateEstimatedCost);
});

document.getElementById('hotel-carousel').addEventListener('click', calculateEstimatedCost);

// Add form validation with visual feedback
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

// Initial load: update the trip name and load hotels for the default destination
updateTripName();
calculateEstimatedCost();
loadHotelsForDestination(destinationSelect.value);

function redirectTo(page) {
    window.location.href = `${page}?trip_id=${tripId}`;
}

// Update the Choices initialization in the event listener
document.addEventListener('DOMContentLoaded', function () {
  const choices = new Choices('#destination', {
    searchEnabled: true,
    itemSelectText: '',
    shouldSort: false,
    position: 'bottom',
    searchPlaceholderValue: 'Search for a destination...',
    classNames: {
      containerOuter: 'choices destination-choices',
    },
    callbackOnInit: function() {
      const choicesInput = document.querySelector('.choices__input');
      if (choicesInput) {
        choicesInput.setAttribute('aria-label', 'Search for a destination');
      }
    }
  });
});
// Global object to store pending reservations until the trip is submitted
const pendingReservations = {
    flights: [],
    lodging: [],
    rentalCars: [],
    restaurants: [],
    attachments: [],
    others: []
};

// Helper function to update the hidden field with pending reservations JSON
function updatePendingReservationsField() {
    document.getElementById('pendingReservations').value = JSON.stringify(pendingReservations);
}

// Flight booking: open flight modal and pre-fill arrival city
document.querySelector('button[type="button"].btn-light i.fas.fa-plane').parentElement.addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('flightModal'));
    modal.show();
    
    // Pre-fill the arrival city with the selected destination
    const destinationSelect = document.getElementById('destination');
    const selectedDestination = destinationSelect.options[destinationSelect.selectedIndex].text;
    document.getElementById('arrivalCity').value = selectedDestination;
});

// Flight search functionality
document.getElementById('searchFlights').addEventListener('click', async function() {
    const departure = document.getElementById('departureCity').value;
    const arrival = document.getElementById('arrivalCity').value;
    
    if (!departure || !arrival) {
        alert('Please enter both departure and arrival cities');
        return;
    }
    
    try {
        const response = await fetch(`flights.php?departure=${encodeURIComponent(departure)}&arrival=${encodeURIComponent(arrival)}`);
        const data = await response.json();
        
        const flightsList = document.getElementById('flightsList');
        flightsList.innerHTML = data.flights.map(flight => `
            <div class="card flight-card mb-3" data-flight-id="${flight.id}" data-flight-type="${flight.type}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="airline-info">
                            <h5 class="mb-0">${flight.airline}</h5>
                            <span class="text-muted">Flight ${flight.id}</span>
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

        // Add click handlers to flight cards
        document.querySelectorAll('.flight-card').forEach(card => {
            card.addEventListener('click', function() {
                // Gather flight details from the card and modal fields
                const flightData = {
                    flight_id: this.dataset.flightId,
                    airline: this.querySelector('.airline-info h5').textContent.trim(),
                    departure: document.getElementById('departureCity').value,
                    arrival: document.getElementById('arrivalCity').value,
                    departureTime: this.querySelector('.departure .time').textContent.trim(),
                    arrivalTime: this.querySelector('.arrival .time').textContent.trim(),
                    duration: this.querySelector('.flight-duration .duration-time').textContent.trim(),
                    price: parseFloat(this.querySelector('.flight-price h4').textContent.replace('$', ''))
                };

                // Store the flight reservation locally
                pendingReservations.flights.push(flightData);
                updatePendingReservationsField();

                console.log('Pending reservations:', pendingReservations);
                // Visual feedback: mark this flight card as selected
                this.classList.add('selected');
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('flightModal')).hide();
                    calculateEstimatedCost(); // update total cost if needed
                }, 500);
            });
        });
        
    } catch (error) {
        console.error('Error fetching flights:', error);
    }
});

// Flight filters functionality
document.querySelectorAll('.flight-filters .btn').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.flight-filters .btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const cards = document.querySelectorAll('.flight-card');
        
        cards.forEach(card => {
            switch(filter) {
                case 'direct':
                    card.style.display = card.dataset.flightType === 'direct' ? '' : 'none';
                    break;
                case 'all':
                    card.style.display = '';
                    break;
                // Add more filter cases as needed
            }
        });
    });
});

