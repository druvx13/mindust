<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindust Setup - Welcome</title>
    <!-- Basic styling, can be improved -->
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; max-width: 700px; margin: auto; background-color: #f4f4f4; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #5a67d8; } /* Indigo-ish */
        .button { background-color: #5a67d8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .button:hover { background-color: #434190; }
        .error { color: red; }
        .success { color: green; }
        ul { list-style-type: none; padding: 0; }
        li.check-ok:before { content: "✔ "; color: green; }
        li.check-fail:before { content: "✘ "; color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Mindust CMS Setup!</h1>
        <p>This wizard will guide you through the installation process.</p>

        <div id="prerequisites">
            <h2>Pre-installation Checks:</h2>
            <?php if (!empty($prerequisites)): ?>
                <ul>
                    <?php foreach ($prerequisites as $check): ?>
                        <li class="<?= $check['status'] ? 'check-ok' : 'check-fail' ?>">
                            <strong><?= htmlspecialchars($check['name']) ?>:</strong> <?= $check['message'] // Message contains HTML like <code>, so don't escape ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No prerequisite checks defined.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($error_message)): ?>
            <p class="error" style="padding:10px; border:1px solid red; background-color:#ffeeee;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form action="index.php" method="post" style="margin-top: 20px;">
            <input type="hidden" name="action" value="start_setup">
            <?= $setupHelper->csrfInputField(); ?>
            <button type="submit" class="button" <?= !$all_critical_prerequisites_ok ? 'disabled title="Please resolve critical issues above before proceeding."' : '' ?>>
                Start Setup
            </button>
            <?php if (!$all_critical_prerequisites_ok): ?>
                <p class="error" style="margin-top:10px;">Please resolve all critical prerequisite issues (marked with ✘ and often in bold) before you can start the setup.</p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
