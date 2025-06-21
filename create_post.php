<?php
include 'config.php';

$thumbnail = 'default.jpg';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['postTitle'];
    $category = $_POST['postCategory'];
    $content = $_POST['postContent'];
    $password = $_POST['password'];

    // Password validation
    // IMPORTANT: CHANGE THIS PASSWORD IMMEDIATELY!
    // This is a default password and is NOT secure.
    // Generate a strong, unique password for your site.
    if ($password !== 'ChangeMeImmediately123!') { // MODIFIED LINE
        $errors[] = 'Incorrect password.';
    }

    // Handle thumbnail upload
    if (isset($_FILES['postThumbnail']) && $_FILES['postThumbnail']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['postThumbnail']['name']);
        $targetPath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['postThumbnail']['tmp_name'], $targetPath)) {
            $thumbnail = $fileName;
        } else {
            $errors[] = 'Failed to upload thumbnail.';
        }
    }

    // Save post if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, category, thumbnail) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $category, $thumbnail]);
        header("Location: index.php");
        exit();
    }
}

// Display errors (if any) - consider a more user-friendly way to show these on the form page
if (!empty($errors)) {
    // For now, just echo them. In a real app, you'd redirect with error messages.
    // Or store them in a session and display on the form page.
    echo '<div style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); background-color: #ef4444; color: white; padding: 1rem; border-radius: 0.5rem; z-index: 100; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">';
    foreach ($errors as $error) {
        echo htmlspecialchars($error) . '<br>';
    }
    echo '<a href="index.php#newPostModal" style="display: block; margin-top: 0.5rem; color: #f3f4f6; text-decoration: underline;">&laquo; Back to form</a>';
    echo '</div>';
}
?>
