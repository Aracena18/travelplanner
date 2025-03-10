// src/reservations.js

export const pendingReservations = {
    flights: [],
    lodging: [],
    rentalCars: [],
    restaurants: [],
    attachments: [],
    others: []
  };
  
  export function updatePendingReservationsField() {
    document.getElementById('pendingReservations').value = JSON.stringify(pendingReservations);
  }
  
  export function isFlightAlreadyAdded(flightId) {
    return pendingReservations.flights.some(flight => flight.flight_id === flightId);
  }
  