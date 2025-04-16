<?php
// Database credentials
$host = 'sql111.byetcluster.com'; // Usually 'localhost'
$dbname = 'ssmm_32394582_blog'; // Database name
$username = 'ssmm_32394582'; // Database username (e.g., 'root')
$password = '13qrny65'; // Database password (empty by default for local setups)

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>