<?php
include 'config.php';

// Fetch all posts
$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch a random quote for the sidebar
$quotes_array = [
    "We are all broken pieces trying to convince ourselves we're whole.",
    "The night doesn't answer questions—it just makes them louder.",
    "Your darkest thoughts are often your most honest ones.",
    "Rebellion starts when you stop agreeing with yourself.",
    "We're all just ghosts with beating hearts.",
    "The most dangerous prison is the one you don't know you're in.",
    "Truth is what remains when you stop believing everything else.",
    "You're not lost—you're just not where they told you to be.",
    "The mind is a haunted house we all live in.",
    "Sleep is just death being shy."
];
$randomQuote = $quotes_array[array_rand($quotes_array)];

$page_title = 'Archive | Mind Dust';
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
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row">
        <?php include 'includes/sidebar.php'; // $randomQuote is available here ?>

        <main class="lg:w-3/4">
            <h1 class="text-3xl md:text-4xl font-bold mb-8 title-font text-indigo-400">Post Archive</h1>

            <?php if (empty($posts)): ?>
                <p class="text-gray-400 text-center">No posts found in the archive.</p>
            <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <?php foreach ($posts as $post): ?>
                        <article class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 card-hover">
                            <div class="h-48 bg-indigo-900/30 flex items-center justify-center">
                                <img src="uploads/<?=htmlspecialchars($post['thumbnail'] ?: 'default.jpg')?>" alt="<?=htmlspecialchars($post['title'])?>" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6">
                                <div class="flex items-center text-xs text-gray-500 mb-2">
                                    <span><?=date("M d, Y", strtotime($post['created_at']))?></span>
                                    <span class="mx-2">•</span>
                                    <span><?=htmlspecialchars($post['category'])?></span>
                                </div>
                                <h2 class="text-xl font-bold mb-2">
                                    <a href="post.php?id=<?=$post['id']?>" class="hover:text-indigo-400 transition"><?=htmlspecialchars($post['title'])?></a>
                                </h2>
                                <p class="text-gray-400 mb-4 text-sm"><?=htmlspecialchars(substr(strip_tags($post['content']), 0, 100)) . '...'?></p>
                                <div class="flex justify-between items-center">
                                    <a href="post.php?id=<?=$post['id']?>" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Read More →</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
    <!-- Note: New Post Modal is not typically included on archive pages, so it's omitted here. -->
    <!-- The main.js includes logic for it, but it won't activate if the button/modal elements aren't present. -->
</body>
</html>