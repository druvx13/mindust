<?php
// Standalone script to create an initial admin user.
// Usage: php create_admin_setup.php
// IMPORTANT: Delete this file after creating the admin user.
// IMPORTANT: Ensure config.php is correctly set up before running this.

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.");
}

require_once 'config.php'; // For database connection ($pdo)

if (!isset($pdo)) {
    die("Failed to load database configuration. Ensure config.php is correct.\n");
}

function prompt($message) {
    echo $message . " ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    return $line;
}

echo "Creating a new admin user...\n";

$username = prompt("Enter admin username:");
if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
    die("Invalid username. Must be 3-50 characters.\n");
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die("Invalid username. Use only alphanumeric characters and underscores.\n");
}


$email = prompt("Enter admin email:");
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.\n");
}

$password = prompt("Enter admin password (min 8 characters):");
if (strlen($password) < 8) {
    die("Password must be at least 8 characters long.\n");
}
$password_confirm = prompt("Confirm admin password:");
if ($password !== $password_confirm) {
    die("Passwords do not match.\n");
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

if (!$password_hash) {
    die("Failed to hash password.\n");
}

try {
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        die("Admin user with this username or email already exists.\n");
    }

    $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password_hash]);

    if ($stmt->rowCount() > 0) {
        echo "Admin user '$username' created successfully.\n";
        echo "IMPORTANT: Delete this script (create_admin_setup.php) immediately!\n";
    } else {
        echo "Failed to create admin user. No rows affected.\n";
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}
?>
