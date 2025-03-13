// carRental.js

import { initializeCarRentalSearch } from '/carRentalSearch.js';
import { cleanupMap, initializeRentalMap } from '/mapUtils.js';

document.addEventListener('DOMContentLoaded', function() {
  // Initialize car rental search functionality.
  initializeCarRentalSearch();

  // Modal event listeners for map initialization and cleanup.
  const carRentalModal = document.getElementById('carRentalModal');
  if (carRentalModal) {
    carRentalModal.addEventListener('show.bs.modal', function() {
      console.log('Modal show event triggered');
      cleanupMap();
    });

    carRentalModal.addEventListener('shown.bs.modal', function() {
      console.log('Modal shown event triggered');
      setTimeout(() => {
        const mapContainer = document.getElementById('carRentalMap');
        console.log('Map container size before initialization:', {
          width: mapContainer.offsetWidth,
          height: mapContainer.offsetHeight
        });
        initializeRentalMap();
      }, 100);
    });

    carRentalModal.addEventListener('hide.bs.modal', function() {
      console.log('Modal hide event triggered');
      cleanupMap();
    });
  }
});
