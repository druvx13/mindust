<?php
session_start();
require_once '../config.php';
require_once '../includes/csrf_helper.php';

// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
// Optional: Add role check here if we want to restrict user management to super-admins later
// if ($_SESSION['admin_role'] !== 'super_admin') {
//     $_SESSION['admin_message'] = "You do not have permission to manage users.";
//     $_SESSION['admin_message_type'] = "error";
//     header("Location: admin_dashboard.php"); // This would also need ../ if used
//     exit;
// }

$action = $_GET['action'] ?? 'list'; // Default action
$page_title_for_head = "Manage Users | Mindust CMS";

include '../includes/admin_header_inc.php';

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
?>

<div class="admin-card">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-indigo-400">
            <?php
            if ($action === 'create') echo 'Create New Admin User';
            elseif ($action === 'edit') echo 'Edit Admin User';
            else echo 'Manage Admin Users';
            ?>
        </h2>
        <?php if ($action === 'list'): ?>
        <a href="admin_manage_users.php?action=create" class="admin-button">
            <i class="fas fa-user-plus mr-2"></i> Create New Admin
        </a>
        <?php else: ?>
        <a href="admin_manage_users.php" class="admin-button bg-gray-600 hover:bg-gray-700">
            <i class="fas fa-list-alt mr-2"></i> Back to User List
        </a>
        <?php endif; ?>
    </div>

    <?php
    switch ($action) {
        case 'list':
        default:
            try {
                $stmt = $pdo->query("SELECT id, username, email, role, is_active, created_at, updated_at FROM admins ORDER BY created_at DESC");
                $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($admins)) {
                    echo "<p class='text-gray-400'>No admin users found. <a href='admin_manage_users.php?action=create' class='text-indigo-400 hover:underline'>Create one now!</a></p>";
                } else {
                    echo '<div class="overflow-x-auto shadow-md rounded-lg border border-slate-700">';
                    echo '<table class="admin-table min-w-full">';
                    echo '<thead class="bg-slate-800"><tr>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Username</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Created At</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                          </tr></thead>';
                    echo '<tbody class="bg-slate-900/70 divide-y divide-slate-700">';
                    foreach ($admins as $admin) {
                        echo '<tr class="hover:bg-slate-800/50 transition-colors">';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . htmlspecialchars($admin['id']) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm font-medium text-gray-200">' . htmlspecialchars($admin['username']) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . htmlspecialchars($admin['email']) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . htmlspecialchars(ucfirst($admin['role'])) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm">';
                        echo $admin['is_active'] ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-700 text-green-100">Active</span>' : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-700 text-red-100">Inactive</span>';
                        echo '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm text-gray-400">' . date("M d, Y H:i", strtotime($admin['created_at'])) . '</td>';
                        echo '<td class="p-3 whitespace-nowrap text-sm font-medium space-x-2 flex items-center">';
                        // Edit link (to be implemented more fully)
                        // echo '<a href="admin_manage_users.php?action=edit&id=' . $admin['id'] . '" class="admin-button text-xs bg-blue-600 hover:bg-blue-700"><i class="fas fa-edit"></i> Edit</a>';

                        // Delete link (to be implemented, with safety checks)
                        // Cannot delete self, cannot delete last admin etc.
                        // if ($_SESSION['admin_id'] !== $admin['id']) { // Example: Prevent self-deletion
                        //    echo '<form method="POST" action="admin_manage_users.php?action=delete_user" class="inline-block m-0 p-0" onsubmit="return confirm(\'Are you sure you want to delete user: ' . htmlspecialchars(addslashes($admin['username'])) . '?\');">';
                        //    echo '<input type="hidden" name="user_id" value="' . $admin['id'] . '">';
                        //    echo '<input type="hidden" name="csrf_token" value="' . generate_csrf_token(true) . '">'; // Generate fresh token for delete
                        echo '<a href="admin_manage_users.php?action=edit&id=' . $admin['id'] . '" class="admin-button text-xs bg-blue-600 hover:bg-blue-700"><i class="fas fa-edit"></i> Edit</a>';

                        if ($_SESSION['admin_id'] != $admin['id']) { // Prevent self-deletion showing button
                           echo '<form method="POST" action="admin_manage_users.php?action=delete_user" class="inline-block m-0 p-0" onsubmit="return confirm(\'Are you sure you want to delete user: ' . htmlspecialchars(addslashes($admin['username'])) . '?\');">';
                           echo '<input type="hidden" name="user_id" value="' . $admin['id'] . '">';
                           echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generate_csrf_token(true)) . '">'; // Generate fresh token for each delete form
                           echo '<button type="submit" class="admin-button text-xs danger"><i class="fas fa-trash"></i> Delete</button>';
                           echo '</form>';
                        } else {
                           echo '<span class="admin-button text-xs bg-gray-500 cursor-not-allowed opacity-50" title="Cannot delete self"><i class="fas fa-trash"></i> Delete</span>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table></div>';
                }
            } catch (PDOException $e) {
                error_log("Error fetching admin users: " . $e->getMessage());
                echo "<p class='text-red-400 p-4 bg-red-900/50 rounded-md'>Error fetching users. Check logs.</p>";
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
            <form action="admin_manage_users.php?action=store_user" method="POST" class="space-y-6">
                <?php csrf_input_field(); ?>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                    <input type="text" id="username" name="username" required class="admin-form-input" value="<?= htmlspecialchars($form_data['username'] ?? '') ?>" pattern="[a-zA-Z0-9_]{3,50}" title="3-50 alphanumeric characters and underscores.">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" required class="admin-form-input" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                    <input type="password" id="password" name="password" required class="admin-form-input" minlength="8" title="Minimum 8 characters.">
                </div>
                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" required class="admin-form-input" minlength="8">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                    <select id="role" name="role" required class="admin-form-input">
                        <option value="admin" <?= (isset($form_data['role']) && $form_data['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                        <!-- Add other roles here later e.g., Editor -->
                    </select>
                </div>
                 <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                    <select id="is_active" name="is_active" required class="admin-form-input">
                        <option value="1" <?= (isset($form_data['is_active']) && $form_data['is_active'] == '1') ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (isset($form_data['is_active']) && $form_data['is_active'] == '0') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="admin-button text-lg px-8 py-3"><i class="fas fa-plus-circle mr-2"></i>Create Admin User</button>
                </div>
            </form>
            <?php
            break;

        case 'store_user':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: admin_manage_users.php");
                exit;
            }
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. User creation aborted.";
                $_SESSION['admin_message_type'] = "error";
                $_SESSION['form_data'] = $_POST;
                header("Location: admin_manage_users.php?action=create");
                exit;
            }

            $errors = [];
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $role = $_POST['role'] ?? 'admin'; // Default role
            $is_active = filter_var($_POST['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;


            if (empty($username) || !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
                $errors[] = "Username must be 3-50 alphanumeric characters or underscores.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email address is required.";
            }
            if (empty($password) || strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }
            if ($password !== $password_confirm) {
                $errors[] = "Passwords do not match.";
            }
            if ($role !== 'admin') { // For now, only 'admin' role is directly supported via this form
                $errors[] = "Invalid role specified.";
            }

            // Check if username or email already exists
            if (empty($errors)) {
                try {
                    $stmt_check = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
                    $stmt_check->execute([$username, $email]);
                    if ($stmt_check->fetch()) {
                        $errors[] = "Username or email already exists.";
                    }
                } catch (PDOException $e) {
                    error_log("Error checking existing user: " . $e->getMessage());
                    $errors[] = "Database error during user validation. Please try again.";
                }
            }

            if (empty($errors)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                if (!$password_hash) {
                    $errors[] = "Failed to hash password."; // Should not happen
                } else {
                    try {
                        $stmt_insert = $pdo->prepare("INSERT INTO admins (username, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?)");
                        $stmt_insert->execute([$username, $email, $password_hash, $role, $is_active]);
                        $_SESSION['admin_message'] = "Admin user '" . htmlspecialchars($username) . "' created successfully.";
                        $_SESSION['admin_message_type'] = "success";
                        header("Location: admin_manage_users.php");
                        exit;
                    } catch (PDOException $e) {
                        error_log("Error creating admin user: " . $e->getMessage());
                        $errors[] = "Database error: Could not create user. Please try again.";
                    }
                }
            }

            // If errors occurred
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Preserve submitted data
            header("Location: admin_manage_users.php?action=create");
            exit;
            break;

        case 'edit':
            $user_id = $_GET['id'] ?? null;
            if (!$user_id || !filter_var($user_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid user ID for editing.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_users.php");
                exit;
            }

            $csrf_token = generate_csrf_token();
            $form_errors = $_SESSION['form_errors'] ?? [];
            $form_data_session = $_SESSION['form_data'] ?? null;
            unset($_SESSION['form_errors'], $_SESSION['form_data']);

            $user_data = [];
            if ($form_data_session && isset($form_data_session['user_id']) && $form_data_session['user_id'] == $user_id) {
                $user_data = $form_data_session; // Use session data if returning from failed update
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT id, username, email, role, is_active FROM admins WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$user_data) {
                        $_SESSION['admin_message'] = "User not found (ID: $user_id).";
                        $_SESSION['admin_message_type'] = "error";
                        header("Location: admin_manage_users.php");
                        exit;
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching user for edit (ID: $user_id): " . $e->getMessage());
                    $_SESSION['admin_message'] = "Error fetching user details.";
                    $_SESSION['admin_message_type'] = "error";
                    header("Location: admin_manage_users.php");
                    exit;
                }
            }

            if (!empty($form_errors)) {
                echo '<div class="my-4 p-4 bg-red-700/50 border border-red-900/70 text-red-200 rounded-lg"><p class="font-bold mb-2">Please correct errors:</p><ul class="list-disc list-inside">';
                foreach ($form_errors as $error) echo '<li>' . htmlspecialchars($error) . '</li>';
                echo '</ul></div>';
            }
            ?>
            <h3 class="text-xl font-semibold text-indigo-300 mb-4">Editing User: "<?= htmlspecialchars($user_data['username']) ?>"</h3>
            <form action="admin_manage_users.php?action=update_user" method="POST" class="space-y-6">
                <?php csrf_input_field(); ?>
                <input type="hidden" name="user_id" value="<?= (int)$user_data['id'] ?>">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                    <input type="text" id="username" name="username" required class="admin-form-input" value="<?= htmlspecialchars($user_data['username'] ?? '') ?>" pattern="[a-zA-Z0-9_]{3,50}" title="3-50 alphanumeric characters and underscores.">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" required class="admin-form-input" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                    <select id="role" name="role" required class="admin-form-input">
                        <option value="admin" <?= (isset($user_data['role']) && $user_data['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                        <!-- Add other roles here later -->
                    </select>
                </div>
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                    <select id="is_active" name="is_active" required class="admin-form-input">
                        <option value="1" <?= (isset($user_data['is_active']) && $user_data['is_active'] == '1') ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (isset($user_data['is_active']) && $user_data['is_active'] == '0') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <p class="text-sm text-gray-400">Password can be changed via a separate "Change Password" feature (to be implemented if needed).</p>
                <div>
                    <button type="submit" class="admin-button text-lg px-8 py-3 bg-green-600 hover:bg-green-700"><i class="fas fa-save mr-2"></i>Update User</button>
                </div>
            </form>
            <?php
            break;

        case 'update_user':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: admin_manage_users.php");
                exit;
            }
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. User update aborted.";
                $_SESSION['admin_message_type'] = "error";
                $_SESSION['form_data'] = $_POST; // Preserve data for repopulation
                header("Location: admin_manage_users.php?action=edit&id=" . ($_POST['user_id'] ?? 0));
                exit;
            }

            $user_id = $_POST['user_id'] ?? null;
            if (!$user_id || !filter_var($user_id, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid user ID for update.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_users.php");
                exit;
            }

            $errors = [];
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'admin';
            $is_active =  filter_var($_POST['is_active'] ?? 1, FILTER_VALIDATE_INT) ? 1 : 0; // Ensure it's 0 or 1

            if (empty($username) || !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
                $errors[] = "Username must be 3-50 alphanumeric characters or underscores.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email address is required.";
            }
             if ($role !== 'admin') { // For now, only 'admin' role
                $errors[] = "Invalid role specified.";
            }

            // Check if new username or email conflicts with another user
            if (empty($errors)) {
                try {
                    $stmt_check = $pdo->prepare("SELECT id FROM admins WHERE (username = ? OR email = ?) AND id != ?");
                    $stmt_check->execute([$username, $email, $user_id]);
                    if ($stmt_check->fetch()) {
                        $errors[] = "New username or email already in use by another account.";
                    }
                } catch (PDOException $e) {
                    error_log("Error checking existing user for update (ID: $user_id): " . $e->getMessage());
                    $errors[] = "Database error during user validation. Please try again.";
                }
            }

            // Prevent deactivating or changing role of the current logged-in admin if it's themselves (basic safeguard)
            if ($user_id == $_SESSION['admin_id']) {
                if($is_active == 0) $errors[] = "You cannot deactivate your own account.";
                // Add role check if roles become more complex:
                // $current_user_data = $pdo->prepare("SELECT role FROM admins WHERE id = ?"); $current_user_data->execute([$user_id]); $db_role = $current_user_data->fetchColumn();
                // if($role !== $db_role) $errors[] = "You cannot change your own role directly.";
            }


            if (empty($errors)) {
                try {
                    $stmt_update = $pdo->prepare("UPDATE admins SET username = ?, email = ?, role = ?, is_active = ? WHERE id = ?");
                    $stmt_update->execute([$username, $email, $role, $is_active, $user_id]);
                    $_SESSION['admin_message'] = "User '" . htmlspecialchars($username) . "' updated successfully.";
                    $_SESSION['admin_message_type'] = "success";

                    // If current admin updated their own username, update session
                    if ($user_id == $_SESSION['admin_id']) {
                        $_SESSION['admin_username'] = $username;
                    }
                    header("Location: admin_manage_users.php");
                    exit;
                } catch (PDOException $e) {
                    error_log("Error updating admin user (ID: $user_id): " . $e->getMessage());
                    $errors[] = "Database error: Could not update user. Please try again.";
                }
            }

            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Preserve submitted data for repopulation
            header("Location: admin_manage_users.php?action=edit&id=" . $user_id);
            exit;
            break;

        case 'delete_user':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Expect POST for delete
                $_SESSION['admin_message'] = "Invalid request method for delete user action.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_users.php");
                exit;
            }
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['admin_message'] = "CSRF token validation failed. User deletion aborted.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_users.php");
                exit;
            }

            $user_id_to_delete = $_POST['user_id'] ?? null;
            if (!$user_id_to_delete || !filter_var($user_id_to_delete, FILTER_VALIDATE_INT)) {
                $_SESSION['admin_message'] = "Invalid user ID for deletion.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_users.php");
                exit;
            }

            // Critical Safety Checks:
            // 1. Prevent self-deletion
            if ($user_id_to_delete == $_SESSION['admin_id']) {
                $_SESSION['admin_message'] = "You cannot delete your own account.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_users.php");
                exit;
            }

            // 2. Ensure at least one admin remains (especially if only one 'admin' role exists)
            try {
                $stmt_count = $pdo->query("SELECT COUNT(*) FROM admins WHERE role = 'admin'"); // Or just COUNT(*) if no other roles yet
                $admin_count = $stmt_count->fetchColumn();

                if ($admin_count <= 1) {
                    // Check if the user to be deleted is the last admin
                    $stmt_role = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
                    $stmt_role->execute([$user_id_to_delete]);
                    $user_role_to_delete = $stmt_role->fetchColumn();
                    if ($user_role_to_delete === 'admin') {
                         $_SESSION['admin_message'] = "Cannot delete the last remaining admin user.";
                         $_SESSION['admin_message_type'] = "error";
                         header("Location: admin_manage_users.php");
                         exit;
                    }
                }
                 // More robust check: count all active admins
                $stmt_active_admins = $pdo->query("SELECT COUNT(*) FROM admins WHERE is_active = 1 AND role = 'admin'");
                if ($stmt_active_admins->fetchColumn() <= 1) {
                    $stmt_is_target_active_admin = $pdo->prepare("SELECT role, is_active FROM admins WHERE id = ?");
                    $stmt_is_target_active_admin->execute([$user_id_to_delete]);
                    $target_user = $stmt_is_target_active_admin->fetch(PDO::FETCH_ASSOC);
                    if ($target_user && $target_user['role'] === 'admin' && $target_user['is_active'] == 1) {
                        $_SESSION['admin_message'] = "Cannot delete the last active admin user.";
                        $_SESSION['admin_message_type'] = "error";
                        header("Location: admin_manage_users.php");
                        exit;
                    }
                }


            } catch (PDOException $e) {
                error_log("Error counting admin users for delete safety check: " . $e->getMessage());
                $_SESSION['admin_message'] = "Database error during delete safety check. User not deleted.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: admin_manage_users.php");
                exit;
            }

            // Proceed with deletion
            try {
                $stmt_delete = $pdo->prepare("DELETE FROM admins WHERE id = ?");
                $stmt_delete->execute([$user_id_to_delete]);

                if ($stmt_delete->rowCount() > 0) {
                    $_SESSION['admin_message'] = "User (ID: $user_id_to_delete) deleted successfully.";
                    $_SESSION['admin_message_type'] = "success";
                } else {
                    $_SESSION['admin_message'] = "User (ID: $user_id_to_delete) not found or already deleted.";
                    $_SESSION['admin_message_type'] = "warning";
                }
            } catch (PDOException $e) {
                error_log("Error deleting user (ID: $user_id_to_delete): " . $e->getMessage());
                $_SESSION['admin_message'] = "Database error: Could not delete user.";
                $_SESSION['admin_message_type'] = "error";
            }
            header("Location: admin_manage_users.php");
            exit;
            break;
    }
    ?>
</div>

<?php
include '../includes/admin_footer_inc.php';
?>
