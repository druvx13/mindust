document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuButton && mobileMenu && closeMobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });
        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
    }

    // Music Toggle
    const musicToggle = document.getElementById('musicToggle');
    const ambientMusic = document.getElementById('ambientMusic');
    let musicPlaying = false;

    if (musicToggle && ambientMusic) {
        musicToggle.addEventListener('click', () => {
            if (musicPlaying) {
                ambientMusic.pause();
                musicToggle.innerHTML = '<i class="fas fa-music"></i>';
                musicToggle.classList.remove('bg-indigo-700');
                musicToggle.classList.add('bg-indigo-600');
            } else {
                ambientMusic.play().catch(error => console.error("Error playing music:", error));
                musicToggle.innerHTML = '<i class="fas fa-pause"></i>';
                musicToggle.classList.remove('bg-indigo-600');
                musicToggle.classList.add('bg-indigo-700');
            }
            musicPlaying = !musicPlaying;
        });
    }

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
    const randomQuoteDisplayElement = document.getElementById('randomQuoteDisplay');

    function updateQuote() {
        if (randomQuoteDisplayElement) {
            const randomIndex = Math.floor(Math.random() * quotes.length);
            // Ensure to escape HTML if quotes could contain it, though these are strings.
            randomQuoteDisplayElement.innerHTML = `<p class="text-gray-300 italic">"${quotes[randomIndex]}"</p>`;
        }
    }

    // Initial quote is set by PHP in sidebar.php, JS updates it periodically.
    if (randomQuoteDisplayElement) {
         setInterval(updateQuote, 30000); // Update every 30 seconds
    }


    // New Post Modal
    const newPostButton = document.getElementById('newPostButton');
    const newPostModal = document.getElementById('newPostModal');
    const closeModalButton = document.getElementById('closeModal'); // Corrected ID from before
    const cancelPostButton = document.getElementById('cancelPost'); // Corrected ID from before
    const postThumbnailInput = document.getElementById('postThumbnail');
    const thumbnailPreviewContainer = document.getElementById('thumbnailPreviewContainer');
    const uploadThumbnailButton = document.getElementById('uploadThumbnail');

    if (newPostButton && newPostModal && closeModalButton && cancelPostButton) {
        newPostButton.addEventListener('click', () => {
            newPostModal.classList.remove('hidden');
        });
        closeModalButton.addEventListener('click', () => {
            newPostModal.classList.add('hidden');
        });
        cancelPostButton.addEventListener('click', () => {
            newPostModal.classList.add('hidden');
        });
        newPostModal.addEventListener('click', (e) => {
            if (e.target === newPostModal) {
                newPostModal.classList.add('hidden');
            }
        });
    }

    if (uploadThumbnailButton && postThumbnailInput) {
        uploadThumbnailButton.addEventListener('click', () => {
            postThumbnailInput.click();
        });
    }

    if (postThumbnailInput && thumbnailPreviewContainer) {
        postThumbnailInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    thumbnailPreviewContainer.innerHTML = `<img src="${event.target.result}" class="w-full h-full object-cover rounded-lg" alt="Thumbnail preview"/>`;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset to default if no file is selected or selection is cleared
                thumbnailPreviewContainer.innerHTML = `<i class="fas fa-image text-gray-500"></i>`;
            }
        });
    }

    // Markdown rendering for post content on post.php
    const postContentDiv = document.getElementById('post-content');
    if (postContentDiv && typeof marked !== 'undefined') {
        const rawMarkdown = postContentDiv.textContent || postContentDiv.innerText;
        postContentDiv.innerHTML = marked.parse(rawMarkdown);
    }
});
