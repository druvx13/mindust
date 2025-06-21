<?php
include 'config.php';
require_once 'includes/csrf_helper.php'; // Include CSRF helper

$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message_content = trim($_POST['message'] ?? ''); // Renamed to avoid conflict

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error_message = "Invalid CSRF token. Please try again.";
    } elseif (empty($name) || empty($email) || empty($message_content)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message_content]);
            // Redirect to avoid form resubmission on refresh
            header("Location: contact.php?success=1");
            exit();
        } catch (PDOException $e) {
            // Log error: error_log("Error sending message: " . $e->getMessage());
            $error_message = "Could not send message. Please try again later.";
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message_sent = true;
}

$page_title = 'Contact Us | Mind Dust';
$csrf_token = generate_csrf_token(); // Generate CSRF token for the contact form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body class="antialiased">
    <?php include 'includes/music_toggle.php'; ?>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/mobile_menu.php'; ?>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <main class="max-w-2xl mx-auto">
            <div class="bg-slate-900/50 rounded-xl overflow-hidden border border-slate-800 p-6 md:p-8">
                <h1 class="text-3xl md:text-4xl font-bold mb-8 title-font text-indigo-400 text-center">Get in Touch</h1>

                <?php if ($message_sent): ?>
                    <div class="bg-green-700/50 border border-green-900/70 text-green-300 p-4 rounded-lg mb-6 text-center">
                        <i class="fas fa-check-circle mr-2"></i>Your message has been sent successfully! We'll get back to you soon.
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="bg-red-700/50 border border-red-900/70 text-red-300 p-3 rounded-lg mb-4 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php" class="space-y-6">
                    <?php csrf_input_field(); ?>
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1 text-gray-300">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1 text-gray-300">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Your Email" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium mb-1 text-gray-300">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="How can we help?" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-500"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg transition-all duration-300 w-full sm:w-auto">Send Message</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>