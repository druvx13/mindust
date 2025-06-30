<?php
session_start();
// No direct config.php include here, admin_header_inc.php will handle it if necessary for DB ops.

// Check if admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$page_title_for_head = "Admin Dashboard | Mindust CMS";
include '../includes/admin_header_inc.php'; // This now includes head, body tag, and admin header/nav
?>

<div class="admin-card">
    <h2 class="text-2xl font-bold mb-6 text-indigo-400">Admin Dashboard</h2>
    <p class="text-gray-300 mb-4">
        Welcome to the Mindust CMS admin area. From here, you will be able to manage your site's content.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="admin_manage_posts.php?action=create" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-lg text-center transition-colors text-lg shadow-lg flex items-center justify-center">
            <i class="fas fa-plus-circle mr-2"></i> Create New Post
        </a>
        <a href="admin_manage_posts.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-center transition-colors text-lg shadow-lg flex items-center justify-center">
            <i class="fas fa-list-alt mr-2"></i> View & Manage Posts
        </a>
        <!-- Add more links here as features are developed -->
        <!-- e.g., Manage Categories, Manage Users, Site Settings -->
    </div>

    <div class="mt-10 p-6 bg-slate-800/70 rounded-lg border border-slate-700">
                <h3 class="text-xl font-semibold text-indigo-300 mb-3">Quick Stats</h3>
        <?php
                // Fetching actual stats. $pdo is available from config.php, included by admin_header_inc.php
                $postCount = "N/A";
                $commentCount = "N/A";
                $messageCount = "N/A";
                $adminCount = "N/A";

                if (isset($pdo)) {
                    try {
                        $stmt_posts = $pdo->query("SELECT COUNT(*) FROM posts");
                        $postCount = $stmt_posts->fetchColumn();

                        $stmt_comments = $pdo->query("SELECT COUNT(*) FROM comments");
                        $commentCount = $stmt_comments->fetchColumn();

                        $stmt_messages = $pdo->query("SELECT COUNT(*) FROM messages");
                        $messageCount = $stmt_messages->fetchColumn();

                        $stmt_admins = $pdo->query("SELECT COUNT(*) FROM admins");
                        $adminCount = $stmt_admins->fetchColumn();

                    } catch (PDOException $e) {
                        error_log("Admin Dashboard Quick Stats Error: " . $e->getMessage());
                        // Variables will remain "N/A"
                    }
                } else {
                     error_log("Admin Dashboard Quick Stats Error: PDO object not available.");
                }
                ?>
        <ul class="text-gray-400 space-y-2">
                    <li class="flex justify-between items-center"><span>Total Posts:</span> <span class="font-bold text-indigo-400 text-lg"><?= htmlspecialchars($postCount) ?></span></li>
                    <li class="flex justify-between items-center"><span>Total Comments:</span> <span class="font-bold text-indigo-400 text-lg"><?= htmlspecialchars($commentCount) ?></span></li>
                    <li class="flex justify-between items-center"><span>Total Contact Messages:</span> <span class="font-bold text-indigo-400 text-lg"><?= htmlspecialchars($messageCount) ?></span></li>
                    <li class="flex justify-between items-center"><span>Total Admin Users:</span> <span class="font-bold text-indigo-400 text-lg"><?= htmlspecialchars($adminCount) ?></span></li>
        </ul>
                <?php if (!isset($pdo) || $postCount === "N/A"): ?>
                <p class="text-xs text-yellow-500 mt-4">Note: Stats could not be loaded. Check database connection or server logs.</p>
                <?php endif; ?>
    </div>
</div>

<?php
include '../includes/admin_footer_inc.php'; // This now includes closing main, footer, scripts, and closing body/html tags
?>
