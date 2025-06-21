<?php
// This file should be included at the top of all admin pages.
// It assumes a session has already been started if needed by the calling page.

// Ensure admin is logged in (this check might be redundant if already on every page,
// but good for includes that might be used in different contexts).
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If session isn't set, and this header is included on a page that *requires* login,
    // the calling page should have already redirected.
    // However, as a fallback, redirect.
    // header("Location: admin_login.php"); // Be careful with pathing if this file is included from different depths
    // exit;
    // Decided against redirecting from an include, parent script should handle this.
}

$admin_username_from_session = $_SESSION['admin_username'] ?? 'Admin';

// $page_title_for_head should be set by the calling script before including this header.
$page_title_for_head = $page_title_for_head ?? "Admin Area | Mindust CMS";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    // Using the main site's head include for now.
    // It contains TailwindCSS, FontAwesome, Marked.js, and main style.css
    // $page_title_for_head is used by includes/head.php
    include_once __DIR__ . '/head.php';
    ?>
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], 'admin_') === false ? '../' : '') . 'assets/css/admin_style.css'; ?>">
    <style>
        /* Additional critical admin-specific styles can go here if needed before admin_style.css loads */
        /* Or if admin_style.css fails to load, provides some fallback */
        body.admin-area { background-color: #1e293b; color: #e2e8f0; } /* slate-800, slate-200 */
    </style>
</head>
<body class="admin-area antialiased">

<header class="admin-header">
    <div class="container mx-auto flex justify-between items-center">
        <a href="admin_dashboard.php" class="text-xl font-semibold glitch" data-text="Mindust CMS - Admin">Mindust CMS - Admin</a>
        <nav>
            <span class="mr-4">Welcome, <?= htmlspecialchars($admin_username_from_session) ?>!</span>
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <a href="admin_dashboard.php" class="hover:text-indigo-300 mr-3 transition-colors">Dashboard</a>
                <a href="admin_manage_posts.php" class="hover:text-indigo-300 mr-3 transition-colors">Manage Posts</a>
                <!-- Add more admin links here -->
                <a href="admin_logout.php" class="text-indigo-400 hover:text-indigo-300 transition-colors">Logout <i class="fas fa-sign-out-alt ml-1"></i></a>
            <?php else: ?>
                <a href="admin_login.php" class="text-indigo-400 hover:text-indigo-300 transition-colors">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="admin-container py-4">
    <!-- Content of the specific admin page will go here -->
