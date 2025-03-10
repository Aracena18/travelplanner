// src/flightModal.js (or add it to flight.js)
document.addEventListener('DOMContentLoaded', function() {
    const flightButton = document.querySelector('button[type="button"].btn-light i.fas.fa-plane');
    if (flightButton) {
      flightButton.parentElement.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('flightModal'));
        modal.show();
        const destinationSelect = document.getElementById('destination');
        const selectedDestination = destinationSelect.options[destinationSelect.selectedIndex].text;
        document.getElementById('arrivalCity').value = selectedDestination;
      });
    }
  });
  