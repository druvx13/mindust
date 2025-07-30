<?php
session_start();
include '../config.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all comments with post titles
$stmt = $pdo->query("
    SELECT c.id, c.author, c.content, c.created_at, p.title as post_title, p.id as post_id
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
");
$comments = $stmt->fetchAll();

$page_title = 'Manage Comments';
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
                    <li><a href="comments.php" class="block py-2 px-4 rounded hover:bg-slate-700 bg-slate-700">Comments</a></li>
                    <li><a href="messages.php" class="block py-2 px-4 rounded hover:bg-slate-700">Messages</a></li>
                    <li><a href="logout.php" class="block py-2 px-4 rounded hover:bg-slate-700">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="flex-1 p-8 overflow-auto">
            <h2 class="text-3xl font-bold mb-4">Manage Comments</h2>
            <div class="bg-slate-800 rounded-lg shadow-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-400 uppercase bg-slate-700">
                        <tr>
                            <th scope="col" class="px-6 py-3">Author</th>
                            <th scope="col" class="px-6 py-3">Comment</th>
                            <th scope="col" class="px-6 py-3">In Response To</th>
                            <th scope="col" class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($comments)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">No comments found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <tr class="border-b border-slate-700">
                                    <td class="px-6 py-4 font-medium"><?= htmlspecialchars($comment['author']) ?></td>
                                    <td class="px-6 py-4"><?= nl2br(htmlspecialchars($comment['content'])) ?></td>
                                    <td class="px-6 py-4"><a href="../post.php?id=<?= $comment['post_id'] ?>" class="text-indigo-400 hover:underline" target="_blank"><?= htmlspecialchars($comment['post_title']) ?></a></td>
                                    <td class="px-6 py-4"><?= date("M d, Y", strtotime($comment['created_at'])) ?></td>
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
