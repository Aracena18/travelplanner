// filepath: /c:/xampp/htdocs/travelplanner-master/js/edit_trip.js

let largeMap;
let currentMarker;

// Global variables for swipe handling
let swipeStartX = 0;
let swipeEndX = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Ensure that the global variables (trip, destinations, subPlans) exist
    if (typeof trip === 'undefined' || typeof destinations === 'undefined' || typeof subPlans === 'undefined') {
        console.error('Global variables trip, destinations, or subPlans are not defined.');
        return;
    }

    console.debug("DOM fully loaded. Initializing map and hotels...");

    // Initialize the map
    initializeMap();

    // Load hotels for the selected destination and calculate the initial estimated cost
    const destinationElement = document.getElementById('destination');
    if (destinationElement) {
        loadHotelsForDestination(destinationElement.value);
    } else {
        console.error('Element with id "destination" not found.');
    }
    calculateEstimatedCost();

    // Handle step navigation
    const steps = document.querySelectorAll('.form-step');
    const indicators = document.querySelectorAll('.step-indicator');
    let currentStep = 1;

    function showStep(stepNumber) {
        steps.forEach(step => step.classList.remove('active'));
        indicators.forEach(indicator => {
            indicator.classList.remove('step-active');
            if (parseInt(indicator.dataset.step) < stepNumber) {
                indicator.classList.add('step-completed');
            } else {
                indicator.classList.remove('step-completed');
            }
        });
        const formStep = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        const indicator = document.querySelector(`.step-indicator[data-step="${stepNumber}"]`);
        if (formStep) formStep.classList.add('active');
        if (indicator) indicator.classList.add('step-active');
        console.debug("Switched to step:", stepNumber);
    }

    indicators.forEach(indicator => {
        indicator.addEventListener('click', () => {
            currentStep = parseInt(indicator.dataset.step);
            showStep(currentStep);
        });
    });

    // Update map size when switching tabs
    const tripTabs = document.getElementById('tripTabs');
    if (tripTabs) {
        tripTabs.addEventListener('shown.bs.tab', function(event) {
            console.debug("Tab shown event:", event);
            setTimeout(() => {
                if (largeMap) {
                    largeMap.invalidateSize();
                    console.debug("Map invalidated due to tab switch.");
                }
            }, 100);
        });
    }
});

function initializeMap() {
    largeMap = L.map('map-large', {
        zoomControl: true,
        scrollWheelZoom: true
    }).setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(largeMap);
    
    // Add initial marker if a hotel is selected and data exists
    if (
        trip && trip.hotel &&
        destinations && destinations[trip.destination] &&
        Array.isArray(destinations[trip.destination].hotels)
    ) {
        const hotelData = destinations[trip.destination].hotels.find(h => h.name === trip.hotel);
        if (hotelData) {
            console.debug("Initializing marker with hotel data:", hotelData);
            updateMapMarker(hotelData.latitude, hotelData.longitude);
        }
    }
}

function updateMapMarker(lat, lng) {
    console.debug("updateMapMarker called with lat:", lat, "lng:", lng);
    if (currentMarker) {
        largeMap.removeLayer(currentMarker);
        console.debug("Removed existing marker.");
    }
    // Ensure lat and lng are valid before updating marker
    if (lat && lng) {
        currentMarker = L.marker([lat, lng]).addTo(largeMap)
            .bindPopup("Hotel Location").openPopup();
        largeMap.setView([lat, lng], 10);
        console.debug("New marker set and map centered.");
    } else {
        console.warn("Invalid coordinates. Marker not updated.");
    }
}

const destinationSelect = document.getElementById('destination');
// Updated reference: change "hotel-cards" to "hotel-grid"
const hotelCardsContainer = document.getElementById('hotel-grid');

function loadHotelsForDestination(destination) {
    console.debug("Loading hotels for destination:", destination);
    if (!hotelCardsContainer) {
        console.error('Element with id "hotel-grid" not found.');
        return;
    }

    hotelCardsContainer.style.opacity = '0';
    
    setTimeout(() => {
        hotelCardsContainer.innerHTML = '';
        if (destinations[destination] && Array.isArray(destinations[destination].hotels)) {
            destinations[destination].hotels.forEach((hotel, index) => {
                const isActive = index === 0 ? 'active' : '';
                const isSelected = hotel.name === trip.hotel;
                const amenities = [
                    { icon: 'wifi', title: 'Free WiFi' },
                    { icon: 'swimming-pool', title: 'Pool' },
                    { icon: 'parking', title: 'Parking' },
                    { icon: 'concierge-bell', title: 'Room Service' }
                ];
                
                const card = `
                    <div class="carousel-item ${isActive}" data-hotel="${hotel.name}" 
                         data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}" 
                         data-price="${hotel.price}">
                        <div class="d-flex justify-content-center">
                            <div class="hotel-card">
                                <div class="hotel-image-container">
                                    <img src="assets/images/${hotel.image_name}" class="hotel-image" alt="${hotel.name}"
                                         onerror="this.src='assets/images/default-hotel.jpg'">
                                    <div class="hotel-price-tag">
                                        <span class="price-amount">$${hotel.price}</span>
                                        <span class="price-period">per night</span>
                                    </div>
                                </div>
                                <div class="hotel-info">
                                    <div>
                                        <h5 class="hotel-name">${hotel.name}</h5>
                                        <div class="hotel-rating">
                                            ${generateStarRating(hotel.stars)}
                                        </div>
                                        <div class="hotel-amenities">
                                            ${amenities.map(a => `
                                                <i class="fas fa-${a.icon}" title="${a.title}"></i>
                                            `).join('')}
                                        </div>
                                        <p class="hotel-description">
                                            ${hotel.description || 'Experience luxury and comfort at our premium location with top-notch amenities and exceptional service.'}
                                        </p>
                                    </div>
                                    <button class="btn btn-select-hotel ${isSelected ? 'selected' : ''}" 
                                            onclick="selectHotel(this.closest('.carousel-item'))">
                                        ${isSelected ? '<i class="fas fa-check"></i>Selected' : '<i class="fas fa-check-circle"></i>Select Hotel'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                hotelCardsContainer.innerHTML += card;
            });

            initializeCarousel();
            hotelCardsContainer.style.opacity = '1';
            updateSelectedHotel();
        }
    }, 300);
}

function generateStarRating(stars) {
    return `
        <div class="stars">
            ${Array(5).fill(0).map((_, i) => `
                <i class="fa${i < stars ? 's' : 'r'} fa-star"></i>
            `).join('')}
        </div>
    `;
}

function initializeCarousel() {
    const carousel = new bootstrap.Carousel(document.getElementById('hotel-carousel'), {
        interval: false,
        keyboard: true,
        touch: true,
        wrap: true
    });

    // Add smooth transitions
    const items = document.querySelectorAll('.carousel-item');
    items.forEach(item => {
        item.addEventListener('transitionend', function() {
            items.forEach(i => i.style.zIndex = '0');
            if (this.classList.contains('active')) {
                this.style.zIndex = '1';
            }
        });
    });

    // Enhanced touch support with animation
    const carouselElement = document.getElementById('hotel-carousel');
    let isDragging = false;

    carouselElement.addEventListener('touchstart', e => {
        swipeStartX = e.changedTouches[0].screenX;
        isDragging = true;
    }, { passive: true });

    carouselElement.addEventListener('touchmove', e => {
        if (!isDragging) return;
        const currentX = e.changedTouches[0].screenX;
        const diff = currentX - swipeStartX;
        const activeItem = carouselElement.querySelector('.carousel-item.active');
        if (activeItem) {
            activeItem.style.transform = `translateX(${diff}px) scale(0.98)`;
        }
    }, { passive: true });

    carouselElement.addEventListener('touchend', e => {
        isDragging = false;
        swipeEndX = e.changedTouches[0].screenX;
        handleSwipe();
        const activeItem = carouselElement.querySelector('.carousel-item.active');
        if (activeItem) {
            activeItem.style.transform = '';
        }
    }, { passive: true });

    return carousel;
}

function handleSwipe() {
    const diff = swipeEndX - swipeStartX;
    const threshold = 50; // pixels threshold to consider a swipe
    console.debug("handleSwipe: diff =", diff);
    if (Math.abs(diff) > threshold) {
        // If swipe right (diff positive), go to previous slide; otherwise, next slide.
        if (diff > 0) {
            bootstrap.Carousel.getInstance(document.getElementById('hotel-carousel')).prev();
            console.debug("handleSwipe: Swiped right, moving to previous slide.");
        } else {
            bootstrap.Carousel.getInstance(document.getElementById('hotel-carousel')).next();
            console.debug("handleSwipe: Swiped left, moving to next slide.");
        }
    }
}

function updateHotelDetails(hotelItem) {
    if (!hotelItem) return;

    const hotelName = hotelItem.querySelector('.hotel-name').textContent;
    const hotelPrice = hotelItem.getAttribute('data-price');
    const stars = hotelItem.querySelector('.stars').innerHTML;

    document.querySelector('.current-hotel-name').textContent = hotelName;
    document.querySelector('.hotel-price').textContent = `$${hotelPrice} per night`;
    document.querySelector('.hotel-rating').innerHTML = stars;

    // Animate the update
    const detailsContainer = document.querySelector('.hotel-details');
    detailsContainer.style.animation = 'fadeInUp 0.3s ease-out';
    setTimeout(() => {
        detailsContainer.style.animation = '';
    }, 300);
}

function selectHotel(hotelItem) {
    if (!hotelItem) return;
    const selectedHotel = hotelItem.getAttribute('data-hotel');
    document.getElementById('hotel').value = selectedHotel;
    console.debug("Hotel selected:", selectedHotel);
    
    // Update map marker with parsed coordinates
    const lat = parseFloat(hotelItem.getAttribute('data-latitude'));
    const lng = parseFloat(hotelItem.getAttribute('data-longitude'));
    console.debug("selectHotel: Parsed coordinates:", lat, lng);
    updateMapMarker(lat, lng);
    
    // Recalculate cost
    calculateEstimatedCost();
    
    // Visual feedback: add "selected" class to the chosen item and remove from others
    document.querySelectorAll('.carousel-item').forEach(item => {
        item.classList.remove('selected');
    });
    hotelItem.classList.add('selected');
}

function updateSelectedHotel() {
    // Updated selector to target the "hotel-grid" container
    let activeHotelItem = document.querySelector('#hotel-grid .carousel-item.active');
    if (!activeHotelItem) {
        // Force the first item to be active if none is active
        activeHotelItem = document.querySelector('#hotel-grid .carousel-item');
        if (activeHotelItem) {
            activeHotelItem.classList.add('active');
            console.debug("updateSelectedHotel: Forcing first carousel item to active.");
        }
    }
    if (activeHotelItem) {
        const selectedHotel = activeHotelItem.getAttribute('data-hotel');
        // Parse coordinates to ensure proper numeric values
        const latitude = parseFloat(activeHotelItem.getAttribute('data-latitude'));
        const longitude = parseFloat(activeHotelItem.getAttribute('data-longitude'));
        document.getElementById('hotel').value = selectedHotel;
        console.debug("updateSelectedHotel: Active hotel:", selectedHotel, "Coordinates:", latitude, longitude);
        if (!isNaN(latitude) && !isNaN(longitude)) {
            updateMapMarker(latitude, longitude);
        } else {
            console.warn("updateSelectedHotel: Invalid coordinates for hotel:", selectedHotel);
        }
    } else {
        console.warn("updateSelectedHotel: No active carousel item found.");
    }
}

if (destinationSelect) {
    destinationSelect.addEventListener('change', function() {
        console.debug("Destination changed to:", this.value);
        loadHotelsForDestination(this.value);
    });
}

const hotelCarousel = document.getElementById('hotel-carousel');
if (hotelCarousel) {
    hotelCarousel.addEventListener('slid.bs.carousel', function(event) {
        console.debug("Carousel slid event triggered:", event);
        updateSelectedHotel();
        calculateEstimatedCost();
    });
}

function calculateEstimatedCost() {
    const adultsNum = parseInt(document.getElementById('adults_num').value) || 0;
    const childsNum = parseInt(document.getElementById('childs_num').value) || 0;
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    if (!startDateInput || !endDateInput) return;
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    
    // Updated selector to target the active item within "hotel-grid"
    const activeHotelItem = document.querySelector('#hotel-grid .carousel-item.active');
    const hotelCost = activeHotelItem ? parseFloat(activeHotelItem.getAttribute('data-price')) : 0;
    const numberOfNights = Math.max((endDate - startDate) / (1000 * 60 * 60 * 24), 1);
    const totalPeople = adultsNum + childsNum;
    const roomsNeeded = Math.ceil(totalPeople / 2);
    let estimatedCost = hotelCost * roomsNeeded * numberOfNights;
    subPlans.forEach(plan => {
        estimatedCost += parseFloat(plan.cost || plan.price || plan.transportation_cost || 0);
    });
    const costDisplay = document.getElementById('estimated-cost-display');
    const estimatedCostInput = document.getElementById('estimated_cost');
    console.debug("Calculated estimated cost:", estimatedCost);
    if (costDisplay) {
        costDisplay.textContent = `Estimated Cost: $${estimatedCost.toFixed(2)}`;
    }
    if (estimatedCostInput) {
        estimatedCostInput.value = estimatedCost.toFixed(2);
    }
}

['adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', calculateEstimatedCost);
    }
});

const editTripForm = document.getElementById('edit-trip-form');
if (editTripForm) {
    editTripForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('trip_id', trip.trip_id);
        console.debug("Submitting form with trip_id:", trip.trip_id);
        fetch('edit_trip.php?trip_id=' + trip.trip_id, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.debug("Form submission response:", data);
            if (data.success) {
                alert('Trip updated successfully!');
            } else {
                alert('Failed to update the trip: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the trip.');
        });
    });
}
