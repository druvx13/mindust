<?php
// Include config and database connection (though not strictly needed for this static page, good practice)
include 'config.php';

$page_title = 'Legal Information | Mind Dust';
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
        <main class="max-w-3xl mx-auto">
            <div class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 p-6 md:p-8 mb-12 prose prose-invert max-w-none">
                <h1 class="text-3xl font-bold mb-6 title-font !text-indigo-400">Legal Information</h1>

                <h2 class="!text-indigo-300">Copyright Notice</h2>
                <p>All content published on <strong>Mind Dust</strong>, including but not limited to text, images, graphics, and multimedia (unless explicitly credited to third parties or falling under fair use), is the intellectual property of the site owners and its authors. All rights are reserved.</p>
                <ul>
                    <li>Blog posts and articles</li>
                    <li>Original artwork, photographs, and illustrations</li>
                    <li>Website design, themes, and visual concepts</li>
                </ul>

                <h2 class="!text-indigo-300">Exclusions & Third-Party Content</h2>
                <p>Certain content featured on this site may be sourced from third parties or used under specific licenses. This includes:</p>
                <ul>
                    <li>
                        <strong>Music:</strong> The ambient music track, typically <em>"Heavenly - Aakash Gandhi"</em> or similar, is used under license or as provided by services like YouTube Audio Library (or other royalty-free sources). Ownership remains with the original artists/composers. Please refer to the source for specific licensing terms if you wish to use such music.
                    </li>
                    <li>
                        <strong>Stock Images/Media:</strong> Any stock photography or media used will be appropriately licensed.
                    </li>
                     <li>
                        <strong>User-Generated Content:</strong> Comments and other user-submitted content are the property of their respective authors. By posting on Mind Dust, users grant us a worldwide, non-exclusive, royalty-free license to display and distribute their content on this platform.
                    </li>
                </ul>

                <h2 class="!text-indigo-300">Permitted Use</h2>
                <p>You are welcome to:</p>
                <ul>
                    <li>Share links to our articles and content on social media or other websites, provided proper attribution to Mind Dust is given.</li>
                    <li>Quote short excerpts from our content for non-commercial, educational, or review purposes, with clear attribution and a link back to the original source on Mind Dust.</li>
                </ul>

                <h2 class="!text-indigo-300">Prohibited Use</h2>
                <p>You may <strong>NOT</strong> without explicit written permission:</p>
                <ul>
                    <li>Republish, reproduce, or redistribute full articles or substantial portions of our content in any form.</li>
                    <li>Use our content for any commercial purposes.</li>
                    <li>Modify, adapt, or create derivative works from our original content.</li>
                    <li>Remove or obscure any copyright notices or author attributions.</li>
                    <li>Hotlink directly to images or other media files hosted on our server.</li>
                </ul>

                <h2 class="!text-indigo-300">Software and Code</h2>
                <p>The underlying PHP code, HTML, CSS, and JavaScript that powers Mind Dust may be based on open-source components or custom-developed. If this project is available on platforms like GitHub, its code is typically licensed under an open-source license (e.g., MIT, GPL). This license pertains to the software code itself and does <strong>NOT</strong> automatically grant rights to reuse the textual or visual content published on the live Mind Dust website.</p>

                <h2 class="!text-indigo-300">Disclaimer</h2>
                <p>The information provided on Mind Dust is for general informational and entertainment purposes only. While we strive for accuracy, we make no warranties regarding the completeness, reliability, or accuracy of this information. Any action you take upon the information on this website is strictly at your own risk.</p>

                <h2 class="!text-indigo-300">Contact for Permissions or Queries</h2>
                <p>For any requests to reuse content, or if you have questions regarding our copyright policy or terms of use, please reach out to us via the <a href="contact.php" class="text-indigo-400 hover:text-indigo-300 hover:underline">Contact Page</a>.</p>

                <p class="text-sm text-gray-500 mt-8">Last Updated: <?= date("F j, Y") ?></p>
            </div>
        </main>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>