<?php
// Path to your SQLite database file
$databasePath = 'movie.db'; // Replace with the actual path

try {
    // Create (connect to) SQLite database in file
    $conn = new PDO("sqlite:" . $databasePath);
    // Set error mode to exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to SQLite database";
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>