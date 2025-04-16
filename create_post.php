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
    if ($password !== 'your-password') {
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

// Display errors (optional)
foreach ($errors as $error) {
    echo "<div class='bg-red-500 text-white p-2'>$error</div>";
}
?>
