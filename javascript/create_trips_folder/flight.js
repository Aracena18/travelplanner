// src/flight.js

import { getFlightLocation } from '/travelplanner-master/javascript/create_trips_folder/api.js';
import { calculateEstimatedCost } from '/travelplanner-master/javascript/create_trips_folder/cost.js';
import { formatDuration } from '/travelplanner-master/javascript/create_trips_folder/helpers.js';
import { isFlightAlreadyAdded, pendingReservations, updatePendingReservationsField } from '/travelplanner-master/javascript/create_trips_folder/reservations.js';

document.getElementById('searchFlights').addEventListener('click', async function() {
  const departureCity = document.getElementById('departureCity').value.trim();
  const arrivalCity = document.getElementById('arrivalCity').value.trim();
  const departDate = document.getElementById('flightDepartureDate').value; // Expected format: YYYY-MM-DD
  const returnDate = document.getElementById('flightReturnDate').value; // Optional

  if (!departureCity || !arrivalCity || !departDate) {
    alert('Please enter departure, arrival cities and a departure date');
    return;
  }

  // Read number of adults and children from form.
  const adults = document.getElementById('adults_num').value || 1;
  const childs = document.getElementById('childs_num').value || 0;
  const childrenParam = (parseInt(childs) > 0)
    ? Array(parseInt(childs)).fill(10).join(',')
    : '0';

  // Show loading indicator.
  document.getElementById('flightLoading').classList.remove('d-none');

  try {
    // Retrieve location IDs via searchDestination endpoint.
    const fromId = await getFlightLocation(departureCity);
    const toId = await getFlightLocation(arrivalCity);

    if (!fromId || !toId) {
      throw new Error('Could not retrieve location IDs for one or both cities');
    }

    // Build the flight search URL.
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

    // Extract and map flight offers.
    const flightOffers = (result.data && result.data.flightOffers) || [];
    console.log('Number of flight offers:', flightOffers.length);

    const flights = flightOffers.map((flight, index) => {
      const id = flight.token || `F${index+1}`;
      const segment = flight.segments[0];
      const leg = segment.legs[0];
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

    // Update the UI for flights.
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
