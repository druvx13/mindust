<?php
session_start();
include '../config.php';

// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$page_title = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> | Mind Dust</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-900 text-white flex items-center justify-center h-screen">
    <div class="w-full max-w-md">
        <form method="POST" action="login.php" class="bg-slate-800 p-8 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold mb-6 text-center">Admin Login</h1>
            <?php if ($error): ?>
                <div class="bg-red-700/50 border border-red-900/70 text-red-300 p-3 rounded-lg mb-4 text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium mb-1">Username</label>
                <input type="text" id="username" name="username" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">Login</button>
        </form>
    </div>
</body>
</html>
