<!-- New Post Modal -->
<div id="newPostModal" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-800 flex justify-between items-center">
            <h3 class="text-xl font-bold">Create New Post</h3>
            <button id="closeModal" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="postForm" action="create_post.php" method="POST" enctype="multipart/form-data">
                <div class="mb-6">
                    <label for="postTitle" class="block text-sm font-medium mb-2">Title</label>
                    <input type="text" id="postTitle" name="postTitle" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="The thought that won't leave you alone..." required>
                </div>
                <div class="mb-6">
                    <label for="postCategory" class="block text-sm font-medium mb-2">Category</label>
                    <select id="postCategory" name="postCategory" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        <option value="psychology">Psychology</option>
                        <option value="philosophy">Philosophy</option>
                        <option value="rebellion">Rebellion</option>
                        <option value="existential">Existential</option>
                        <option value="spiritual">Spiritual</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="postThumbnail" class="block text-sm font-medium mb-2">Thumbnail</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-24 h-24 rounded-lg bg-slate-800 border border-dashed border-slate-700 flex items-center justify-center cursor-pointer" id="thumbnailPreviewContainer">
                            <i class="fas fa-image text-gray-500"></i>
                        </div>
                        <div>
                            <input type="file" id="postThumbnail" name="postThumbnail" class="hidden" accept="image/*">
                            <button type="button" id="uploadThumbnail" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm transition">Choose Image</button>
                            <p class="text-xs text-gray-500 mt-1">Recommended: 800x450px (16:9)</p>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <label for="postContent" class="block text-sm font-medium mb-2">Content (Markdown supported)</label>
                    <textarea id="postContent" name="postContent" rows="10" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Pour your thoughts here... Markdown formatting supported" required></textarea>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelPost" class="bg-slate-800 hover:bg-slate-700 text-white px-6 py-2 rounded-lg transition">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
