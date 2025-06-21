<?php
// Database Configuration
// ----------------------
// Update these settings with your actual database credentials.

// Database host (e.g., 'localhost' or IP address)
$host = 'YOUR_DB_HOST'; // Example: 'localhost'

// Database name
$dbname = 'YOUR_DB_NAME'; // Example: 'mindust_db'

// Database username
$username = 'YOUR_DB_USERNAME'; // Example: 'root' or your specific user

// Database password
$password = 'YOUR_DB_PASSWORD'; // Example: 'your_secure_password'

// PDO Connection Options (optional, but recommended for production)
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    // Create PDO instance for database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    // Display a user-friendly error message.
    // In a production environment, you might want to log this error instead of die()
    // and show a generic error page to the user.
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please check your configuration or contact support if the issue persists. Details have been logged.");
}
?>
