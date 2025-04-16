<?php
// Database credentials
$host = 'localhost'; // Usually 'localhost'
$dbname = 'db_name'; // Database name
$username = 'user_name'; // Database username (e.g., 'root')
$password = 'user_pass'; // Database password (empty by default for local setups)

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
