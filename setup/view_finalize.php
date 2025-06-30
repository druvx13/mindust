<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindust Setup - Finalizing</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; max-width: 700px; margin: auto; background-color: #f4f4f4; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #5a67d8; }
        .button { background-color: #5a67d8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top:10px; }
        .button:hover { background-color: #434190; }
        .error-message { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .success-message { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; padding: 10px; border-radius: 4px; margin-bottom: 15px;}
        .warning-message { background-color: #fff3e0; color: #ef6c00; border: 1px solid #ffcc80; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold;}
        textarea { width: 100%; min-height: 150px; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; font-size: 0.9em; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Step 3: Finalizing Installation</h1>

        <!-- Messages will be displayed here by index.php -->
        <div id="finalize-messages">
            <?php if (!empty($finalize_messages)): ?>
                <?php foreach ($finalize_messages as $message): ?>
                    <div class="<?= htmlspecialchars($message['type']) ?>-message">
                        <?= $message['text'] // Text is already HTML or safe string ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($config_content_for_manual_copy): ?>
                <div>
                    <p><strong>Manual <code>config.php</code> Creation Required:</strong></p>
                    <p>Please copy the entire content below and paste it into a new file named <code>config.php</code> in the main directory of your Mindust installation (the directory that contains the <code>setup</code> and <code>admin</code> folders).</p>
                    <textarea readonly rows="20" style="width: 98%; white-space: pre; word-wrap: normal; overflow-x: scroll;"><?= htmlspecialchars($config_content_for_manual_copy) ?></textarea>
                </div>
            <?php endif; ?>

            <?php if ($show_manual_config_confirm_button): ?>
                <form action="index.php" method="post" style="margin-top: 15px;">
                    <input type="hidden" name="action" value="confirm_manual_config">
                    <?= $setupHelper->csrfInputField(); ?>
                    <button type="submit" class="button">I have created config.php manually and saved it</button>
                </form>
            <?php endif; ?>

            <!-- Links for successful installation (to be shown conditionally later) -->
            <?php /*
            if (isset($installation_complete) && $installation_complete) {
                echo '<p style="margin-top:20px;">';
                echo '<a href="../index.php" class="button">Go to Homepage</a> ';
                echo '<a href="../admin/admin_login.php" class="button">Go to Admin Login</a>';
                echo '</p>';
            }
            */ ?>
        </div>
    </div>
</body>
</html>
