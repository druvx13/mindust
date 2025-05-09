<?php
include 'config.php';
$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Full copy of head section from index.php -->
    
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Archive | Mind Dust</title>
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
                <a href="copyright.php" class="text-gray-400 hover:text-white transition">Legal</a>
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
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php foreach ($posts as $post): ?>
                <article class="card-hover">
                    <div class="h-48 bg-indigo-900/30 flex items-center justify-center">
                        <img src="uploads/<?=$post['thumbnail']?>" alt="Thumbnail" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-xs text-gray-500 mb-2">
                            <span><?=$post['created_at']?></span>
                            <span class="mx-2">•</span>
                            <span><?=$post['category']?></span>
                        </div>
                        <h2 class="text-xl font-bold mb-2"><a href="post.php?id=<?=$post['id']?>"><?=htmlspecialchars($post['title'])?></a></h2>
                        <div class="flex justify-between items-center">
                            <a href="post.php?id=<?=$post['id']?>" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Read More →</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
    
    <!-- Full copy of footer and scripts from index.php -->
    		    <!-- Footer -->
    <footer class="border-t border-indigo-900/20 py-8 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto text-center">
            <p class="text-gray-500 text-sm">© ∞ Mind Dust. All thoughts are fragments of a wandering mind.</p>
            <p class="text-gray-600 text-xs mt-2">Made with insomnia and existential dread.</p>
        </div>
    </footer>
    
    <script>
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