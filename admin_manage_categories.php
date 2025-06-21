<?php
session_start();
require_once 'config.php';
require_once 'includes/csrf_helper.php';

// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
// Optional: Role check for category management if needed in future

$action = $_GET['action'] ?? 'list'; // Default action
$page_title_for_head = "Manage Categories | Mindust CMS";

include 'includes/admin_header_inc.php';

// Display session messages
if (isset($_SESSION['admin_message'])) {
    $message_type = $_SESSION['admin_message_type'] ?? 'info';
    $alert_class = 'bg-blue-500 border-blue-700 text-blue-100';
    if ($message_type === 'success') $alert_class = 'bg-green-500 border-green-700 text-green-100';
    if ($message_type === 'error') $alert_class = 'bg-red-500 border-red-700 text-red-100';

    echo '<div class="container mx-auto my-4"><div class="' . $alert_class . ' border-l-4 p-4 rounded-md shadow-lg" role="alert">';
    echo '<p class="font-bold">' . ucfirst($message_type) . '</p><p>' . htmlspecialchars($_SESSION['admin_message']) . '</p></div></div>';
    unset($_SESSION['admin_message'], $_SESSION['admin_message_type']);
}

// Helper function to generate a slug from a name
function generate_slug(string $name): string {
    $slug = strtolower($name);
    $slug = preg_replace('/\s+/', '-', $slug); // Replace spaces with -
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // Remove special chars except -
    $slug = trim($slug, '-'); // Trim leading/trailing -
    return $slug ?: 'category'; // Fallback for empty slug
}
?>

<div class="admin-card">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-indigo-400">
            <?php
            if ($action === 'create') echo 'Create New Category';
            elseif ($action === 'edit') echo 'Edit Category';
            else echo 'Manage Categories';
            ?>
        </h2>
        <?php if ($action === 'list'): ?>
        <a href="admin_manage_categories.php?action=create" class="admin-button">
            <i class="fas fa-plus-circle mr-2"></i> Create New Category
        </a>
        <?php else: ?>
        <a href="admin_manage_categories.php" class="admin-button bg-gray-600 hover:bg-gray-700">
            <i class="fas fa-list-alt mr-2"></i> Back to Category List
        </a>
        <?php endif; ?>
    </div>

    <?php
    switch ($action) {
        case 'list':
        default:
            try {
                $stmt = $pdo->query("SELECT c.id, c.name, c.slug, c.created_at, COUNT(p.id) as post_count
                                     FROM categories c
                                     LEFT JOIN posts p ON LOWER(p.category) = LOWER(c.slug) -- Case-insensitive join for slug
                                     GROUP BY c.id, c.name, c.slug, c.created_at
                                     ORDER BY c.name ASC");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($categories)) {
                    echo "<p class='text-gray-400'>No categories found. <a href='admin_manage_categories.php?action=create' class='text-indigo-400 hover:underline'>Create one now!</a></p>";
                } else {
                    echo '<div class="overflow-x-auto shadow-md rounded-lg border border-slate-700">';
                    echo '<table class="admin-table min-w-full">';
                    echo '<thead class="bg-slate-800"><tr>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Slug</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Post Count</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Created At</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                          </tr></thead>';
                    echo '<tbody class="bg-slate-900/70 divide-y divide-slate-700">';
                    foreach ($categories as $category) {
                        echo '<tr class="hover:bg-slate-800/50 transition-colors">';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . htmlspecialchars($category['id']) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm font-medium text-gray-200">' . htmlspecialchars($category['name']) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . htmlspecialchars($category['slug']) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . htmlspecialchars($category['post_count']) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . date("M d, Y", strtotime($category['created_at'])) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm font-medium space-x-2 flex items-center">';
                        echo '<a href="admin_manage_categories.php?action=edit&id=' . $category['id'] . '" class="admin-button text-xs bg-blue-600 hover:bg-blue-700"><i class="fas fa-edit"></i> Edit</a>';

                        echo '<form method="POST" action="admin_manage_categories.php?action=delete_category" class="inline-block m-0 p-0" onsubmit="return confirm(\'Are you sure you want to delete category: ' . htmlspecialchars(addslashes($category['name'])) . '? This might affect posts in this category.\');">';
                        echo '<input type="hidden" name="category_id" value="' . $category['id'] . '">';
                        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generate_csrf_token(true)) . '">';
                        echo '<button type="submit" class="admin-button text-xs danger" ' . ($category['post_count'] > 0 ? 'disabled title="Cannot delete category with posts. Reassign posts first."' : '') . '><i class="fas fa-trash"></i> Delete</button>';
                        echo '</form>';
                        echo '</td></tr>';
                    }
                    echo '</tbody></table></div>';
                }
            } catch (PDOException $e) {
                error_log("Error fetching categories: " . $e->getMessage());
                echo "<p class='text-red-400 p-4 bg-red-900/50 rounded-md'>Error fetching categories. Check logs.</p>";
            }
            break;

        case 'create':
            $csrf_token = generate_csrf_token();
            $form_errors = $_SESSION['form_errors'] ?? [];
            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_errors'], $_SESSION['form_data']);

            if (!empty($form_errors)) {
                echo '<div class="my-4 p-4 bg-red-700/50 border border-red-900/70 text-red-200 rounded-lg"><p class="font-bold mb-2">Please correct errors:</p><ul class="list-disc list-inside">';
                foreach ($form_errors as $error) echo '<li>' . htmlspecialchars($error) . '</li>';
                echo '</ul></div>';
            }
            ?>
            <form action="admin_manage_categories.php?action=store_category" method="POST" class="space-y-6">
                <?php csrf_input_field(); ?>
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-300 mb-1">Category Name</label>
                    <input type="text" id="category_name" name="category_name" required class="admin-form-input" value="<?= htmlspecialchars($form_data['category_name'] ?? '') ?>" maxlength="100">
                    <p class="text-xs text-gray-500 mt-1">A unique slug will be automatically generated from this name.</p>
                </div>
                <div>
                    <button type="submit" class="admin-button text-lg px-8 py-3"><i class="fas fa-plus-circle mr-2"></i>Create Category</button>
                </div>
            </form>
            <?php
            break;

        case 'store_category':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: admin_manage_categories.php");
                exit;
            }
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. Category creation aborted.";
                $_SESSION['admin_message_type'] = "error";
                $_SESSION['form_data'] = $_POST;
                header("Location: admin_manage_categories.php?action=create");
                exit;
            }

            $errors = [];
            $category_name = trim($_POST['category_name'] ?? '');

            if (empty($category_name) || strlen($category_name) > 100) {
                $errors[] = "Category name is required and must be 1-100 characters.";
            }

            $slug = generate_slug($category_name);
            if (empty($slug)) {
                 $errors[] = "Could not generate a valid slug from the category name.";
            }

            if (empty($errors)) {
                try {
                    // Check if name or slug already exists
                    $stmt_check = $pdo->prepare("SELECT id FROM categories WHERE name = ? OR slug = ?");
                    $stmt_check->execute([$category_name, $slug]);
                    if ($stmt_check->fetch()) {
                        $errors[] = "Category name or its generated slug already exists.";
                    } else {
                        $stmt_insert = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
                        $stmt_insert->execute([$category_name, $slug]);
                        $_SESSION['admin_message'] = "Category '" . htmlspecialchars($category_name) . "' created successfully.";
                        $_SESSION['admin_message_type'] = "success";
                        header("Location: admin_manage_categories.php");
                        exit;
                    }
                } catch (PDOException $e) {
                    error_log("Error creating category: " . $e->getMessage());
                    $errors[] = "Database error: Could not create category. Please try again.";
                }
            }

            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header("Location: admin_manage_categories.php?action=create");
            exit;
            break;

        case 'edit':
            $category_id = $_GET['id'] ?? null;
            if (!$category_id || !filter_var($category_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid category ID for editing.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_categories.php");
                exit;
            }

            $csrf_token = generate_csrf_token();
            $form_errors = $_SESSION['form_errors'] ?? [];
            $form_data_session = $_SESSION['form_data'] ?? null;
            unset($_SESSION['form_errors'], $_SESSION['form_data']);

            $category_data = [];
            if ($form_data_session && isset($form_data_session['category_id']) && $form_data_session['category_id'] == $category_id) {
                $category_data = $form_data_session; // Use session data if returning from failed update
                $category_data['name'] = $category_data['category_name'] ?? ($category_data['name'] ?? ''); // Map form field name
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT id, name, slug FROM categories WHERE id = ?");
                    $stmt->execute([$category_id]);
                    $category_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$category_data) {
                        $_SESSION['admin_message'] = "Category not found (ID: $category_id).";
                        $_SESSION['admin_message_type'] = "error";
                        header("Location: admin_manage_categories.php");
                        exit;
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching category for edit (ID: $category_id): " . $e->getMessage());
                    $_SESSION['admin_message'] = "Error fetching category details.";
                    $_SESSION['admin_message_type'] = "error";
                    header("Location: admin_manage_categories.php");
                    exit;
                }
            }

            if (!empty($form_errors)) {
                echo '<div class="my-4 p-4 bg-red-700/50 border border-red-900/70 text-red-200 rounded-lg"><p class="font-bold mb-2">Please correct errors:</p><ul class="list-disc list-inside">';
                foreach ($form_errors as $error) echo '<li>' . htmlspecialchars($error) . '</li>';
                echo '</ul></div>';
            }
            ?>
            <h3 class="text-xl font-semibold text-indigo-300 mb-4">Editing Category: "<?= htmlspecialchars($category_data['name']) ?>"</h3>
            <form action="admin_manage_categories.php?action=update_category" method="POST" class="space-y-6">
                <?php csrf_input_field(); ?>
                <input type="hidden" name="category_id" value="<?= (int)$category_data['id'] ?>">
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-300 mb-1">Category Name</label>
                    <input type="text" id="category_name" name="category_name" required class="admin-form-input" value="<?= htmlspecialchars($category_data['name'] ?? '') ?>" maxlength="100">
                    <p class="text-xs text-gray-500 mt-1">Slug (<code><?= htmlspecialchars($category_data['slug'] ?? '') ?></code>) will be regenerated if name changes significantly.</p>
                </div>
                <div>
                    <button type="submit" class="admin-button text-lg px-8 py-3 bg-green-600 hover:bg-green-700"><i class="fas fa-save mr-2"></i>Update Category</button>
                </div>
            </form>
            <?php
            break;

        case 'update_category':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: admin_manage_categories.php");
                exit;
            }
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. Category update aborted.";
                $_SESSION['admin_message_type'] = "error";
                $_SESSION['form_data'] = $_POST;
                header("Location: admin_manage_categories.php?action=edit&id=" . ($_POST['category_id'] ?? 0));
                exit;
            }

            $category_id = $_POST['category_id'] ?? null;
            if (!$category_id || !filter_var($category_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid category ID for update.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_categories.php");
                exit;
            }

            $errors = [];
            $category_name = trim($_POST['category_name'] ?? '');

            if (empty($category_name) || strlen($category_name) > 100) {
                $errors[] = "Category name is required and must be 1-100 characters.";
            }

            $new_slug = generate_slug($category_name);
            if (empty($new_slug)) {
                 $errors[] = "Could not generate a valid slug from the new category name.";
            }

            if (empty($errors)) {
                try {
                    // Check if new name or slug conflicts with another category
                    $stmt_check = $pdo->prepare("SELECT id FROM categories WHERE (name = ? OR slug = ?) AND id != ?");
                    $stmt_check->execute([$category_name, $new_slug, $category_id]);
                    if ($stmt_check->fetch()) {
                        $errors[] = "New category name or its generated slug already exists for another category.";
                    } else {
                        // Note: If slug changes, existing posts' category field (which stores slug) might need updating.
                        // For simplicity now, we are not automatically updating posts. This could be a future enhancement or require manual post updates.
                        // A more robust system might store category_id in posts table.
                        $stmt_update = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
                        $stmt_update->execute([$category_name, $new_slug, $category_id]);
                        $_SESSION['admin_message'] = "Category '" . htmlspecialchars($category_name) . "' updated successfully.";
                        $_SESSION['admin_message_type'] = "success";
                        header("Location: admin_manage_categories.php");
                        exit;
                    }
                } catch (PDOException $e) {
                    error_log("Error updating category (ID: $category_id): " . $e->getMessage());
                    $errors[] = "Database error: Could not update category. Please try again.";
                }
            }

            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Includes category_id, category_name
            header("Location: admin_manage_categories.php?action=edit&id=" . $category_id);
            exit;
            break;

        case 'delete_category':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['admin_message'] = "Invalid request method for delete category action.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_categories.php");
                exit;
            }
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. Category deletion aborted.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_categories.php");
                exit;
            }

            $category_id = $_POST['category_id'] ?? null;
            if (!$category_id || !filter_var($category_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid category ID for deletion.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_categories.php");
                exit;
            }

            try {
                // Check if any posts are using this category (by slug, as posts.category stores the slug)
                $stmt_slug = $pdo->prepare("SELECT slug FROM categories WHERE id = ?");
                $stmt_slug->execute([$category_id]);
                $category_slug = $stmt_slug->fetchColumn();

                if ($category_slug) {
                    $stmt_posts = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE LOWER(category) = LOWER(?)");
                    $stmt_posts->execute([$category_slug]);
                    $post_count = $stmt_posts->fetchColumn();

                    if ($post_count > 0) {
                        $_SESSION['admin_message'] = "Cannot delete category: '" . htmlspecialchars($category_slug) . "' as it is currently used by $post_count post(s). Please reassign posts to other categories first.";
                        $_SESSION['admin_message_type'] = "error";
                        header("Location: admin_manage_categories.php");
                        exit;
                    }
                } else {
                     $_SESSION['admin_message'] = "Category not found for deletion (ID: $category_id).";
                     $_SESSION['admin_message_type'] = "error";
                     header("Location: admin_manage_categories.php");
                     exit;
                }

                // Proceed with deletion if no posts are using it
                $stmt_delete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmt_delete->execute([$category_id]);

                if ($stmt_delete->rowCount() > 0) {
                    $_SESSION['admin_message'] = "Category (ID: $category_id, Slug: $category_slug) deleted successfully.";
                    $_SESSION['admin_message_type'] = "success";
                } else {
                    // This case might be redundant if the slug check above already exited for not found.
                    $_SESSION['admin_message'] = "Category (ID: $category_id) not found or already deleted.";
                    $_SESSION['admin_message_type'] = "warning";
                }
            } catch (PDOException $e) {
                error_log("Error deleting category (ID: $category_id): " . $e->getMessage());
                $_SESSION['admin_message'] = "Database error: Could not delete category.";
                $_SESSION['admin_message_type'] = "error";
            }
            header("Location: admin_manage_categories.php");
            exit;
            break;
    }
    ?>
</div>

<?php
include 'includes/admin_footer_inc.php';
?>
