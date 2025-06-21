<?php
include 'config.php';
require_once 'includes/csrf_helper.php'; // Include CSRF helper

// Fetch post data
$post_id = $_GET['id'] ?? null;
if (!$post_id || !filter_var($post_id, FILTER_VALIDATE_INT)) {
    // Redirect to homepage or an error page if ID is missing or invalid
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    // Post not found, redirect or show a 404 message
    header("Location: index.php"); // Or a custom 404 page
    exit();
}

$error = null; // Initialize error variable

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = "Invalid CSRF token. Please try again.";
    } else {
        $author = trim($_POST['author'] ?? '');
        $email = trim($_POST['email'] ?? ''); // Email is collected but not displayed by default, consider privacy.
    $content = trim($_POST['content'] ?? '');
    
    if (empty($author) || empty($content)) {
        $error = "Name and comment are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) { // Email is optional, but if provided, validate it.
        $error = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, author, email, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$post_id, $author, $email, $content]);
            header("Location: post.php?id={$post_id}#comments-section"); // Refresh to show new comment and jump to comments
            exit();
        } catch (PDOException $e) {
            // Log error $e->getMessage();
            $error = "Could not submit comment. Please try again later.";
        }
    }
} // This closes the main if POST && isset submit_comment
} // This closes the else for CSRF token verification

// Fetch comments for the current post
$stmt_comments = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
$stmt_comments->execute([$post_id]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

$page_title = htmlspecialchars($post['title']) . ' | Mind Dust';
$csrf_token = generate_csrf_token(); // Generate CSRF token for the comment form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body class="antialiased">
    <?php include 'includes/music_toggle.php'; ?>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/mobile_menu.php'; ?>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <main class="max-w-4xl mx-auto">
            <!-- Post Display -->
            <article class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 mb-12">
                <div class="h-64 md:h-96 bg-indigo-900/30 flex items-center justify-center">
                    <img src="uploads/<?=htmlspecialchars($post['thumbnail'] ?: 'default.jpg')?>" alt="<?=htmlspecialchars($post['title'])?>" class="w-full h-full object-cover">
                </div>
                <div class="p-6 md:p-8">
                    <div class="flex items-center text-xs text-gray-500 mb-2">
                        <span><?=date("M d, Y", strtotime($post['created_at']))?></span>
                        <span class="mx-2">•</span>
                        <span><?=htmlspecialchars($post['category'])?></span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-6 title-font"><?=htmlspecialchars($post['title'])?></h1>
                    <div id="post-content" class="markdown-content prose prose-invert max-w-none">
                        <?=htmlspecialchars($post['content']) /* Escaped for security, JS will parse Markdown */?>
                    </div>
                </div>
            </article>

            <!-- Comments Section -->
            <section id="comments-section" class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 p-6 md:p-8 mb-12">
                <h2 class="text-2xl font-bold mb-6">Comments (<?=count($comments)?>)</h2>

                <!-- Comment Form -->
                <form method="POST" action="post.php?id=<?=$post_id?>#comments-section" class="mb-8 space-y-4">
                    <?php csrf_input_field(); ?>
                    <?php if ($error): ?>
                        <div class="bg-red-700/50 border border-red-900/70 text-red-300 p-3 rounded-lg mb-4 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i><?=htmlspecialchars($error)?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <label for="author" class="block text-sm font-medium mb-1 text-gray-300">Name</label>
                        <input type="text" id="author" name="author" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500" required placeholder="Your name">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1 text-gray-300">Email (Optional, not displayed)</label>
                        <input type="email" id="email" name="email" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500" placeholder="your@email.com">
                    </div>
                    <div>
                        <label for="content" class="block text-sm font-medium mb-1 text-gray-300">Comment</label>
                        <textarea id="content" name="content" rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500" required placeholder="Share your thoughts..."></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">Submit Comment</button>
                </form>

                <!-- Display Existing Comments -->
                <?php if (empty($comments)): ?>
                    <p class="text-gray-400">Be the first to comment on this post!</p>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($comments as $comment): ?>
                            <div class="p-4 bg-slate-800/70 rounded-lg border border-slate-700/50">
                                <div class="flex items-center mb-2">
                                    <span class="text-indigo-400 font-bold text-sm"><?=htmlspecialchars($comment['author'])?></span>
                                    <span class="mx-2 text-gray-500">•</span>
                                    <span class="text-xs text-gray-500"><?=date("M d, Y, H:i", strtotime($comment['created_at']))?></span>
                                </div>
                                <p class="text-gray-300 text-sm leading-relaxed"><?=nl2br(htmlspecialchars($comment['content']))?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>