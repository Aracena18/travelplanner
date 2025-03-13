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
            // Normalize flights to ensure type "flight"
            const normalizedFlights = flights.map(flight => ({
                ...flight,
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
            // Normalize car rentals using pickup and dropoff date fields
            const normalizedCars = cars.map(car => ({
                ...car,
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
        const startDate = new Date(reservation.start_time);
        const endDate = reservation.end_time ? new Date(reservation.end_time) : null;
        
        let title = reservation.title || '';
        if (!title) {
            if (reservation.type === 'flight') {
                title = `${reservation.airline} Flight`;
            } else if (reservation.type === 'rentalCar') {
                title = reservation.name;
            }
        }
        console.log("Creating card for reservation:", title, "Type:", reservation.type);
        
        return `
            <div class="reservation-card ${reservation.type}" data-id="${reservation.id}">
                <div class="reservation-timeline-dot">
                    <i class="fas ${this.getIconForType(reservation.type)}"></i>
                </div>
                <div class="reservation-content">
                    <div class="reservation-header">
                        <h4>${this.escapeHtml(title)}</h4>
                        ${reservation.status ? `<span class="badge bg-${this.getStatusColor(reservation.status)}">${reservation.status}</span>` : ''}
                    </div>
                    ${this.getReservationDetails(reservation, startDate, endDate)}
                </div>
            </div>
        `;
    }

    getReservationDetails(reservation, startDate, endDate) {
        return `
            <div class="reservation-details">
                <div class="time-location">
                    <i class="far fa-clock"></i> 
                    ${this.formatDateTime(startDate)}
                    ${endDate ? ` - ${this.formatDateTime(endDate)}` : ''}
                </div>
                ${this.getTypeSpecificDetails(reservation)}
                <div class="reservation-actions mt-3">
                    <button class="btn btn-sm btn-outline-primary" onclick="editReservation(${reservation.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReservation(${reservation.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
    }

    getTypeSpecificDetails(reservation) {
        switch (reservation.type) {
            case 'flight':
                return `
                    <div class="flight-details">
                        <div class="airline-info">
                            ${reservation.airline_logo ?
                                `<img src="${this.escapeHtml(reservation.airline_logo)}" alt="${this.escapeHtml(reservation.airline)}" class="airline-logo">` :
                                '<i class="fas fa-plane"></i>'
                            }
                            <span class="airline-name">${this.escapeHtml(reservation.airline)}</span>
                        </div>
                        <div class="flight-route">
                            <div class="departure">
                                <span class="city">${this.escapeHtml(reservation.departure_city)}</span>
                                <span class="time">${new Date(reservation.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            </div>
                            <div class="flight-duration">
                                <i class="fas fa-plane"></i>
                                <span>${this.escapeHtml(reservation.duration)}</span>
                            </div>
                            <div class="arrival">
                                <span class="city">${this.escapeHtml(reservation.arrival_city)}</span>
                                <span class="time">${new Date(reservation.end_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            </div>
                        </div>
                        <div class="flight-price">
                            <strong>$${parseFloat(reservation.cost).toFixed(2)}</strong>
                        </div>
                    </div>
                `;
            case 'rentalCar':
                return `
                    <div class="car-rental-details">
                        <div class="car-info">
                            ${reservation.image ?
                                `<img src="${this.escapeHtml(reservation.image)}" alt="${this.escapeHtml(reservation.name)}" class="car-image">` :
                                '<i class="fas fa-car"></i>'
                            }
                            <div class="car-specs">
                                <h5>${this.escapeHtml(reservation.name)}</h5>
                                <div class="specs-grid">
                                    <span><i class="fas fa-cog"></i> ${this.escapeHtml(reservation.transmission)}</span>
                                    <span><i class="fas fa-users"></i> ${reservation.number_of_seats} seats</span>
                                    <span><i class="fas fa-suitcase"></i> ${parseInt(reservation.big_suitcases || 0) + parseInt(reservation.small_suitcases || 0)} bags</span>
                                    ${reservation.air_conditioning ? '<span><i class="fas fa-snowflake"></i> A/C</span>' : ''}
                                </div>
                            </div>
                        </div>
                        <div class="rental-period">
                            <div class="pickup">
                                <strong>Pickup:</strong> ${this.formatDateTime(new Date(reservation.pickup_datetime))}
                                <div class="location">${this.escapeHtml(reservation.pickup)}</div>
                            </div>
                            <div class="dropoff">
                                <strong>Drop-off:</strong> ${this.formatDateTime(new Date(reservation.dropoff_datetime))}
                                <div class="location">${this.escapeHtml(reservation.dropoff)}</div>
                            </div>
                        </div>
                        <div class="rental-price">
                            <strong>$${parseFloat(reservation.price).toFixed(2)}</strong>
                            <span class="duration">${this.calculateDuration(new Date(reservation.pickup_datetime), new Date(reservation.dropoff_datetime))}</span>
                        </div>
                    </div>
                `;
            default:
                return '';
        }
    }

    calculateDuration(start, end) {
        const diff = Math.abs(end - start);
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        return days > 0 ? `${days}d ${hours}h` : `${hours}h`;
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

// Initialize the reservation manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded. Initializing ReservationManager.");
    console.log("window.currentTripId:", window.currentTripId);
    const timelineContainer = document.getElementById('reservationsTimeline');
    if (timelineContainer) {
        console.log("Found reservationsTimeline container:", timelineContainer);
        const reservationManager = new ReservationManager(timelineContainer);
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
