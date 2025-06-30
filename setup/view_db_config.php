<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindust Setup - Database Configuration</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; max-width: 700px; margin: auto; background-color: #f4f4f4; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #5a67d8; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: calc(100% - 22px); padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .button { background-color: #5a67d8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 1em; }
        .button:hover { background-color: #434190; }
        .error-message { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Step 1: Database Configuration</h1>
        <p>Please provide your database connection details.</p>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="index.php" method="post">
            <input type="hidden" name="action" value="submit_db_config">
            <?= $setupHelper->csrfInputField(); ?>

            <div>
                <label for="db_host">Database Host:</label>
                <input type="text" id="db_host" name="db_host" value="localhost" required>
            </div>
            <div>
                <label for="db_name">Database Name:</label>
                <input type="text" id="db_name" name="db_name" required>
            </div>
            <div>
                <label for="db_user">Database Username:</label>
                <input type="text" id="db_user" name="db_user" required>
            </div>
            <div>
                <label for="db_pass">Database Password:</label>
                <input type="password" id="db_pass" name="db_pass">
            </div>
            <button type="submit" class="button">Test Connection & Save</button>
        </form>
    </div>
</body>
</html>
