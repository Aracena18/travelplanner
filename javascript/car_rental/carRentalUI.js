// carRentalUI.js

import { calculateEstimatedCost } from '../create_trips_folder/cost.js';
import { pendingReservations, updatePendingReservationsField } from '../create_trips_folder/reservations.js';

export function updateSelectedCarsDisplay(carData) {
  const selectedCarsContainer = document.getElementById('selectedCarsContainer');
  const selectedCarsList = document.getElementById('selectedCarsList');
  const reservationCount = document.querySelector('#selectedCarsContainer .reservation-count');
  
  // Ensure the container is visible.
  selectedCarsContainer.style.display = 'block';
  
  // Remove any empty state placeholder.
  const emptyState = selectedCarsList.querySelector('.empty-state');
  if (emptyState) {
    emptyState.remove();
  }

  const carElement = document.createElement('div');
  carElement.className = 'selected-car-item animate__animated animate__fadeIn';
  carElement.dataset.carId = carData.car_id;
  
  const pickupDate = new Date(carData.pickupDateTime);
  const dropoffDate = new Date(carData.dropoffDateTime);
  
  const totalPrice = calculateTotalPrice(
    carData.price, 
    carData.pickupDateTime, 
    carData.dropoffDateTime
  );
  
  carElement.innerHTML = `
    <div class="car-info-compact">
      <div class="car-image-badge">
        ${carData.image ? `<img src="${carData.image}" alt="${carData.name}" style="width: 60px; height: 60px; object-fit: contain;">` : 
        '<i class="fas fa-car fa-2x text-primary"></i>'}
      </div>
      <div class="car-details-compact">
        <div class="car-name-badge">
          <i class="fas fa-car"></i>
          ${carData.name}
        </div>
        <div class="rental-period">
          <div class="period-point">
            <span class="period-label">Pickup</span>
            <span class="period-time">${pickupDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
            <span class="period-date">${pickupDate.toLocaleDateString()}</span>
          </div>
          <div class="rental-duration">
            <i class="fas fa-arrow-right"></i>
            <span>${calculateDuration(pickupDate, dropoffDate)}</span>
          </div>
          <div class="period-point">
            <span class="period-label">Drop-off</span>
            <span class="period-time">${dropoffDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
            <span class="period-date">${dropoffDate.toLocaleDateString()}</span>
          </div>
        </div>
      </div>
      <div class="price-section">
        <div class="price-details">
          <span class="price-amount">$${totalPrice}</span>
          <span class="price-period">total</span>
          <div class="price-breakdown text-muted">
            <small>$${carData.price} × ${Math.ceil((dropoffDate - pickupDate) / (1000 * 60 * 60 * 24))} days</small>
          </div>
        </div>
      </div>
    </div>
    <button class="remove-car" onclick="removeSelectedCarHandler('${carData.car_id}')">
      <i class="fas fa-times"></i>
    </button>
  `;

  selectedCarsList.appendChild(carElement);
  reservationCount.textContent = `${pendingReservations.rentalCars.length} ${pendingReservations.rentalCars.length === 1 ? 'Car' : 'Cars'}`;
}

export function calculateDuration(start, end) {
  const diff = Math.abs(end - start);
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  return days > 0 ? `${days}d ${hours}h` : `${hours}h`;
}

export function calculateTotalPrice(price, startDate, endDate) {
  const days = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24));
  return (parseFloat(price) * days).toFixed(2);
}

export function removeSelectedCar(carId) {
  const index = pendingReservations.rentalCars.findIndex(c => c.car_id === carId);
  if (index !== -1) {
    pendingReservations.rentalCars.splice(index, 1);
    updatePendingReservationsField();
    
    const carElement = document.querySelector(`.selected-car-item[data-car-id="${carId}"]`);
    if (carElement) {
      carElement.classList.add('animate__fadeOut');
      setTimeout(() => {
        carElement.remove();
        // Update UI if no cars remain.
        if (pendingReservations.rentalCars.length === 0) {
          const selectedCarsContainer = document.getElementById('selectedCarsContainer');
          selectedCarsContainer.style.display = 'none';
          const selectedCarsList = document.getElementById('selectedCarsList');
          selectedCarsList.innerHTML = `
            <div class="empty-state text-center py-4">
              <i class="fas fa-car text-muted"></i>
              <p class="text-muted mb-0">No rental cars selected yet</p>
            </div>
          `;
        } else {
          const reservationCount = document.querySelector('#selectedCarsContainer .reservation-count');
          reservationCount.textContent = `${pendingReservations.rentalCars.length} ${pendingReservations.rentalCars.length === 1 ? 'Car' : 'Cars'}`;
        }
      }, 300);
    }
    calculateEstimatedCost();
  }
}

// Expose a handler on the global window for the inline onclick.
window.removeSelectedCarHandler = removeSelectedCar;
