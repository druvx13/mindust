<?php
include 'config.php';

// Fetch random quotes for initial display
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

// Fetch posts
$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Mind Dust | Late-Night Thoughts for Wanderers';
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
            <!-- Hero Section -->
            <div class="mb-12 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 title-font fade-in">Thoughts for the <span class="text-indigo-400">Awake</span> and <span class="text-indigo-400">Alone</span></h1>
            </div>
            <!-- Create New Post Button -->
            <div class="flex justify-end mb-6">
                <a href="admin_manage_posts.php?action=create" id="adminNewPostLink" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition">
                    <i class="fas fa-plus mr-2"></i> New Post (Admin)
                </a>
            </div>
            <!-- Blog Posts Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <?php if (empty($posts)): ?>
                    <p class="text-gray-400 col-span-full text-center">No posts yet. Be the first to create one!</p>
                <?php else: ?>
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
                                <h2 class="text-xl font-bold mb-2"><?=htmlspecialchars($post['title'])?></h2>
                                <p class="text-gray-400 mb-4 text-sm"><?=htmlspecialchars(substr(strip_tags($post['content']), 0, 100)) . '...'?></p>
                                <div class="flex justify-between items-center">
                                    <a href="post.php?id=<?=$post['id']?>" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Read More →</a>
                                    <!-- <span class="text-xs text-gray-500">5 min read</span> -->
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- Featured Post (Placeholder) -->
            <div class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 mb-12">
                <div class="md:flex">
                    <div class="md:w-1/2 h-64 md:h-auto bg-indigo-900/30 flex items-center justify-center">
                        <i class="fas fa-question text-7xl text-indigo-400 opacity-50"></i>
                    </div>
                    <div class="md:w-1/2 p-8">
                        <div class="flex items-center text-xs text-gray-500 mb-2">
                            <span>FEATURED</span>
                            <span class="mx-2">•</span>
                            <span>Deep Thoughts</span>
                        </div>
                        <h2 class="text-2xl font-bold mb-4">The Unseen Symphony</h2>
                        <p class="text-gray-400 mb-6">Beyond the noise of the everyday, there's a rhythm, a pulse. Are you listening? Explore the thoughts that echo in the quietest hours.</p>
                         </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>