// src/hotels.js

// Import the updateMapMarker function from the map module.
import { updateMapMarker } from '/travelplanner-master/javascript/create_trips_folder/map.js';

// Update the trip name based on the selected destination.
export function updateTripName() {
  const tripNameInput = document.getElementById('trip_name');
  const destinationSelect = document.getElementById('destination');
  const selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
  if (selectedOption) {
    tripNameInput.value = selectedOption.textContent.trim();
  }
}
  
// Load hotel cards for the given destination.
export function loadHotelsForDestination(destination) {
  const hotelCardsContainer = document.getElementById('hotel-cards');
  hotelCardsContainer.style.opacity = '0';
  setTimeout(() => {
    hotelCardsContainer.innerHTML = '';
    if (hotels[destination]) {
      hotels[destination].hotels.forEach((hotel, index) => {
        const isActive = index === 0 ? 'active' : '';
        const card = `
          <div class="carousel-item ${isActive}" data-hotel="${hotel.name}" data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}">
            <div class="card hotel-card">
              <div class="hotel-image-wrapper">
                <img src="/travelplanner-master/assets/images/${hotel.image_name}" class="card-img-top hotel-image" alt="${hotel.name}">
                <div class="hotel-image-overlay">
                  <div class="hotel-features">
                    <span class="feature-tag"><i class="fas fa-wifi"></i> Free WiFi</span>
                    <span class="feature-tag"><i class="fas fa-swimming-pool"></i> Pool</span>
                    <span class="feature-tag"><i class="fas fa-parking"></i> Parking</span>
                  </div>
                </div>
                <div class="hotel-rating">
                  ${Array(hotel.stars).fill('<i class="fas fa-star"></i>').join('')}
                </div>
              </div>
              <div class="card-body">
                <h5 class="card-title text-truncate">${hotel.name}</h5>
                <div class="price-tag">
                  <span class="price-label">Per Night</span>
                  <span class="price-amount">$${hotel.price}</span>
                </div>
              </div>
            </div>
          </div>`;
        hotelCardsContainer.innerHTML += card;
      });
      updateSelectedHotel();
      centerCarousel();
    }
    hotelCardsContainer.style.opacity = '1';
  }, 300);
}
  
// Update the selected hotel details and map marker.
export function updateSelectedHotel() {
  const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
  if (activeHotelItem) {
    const selectedHotel = activeHotelItem.getAttribute('data-hotel');
    const latitude = activeHotelItem.getAttribute('data-latitude');
    const longitude = activeHotelItem.getAttribute('data-longitude');
    document.getElementById('hotel').value = selectedHotel;
    // Now call the imported updateMapMarker to update the map marker.
    updateMapMarker(latitude, longitude);
  }
}
  
// Populate the All Hotels modal.
export function populateAllHotelsModal(destination) {
  const allHotelsGrid = document.getElementById('allHotelsGrid');
  allHotelsGrid.innerHTML = '';
  if (hotels[destination]) {
    hotels[destination].hotels.forEach((hotel) => {
      const card = `
        <div class="col-md-6 col-lg-4">
          <div class="card hotel-card" data-hotel="${hotel.name}" data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}">
            <div class="hotel-image-wrapper">
              <img src="/travelplanner-master/assets/images/${hotel.image_name}" class="card-img-top hotel-image" alt="${hotel.name}">
              <div class="hotel-rating">
                ${Array(hotel.stars).fill('<i class="fas fa-star"></i>').join('')}
              </div>
            </div>
            <div class="card-body">
              <h5 class="card-title">${hotel.name}</h5>
              <div class="price-tag">
                <span class="price-label">Per Night</span>
                <span class="price-amount">$${hotel.price}</span>
              </div>
            </div>
          </div>
        </div>`;
      allHotelsGrid.innerHTML += card;
    });
    // Attach click event listeners for the hotel cards.
    document.querySelectorAll('#allHotelsGrid .hotel-card').forEach(card => {
      card.addEventListener('click', function() {
        const hotelName = this.dataset.hotel;
        const latitude = this.dataset.latitude;
        const longitude = this.dataset.longitude;
        document.getElementById('hotel').value = hotelName;
        // Update the map marker.
        updateMapMarker(latitude, longitude);
        const carouselItems = document.querySelectorAll('#hotel-cards .carousel-item');
        carouselItems.forEach(item => {
          item.classList.toggle('active', item.dataset.hotel === hotelName);
        });
        document.querySelectorAll('#allHotelsGrid .hotel-card').forEach(c => {
          c.classList.remove('selected');
        });
        this.classList.add('selected');
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById('allHotelsModal')).hide();
        }, 500);
      });
    });
  }
}
  
// Center the hotel carousel based on the active item.
export function centerCarousel() {
  const carouselContainer = document.querySelector('.hotel-carousel');
  const carouselInner = document.querySelector('.carousel-inner');
  const activeItem = document.querySelector('#hotel-cards .carousel-item.active');
  if (activeItem && carouselContainer && carouselInner) {
    const containerRect = carouselContainer.getBoundingClientRect();
    const activeRect = activeItem.getBoundingClientRect();
    const offset = (containerRect.left + containerRect.width / 2) - (activeRect.left + activeRect.width / 2);
    carouselInner.style.transform = `translateX(${offset}px)`;
  }
}
