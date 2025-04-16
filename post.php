<?php
include 'config.php';

// Fetch post data
$post_id = $_GET['id'] ?? 1;
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author = $_POST['author'];
    $email = $_POST['email'];
    $content = $_POST['content'];
    
    // Basic validation
    if (empty($author) || empty($content)) {
        $error = "Name and comment are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, author, email, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$post_id, $author, $email, $content]);
        header("Location: post.php?id={$post_id}"); // Refresh to show new comment
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	
    <!-- Full copy of head section from index.php -->
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?=htmlspecialchars($post['title'])?> | Mind Dust</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Syne:wght@400;500;600;700&display=swap');
        :root {
            --primary: #0f172a;
            --secondary: #1e293b;
            --accent: #6366f1;
            --text: #e2e8f0;
            --text-secondary: #94a3b8;
        }
        body {
            font-family: 'Space Mono', monospace;
            background-color: var(--primary);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }
        .title-font {
            font-family: 'Syne', sans-serif;
        }
        .glitch {
            position: relative;
        }
        .glitch::before, .glitch::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .glitch::before {
            color: var(--accent);
            animation: glitch-effect 3s infinite;
            clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%);
        }
        .glitch::after {
            color: #f472b6;
            animation: glitch-effect 2s infinite reverse;
            clip-path: polygon(0 60%, 100% 60%, 100% 100%, 0 100%);
        }
        @keyframes glitch-effect {
            0% { transform: translate(0); }
            20% { transform: translate(-3px, 3px); }
            40% { transform: translate(-3px, -3px); }
            60% { transform: translate(3px, 3px); }
            80% { transform: translate(3px, -3px); }
            100% { transform: translate(0); }
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }
        .typewriter {
            overflow: hidden;
            border-right: 2px solid var(--accent);
            white-space: nowrap;
            margin: 0 auto;
            letter-spacing: 2px;
            animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
        }
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: var(--accent) }
        }
        .fade-in {
            animation: fadeIn 1.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: var(--accent) var(--secondary);
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: var(--secondary);
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: var(--accent);
            border-radius: 6px;
        }
        .modal {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .modal-enter {
            opacity: 0;
            transform: scale(0.9);
        }
        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
        }
        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }
        .modal-exit-active {
            opacity: 0;
            transform: scale(0.9);
        }
        .markdown-content h1 {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 1.5rem 0 1rem;
            color: var(--text);
        }
        .markdown-content h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1.3rem 0 0.8rem;
            color: var(--text);
        }
        .markdown-content p {
            margin: 0.8rem 0;
            line-height: 1.6;
        }
        .markdown-content a {
            color: var(--accent);
            text-decoration: underline;
        }
        .markdown-content ul, .markdown-content ol {
            margin: 0.8rem 0;
            padding-left: 1.5rem;
        }
        .markdown-content blockquote {
            border-left: 3px solid var(--accent);
            padding-left: 1rem;
            margin: 1rem 0;
            color: var(--text-secondary);
            font-style: italic;
        }
        .markdown-content code {
            background-color: var(--secondary);
            padding: 0.2rem 0.4rem;
            border-radius: 0.2rem;
            font-family: 'Space Mono', monospace;
        }
        .markdown-content pre {
            background-color: var(--secondary);
            padding: 1rem;
            border-radius: 0.4rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .markdown-content pre code {
            background-color: transparent;
            padding: 0;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Full copy of header, mobile menu, and sidebar from index.php -->
    <!-- Music Toggle -->
    <div class="fixed bottom-6 right-6 z-50">
        <button id="musicToggle" class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-full shadow-lg transition-all duration-300">
            <i class="fas fa-music"></i>
        </button>
        <audio id="ambientMusic" loop>
            <source src="music/Heavenly - Aakash Gandhi.mp3" type="audio/mpeg">
        </audio>
    </div>
    
    <!-- Header -->
    <header class="border-b border-indigo-900/20 py-6 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="glitch text-3xl font-bold title-font" data-text="Mind Dust">Mind Dust</span>
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="index.php" class="text-indigo-300 hover:text-white transition">Home</a>
                <a href="archive.php" class="text-gray-400 hover:text-white transition">Archive</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Themes</a>
                <a href="contact.php" class="text-gray-400 hover:text-white transition">Contact</a>
            </nav>
            <button id="mobileMenuButton" class="md:hidden text-gray-400 hover:text-white">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden fixed inset-0 bg-black/90 z-50 flex flex-col items-center justify-center space-y-8">
        <button id="closeMobileMenu" class="absolute top-6 right-6 text-gray-400 hover:text-white">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <a href="index.php" class="text-2xl text-indigo-300 hover:text-white transition">Home</a>
        <a href="archive.php" class="text-2xl text-gray-400 hover:text-white transition">Archive</a>
        <a href="#" class="text-2xl text-gray-400 hover:text-white transition">Themes</a>
        <a href="contact.php" class="text-2xl text-gray-400 hover:text-white transition">Contact</a>
    </div>
         <!-- Sidebar -->
     <aside class="lg:w-1/4 lg:pr-8 mb-8 lg:mb-0">
            <div class="bg-slate-900/50 rounded-xl p-6 sidebar sticky top-8 max-h-[90vh] overflow-y-auto">
 <main class="lg:w-3/4">
    <!-- Existing post content (keep this part) -->
    <div class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 mb-12">
        <div class="md:flex">
            <div class="md:w-1/2 h-64 md:h-auto bg-indigo-900/30 flex items-center justify-center">
                <img src="uploads/<?=$post['thumbnail']?>" alt="Thumbnail" class="w-full h-full object-cover">
            </div>
            <div class="md:w-1/2 p-8">
                <div class="flex items-center text-xs text-gray-500 mb-2">
                    <span><?=$post['created_at']?></span>
                    <span class="mx-2">•</span>
                    <span><?=$post['category']?></span>
                </div>
                <h2 class="text-2xl font-bold mb-4"><?=htmlspecialchars($post['title'])?></h2>
                <!-- Render post content with marked.js -->
                <div id="post-content" class="markdown-content">
                    <?=htmlspecialchars($post['content'])?>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const content = document.getElementById('post-content');
                        content.innerHTML = marked.parse(content.textContent);
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- NEW COMMENT SECTION START -->
    <div class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 p-6 mb-12">
        <h2 class="text-2xl font-bold mb-6">Comments</h2>

        <!-- Comment Form -->
        <form method="POST" class="mb-6 space-y-4">
            <?php if (isset($error)): ?>
                <div class="bg-red-500 text-white p-2 rounded-lg mb-4"><?=$error?></div>
            <?php endif; ?>
            <div>
                <label for="author" class="block text-sm font-medium mb-2">Name</label>
                <input type="text" name="author" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="content" class="block text-sm font-medium mb-2">Comment</label>
                <textarea name="content" rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">Submit Comment</button>
        </form>

        <!-- Display Existing Comments -->
        <?php
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
        $stmt->execute([$post_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if (count($comments) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($comments as $comment): ?>
                    <div class="p-4 bg-slate-800 rounded-lg">
                        <div class="flex items-center mb-2">
                            <span class="text-indigo-400 font-bold"><?=$comment['author']?></span>
                            <span class="mx-2">•</span>
                            <span><?=$comment['created_at']?></span>
                        </div>
                        <p><?=$comment['content']?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-400">Be the first to comment!</p>
        <?php endif; ?>
    </div>
    <!-- NEW COMMENT SECTION END -->
</main>

    
    <!-- Full copy of footer and scripts from index.php -->
    	    <!-- Footer -->
    <footer class="border-t border-indigo-900/20 py-8 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto text-center">
            <p class="text-gray-500 text-sm">© 2023 Mind Dust. All thoughts are fragments of a wandering mind.</p>
            <p class="text-gray-600 text-xs mt-2">Made with insomnia and existential dread.</p>
        </div>
    </footer>
    
    <script>
    	document.addEventListener('DOMContentLoaded', function() {
            const markdownContent = document.getElementById('markdown-content');
            const html = marked.parse(markdownContent.textContent);
            markdownContent.innerHTML = html;
        });
        
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });
        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
        
        // Music Toggle
        const musicToggle = document.getElementById('musicToggle');
        const ambientMusic = document.getElementById('ambientMusic');
        let musicPlaying = false;
        musicToggle.addEventListener('click', () => {
            if (musicPlaying) {
                ambientMusic.pause();
                musicToggle.innerHTML = '<i class="fas fa-music"></i>';
                musicToggle.classList.remove('bg-indigo-700');
                musicToggle.classList.add('bg-indigo-600');
            } else {
                ambientMusic.play();
                musicToggle.innerHTML = '<i class="fas fa-pause"></i>';
                musicToggle.classList.remove('bg-indigo-600');
                musicToggle.classList.add('bg-indigo-700');
            }
            musicPlaying = !musicPlaying;
        });
        
        // Random Quotes
        const quotes = [
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
        const randomQuoteElement = document.getElementById('randomQuote');
        function updateQuote() {
            const randomIndex = Math.floor(Math.random() * quotes.length);
            randomQuoteElement.innerHTML = `<p class="text-gray-300 italic">"${quotes[randomIndex]}"</p>`;
        }
        updateQuote();
        setInterval(updateQuote, 30000);
        
        // New Post Modal
        const newPostButton = document.getElementById('newPostButton');
        const newPostModal = document.getElementById('newPostModal');
        const closeModal = document.getElementById('closeModal');
        const cancelPost = document.getElementById('cancelPost');
        const postThumbnail = document.getElementById('postThumbnail');
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        
        newPostButton.addEventListener('click', () => {
            newPostModal.classList.remove('hidden');
        });
        closeModal.addEventListener('click', () => {
            newPostModal.classList.add('hidden');
        });
        cancelPost.addEventListener('click', () => {
            newPostModal.classList.add('hidden');
        });
        
        newPostModal.addEventListener('click', (e) => {
            if (e.target === newPostModal) {
                newPostModal.classList.add('hidden');
            }
        });
        
        document.getElementById('uploadThumbnail').addEventListener('click', () => {
            postThumbnail.click();
        });
        
        postThumbnail.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    thumbnailPreview.innerHTML = `<img src="${event.target.result}" class="w-full h-full object-cover rounded-lg" />`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>