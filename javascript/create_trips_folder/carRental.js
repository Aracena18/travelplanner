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
