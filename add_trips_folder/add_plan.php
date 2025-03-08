<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Plan Trip</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="add_plan.css" />
</head>
<body>
  <!-- If you want a top navigation bar, place it here -->
  <!-- <div class="top-bar"> ... </div> -->

  <!-- Main layout container -->
  <div class="layout">
    <!-- Left planning section -->
    <div class="planning-section">
      <!-- Place this inside your .planning-section (from the earlier layout) -->
        <div class="hero">
        <!-- Background image overlay -->
        <div class="hero-background"></div>

        <!-- Foreground card content -->
        <div class="hero-content">
            <h1>Trip to Japan</h1>
            <div class="hero-actions">
            <button class="add-trip-btn">Add trip dates</button>
            <!-- Replace with your avatar/profile icon -->
            <img src="profile-icon.png" alt="Profile Icon" class="user-avatar">
            </div>
        </div>
        </div>

        <div class="explore-section">
        <h2>Explore</h2>
        <button class="browse-all-btn">Browse all</button>

        <div class="cards">
            <!-- Card 1 -->
            <div class="card">
            <img src="japan1.jpg" alt="Top places for Japan" />
            <h3>Top places for Japan</h3>
            <p>Most often-seen on the web</p>
            </div>

            <!-- Card 2 -->
            <div class="card">
            <img src="japan2.jpg" alt="Tuyet's Japan: Video Game Guide 2025" />
            <h3>Tuyet's Japan: Video Game Guide 2025</h3>
            <p>Popular guide by a Wanderlog community member</p>
            </div>

            <!-- Card 3 -->
            <div class="card">
            <img src="japan3.jpg" alt="Search hotels with transparent pricing" />
            <h3>Search hotels with transparent pricing</h3>
            <p>Unlike most sites, we don't sort based on commissions</p>
            </div>
        </div>
        </div>

      <!-- More sections as needed... -->
    </div>

    <!-- Right map section (non-scrollable) -->
    <div class="map-section">
      <!-- Leaflet map will go here -->
      <div id="map"></div>
    </div>
  </div>

  <!-- Script tags for Leaflet (and your custom JS) go here -->
  <!-- 
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Initialize your Leaflet map on #map
  </script>
  -->
</body>
</html>
