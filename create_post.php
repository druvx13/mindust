<?php
session_start();
require_once 'config.php'; // For $pdo
require_once 'includes/csrf_helper.php'; // Include CSRF helper

// Check if admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$thumbnail = 'default.jpg'; // Default thumbnail
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid or missing CSRF token. Action blocked.';
        // Set session message and redirect back to form or dashboard
        $_SESSION['admin_message'] = 'CSRF token validation failed. Please try submitting the form again.';
        $_SESSION['admin_message_type'] = 'error';
        // Preserve form data if possible, then redirect
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: admin_manage_posts.php?action=create");
        exit;
    }

    $title = trim($_POST['postTitle'] ?? '');
    $category = trim($_POST['postCategory'] ?? '');
    $content = trim($_POST['postContent'] ?? '');
    // Password field from form is no longer used for post creation authorization.

    if (empty($title)) $errors[] = 'Post title is required.';
    if (empty($category)) $errors[] = 'Post category is required.';
    if (empty($content)) $errors[] = 'Post content is required.';

    // Handle thumbnail upload
    if (isset($_FILES['postThumbnail']) && $_FILES['postThumbnail']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = preg_replace("/[^a-zA-Z0-9._-]+/", "", basename($_FILES['postThumbnail']['name']));
        // Ensure filename is not empty after sanitization
        if (empty($fileName)) {
            $fileName = bin2hex(random_bytes(8)) . ".jpg"; // Generate a random name if sanitized is empty
        }
        $targetPath = $targetDir . $fileName;

        $imageFileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowedExtensions)) {
            $errors[] = 'Invalid thumbnail file type. Only JPG, JPEG, PNG, GIF allowed.';
        } elseif ($_FILES['postThumbnail']['size'] > 2 * 1024 * 1024) { // Max 2MB
            $errors[] = 'Thumbnail file size exceeds 2MB limit.';
        } else {
            // Prevent directory traversal
            if (strpos($fileName, '/') !== false || strpos($fileName, '\\') !== false || $fileName === '.' || $fileName === '..') {
                $errors[] = 'Invalid thumbnail filename.';
            } else {
                 if (move_uploaded_file($_FILES['postThumbnail']['tmp_name'], $targetPath)) {
                    $thumbnail = $fileName;
                } else {
                    $errors[] = 'Failed to upload thumbnail. Check permissions on uploads/ directory.';
                }
            }
        }
    } elseif (isset($_FILES['postThumbnail']) && $_FILES['postThumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Error uploading thumbnail. Code: ' . $_FILES['postThumbnail']['error'];
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, category, thumbnail) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $category, $thumbnail]);

            $_SESSION['admin_message'] = "Post '" . htmlspecialchars($title) . "' created successfully!";
            $_SESSION['admin_message_type'] = "success";
            header("Location: admin_manage_posts.php");
            exit();
        } catch (PDOException $e) {
            error_log("Error creating post: " . $e->getMessage());
            // Do not expose detailed SQL errors to the user in production
            $errors[] = 'A database error occurred while creating the post. Please try again later.';
            $_SESSION['admin_message'] = 'A database error occurred. Post not created.';
            $_SESSION['admin_message_type'] = "error";
        }
    }

    // If errors occurred, store them and relevant data in session for the form page
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        // Sanitize form data before putting it back in session to prevent XSS if re-displayed raw
        $_SESSION['form_data'] = [
            'postTitle' => htmlspecialchars($title),
            'postCategory' => htmlspecialchars($category),
            'postContent' => htmlspecialchars($content)
            // Don't re-populate file input
        ];
        // Redirect back to the form page (which needs to be created/identified)
        // Assuming admin_manage_posts.php?action=create will be the form page
        header("Location: admin_manage_posts.php?action=create");
        exit();
    }
} else {
    // Redirect if not a POST request, as this script is for processing form submissions
    $_SESSION['admin_message'] = "Invalid request to create post.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: admin_dashboard.php");
    exit();
}
?>
