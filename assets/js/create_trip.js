const destinationSelect = document.getElementById('destination');
const hotelCardsContainer = document.getElementById('hotel-cards');
let currentMarker;
const map = initializeMap();

function initializeMap() {
    const map = L.map('map').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    return map;
}

function loadHotelsForDestination(destination) {
    hotelCardsContainer.innerHTML = '';
    if (destinations[destination]) {
        destinations[destination].hotels.forEach((hotel, index) => {
            const hotelCard = createHotelCard(hotel, index === 0);
            hotelCardsContainer.innerHTML += hotelCard;
        });
        updateSelectedHotel();
    }
}

function createHotelCard(hotel, isActive) {
    return `
        <div class="carousel-item ${isActive ? 'active' : ''}" 
             data-hotel="${hotel.name}" 
             data-latitude="${hotel.latitude}" 
             data-longitude="${hotel.longitude}">
            <div class="card hotel-card">
                <img src="assets/images/${hotel.image_name}" class="card-img-top" alt="${hotel.name}">
                <div class="card-body">
                    <h5 class="card-title">${hotel.name}</h5>
                    <div class="d-flex justify-content-start">
                        ${'★'.repeat(hotel.stars)}${'☆'.repeat(5 - hotel.stars)}
                    </div>
                    <div class="mt-3">
                        <h5 class="text-success fs-3 fw-bold">$${hotel.price}</h5>
                    </div>
                </div>
            </div>
        </div>`;
}

// ...existing JavaScript functions (updateSelectedHotel, calculateEstimatedCost, etc)...

// Event Listeners
destinationSelect.addEventListener('change', function() {
    loadHotelsForDestination(this.value);
});

document.getElementById('hotel-carousel').addEventListener('slid.bs.carousel', updateSelectedHotel);

['adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateEstimatedCost);
});

// Initialize
loadHotelsForDestination(destinationSelect.value);
calculateEstimatedCost();
