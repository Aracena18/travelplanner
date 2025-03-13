console.log("load_reservations.js loaded");

class ReservationManager {
    constructor(timelineContainer) {
        this.container = timelineContainer;
        this.activeCategory = 'all';
        this.reservations = [];
        console.log("ReservationManager instantiated. Container:", timelineContainer);
        this.initializeEventListeners();
    }

    async loadReservations(tripId) {
        try {
            console.log("Loading reservations for tripId:", tripId);
            
            // Fetch flight reservations
            console.log("Fetching flight reservations...");
            const flightResponse = await fetch(`/travelplanner-master/create_trips/flights.php?trip_id=${tripId}&type=reservations`);
            const flightData = await flightResponse.json();
            console.log("Flight data received:", flightData);
            if (!flightData.success) {
                throw new Error(flightData.error || 'Unknown error occurred fetching flights');
            }
            // Use flights if available or fallback to reservations
            const flights = flightData.flights || flightData.reservations || [];
            console.log("Number of flights retrieved:", flights.length);
            // Normalize flights to ensure type "flight" and standardize the identifier to 'id'
            const normalizedFlights = flights.map(flight => ({
                ...flight,
                id: flight.flight_id, // Use flight_id as the standard identifier
                type: 'flight'
            }));
            
            console.log("Normalized flights:", normalizedFlights);

            // Fetch car rental reservations
            let cars = [];
            try {
                console.log("Fetching car rental reservations...");
                const carResponse = await fetch(`/travelplanner-master/create_trips/cars.php?trip_id=${tripId}&type=reservations`);
                const carData = await carResponse.json();
                console.log("Car rental data received:", carData);
                if (carData.success) {
                    // Ensure the backend returns the key "rentalCars"
                    cars = carData.rentalCars || [];
                }
            } catch (e) {
                console.warn("Car rental reservations could not be loaded:", e);
                cars = [];
            }
            console.log("Number of car rentals retrieved:", cars.length);
            // Normalize car rentals using pickup and dropoff date fields and standardize the identifier to 'id'
            const normalizedCars = cars.map(car => ({
                ...car,
                id: car.rental_id, // Use rental_id as the standard identifier
                type: 'rentalCar',
                start_time: car.pickup_datetime,
                end_time: car.dropoff_datetime
            }));
            console.log("Normalized car rentals:", normalizedCars);

            // Combine flights and car rentals
            this.reservations = [...normalizedFlights, ...normalizedCars];
            console.log("Total reservations combined:", this.reservations.length);

            // Sort reservations by start_time
            this.reservations.sort((a, b) => new Date(a.start_time) - new Date(b.start_time));
            console.log("Reservations sorted by start_time:", this.reservations.map(r => r.start_time));

            // Render and update filters
            this.renderReservations();
            this.updateFilterCounts();
        } catch (error) {
            console.error('Error loading reservations:', error);
            this.showError(`Failed to load reservations: ${error.message}`);
        }
    }

    async removeReservation(id, type) {
        try {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('type', type);

            const response = await fetch('/travelplanner-master/handlers/delete_reservation.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                // Remove the reservation from the array using the standardized 'id'
                this.reservations = this.reservations.filter(r => 
                    !(r.id === id && r.type === (type === 'car' ? 'rentalCar' : 'flight'))
                );
                
                // Re-render the reservations
                this.renderReservations();
                this.updateFilterCounts();
                
                // Show success message
                this.showToast('Reservation removed successfully', 'success');
            } else {
                throw new Error(data.error || 'Failed to remove reservation');
            }
        } catch (error) {
            console.error('Error removing reservation:', error);
            this.showToast(error.message, 'error');
        }
    }

    showToast(message, type = 'info') {
        const toastContainer = document.createElement('div');
        toastContainer.className = `toast-container position-fixed bottom-0 end-0 p-3`;
        toastContainer.style.zIndex = '1050';

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : 'success'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        document.body.appendChild(toastContainer);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toastContainer);
        });
    }

    renderReservations() {
        const filteredReservations = this.filterReservations();
        console.log("Filtered reservations count:", filteredReservations.length);
        if (filteredReservations.length === 0) {
            this.showEmptyState();
            return;
        }
        this.container.innerHTML = filteredReservations.map(reservation =>
            this.createReservationCard(reservation)
        ).join('');
    }

    filterReservations() {
        // Normalize the activeCategory: map "flights" to "flight" and "rentalCars" to "rentalCar"
        let category = this.activeCategory;
        if (category === 'flights') {
            category = 'flight';
        } else if (category === 'rentalCars') {
            category = 'rentalCar';
        }
        const filtered = category === 'all'
            ? this.reservations
            : this.reservations.filter(r => r.type === category);
        console.log(`Filtering reservations by category '${this.activeCategory}' (normalized as '${category}'):`, filtered.length);
        return filtered;
    }

    createReservationCard(reservation) {
        if (reservation.type === 'flight') {
            return this.createFlightCard(reservation);
        } else if (reservation.type === 'rentalCar') {
            return this.createCarRentalCard(reservation);
        }
    }

    createFlightCard(flight) {
        return `
            <div class="reservation-card flight-card" data-id="${flight.id}">
                <div class="airline-info">
                    <img src="${this.escapeHtml(flight.airline_logo)}" alt="${this.escapeHtml(flight.airline)}" class="airline-logo">
                    <div class="airline-details">
                        <div class="airline-name">${this.escapeHtml(flight.airline)}</div>
                        <div class="flight-number">Flight ${this.escapeHtml(flight.flight_number)}</div>
                    </div>
                    <div class="status-badge status-${flight.status || 'pending'}">${flight.status || 'Pending'}</div>
                </div>
                <div class="flight-main-info">
                    <div class="flight-route">
                        <div class="route-point departure">
                            <div class="city-code">${this.escapeHtml(flight.departure_code)}</div>
                            <div class="city-name">${this.escapeHtml(flight.departure_city)}</div>
                            <div class="time-info">${new Date(flight.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                            <div class="date-info">${new Date(flight.start_time).toLocaleDateString([], {month: 'short', day: 'numeric'})}</div>
                        </div>
                        <div class="flight-path">
                            <div class="duration-info">
                                <i class="fas fa-plane"></i>
                                <span>${this.calculateDuration(new Date(flight.start_time), new Date(flight.end_time))}</span>
                            </div>
                        </div>
                        <div class="route-point arrival">
                            <div class="city-code">${this.escapeHtml(flight.arrival_code)}</div>
                            <div class="city-name">${this.escapeHtml(flight.arrival_city)}</div>
                            <div class="time-info">${new Date(flight.end_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                            <div class="date-info">${new Date(flight.end_time).toLocaleDateString([], {month: 'short', day: 'numeric'})}</div>
                        </div>
                    </div>
                    <div class="flight-details">
                        <div class="feature-badge">
                            <i class="fas fa-suitcase"></i>
                            <span>${flight.baggage_allowance || '1 Carry-on'}</span>
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-chair"></i>
                            <span>${flight.seat_type || 'Economy'}</span>
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-utensils"></i>
                            <span>${flight.meal_service ? 'Meal Included' : 'No Meal'}</span>
                        </div>
                    </div>
                </div>
                <div class="price-section">
                    <div class="price-tag">
                        <div class="price-amount">$${parseFloat(flight.cost).toFixed(2)}</div>
                        <div class="price-period">per person</div>
                    </div>
                    <button class="btn btn-outline-danger btn-sm mt-2" 
                         onclick="reservationManager.removeReservation('${flight.id}', 'flight')"
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
        `;
    }

    createCarRentalCard(car) {
        return `
            <div class="reservation-card car-rental-card" data-id="${car.rental_id}">
                <div class="car-thumbnail">
                    <img src="${this.escapeHtml(car.image)}" alt="${this.escapeHtml(car.name)}">
                    <span class="car-type-badge">
                        <i class="fas fa-car me-1"></i>${car.category || 'Standard'}
                    </span>
                </div>
                <div class="car-info">
                    <div class="car-header">
                        <h4 class="car-name">${this.escapeHtml(car.name)}</h4>
                        <div class="rental-company">
                            <img src="${this.escapeHtml(car.company_logo)}" alt="" class="company-logo">
                            <span class="company-name">${this.escapeHtml(car.company_name)}</span>
                        </div>
                    </div>
                    <div class="rental-details">
                        <div class="rental-point">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            <div class="point-time">${new Date(car.pickup_datetime).toLocaleString([], {
                                month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit'
                            })}</div>
                            <div class="point-location">${this.escapeHtml(car.pickup)}</div>
                        </div>
                        <div class="rental-duration">
                            <i class="fas fa-clock"></i>
                            ${this.calculateDuration(new Date(car.pickup_datetime), new Date(car.dropoff_datetime))}
                        </div>
                        <div class="rental-point">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <div class="point-time">${new Date(car.dropoff_datetime).toLocaleString([], {
                                month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit'
                            })}</div>
                            <div class="point-location">${this.escapeHtml(car.dropoff)}</div>
                        </div>
                    </div>
                    <div class="car-features">
                        ${this.createFeatureBadges(car)}
                    </div>
                </div>
                <div class="price-section">
                    <div class="price-tag">
                        <div class="price-amount">$${parseFloat(car.price).toFixed(2)}</div>
                        <div class="daily-rate">$${(parseFloat(car.price) / this.calculateDays(car.pickup_datetime, car.dropoff_datetime)).toFixed(2)}/day</div>
                    </div>
                    <button class="btn btn-outline-danger btn-sm mt-2 w-100" 
                            onclick="reservationManager.removeReservation(${car.id}, 'car')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
    }

    createFeatureBadges(car) {
        const features = [
            { icon: 'cog', text: car.transmission },
            { icon: 'users', text: `${car.number_of_seats} seats` },
            { icon: 'suitcase', text: `${parseInt(car.big_suitcases) + parseInt(car.small_suitcases)} bags` },
            car.air_conditioning && { icon: 'snowflake', text: 'A/C' },
            car.gps && { icon: 'satellite-dish', text: 'GPS' },
            car.unlimited_mileage && { icon: 'infinity', text: 'Unlimited miles' }
        ].filter(Boolean);

        return features.map(feature => `
            <span class="feature-badge">
                <i class="fas fa-${feature.icon}"></i>
                ${feature.text}
            </span>
        `).join('');
    }

    calculateDuration(start, end) {
        const diff = Math.abs(end - start);
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        return days > 0 ? `${days}d ${hours}h` : `${hours}h`;
    }

    calculateDays(start, end) {
        const diff = Math.abs(new Date(end) - new Date(start));
        return Math.ceil(diff / (1000 * 60 * 60 * 24));
    }

    initializeEventListeners() {
        // Filter buttons should have a data-category value matching the normalized types (e.g. 'all', 'flight', 'rentalCar')
        document.querySelectorAll('.filter-chip').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Remove active class from all filter chips.
                document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                // Set active category and log it.
                this.activeCategory = e.target.dataset.category;
                console.log("Active filter category changed to:", this.activeCategory);
                this.renderReservations();
            });
        });
    }

    updateFilterCounts() {
        const counts = {
            all: this.reservations.length,
            flight: this.reservations.filter(r => r.type === 'flight').length,
            rentalCar: this.reservations.filter(r => r.type === 'rentalCar').length,
            lodging: this.reservations.filter(r => r.type === 'lodging').length,
            restaurant: this.reservations.filter(r => r.type === 'restaurant').length
        };

        console.log("Updating filter counts:", counts);
        document.querySelectorAll('.filter-chip').forEach(chip => {
            // Normalize the chip's data-category value
            let category = chip.dataset.category;
            if (category === 'flights') {
                category = 'flight';
            } else if (category === 'rentalCars') {
                category = 'rentalCar';
            }
            const countSpan = chip.querySelector('.count');
            if (countSpan && counts[category] !== undefined) {
                countSpan.textContent = counts[category];
            }
        });
    }

    // Utility methods
    getIconForType(type) {
        const icons = {
            'flight': 'fa-plane',
            'rentalCar': 'fa-car',
            'activity': 'fa-calendar-alt',
            'restaurant': 'fa-utensils'
        };
        return icons[type] || 'fa-calendar-check';
    }

    getStatusColor(status) {
        const colors = {
            'confirmed': 'success',
            'pending': 'warning',
            'cancelled': 'danger'
        };
        return colors[status] || 'secondary';
    }

    formatDateTime(date) {
        return date.toLocaleString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    showEmptyState() {
        console.log("No reservations found. Showing empty state.");
        this.container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <h4>No Reservations Found</h4>
                <p>Start by adding your first reservation to this trip.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                    Add Reservation
                </button>
            </div>
        `;
    }

    showError(message) {
        console.error("Displaying error message:", message);
        this.container.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                ${message}
            </div>
        `;
    }
}

// Make reservationManager globally available
let reservationManager;

// Initialize the reservation manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded. Initializing ReservationManager.");
    console.log("window.currentTripId:", window.currentTripId);
    const timelineContainer = document.getElementById('reservationsTimeline');
    if (timelineContainer) {
        console.log("Found reservationsTimeline container:", timelineContainer);
        reservationManager = new ReservationManager(timelineContainer);
        if (window.currentTripId) {
            console.log("Loading reservations for trip:", window.currentTripId);
            reservationManager.loadReservations(window.currentTripId);
        } else {
            console.warn("currentTripId is not defined.");
        }
    } else {
        console.error("Reservations timeline container not found.");
    }
});
