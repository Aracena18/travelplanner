// mapUtils.js

// Export global variables if they are needed across modules.
export let rentalMap;
export let pickupMarker;
export let dropoffMarker;
export let activeLocationType = null;

export function initializeRentalMap() {
  console.log('Starting map initialization...');
  
  const mapContainer = document.getElementById('carRentalMap');
  if (!mapContainer) {
    console.error('Map container not found! ID: carRentalMap');
    return;
  }
  
  try {
    rentalMap = L.map('carRentalMap', {
      fadeAnimation: false,
      zoomAnimation: false,
      center: [40.7128, -74.0060],
      zoom: 13
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19,
      updateWhenIdle: true,
      keepBuffer: 2
    }).addTo(rentalMap);

    rentalMap.invalidateSize(true);
    initializeMapFeatures();
    console.log('Map initialization completed successfully');
  } catch (error) {
    console.error('Error during map initialization:', error);
  }
}

export function initializeMapFeatures() {
  const provider = new GeoSearch.OpenStreetMapProvider();
  initializeLocationSearch('pickup', provider);
  initializeLocationSearch('dropoff', provider);

  rentalMap.on('click', function(e) {
    if (activeLocationType) {
      setLocation(e.latlng, activeLocationType);
    }
  });

  initializeLocationPickers();
}

export function initializeLocationPickers() {
  document.querySelectorAll('.btn-pick-map').forEach(btn => {
    btn.addEventListener('click', function() {
      const type = this.dataset.type;
      activeLocationType = activeLocationType === type ? null : type;
      document.querySelectorAll('.btn-pick-map').forEach(b => {
        b.classList.toggle('active', b.dataset.type === activeLocationType);
      });
      updateMapInstructions();
    });
  });
}

export function updateMapInstructions() {
  const instructions = document.getElementById('mapInstructions');
  if (instructions) {
    instructions.textContent = activeLocationType ? 
      `Click on the map to set ${activeLocationType} location` : 
      'Use the search boxes or click the map pin buttons to set locations';
    instructions.classList.toggle('hidden', !activeLocationType);
  }
}

// Add the missing initializeLocationSearch function
function initializeLocationSearch(type, provider) {
  const input = document.getElementById(`${type}Location`);
  if (!input) return;
  
  const resultsContainer = document.createElement('div');
  resultsContainer.className = 'location-search-results';
  resultsContainer.style.display = 'none';
  input.parentNode.appendChild(resultsContainer);

  let searchTimeout;

  input.addEventListener('input', async function() {
    clearTimeout(searchTimeout);
    const query = this.value;
    if (query.length < 3) {
      resultsContainer.style.display = 'none';
      return;
    }
    searchTimeout = setTimeout(async () => {
      try {
        const results = await provider.search({ query });
        resultsContainer.innerHTML = '';
        if (results.length > 0) {
          results.slice(0, 5).forEach(result => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            item.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${result.label}`;
            item.addEventListener('click', () => {
              const latlng = { lat: result.y, lng: result.x };
              setLocation(latlng, type);
              input.value = result.label;
              resultsContainer.style.display = 'none';
            });
            resultsContainer.appendChild(item);
          });
          resultsContainer.style.display = 'block';
        }
      } catch (error) {
        console.error('Search error:', error);
      }
    }, 300);
  });

  document.addEventListener('click', function(e) {
    if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
      resultsContainer.style.display = 'none';
    }
  });
}

export async function setLocation(latlng, type) {
  const input = document.getElementById(`${type}Location`);
  if (type === 'pickup' && pickupMarker) {
    pickupMarker.remove();
  }
  if (type === 'dropoff' && dropoffMarker) {
    dropoffMarker.remove();
  }
  
  // Create a new marker.
  const newMarker = L.marker(latlng, {
    icon: L.divIcon({
      className: `location-marker ${type}`,
      html: `<i class="fas fa-map-marker-alt"></i>`,
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    })
  }).addTo(rentalMap);

  if (type === 'pickup') {
    pickupMarker = newMarker;
  } else {
    dropoffMarker = newMarker;
  }

  panToLocation(latlng);

  const address = await reverseGeocode(latlng);
  input.value = address;

  if (pickupMarker && dropoffMarker) {
    drawRoute(pickupMarker.getLatLng(), dropoffMarker.getLatLng());
  }
}

export function panToLocation(latlng) {
  const zoomLevel = 13;
  rentalMap.setView(latlng, zoomLevel, {
    animate: true,
    duration: 1,
    easeLinearity: 0.25
  });
}

let routeLayer = null;

export async function drawRoute(start, end) {
  if (routeLayer) {
    routeLayer.remove();
  }
  try {
    const response = await fetch(
      `https://router.project-osrm.org/route/v1/driving/${start.lng},${start.lat};${end.lng},${end.lat}?overview=full&geometries=geojson`
    );
    const data = await response.json();
    if (data.routes && data.routes.length > 0) {
      const route = data.routes[0];
      
      // Draw background route.
      L.geoJSON(route.geometry, {
        style: {
          color: '#e2e8f0',
          weight: 6,
          opacity: 1,
          lineCap: 'round',
          lineJoin: 'round'
        }
      }).addTo(rentalMap);

      // Main animated route.
      routeLayer = L.geoJSON(route.geometry, {
        style: {
          color: '#2563eb',
          weight: 4,
          opacity: 0.8,
          lineCap: 'round',
          lineJoin: 'round',
          dashArray: '10,15',
          className: 'animated-route'
        }
      }).addTo(rentalMap);

      // Direction arrows.
      L.polylineDecorator(routeLayer, {
        patterns: [
          {
            offset: '5%',
            repeat: '10%',
            symbol: L.Symbol.arrowHead({
              pixelSize: 12,
              polygon: false,
              pathOptions: {
                color: '#2563eb',
                fillOpacity: 1,
                weight: 2
              }
            })
          }
        ]
      }).addTo(rentalMap);

      // Route highlights.
      L.geoJSON(route.geometry, {
        style: {
          color: 'rgba(37, 99, 235, 0.3)',
          weight: 12,
          opacity: 1,
          lineCap: 'round',
          lineJoin: 'round'
        }
      }).addTo(rentalMap);

      rentalMap.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
      updateRouteInfo(route);
    }
  } catch (error) {
    console.error('Error drawing route:', error);
  }
}

export function updateRouteInfo(route) {
  const routeStatsContainer = document.querySelector('.route-stats-container');
  const distance = (route.distance / 1000).toFixed(1);
  const duration = Math.round(route.duration / 60);
  const hours = Math.floor(duration / 60);
  const minutes = duration % 60;
  const formattedDuration = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;

  routeStatsContainer.innerHTML = `
    <div class="route-stat-box">
      <div class="stat-icon">
        <i class="fas fa-road"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Total Distance</div>
        <div class="stat-value">${distance} km</div>
      </div>
    </div>
    <div class="route-stat-box">
      <div class="stat-icon">
        <i class="fas fa-clock"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Estimated Drive Time</div>
        <div class="stat-value">${formattedDuration}</div>
      </div>
    </div>
  `;
}

export async function reverseGeocode(latlng) {
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?lat=${latlng.lat}&lon=${latlng.lng}&format=json`
    );
    const data = await response.json();
    return data.display_name;
  } catch (error) {
    console.error('Reverse geocoding error:', error);
    return `${latlng.lat}, ${latlng.lng}`;
  }
}

export function cleanupMap() {
  if (routeLayer) {
    routeLayer.remove();
    routeLayer = null;
  }
  console.log('Cleaning up map...');
  if (rentalMap) {
    try {
      rentalMap.off();
      rentalMap.remove();
      rentalMap = null;
      pickupMarker = null;
      dropoffMarker = null;
      activeLocationType = null;
      console.log('Map cleanup completed');
    } catch (error) {
      console.error('Error during map cleanup:', error);
    }
  }
}
