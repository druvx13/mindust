<?php
session_start();
require_once 'config.php'; // For $pdo
require_once 'includes/csrf_helper.php'; // Include CSRF helper

// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$action = $_GET['action'] ?? 'list'; // Default action is 'list'
$page_title_for_head = "Manage Posts | Mindust CMS";

// Include admin header
include 'includes/admin_header_inc.php';

// Display session messages (success/error)
if (isset($_SESSION['admin_message'])) {
    $message_type = $_SESSION['admin_message_type'] ?? 'info';
    $alert_class = 'bg-blue-600'; // Default info
    if ($message_type === 'success') $alert_class = 'bg-green-600';
    if ($message_type === 'error') $alert_class = 'bg-red-600';

    echo '<div class="container mx-auto mt-4 mb-0">';
    echo '<div class="' . $alert_class . ' text-white p-4 rounded-lg shadow-md">';
    echo htmlspecialchars($_SESSION['admin_message']);
    echo '</div>';
    echo '</div>';

    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
    // Clear form errors and data if they were set
    unset($_SESSION['form_errors']);
    unset($_SESSION['form_data']);
}

?>

<div class="admin-card">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-indigo-400">Manage Posts</h2>
        <?php if ($action !== 'create' && $action !== 'edit'): ?>
        <a href="admin_manage_posts.php?action=create" class="admin-button">
            <i class="fas fa-plus-circle mr-2"></i> Create New Post
        </a>
        <?php endif; ?>
    </div>

    <?php
    // Main action switch
    switch ($action) {
        case 'list':
        default:
            // List Posts
            try {
                $csrf_token_list_page = generate_csrf_token(); // For delete forms on this page
                $stmt = $pdo->query("SELECT id, title, category, created_at, thumbnail FROM posts ORDER BY created_at DESC");
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($posts)) {
                    echo "<p class='text-gray-400'>No posts found. <a href='admin_manage_posts.php?action=create' class='text-indigo-400 hover:underline'>Create one now!</a></p>";
                } else {
                    echo '<div class="overflow-x-auto">';
                    echo '<table class="admin-table min-w-full">';
                    echo '<thead><tr><th>Thumbnail</th><th>Title</th><th>Category</th><th>Created At</th><th>Actions</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($posts as $post) {
                        echo '<tr>';
                        echo '<td><img src="uploads/' . htmlspecialchars($post['thumbnail'] ?: 'default.jpg') . '" alt="' . htmlspecialchars($post['title']) . '" class="w-16 h-10 object-cover rounded"></td>';
                        echo '<td>' . htmlspecialchars($post['title']) . '</td>';
                        echo '<td>' . htmlspecialchars($post['category']) . '</td>';
                        echo '<td>' . date("M d, Y H:i", strtotime($post['created_at'])) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm font-medium space-x-2 flex items-center">'; // Added flex for button alignment
                        echo '<a href="post.php?id=' . $post['id'] . '" target="_blank" class="admin-button text-xs bg-green-600 hover:bg-green-700"><i class="fas fa-eye"></i> View</a>';
                        echo '<a href="admin_manage_posts.php?action=edit&id=' . $post['id'] . '" class="admin-button text-xs bg-blue-600 hover:bg-blue-700"><i class="fas fa-edit"></i> Edit</a>';
                        // Delete form
                        echo '<form method="POST" action="admin_manage_posts.php?action=delete" class="inline-block m-0 p-0" onsubmit="return confirm(\'Are you sure you want to delete this post: ' . htmlspecialchars(addslashes($post['title'])) . '?\');">';
                        echo '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
                        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token_list_page) . '">'; // Use the token generated for the list page
                        echo '<button type="submit" class="admin-button text-xs danger"><i class="fas fa-trash"></i> Delete</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>'; // overflow-x-auto
                }
            } catch (PDOException $e) {
                error_log("Error fetching posts for admin list: " . $e->getMessage());
                echo "<p class='text-red-400'>Error fetching posts. Please check the logs.</p>";
            }
            break;

        case 'create':
            // Display Create Post Form
            $csrf_token = generate_csrf_token(); // Generate token for create form
            $form_errors = $_SESSION['form_errors'] ?? [];
            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_errors'], $_SESSION['form_data']); // Clear after use

            if (!empty($form_errors)) {
                echo '<div class="mb-4 p-4 bg-red-700/50 border border-red-900/70 text-red-200 rounded-lg">';
                echo '<p class="font-bold mb-2">Please correct the following errors:</p>';
                echo '<ul class="list-disc list-inside">';
                foreach ($form_errors as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            ?>
            <form action="create_post.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="postTitle" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                    <input type="text" id="postTitle" name="postTitle" required class="admin-form-input" value="<?= htmlspecialchars($form_data['postTitle'] ?? '') ?>">
                </div>
                <div>
                    <label for="postCategory" class="block text-sm font-medium text-gray-300 mb-1">Category</label>
                    <select id="postCategory" name="postCategory" required class="admin-form-input">
                        <?php
                        try {
                            $stmt_cat = $pdo->query("SELECT slug, name FROM categories ORDER BY name ASC");
                            $available_categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($available_categories)) {
                                echo '<option value="" disabled selected>No categories available. Please create one first.</option>';
                            } else {
                                echo '<option value="" disabled ' . (!isset($form_data['postCategory']) || empty($form_data['postCategory']) ? 'selected' : '') . '>Select a category...</option>';
                                foreach ($available_categories as $cat_db) {
                                    $selected = (isset($form_data['postCategory']) && $form_data['postCategory'] == $cat_db['slug']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($cat_db['slug']) . '" ' . $selected . '>' . htmlspecialchars($cat_db['name']) . '</option>';
                                }
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching categories for post form: " . $e->getMessage());
                            echo '<option value="" disabled selected>Error loading categories.</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="postThumbnail" class="block text-sm font-medium text-gray-300 mb-1">Thumbnail</label>
                    <input type="file" id="postThumbnail" name="postThumbnail" accept="image/*" class="admin-form-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-indigo-50 hover:file:bg-indigo-600">
                    <p class="text-xs text-gray-500 mt-1">Max 2MB. Allowed: JPG, JPEG, PNG, GIF. Current: <span id="thumbnailFileName">None</span></p>
                     <!-- Simple preview, can be enhanced with JS -->
                    <img id="thumbnailPreview" src="#" alt="Thumbnail Preview" class="hidden mt-2 w-32 h-auto rounded-md border border-slate-600"/>

                </div>
                <div>
                    <label for="postContent" class="block text-sm font-medium text-gray-300 mb-1">Content (Markdown Supported)</label>
                    <textarea id="postContent" name="postContent" rows="10" required class="admin-form-input admin-form-textarea"><?= htmlspecialchars($form_data['postContent'] ?? '') ?></textarea>
                </div>
                <div>
                    <?php csrf_input_field(); ?>
                    <button type="submit" class="admin-button text-lg px-8 py-3">
                        <i class="fas fa-paper-plane mr-2"></i>Publish Post
                    </button>
                </div>
            </form>
            <script>
                // Basic thumbnail preview
                const postThumbnailInput = document.getElementById('postThumbnail');
                const thumbnailPreviewImg = document.getElementById('thumbnailPreview');
                const thumbnailFileNameSpan = document.getElementById('thumbnailFileName');

                if (postThumbnailInput && thumbnailPreviewImg && thumbnailFileNameSpan) {
                    postThumbnailInput.addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            thumbnailPreviewImg.src = URL.createObjectURL(file);
                            thumbnailPreviewImg.classList.remove('hidden');
                            thumbnailFileNameSpan.textContent = file.name;
                        } else {
                            thumbnailPreviewImg.src = "#";
                            thumbnailPreviewImg.classList.add('hidden');
                            thumbnailFileNameSpan.textContent = "None";
                        }
                    });
                }
            </script>
            <?php
            break;

        case 'edit':
            $csrf_token = generate_csrf_token(); // Generate token for edit form
            $post_id = $_GET['id'] ?? null;
            if (!$post_id || !filter_var($post_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid post ID for editing.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_posts.php");
                exit;
            }

            $form_errors = $_SESSION['form_errors'] ?? [];
            $form_data_from_session = $_SESSION['form_data'] ?? null;
            unset($_SESSION['form_errors'], $_SESSION['form_data']);

            $post_data_for_form = [];
            if ($form_data_from_session && isset($form_data_from_session['id']) && $form_data_from_session['id'] == $post_id) {
                $post_data_for_form = $form_data_from_session;
                $post_data_for_form['title'] = $post_data_for_form['postTitle'] ?? ($post_data_for_form['title'] ?? '');
                $post_data_for_form['category'] = $post_data_for_form['postCategory'] ?? ($post_data_for_form['category'] ?? '');
                $post_data_for_form['content'] = $post_data_for_form['postContent'] ?? ($post_data_for_form['content'] ?? '');
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
                    $stmt->execute([$post_id]);
                    $post_data_for_form = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$post_data_for_form) {
                        $_SESSION['admin_message'] = "Post not found (ID: $post_id).";
                        $_SESSION['admin_message_type'] = "error";
                        header("Location: admin_manage_posts.php");
                        exit;
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching post for edit (ID: $post_id): " . $e->getMessage());
                    $_SESSION['admin_message'] = "Error fetching post details.";
                    $_SESSION['admin_message_type'] = "error";
                    header("Location: admin_manage_posts.php");
                    exit;
                }
            }

            if (!empty($form_errors)) {
                echo '<div class="my-4 p-4 bg-red-700/50 border border-red-900/70 text-red-200 rounded-lg">';
                echo '<p class="font-bold mb-2">Please correct the following errors:</p>';
                echo '<ul class="list-disc list-inside">';
                foreach ($form_errors as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul></div>';
            }
            ?>
            <h3 class="text-xl font-semibold text-indigo-300 mb-4">Editing Post: "<?= htmlspecialchars($post_data_for_form['title']) ?>"</h3>
            <form action="admin_manage_posts.php?action=update&id=<?= (int)$post_id ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php csrf_input_field(); ?>
                <input type="hidden" name="post_id" value="<?= (int)$post_id ?>">
                <div>
                    <label for="postTitle" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                    <input type="text" id="postTitle" name="postTitle" required class="admin-form-input" value="<?= htmlspecialchars($post_data_for_form['title'] ?? '') ?>">
                </div>
                <div>
                    <label for="postCategory" class="block text-sm font-medium text-gray-300 mb-1">Category</label>
                    <select id="postCategory" name="postCategory" required class="admin-form-input">
                        <?php
                        try {
                            // $available_categories should already be fetched if create form was just used, but good to re-fetch or ensure it's in scope
                            // For edit form, it's safer to re-fetch to ensure fresh data.
                            $stmt_cat_edit = $pdo->query("SELECT slug, name FROM categories ORDER BY name ASC");
                            $available_categories_edit = $stmt_cat_edit->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($available_categories_edit)) {
                                echo '<option value="" disabled selected>No categories defined. Create one first.</option>';
                            } else {
                                 echo '<option value="" disabled ' . (!isset($post_data_for_form['category']) || empty($post_data_for_form['category']) ? 'selected' : '') . '>Select a category...</option>';
                                foreach ($available_categories_edit as $cat_db_edit) {
                                    $selected_edit = (isset($post_data_for_form['category']) && $post_data_for_form['category'] == $cat_db_edit['slug']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($cat_db_edit['slug']) . '" ' . $selected_edit . '>' . htmlspecialchars($cat_db_edit['name']) . '</option>';
                                }
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching categories for post edit form: " . $e->getMessage());
                            echo '<option value="" disabled selected>Error loading categories.</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Current Thumbnail</label>
                    <?php if (!empty($post_data_for_form['thumbnail']) && $post_data_for_form['thumbnail'] !== 'default.jpg'): ?>
                        <img src="uploads/<?= htmlspecialchars($post_data_for_form['thumbnail']) ?>" alt="Current Thumbnail" class="mb-2 w-40 h-auto rounded-md border border-slate-600 object-cover">
                        <p class="text-xs text-gray-400 mb-1">Filename: <?= htmlspecialchars($post_data_for_form['thumbnail']) ?></p>
                        <label class="inline-flex items-center text-xs text-gray-400 cursor-pointer">
                            <input type="checkbox" name="removeThumbnail" value="1" class="form-checkbox h-4 w-4 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-indigo-500 mr-1">
                            Remove current thumbnail
                        </label>
                    <?php elseif(!empty($post_data_for_form['thumbnail'])): ?>
                         <img src="uploads/<?= htmlspecialchars($post_data_for_form['thumbnail']) ?>" alt="Default Thumbnail" class="mb-2 w-40 h-auto rounded-md border border-slate-600 object-cover">
                         <p class="text-xs text-gray-400 mb-1">Current: default.jpg</p>
                    <?php else: ?>
                        <p class="text-xs text-gray-400 mb-1">No thumbnail set (will use default.jpg).</p>
                    <?php endif; ?>

                    <label for="postThumbnailNew" class="block text-sm font-medium text-gray-300 mb-1 mt-3">Upload New Thumbnail (Optional)</label>
                    <input type="file" id="postThumbnailNew" name="postThumbnailNew" accept="image/*" class="admin-form-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-indigo-50 hover:file:bg-indigo-600">
                    <p class="text-xs text-gray-500 mt-1">Max 2MB. Allowed: JPG, JPEG, PNG, GIF.</p>
                </div>
                <div>
                    <label for="postContent" class="block text-sm font-medium text-gray-300 mb-1">Content (Markdown Supported)</label>
                    <textarea id="postContent" name="postContent" rows="10" required class="admin-form-input admin-form-textarea"><?= htmlspecialchars($post_data_for_form['content'] ?? '') ?></textarea>
                </div>
                <div>
                    <button type="submit" class="admin-button text-lg px-8 py-3 bg-green-600 hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i>Update Post
                    </button>
                </div>
            </form>
            <?php
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: admin_manage_posts.php");
                exit;
            }
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. Post update aborted.";
                $_SESSION['admin_message_type'] = "error";
                // To repopulate form correctly, store submitted data and redirect back to edit form
                $_SESSION['form_errors'] = ['CSRF token validation failed.'];
                $_SESSION['form_data'] = $_POST; // Includes post_id, postTitle etc.
                header("Location: admin_manage_posts.php?action=edit&id=" . ($_POST['post_id'] ?? 0));
                exit;
            }

            $post_id = $_POST['post_id'] ?? null;
            if (!$post_id || !filter_var($post_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid post ID for update.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_posts.php");
                exit;
            }

            $errors = [];
            $title = trim($_POST['postTitle'] ?? '');
            $category = trim($_POST['postCategory'] ?? '');
            $content = trim($_POST['postContent'] ?? '');
            $removeThumbnail = isset($_POST['removeThumbnail']) && $_POST['removeThumbnail'] == '1';

            if (empty($title)) $errors[] = 'Post title is required.';
            if (empty($category)) $errors[] = 'Post category is required.';
            if (empty($content)) $errors[] = 'Post content is required.';

            $currentThumbnail = null;
            $targetDir = "uploads/";

            if (!is_dir($targetDir) || !is_writable($targetDir)) {
                $errors[] = "Uploads directory does not exist or is not writable. Please check server configuration.";
            }
            // Only proceed with DB call if uploads dir is fine, or make this check later before move_uploaded_file
            // For now, let's keep DB call and add check before file operations.

            try {
                $stmt_curr = $pdo->prepare("SELECT thumbnail FROM posts WHERE id = ?");
                $stmt_curr->execute([$post_id]);
                $currentThumbnail = $stmt_curr->fetchColumn();
                if($currentThumbnail === false) {
                     $_SESSION['admin_message'] = "Post not found for update (ID: $post_id).";
                     $_SESSION['admin_message_type'] = "error";
                     header("Location: admin_manage_posts.php");
                     exit;
                }
            } catch (PDOException $e) {
                error_log("Error fetching current thumbnail for update (ID: $post_id): " . $e->getMessage());
                $errors[] = "Could not verify current thumbnail. Update aborted for safety.";
            }

            $newThumbnailName = $currentThumbnail;

            if (isset($_FILES['postThumbnailNew']) && $_FILES['postThumbnailNew']['error'] === UPLOAD_ERR_OK) {
                $fileName = preg_replace("/[^a-zA-Z0-9._-]+/", "", basename($_FILES['postThumbnailNew']['name']));
                if (empty($fileName)) $fileName = bin2hex(random_bytes(8)) . ".jpg";
                $targetPath = $targetDir . $fileName;
                $imageFileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($imageFileType, $allowedExtensions)) {
                    $errors[] = 'Invalid new thumbnail file type.';
                } elseif ($_FILES['postThumbnailNew']['size'] > 2 * 1024 * 1024) {
                    $errors[] = 'New thumbnail file size exceeds 2MB limit.';
                } else {
                     if (strpos($fileName, '/') !== false || strpos($fileName, '\\') !== false || $fileName === '.' || $fileName === '..') {
                        $errors[] = 'Invalid new thumbnail filename.';
                    } else {
                        if (move_uploaded_file($_FILES['postThumbnailNew']['tmp_name'], $targetPath)) {
                            if ($currentThumbnail && $currentThumbnail !== 'default.jpg' && $currentThumbnail !== $fileName) {
                                if (file_exists($targetDir . $currentThumbnail)) { @unlink($targetDir . $currentThumbnail); }
                            }
                            $newThumbnailName = $fileName;
                        } else { $errors[] = 'Failed to upload new thumbnail.'; }
                    }
                }
            } elseif ($removeThumbnail) {
                if ($currentThumbnail && $currentThumbnail !== 'default.jpg') {
                    if (file_exists($targetDir . $currentThumbnail)) { @unlink($targetDir . $currentThumbnail); }
                }
                $newThumbnailName = 'default.jpg';
            }

            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, category = ?, thumbnail = ? WHERE id = ?");
                    $stmt->execute([$title, $content, $category, $newThumbnailName, $post_id]);
                    $_SESSION['admin_message'] = "Post '" . htmlspecialchars($title) . "' updated successfully!";
                    $_SESSION['admin_message_type'] = "success";
                    header("Location: admin_manage_posts.php");
                    exit();
                } catch (PDOException $e) {
                    error_log("Error updating post (ID: $post_id): " . $e->getMessage());
                    $errors[] = 'Database error: Could not update post.';
                }
            }

            if (!empty($errors)) {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = [
                    'id' => $post_id,
                    'postTitle' => $title,
                    'postCategory' => $category,
                    'postContent' => $content,
                    'thumbnail' => $currentThumbnail,
                ];
                header("Location: admin_manage_posts.php?action=edit&id=" . $post_id);
                exit();
            }
            break;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['admin_message'] = "Invalid request method for delete action.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_posts.php");
                exit;
            }

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. Delete action aborted.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_posts.php");
                exit;
            }

            $post_id = $_POST['post_id'] ?? null;
            if (!$post_id || !filter_var($post_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid post ID for deletion.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_posts.php");
                exit;
            }

            try {
                // First, get the thumbnail filename to delete it
                $stmt_thumb = $pdo->prepare("SELECT thumbnail FROM posts WHERE id = ?");
                $stmt_thumb->execute([$post_id]);
                $thumbnail_to_delete = $stmt_thumb->fetchColumn();

                // Delete the post
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->execute([$post_id]);

                if ($stmt->rowCount() > 0) {
                    // If post deleted successfully, try to delete its thumbnail
                    if ($thumbnail_to_delete && $thumbnail_to_delete !== 'default.jpg') {
                        $thumbnail_path = "uploads/" . $thumbnail_to_delete;
                        if (file_exists($thumbnail_path)) {
                            @unlink($thumbnail_path); // Suppress error if unlink fails, but ideally log it
                        }
                    }
                    // Also delete associated comments (this is handled by ON DELETE CASCADE if DB schema is set up correctly)
                    // If not using ON DELETE CASCADE, uncomment below:
                    // $stmt_comments = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
                    // $stmt_comments->execute([$post_id]);

                    $_SESSION['admin_message'] = "Post (ID: $post_id) and its associated thumbnail (if any) deleted successfully.";
                    $_SESSION['admin_message_type'] = "success";
                } else {
                    $_SESSION['admin_message'] = "Post (ID: $post_id) not found or already deleted.";
                    $_SESSION['admin_message_type'] = "warning";
                }
            } catch (PDOException $e) {
                error_log("Error deleting post (ID: $post_id): " . $e->getMessage());
                $_SESSION['admin_message'] = "Database error: Could not delete post.";
                $_SESSION['admin_message_type'] = "error";
            }
            header("Location: admin_manage_posts.php");
            exit;
            break;

    }
    ?>
</div>

<?php
// Include admin footer
include 'includes/admin_footer_inc.php';
?>
