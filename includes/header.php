<!-- filepath: /c:/xampp/htdocs/travelplanner-master/includes/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trip Details - <?= htmlspecialchars($trip['trip_name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/travelplanner-master/create_trips/create_trip.css">
  <link rel="stylesheet" href="/travelplanner-master/create_trips/trip_details.css">
  <link rel="stylesheet" href="/travelplanner-master/create_trips/flights.css">
  <link rel="stylesheet" href="/travelplanner-master/css/edit_trip.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
  <style>
      .map-overlay {
          background: rgba(255, 255, 255, 0.95);
          padding: 20px;
          border-radius: 10px;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">
        <i class="fas fa-globe-americas"></i> Travel Planner
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <div class="ms-auto">
          <button class="btn btn-light" onclick="window.location.href='index.php'">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
          </button>
        </div>
      </div>
    </div>
  </nav>
