// Save new activity via AJAX
function saveActivity() {
    const form = document.getElementById("activity-form");
    const formData = new FormData(form);
    
    // Append current trip id if not already included
    if (!formData.has("trip_id")) {
        formData.append("trip_id", window.currentTripId);
    }
    
    // Disable the save button to prevent duplicate submissions
    const saveButton = document.querySelector("#addActivityModal .btn-primary");
    saveButton.disabled = true;
    
    fetch('/travelplanner-master/create_trips/add_activity.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        saveButton.disabled = false;
        if (data.success) {
            // Reset the form and close the modal
            form.reset();
            const modalEl = document.getElementById('addActivityModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            // Update the timeline with the new activity
            addActivityToTimeline(data.activity);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        saveButton.disabled = false;
        alert("An error occurred while saving the activity.");
    });
}

// Function to add the new activity to the timeline
function addActivityToTimeline(activity) {
    // Create the timeline item element
    const timelineItem = document.createElement('div');
    timelineItem.classList.add('timeline-item');
    timelineItem.setAttribute('data-activity-id', activity.id);
    
    // Get the appropriate icon based on activity type
    const iconClass = getActivityIcon(activity.type);
    
    // Create the inner HTML matching your timeline structure
    timelineItem.innerHTML = `
        <div class="timeline-icon">
            <i class="fas ${iconClass}"></i>
        </div>
        <div class="timeline-content">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="mb-1">${activity.name}</h5>
                    <p class="text-muted mb-2">
                        <i class="fas fa-clock me-2"></i>
                        ${new Date(activity.start_time).toLocaleString()}
                    </p>
                </div>
                <div class="activity-cost">
                    <span class="badge bg-primary">
                        $${parseFloat(activity.cost).toFixed(2)}
                    </span>
                </div>
            </div>
            <p class="mb-3">${activity.description}</p>
            <div class="activity-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="editActivity(${activity.id})">
                    <i class="fas fa-edit me-1"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteActivity(${activity.id})">
                    <i class="fas fa-trash-alt me-1"></i> Delete
                </button>
            </div>
        </div>
    `;
    
    // Append the new timeline item before the "Add New Activity" button container.
    // Assuming the last child of the timeline is the container for the add button.
    const timeline = document.querySelector('.timeline');
    const addButtonContainer = timeline.querySelector('.text-center');
    timeline.insertBefore(timelineItem, addButtonContainer);
}

// Helper function to determine the appropriate icon class for an activity type
function getActivityIcon(type) {
    switch(type) {
        case 'tour': return 'fa-map-marked-alt';
        case 'adventure': return 'fa-mountain';
        case 'cultural': return 'fa-landmark';
        case 'dining': return 'fa-utensils';
        case 'entertainment': return 'fa-ticket-alt';
        default: return 'fa-calendar-day';
    }
}
