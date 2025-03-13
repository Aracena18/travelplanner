// carRentalSearch.js

import { calculateEstimatedCost } from '../create_trips_folder/cost.js';
import { pendingReservations, updatePendingReservationsField } from '../create_trips_folder/reservations.js';
import { updateSelectedCarsDisplay } from './carRentalUI.js';
import { dropoffMarker, pickupMarker } from './mapUtils.js';

export function initializeCarRentalSearch() {
  const searchCarsBtn = document.getElementById('searchCars');
  const carsListContainer = document.getElementById('carsList');
  const carLoadingIndicator = document.getElementById('carLoading');

  searchCarsBtn.addEventListener('click', async function() {
    // Retrieve input values.
    const pickupLocation = document.getElementById('pickupLocation').value;
    const dropoffLocation = document.getElementById('dropoffLocation').value;
    const pickupDateTime = document.getElementById('pickupDateTime').value;
    const dropoffDateTime = document.getElementById('dropoffDateTime').value;
    const selectedType = document.querySelector('input[name="carType"]:checked').value;

    if (!pickupLocation || !dropoffLocation || !pickupDateTime || !dropoffDateTime) {
      alert('Please fill in all required fields');
      return;
    }

    // Retrieve map marker coordinates (assuming these are globally set by mapUtils.js).
    const pickupLatLng = pickupMarker ? pickupMarker.getLatLng() : null;
    const dropoffLatLng = dropoffMarker ? dropoffMarker.getLatLng() : null;    
    if (!pickupLatLng || !dropoffLatLng) {
      alert('Please select both pickup and dropoff locations on the map.');
      return;
    }

    // Extract date and time.
    const pickupDate = pickupDateTime.substr(0, 10);
    const dropoffDate = dropoffDateTime.substr(0, 10);
    const pickupTime = pickupDateTime.substr(11, 5);
    const dropoffTime = dropoffDateTime.substr(11, 5);

    // Construct the API URL.
    const url = `https://booking-com15.p.rapidapi.com/api/v1/cars/searchCarRentals?` +
      `pick_up_latitude=${pickupLatLng.lat}&pick_up_longitude=${pickupLatLng.lng}` +
      `&drop_off_latitude=${dropoffLatLng.lat}&drop_off_longitude=${dropoffLatLng.lng}` +
      `&pick_up_date=${pickupDate}&drop_off_date=${dropoffDate}` +
      `&pick_up_time=${encodeURIComponent(pickupTime)}&drop_off_time=${encodeURIComponent(dropoffTime)}` +
      `&driver_age=30&currency_code=USD`;

    const options = {
      method: 'GET',
      headers: {
        'x-rapidapi-key': 'a4f936d18fmsh72c62428617d573p1b6010jsn36884d1c931d',
        'x-rapidapi-host': 'booking-com15.p.rapidapi.com'
      }
    };

    // Show loading indicator and clear previous results.
    carLoadingIndicator.classList.remove('d-none');
    carsListContainer.innerHTML = '';

    try {
      const response = await fetch(url, options);
      const resultData = await response.json();
      console.log('API Search Response:', resultData);

      carsListContainer.innerHTML = '';

      if (resultData.status && resultData.data && resultData.data.search_results) {
        const searchResults = resultData.data.search_results;
        searchResults.forEach(resultItem => {
          const vehicle = resultItem.vehicle_info || {};
          const vehicleId = resultItem.vehicle_id;
          const carName = vehicle.v_name || `Car ${vehicleId}`;
          const carImage = vehicle.image_url || vehicle.image_thumbnail_url || 'default_car_image.jpg';
          const transmission = vehicle.transmission || 'N/A';
          const mileage = vehicle.mileage || 'N/A';
          const seats = vehicle.seats || 'N/A';
          const doors = vehicle.doors || 'N/A';
          const suitcasesSmall = vehicle.suitcases ? vehicle.suitcases.small : 'N/A';
          const suitcasesBig = vehicle.suitcases ? vehicle.suitcases.big : 'N/A';
          
          const supplier = resultItem.supplier_info || {};
          const companyName = supplier.name || 'Unknown';
          const companyLogo = supplier.imageUrl || '';

          const pricing = resultItem.pricing_info || {};
          const price = pricing.price || 'N/A';
          const currency = pricing.currency || 'USD';

          // Build the car card layout.
          const carCard = document.createElement('div');
          carCard.className = 'car-card';
          carCard.innerHTML = `
            <div class="car-image">
              <img src="${carImage}" alt="${carName}">
              <span class="car-type-badge">${transmission}</span>
            </div>
            <div class="car-details">
              <h5 class="car-name">${carName}</h5>
              <div class="car-features">
                <span class="feature"><i class="fas fa-cog"></i> ${transmission}</span>
                <span class="feature"><i class="fas fa-tachometer-alt"></i> ${mileage}</span>
                <span class="feature"><i class="fas fa-users"></i> ${seats} seats</span>
                <span class="feature"><i class="fas fa-door-open"></i> ${doors} doors</span>
              </div>
              <div class="rental-company">
                <img src="${companyLogo}" alt="${companyName}" class="company-logo">
                <span class="company-name">${companyName}</span>
              </div>
              <div class="price-section">
                <div class="price-details">
                  <span class="price-amount">${currency}${price}</span>
                  <span class="price-period">per day</span>
                </div>
                <button class="btn btn-primary select-car-btn" data-car-id="${vehicleId}">Select</button>
              </div>
            </div>
          `;

          // Bind the select button event.
          carCard.querySelector('.select-car-btn').addEventListener('click', function() {
            const carId = this.dataset.carId;
            if (pendingReservations.rentalCars.some(c => c.car_id === carId)) {
              alert('This car is already added to your reservation.');
              return;
            }

            // Build the car data object.
            const carData = {
              car_id: carId,
              name: carName,
              image: carImage,
              transmission: transmission,
              mileage: mileage,
              fuelPolicy: 'N/A',
              numberOfDoors: doors,
              smallSuitcases: suitcasesSmall,
              bigSuitcases: suitcasesBig,
              numberOfSeats: seats,
              airConditioning: 'N/A',
              carClass: 'N/A',
              company: companyName,
              price: price,
              currency: currency,
              details: resultItem,
              pickup: pickupLocation,
              dropoff: dropoffLocation,
              pickupDateTime: pickupDateTime,
              dropoffDateTime: dropoffDateTime
            };

            pendingReservations.rentalCars.push(carData);
            updatePendingReservationsField();
            calculateEstimatedCost();

            // Close the modal and update the selected cars display.
            bootstrap.Modal.getInstance(document.getElementById('carRentalModal')).hide();
            updateSelectedCarsDisplay(carData);
            calculateEstimatedCost();
          });

          carsListContainer.appendChild(carCard);
        });
      } else {
        carsListContainer.innerHTML = `<p>No cars found</p>`;
      }
    } catch (error) {
      console.error('Error fetching car rentals:', error);
      alert('An error occurred while fetching available car rentals. Please try again.');
    } finally {
      carLoadingIndicator.classList.add('d-none');
    }
  });
}
