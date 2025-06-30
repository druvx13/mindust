<?php
// Mindust Web Setup Wizard
// Main controller

session_start();

// Mindust Web Setup Wizard
// Main controller

session_start();
error_reporting(E_ALL); // TODO: Set to 0 in production or handle errors better
ini_set('display_errors', '1'); // TODO: Set to 0 in production

require_once 'SetupHelper.php';
$setupHelper = new SetupHelper();

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// If already installed, show message and exit.
if ($setupHelper->isInstalled()) {
    // It's important this check happens before any session/step logic for starting new install.
    // But allow access if a specific 'unlock' parameter is given for testing/dev (REMOVE FOR PRODUCTION)
    if (!isset($_GET['force_reinstall_dev'])) {
        echo "<!DOCTYPE html><html lang='en'><head><title>Mindust Setup - Error</title><style>body{font-family:sans-serif;padding:20px;background-color:#f4f4f4;color:#333;}.container{background-color:#fff;padding:20px;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,0.1);text-align:center;}h1{color:red;}</style></head><body>";
        echo "<div class='container'>";
        echo "<h1>Already Installed!</h1>";
        echo "<p>Mindust CMS appears to be already installed (<code>installed.lock</code> found).</p>";
        echo "<p style='font-weight:bold; color:red;'>For security reasons, please DELETE the entire 'setup/' directory from your server NOW.</p>";
        echo "<p><a href='../index.php'>Go to Homepage</a></p>";
        echo "</div></body></html>";
        exit;
    }
}


$current_step = $_SESSION['setup_step'] ?? 0;
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$error_message = ''; // For displaying errors on the views

// --- Step 0: Welcome & Pre-requisites ---
if ($action === 'start_setup' && $current_step == 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $setupHelper->validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['setup_step'] = 1;
        $current_step = 1;
        header("Location: index.php");
        exit;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $error_message = "CSRF token validation failed. Please try again.";
        // Fall through to display welcome page with error
    }
}


// --- Step 1: Database Configuration ---
if ($current_step == 1 && $action === 'submit_db_config') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$setupHelper->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $error_message = "CSRF token validation failed. Please try again.";
        } else {
            $db_config = [
                'host' => trim($_POST['db_host'] ?? 'localhost'),
                'dbname' => trim($_POST['db_name'] ?? ''),
            'username' => trim($_POST['db_user'] ?? ''),
            'password' => $_POST['db_pass'] ?? '' // Password can be empty
        ];

        if (empty($db_config['dbname']) || empty($db_config['username'])) {
            $error_message = "Database Name and Username are required.";
        } else {
            $connection_test = $setupHelper->testDbConnection($db_config);
            if ($connection_test === true) {
                $_SESSION['setup_db_config'] = $db_config;
                $_SESSION['setup_step'] = 2;
                $current_step = 2;
                header("Location: index.php"); // Redirect
                exit;
            } else {
                $error_message = "Database connection failed: " . htmlspecialchars($connection_test);
            }
        }
    }
}


// --- Step 2: Admin User Creation ---
if ($current_step == 2 && $action === 'submit_admin_creation') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$setupHelper->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $error_message = "CSRF token validation failed. Please try again.";
        } else {
            $admin_details = [
                'username' => trim($_POST['admin_username'] ?? ''),
                'email' => trim($_POST['admin_email'] ?? ''),
            'password' => $_POST['admin_password'] ?? '',
            'password_confirm' => $_POST['admin_password_confirm'] ?? ''
        ];

        // Validation
        if (empty($admin_details['username']) || !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $admin_details['username'])) {
            $error_message = "Admin username must be 3-50 alphanumeric characters or underscores.";
        } elseif (empty($admin_details['email']) || !filter_var($admin_details['email'], FILTER_VALIDATE_EMAIL)) {
            $error_message = "A valid admin email address is required.";
        } elseif (empty($admin_details['password']) || strlen($admin_details['password']) < 8) {
            $error_message = "Admin password must be at least 8 characters long.";
        } elseif ($admin_details['password'] !== $admin_details['password_confirm']) {
            $error_message = "Admin passwords do not match.";
        } else {
            // All checks passed (for this stage)
            $_SESSION['setup_admin_details'] = [
                'username' => $admin_details['username'],
                'email' => $admin_details['email'],
                'password' => $admin_details['password'] // Store plain password temporarily, will be hashed at finalization
            ];
            $_SESSION['setup_step'] = 3;
            $current_step = 3;
            header("Location: index.php"); // Redirect
            exit;
        }
    }
}

// --- Step 3: Finalization ---
$finalize_messages = []; // To pass messages to the view_finalize.php
$config_content_for_manual_copy = null;
$show_manual_config_confirm_button = false;

if ($current_step == 3) {
    // This step is triggered directly after admin creation or by 'confirm_manual_config'

    if (!isset($_SESSION['setup_db_config']) || !isset($_SESSION['setup_admin_details'])) {
        // Something went wrong, db_config or admin_details not in session. Reset.
        $finalize_messages[] = ['type' => 'error', 'text' => 'Session data missing. Please restart setup.'];
        $_SESSION['setup_step'] = 0;
        // No redirect here, let view_finalize display the error, or redirect to step 0.
        // For now, let view_finalize handle it.
    } else {
        $db_config = $_SESSION['setup_db_config'];
        $config_php_content = $setupHelper->generateConfigContent($db_config);

        $config_file_exists_and_seems_valid = file_exists('../config.php') && strpos(file_get_contents('../config.php'), '// Setup completed marker - Mindust CMS') !== false;

        if (!$config_file_exists_and_seems_valid || $action === 'confirm_manual_config') {
            if ($action !== 'confirm_manual_config' && !$setupHelper->writeConfigFile($config_php_content)) {
                // Failed to write config.php automatically
                $finalize_messages[] = ['type' => 'warning', 'text' => 'Could not write <code>config.php</code> automatically due to permissions.'];
                $finalize_messages[] = ['type' => 'info', 'text' => 'Please copy the content below into a file named <code>config.php</code> in the main directory of your Mindust installation (where the <code>setup</code> folder is).'];
                $config_content_for_manual_copy = $config_php_content;
                $show_manual_config_confirm_button = true;
            } else {
                // Config file written automatically OR user confirmed manual creation
                if ($action === 'confirm_manual_config' && !file_exists('../config.php')) {
                     $finalize_messages[] = ['type' => 'error', 'text' => 'You confirmed manual creation, but <code>../config.php</code> was not found or is empty. Please create it or try the automatic method again if permissions allow.'];
                     $config_content_for_manual_copy = $config_php_content; // Show content again
                     $show_manual_config_confirm_button = true;
                } else {
                    // Config.php is now expected to be in place.
                    $finalize_messages[] = ['type' => 'info', 'text' => 'Configuration file is ready. Proceeding with database setup...'];

                    $pdo_setup = null;
                    $db_operation_failed = false;
                    try {
                        // Use the session's DB config for these final setup steps
                        $db_c = $_SESSION['setup_db_config'];
                        $dsn = "mysql:host=" . $db_c['host'] . ";dbname=" . $db_c['dbname'] . ";charset=utf8mb4";
                        $options = [
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES   => false,
                        ];
                        $pdo_setup = new PDO($dsn, $db_c['username'], $db_c['password'], $options);
                    } catch (PDOException $e) {
                        $finalize_messages[] = ['type' => 'error', 'text' => 'Failed to connect to the database using the provided credentials for final setup: ' . htmlspecialchars($e->getMessage())];
                        $db_operation_failed = true;
                        // To allow user to retry or see config, keep showing manual config options if they were active before this attempt
                        if ($action === 'confirm_manual_config' || !empty($config_content_for_manual_copy) ) {
                            $config_content_for_manual_copy = $config_php_content;
                            $show_manual_config_confirm_button = true;
                        }
                    }

                    if ($pdo_setup && !$db_operation_failed) {
                        // Step 1: Create Database Tables
                        $tables_result = $setupHelper->createDatabaseTables($pdo_setup);
                        if ($tables_result !== true) {
                            $finalize_messages[] = ['type' => 'error', 'text' => 'Failed to create database tables: ' . $tables_result];
                            $db_operation_failed = true;
                        } else {
                            $finalize_messages[] = ['type' => 'success', 'text' => 'Database tables created or verified successfully.'];

                            // Step 2: Create Admin User
                            if (isset($_SESSION['setup_admin_details'])) {
                                $admin_details = $_SESSION['setup_admin_details'];
                                $admin_creation_result = $setupHelper->createAdminUser($pdo_setup, $admin_details);

                                if ($admin_creation_result !== true) {
                                    $finalize_messages[] = ['type' => 'error', 'text' => 'Failed to create admin user: ' . $admin_creation_result];
                                    $db_operation_failed = true;
                                } else {
                                    $finalize_messages[] = ['type' => 'success', 'text' => 'Admin user <strong>' . htmlspecialchars($admin_details['username']) . '</strong> created successfully!'];
                                    $finalize_messages[] = ['type' => 'success', 'text' => '<strong>Mindust CMS Installation Complete!</strong>'];

                                    $_SESSION['setup_completed'] = true;

                                    if ($setupHelper->createLockFile()) {
                                        $finalize_messages[] = ['type' => 'info', 'text' => 'Installation lock file created successfully.'];
                                    } else {
                                        $finalize_messages[] = ['type' => 'error', 'text' => '<strong>CRITICAL WARNING:</strong> Failed to create the installation lock file (<code>setup/installed.lock</code>). This is a security risk. Please ensure the <code>setup/</code> directory is writable by the web server or delete the <code>setup/</code> directory manually immediately.'];
                                    }

                                    // Clear sensitive session data
                                    unset($_SESSION['setup_db_config']);
                                    unset($_SESSION['setup_admin_details']);
                                    // $_SESSION['setup_step'] could be unset too, or kept to show "completed" view.
                                    // Let's keep setup_step and setup_completed for now to ensure final view is shown.

                                    $finalize_messages[] = ['type' => 'warning', 'text' => '<strong>IMPORTANT SECURITY STEP:</strong> For security reasons, you MUST DELETE the entire <code>setup/</code> directory from your server NOW.'];
                                    $finalize_messages[] = ['type' => 'info', 'text' => '<a href="../admin/admin_login.php" class="button">Go to Admin Login</a> &nbsp; <a href="../index.php" class="button">View Your New Site</a>'];

                                    // Hide manual config button if everything succeeded up to this point
                                    $show_manual_config_confirm_button = false;
                                    $config_content_for_manual_copy = null;
                                }
                            } else {
                                $finalize_messages[] = ['type' => 'error', 'text' => 'Admin details were not found in session. Cannot create admin user.'];
                                $db_operation_failed = true;
                            }
                        }
                    }
                    // If any DB operation failed, and we were in manual config mode or about to show it, ensure options remain.
                    if ($db_operation_failed && ($action === 'confirm_manual_config' || !empty($config_content_for_manual_copy) )) {
                         if(empty($config_content_for_manual_copy)) $config_content_for_manual_copy = $config_php_content;
                         $show_manual_config_confirm_button = true;
                    }
                }
            }
        } else {
             // Config file already exists and contains the setup marker.
             $finalize_messages[] = ['type' => 'info', 'text' => 'Mindust CMS appears to be already configured (<code>config.php</code> found with setup marker).'];
             $finalize_messages[] = ['type' => 'warning', 'text' => '<strong>IMPORTANT SECURITY STEP:</strong> If installation is complete and your site is working, please DELETE the entire <code>setup/</code> directory from your server NOW.'];
             $finalize_messages[] = ['type' => 'info', 'text' => '<a href="../admin/admin_login.php" class="button">Go to Admin Login</a> &nbsp; <a href="../index.php" class="button">View Your Site</a>'];
             $_SESSION['setup_completed'] = true; // Also mark as completed here
        }
    }
}


// --- View Loading ---
switch ($current_step) {
    case 1:
        // Pass $error_message to the view
        include 'view_db_config.php';
        break;
    case 2:
        include 'view_admin_creation.php';
        break;
    case 3:
        // Pass messages to view_finalize.php
        include 'view_finalize.php';
        break;
    case 0:
    default:
        $prerequisites = $setupHelper->checkPrerequisites();
        $all_critical_prerequisites_ok = true;
        foreach ($prerequisites as $key => $check) {
            // Define which checks are critical for proceeding
            $is_critical = in_array($key, ['pdo_mysql', 'config_example_exists']); // Add more if needed
            if ($is_critical && !$check['status']) {
                $all_critical_prerequisites_ok = false;
            }
        }
        // Pass $prerequisites and $all_critical_prerequisites_ok to the view
        include 'view_welcome.php';
        break;
}
?>
