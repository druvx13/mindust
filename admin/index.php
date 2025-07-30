<?php
session_start();
include '../config.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> | Mind Dust</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-900 text-white">
    <div class="flex h-screen">
        <div class="w-64 bg-slate-800 p-4">
            <h1 class="text-2xl font-bold mb-4">Admin</h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="block py-2 px-4 rounded hover:bg-slate-700">Dashboard</a></li>
                    <li><a href="comments.php" class="block py-2 px-4 rounded hover:bg-slate-700">Comments</a></li>
                    <li><a href="messages.php" class="block py-2 px-4 rounded hover:bg-slate-700">Messages</a></li>
                    <li><a href="logout.php" class="block py-2 px-4 rounded hover:bg-slate-700">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-4">Welcome to the Admin Dashboard</h2>
            <p>Select a section from the sidebar to manage comments or messages.</p>
        </div>
    </div>
</body>
</html>
