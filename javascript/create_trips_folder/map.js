// src/map.js

// Initialize the map.
export const map = L.map('map', { zoomControl: false }).setView([0, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '© OpenStreetMap contributors'
}).addTo(map);
L.control.zoom({ position: 'topright' }).addTo(map);

let currentMarker;

// Update the map marker for a given latitude and longitude.
export function updateMapMarker(lat, lng) {
  if (currentMarker) {
    map.removeLayer(currentMarker);
  }
  if (lat && lng) {
    currentMarker = L.marker([lat, lng]).addTo(map)
      .bindPopup("Hotel Location").openPopup();
    map.setView([lat, lng], 10);
  }
}
