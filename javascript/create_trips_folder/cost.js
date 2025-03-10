// src/cost.js

export function calculateEstimatedCost() {
    const adultsNum = parseInt(document.getElementById('adults_num').value) || 0;
    const childsNum = parseInt(document.getElementById('childs_num').value) || 0;
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
    const hotelCost = activeHotelItem
      ? parseFloat(activeHotelItem.querySelector('.price-amount').textContent.replace('$', ''))
      : 0;
    const numberOfNights = Math.max((endDate - startDate) / (1000 * 60 * 60 * 24), 1);
    const totalPeople = adultsNum + childsNum;
    const roomsNeeded = Math.ceil(totalPeople / 2);
    let estimatedCost = (hotelCost * roomsNeeded * numberOfNights);
    
    // Assume "activities" is available globally or imported
    activities.forEach(activity => {
      estimatedCost += parseFloat(activity.cost);
    });
    
    const costDisplay = document.getElementById('estimated-cost-display');
    costDisplay.style.transform = 'scale(1.1)';
    setTimeout(() => { costDisplay.style.transform = 'scale(1)'; }, 200);
    costDisplay.textContent = `Estimated Cost: $${estimatedCost.toFixed(2)}`;
  }
  
  // Bind event listeners for cost recalculation.
  export function bindCostCalculationEvents() {
    ['adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
      document.getElementById(id).addEventListener('input', calculateEstimatedCost);
    });
    document.getElementById('hotel-carousel').addEventListener('click', calculateEstimatedCost);
    
    document.querySelectorAll('.form-control, .form-select').forEach(input => {
      input.addEventListener('input', function() {
        if (this.value) {
          this.classList.add('is-valid');
          this.classList.remove('is-invalid');
        } else {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
        }
      });
    });
  }
  