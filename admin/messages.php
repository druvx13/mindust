<?php
session_start();
include '../config.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all messages
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

$page_title = 'Manage Messages';
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
                    <li><a href="messages.php" class="block py-2 px-4 rounded hover:bg-slate-700 bg-slate-700">Messages</a></li>
                    <li><a href="logout.php" class="block py-2 px-4 rounded hover:bg-slate-700">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="flex-1 p-8 overflow-auto">
            <h2 class="text-3xl font-bold mb-4">Manage Messages</h2>
            <div class="bg-slate-800 rounded-lg shadow-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-400 uppercase bg-slate-700">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Message</th>
                            <th scope="col" class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">No messages found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <tr class="border-b border-slate-700">
                                    <td class="px-6 py-4 font-medium"><?= htmlspecialchars($message['name']) ?></td>
                                    <td class="px-6 py-4"><a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="text-indigo-400 hover:underline"><?= htmlspecialchars($message['email']) ?></a></td>
                                    <td class="px-6 py-4"><?= nl2br(htmlspecialchars($message['message'])) ?></td>
                                    <td class="px-6 py-4"><?= date("M d, Y", strtotime($message['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
