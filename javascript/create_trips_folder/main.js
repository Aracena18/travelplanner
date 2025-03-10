// /travelplanner-master/javascript/create_trips_folder/main.js

import { bindCostCalculationEvents, calculateEstimatedCost } from '/travelplanner-master/javascript/create_trips_folder/cost.js';
import { centerCarousel, loadHotelsForDestination, populateAllHotelsModal, updateSelectedHotel, updateTripName } from '/travelplanner-master/javascript/create_trips_folder/hotels.js';

// Import modules that run their own initialization code.
import '/travelplanner-master/javascript/create_trips_folder/carRental.js';
import '/travelplanner-master/javascript/create_trips_folder/flight.js';
import '/travelplanner-master/javascript/create_trips_folder/map.js';
import '/travelplanner-master/javascript/create_trips_folder/show_flights.js';

// Bind destination change event.
const destinationSelect = document.getElementById('destination');
destinationSelect.addEventListener('change', function(e) {
  updateTripName();
  loadHotelsForDestination(e.target.value);
  populateAllHotelsModal(e.target.value);
});

// Carousel controls.
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

  
// Bind cost calculation events.
bindCostCalculationEvents();

// Initial load.
updateTripName();
calculateEstimatedCost();
loadHotelsForDestination(destinationSelect.value);
