<?php
session_start();
require_once '../config.php';
require_once '../includes/csrf_helper.php'; // Include CSRF helper

// If already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error_message = "Invalid or missing CSRF token. Please try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error_message = "Username and password are required.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id']       = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    session_regenerate_id(true);
                    header("Location: admin_dashboard.php");
                    exit;
                } else {
                    $error_message = "Invalid username or password.";
                }
            } catch (PDOException $e) {
                error_log("Admin Login Error: " . $e->getMessage());
                $error_message = "An error occurred. Please try again later.";
            }
        }
    }
}  // ← Added this closing brace to match the POST handler

$page_title_for_head = "Admin Login | Mind Dust";
$csrf_token = generate_csrf_token(); // Generate CSRF token for the form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/head.php'; ?>
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: var(--primary); }
        .login-container { background-color: var(--secondary); padding: 2rem; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); width: 100%; max-width: 400px; }
        .form-input { background-color: var(--primary); border: 1px solid var(--accent); color: var(--text); }
        .submit-btn { background-color: var(--accent); }
        .submit-btn:hover { opacity: 0.9; }
    </style>
</head>
<body class="antialiased">
    <div class="login-container">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold title-font glitch" data-text="Admin Login">Admin Login</h1>
            <p class="text-sm text-gray-400">Mindust CMS</p>
        </div>
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-700/50 border border-red-900/70 text-red-300 p-3 rounded-lg mb-4 text-sm text-center">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="admin_login.php" class="space-y-6">
            <?php csrf_input_field(); ?>
            <div>
                <label for="username" class="block text-sm font-medium mb-1 text-gray-300">Username</label>
                <input type="text" id="username" name="username" required class="w-full form-input rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500" placeholder="Enter your username">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium mb-1 text-gray-300">Password</label>
                <input type="password" id="password" name="password" required class="w-full form-input rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500" placeholder="Enter your password">
            </div>
            <div>
                <button type="submit" class="w-full submit-btn text-white px-6 py-3 rounded-lg transition-opacity duration-200 font-semibold">Login</button>
            </div>
        </form>
        <p class="text-center mt-6 text-sm">
            <a href="../index.php" class="text-indigo-400 hover:text-indigo-300 transition">
                &larr; Back to site
            </a>
        </p>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>
