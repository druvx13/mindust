<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindust Setup - Admin User Creation</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; max-width: 700px; margin: auto; background-color: #f4f4f4; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #5a67d8; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { width: calc(100% - 22px); padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .button { background-color: #5a67d8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 1em; }
        .button:hover { background-color: #434190; }
        .error-message { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Step 2: Create Admin User</h1>
        <p>Set up your primary administrator account.</p>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="index.php" method="post">
            <input type="hidden" name="action" value="submit_admin_creation">
            <?= $setupHelper->csrfInputField(); ?>

            <div>
                <label for="admin_username">Admin Username:</label>
                <input type="text" id="admin_username" name="admin_username" required pattern="[a-zA-Z0-9_]{3,50}" title="3-50 alphanumeric characters and underscores.">
            </div>
            <div>
                <label for="admin_email">Admin Email:</label>
                <input type="email" id="admin_email" name="admin_email" required>
            </div>
            <div>
                <label for="admin_password">Password (min 8 characters):</label>
                <input type="password" id="admin_password" name="admin_password" required minlength="8">
            </div>
            <div>
                <label for="admin_password_confirm">Confirm Password:</label>
                <input type="password" id="admin_password_confirm" name="admin_password_confirm" required minlength="8">
            </div>
            <button type="submit" class="button">Create Admin & Proceed</button>
        </form>
    </div>
</body>
</html>
