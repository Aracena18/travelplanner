<?php
// Include the database connection
include('db.php');

if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
    // Sanitize the input to avoid SQL injection
    $searchQuery = mysqli_real_escape_string($conn, $searchQuery);

    // Query to search trips by name or destination
    $sql = "SELECT * FROM trips WHERE trip_name LIKE '%$searchQuery%' OR destination LIKE '%$searchQuery%'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Display trip details (trip name, destination, etc.)
            echo "<div class='trip'>";
            echo "<h3>" . htmlspecialchars($row['trip_name']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['destination']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No trips found matching your search.</p>";
    }
} else {
    // If no search query, display all trips or a default message
    echo "<p>Please enter a search term.</p>";
}
