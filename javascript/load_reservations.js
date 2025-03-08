class ReservationManager {
    constructor(timelineContainer) {
        this.container = timelineContainer;
        this.reservations = [];
        this.activeCategory = 'all';
        this.initializeEventListeners();
    }

    async loadReservations(tripId) {
        try {
            const response = await fetch(`/travelplanner-master/create_trips/flights.php?trip_id=${tripId}&type=reservations`);
            const contentType = response.headers.get('content-type');
            
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server did not return JSON');
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Unknown error occurred');
            }
            
            this.reservations = data.reservations;
            this.renderReservations();
        } catch (error) {
            console.error('Error loading reservations:', error);
            this.showError(`Failed to load reservations: ${error.message}`);
        }
    }

    renderReservations() {
        const filteredReservations = this.filterReservations();
        
        if (filteredReservations.length === 0) {
            this.showEmptyState();
            return;
        }

        this.container.innerHTML = filteredReservations.map(reservation => 
            this.createReservationCard(reservation)
        ).join('');
    }

    filterReservations() {
        return this.activeCategory === 'all' 
            ? this.reservations 
            : this.reservations.filter(r => r.type === this.activeCategory);
    }

    createReservationCard(reservation) {
        const startDate = new Date(reservation.start_time);
        const endDate = reservation.end_time ? new Date(reservation.end_time) : null;
        
        return `
            <div class="reservation-card ${reservation.type}" data-id="${reservation.id}">
                <div class="reservation-timeline-dot">
                    <i class="fas ${this.getIconForType(reservation.type)}"></i>
                </div>
                <div class="reservation-content">
                    <div class="reservation-header">
                        <h4>${this.escapeHtml(reservation.title)}</h4>
                        <span class="badge bg-${this.getStatusColor(reservation.status)}">
                            ${reservation.status}
                        </span>
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
                        <div class="airline">${this.escapeHtml(reservation.airline)}</div>
                        <div class="route">
                            ${this.escapeHtml(reservation.departure_city)} 
                            <i class="fas fa-plane"></i> 
                            ${this.escapeHtml(reservation.arrival_city)}
                        </div>
                    </div>
                `;
            // Add more cases for other reservation types
            default:
                return '';
        }
    }

    initializeEventListeners() {
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.activeCategory = e.target.dataset.category;
                this.renderReservations();
            });
        });
    }

    // Utility methods
    getIconForType(type) {
        const icons = {
            'flight': 'fa-plane',
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
        this.container.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                ${message}
            </div>
        `;
    }
}

// Initialize the reservation manager
document.addEventListener('DOMContentLoaded', () => {
    const timelineContainer = document.getElementById('reservationsTimeline');
    if (timelineContainer) {
        const reservationManager = new ReservationManager(timelineContainer);
        if (typeof currentTripId !== 'undefined') {
            reservationManager.loadReservations(currentTripId);
        }
    }
});
