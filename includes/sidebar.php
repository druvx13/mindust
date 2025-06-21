<?php
// Expects $randomQuote to be defined before including this file.
// If not defined, provide a default or handle gracefully.
$randomQuote = $randomQuote ?? "The mind is a haunted house we all live in.";
?>
<!-- Sidebar -->
<aside class="lg:w-1/4 lg:pr-8 mb-8 lg:mb-0">
    <div class="bg-slate-900/50 rounded-xl p-6 sidebar sticky top-8 max-h-[90vh] overflow-y-auto">
        <!-- About the Writer -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4 text-indigo-300 title-font">About the Writer</h2>
            <div class="flex items-center mb-4">
                <div class="w-16 h-16 rounded-full bg-indigo-900/50 flex items-center justify-center">
                    <i class="fas fa-user-astronaut text-2xl text-indigo-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="font-bold">The Wanderer</h3>
                    <p class="text-sm text-gray-400">Night-thinker & Truth-seeker</p>
                </div>
            </div>
            <p class="text-gray-300 text-sm leading-relaxed">
                A collection of thoughts that escape during the witching hour.
                Not a guru, not a leader—just another lost soul mapping the darkness.
                These words are my rebellion against the sleepwalking world.
            </p>
        </div>
        <!-- Social Links -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4 text-indigo-300 title-font">Find Me</h2>
            <div class="flex space-x-4">
                <a href="#" class="w-10 h-10 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-300 hover:bg-indigo-800/50 hover:text-white transition">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-300 hover:bg-indigo-800/50 hover:text-white transition">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>
        <!-- Random Quote -->
        <div>
            <h2 class="text-xl font-bold mb-4 text-indigo-300 title-font">Tonight's Thought</h2>
            <div id="randomQuoteDisplay" class="bg-indigo-900/20 p-4 rounded-lg border border-indigo-900/30">
                <p class="text-gray-300 italic">"<?= htmlspecialchars($randomQuote) ?>"</p>
            </div>
        </div>
    </div>
</aside>
