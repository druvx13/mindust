<?php
// Mindust Web Setup Wizard
// Helper class for setup logic

class SetupHelper {

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken($token): bool {
        if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        }
        return false;
    }

    public function csrfInputField(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($this->generateCsrfToken()) . '">';
    }

    /**
     * Checks server pre-requisites.
     * @return array Associative array of checks and their status (boolean) and messages.
     */
    public function checkPrerequisites(): array {
        $checks = [];
        $php_version_required = '7.4.0'; // Recommended PHP version
        $php_version_ok = version_compare(PHP_VERSION, $php_version_required, '>=');
        $checks['php_version'] = [
            'name' => "PHP Version (>= {$php_version_required})",
            'status' => $php_version_ok,
            'message' => "Your version: " . PHP_VERSION . ($php_version_ok ? " (OK)" : " - Warning: Older than recommended. Please upgrade if possible for best compatibility and security.")
        ];

        $pdo_mysql_ok = extension_loaded('pdo_mysql');
        $checks['pdo_mysql'] = [
            'name' => "PDO MySQL Extension",
            'status' => $pdo_mysql_ok,
            'message' => $pdo_mysql_ok ? "Loaded (OK)" : "<strong>Required:</strong> Not loaded. This is essential for database operations."
        ];

        $config_example_path = __DIR__ . '/../config.php.example';
        $config_example_ok = file_exists($config_example_path);
        $checks['config_example'] = [
            'name' => "<code>config.php.example</code> File",
            'status' => $config_example_ok,
            'message' => $config_example_ok ? "Found (OK)" : "<strong>Required:</strong> Not found in project root. This file is needed by the setup."
        ];

        $config_path = __DIR__ . '/../config.php';
        $config_dir = dirname($config_path);
        $can_write_config = (!file_exists($config_path) && is_writable($config_dir)) || (file_exists($config_path) && is_writable($config_path));
        $checks['config_writable'] = [
            'name' => "Configuration File Writable",
            'status' => $can_write_config,
            'message' => $can_write_config ? "<code>config.php</code> can be written/updated in project root. (OK)" : "Warning: <code>config.php</code> may not be writable in project root. You might need to create/update it manually."
        ];

        $uploads_path = __DIR__ . '/../uploads';
        $uploads_writable_ok = false;
        $uploads_msg = '';
        if (is_dir($uploads_path)) {
            if (is_writable($uploads_path)) {
                $uploads_writable_ok = true;
                $uploads_msg = "<code>uploads/</code> directory exists and is writable. (OK)";
            } else {
                $uploads_msg = "<strong>Required:</strong> <code>uploads/</code> directory exists but is NOT writable. File uploads will fail.";
            }
        } else {
            if (is_writable($config_dir)) { // Check if project root is writable to create uploads/
                 if (@mkdir($uploads_path, 0755)) {
                    if (is_writable($uploads_path)) {
                        $uploads_writable_ok = true;
                        $uploads_msg = "<code>uploads/</code> directory created and is writable. (OK)";
                    } else {
                         $uploads_msg = "<strong>Required:</strong> <code>uploads/</code> directory created but is NOT writable. File uploads will fail.";
                    }
                    // Silently rmdir if we created it just for check, app should handle its own creation if needed.
                    // Or leave it. For setup, better to leave it and report status.
                 } else {
                    $uploads_msg = "<strong>Required:</strong> <code>uploads/</code> directory does not exist and could not be automatically created (project root might not be writable enough or other issues). File uploads will fail.";
                 }
            } else {
                $uploads_msg = "<strong>Required:</strong> <code>uploads/</code> directory does not exist and parent directory (project root) is NOT writable. File uploads will fail.";
            }
        }
         $checks['uploads_dir_writable'] = [
            'name' => "<code>uploads/</code> Directory Writable",
            'status' => $uploads_writable_ok,
            'message' => $uploads_msg
        ];
        return $checks;
    }

    public function testDbConnection(array $dbConfig) {
        if (empty($dbConfig['host']) || empty($dbConfig['dbname']) || empty($dbConfig['username'])) {
            return "Database host, name, and username are required.";
        }
        try {
            $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['dbname'] . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 1049) {
                return "Database '" . htmlspecialchars($dbConfig['dbname']) . "' does not exist. Please create it first, or ensure the name is correct.";
            } elseif ($e->getCode() == 1045) {
                return "Access denied for user '" . htmlspecialchars($dbConfig['username']) . "'. Check username/password.";
            } elseif ($e->getCode() == 2002) {
                 return "Could not connect to database host '" . htmlspecialchars($dbConfig['host']) . "'. Is the server running and accessible?";
            }
            return "Connection Error: " . htmlspecialchars($e->getMessage());
        }
    }

    public function generateConfigContent(array $dbConfig): string {
        $host = $dbConfig['host'] ?? 'localhost';
        $dbname = $dbConfig['dbname'] ?? 'mindust_db';
        $username = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';
        return "<?php\n" .
               "// Database Configuration (Generated by Mindust Web Setup)\n" .
               "\$host = '" . addslashes($host) . "';\n" .
               "\$dbname = '" . addslashes($dbname) . "';\n" .
               "\$username = '" . addslashes($username) . "';\n" .
               "\$password = '" . addslashes($password) . "';\n\n" .
               "\$options = [\n" .
               "    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,\n" .
               "    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n" .
               "    PDO::ATTR_EMULATE_PREPARES   => false,\n" .
               "];\n\n" .
               "try {\n" .
               "    // Create PDO instance for database connection\n" .
               "    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$dbname;charset=utf8mb4\", \$username, \$password, \$options);\n" .
               "} catch (PDOException \$e) {\n" .
               "    error_log(\"Database Connection Error: \" . \$e->getMessage());\n" .
               "    // Display a user-friendly error message.\n" .
               "    die(\"Database connection failed. Please check your configuration or contact support if the issue persists. Details have been logged.\");\n" .
               "}\n" .
               "// Setup completed marker - Mindust CMS\n" .
               "?>\n";
    }

    public function writeConfigFile(string $content): bool {
        $configPath = __DIR__ . '/../config.php';
        $targetIsWritable = is_writable(file_exists($configPath) ? $configPath : dirname($configPath));
        if ($targetIsWritable) {
            if (@file_put_contents($configPath, $content) !== false) {
                return true;
            }
        }
        return false;
    }

    public function createDatabaseTables(PDO $pdo) {
        $sqlFilePath = __DIR__ . '/../db/database.sql';
        if (!file_exists($sqlFilePath)) {
            return "Database schema file (<code>db/database.sql</code>) not found.";
        }
        try {
            $sql = file_get_contents($sqlFilePath);
            if ($sql === false) {
                return "Could not read database schema file (<code>db/database.sql</code>).";
            }
            $pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            return "Error creating database tables: " . htmlspecialchars($e->getMessage());
        }
    }

    public function createAdminUser(PDO $pdo, array $adminDetails) {
        if (empty($adminDetails['username']) || empty($adminDetails['email']) || empty($adminDetails['password'])) {
            return "Admin details (username, email, password) are incomplete for user creation.";
        }
        $password_hash = password_hash($adminDetails['password'], PASSWORD_DEFAULT);
        if (!$password_hash) {
            return "Failed to hash admin password.";
        }
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO admins (username, email, password_hash, role, is_active, created_at, updated_at)
                 VALUES (:username, :email, :password_hash, 'admin', 1, NOW(), NOW())"
            );
            $stmt->execute([
                ':username' => $adminDetails['username'],
                ':email' => $adminDetails['email'],
                ':password_hash' => $password_hash
            ]);
            return $stmt->rowCount() > 0 ? true : "Failed to insert admin user (no rows affected). User might already exist if tables were not freshly created.";
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || strpos(strtolower($e->getMessage()), 'duplicate entry') !== false) {
                return "Admin user with this username or email already exists in the database.";
            }
            return "Error creating admin user: " . htmlspecialchars($e->getMessage());
        }
    }

    public function createLockFile(): bool {
        $lockFilePath = __DIR__ . '/installed.lock';
        $lockFileContent = 'Mindust CMS Installed on: ' . date('Y-m-d H:i:s') . PHP_EOL;
        $lockFileContent .= "This file prevents the setup wizard from running again." . PHP_EOL;
        $lockFileContent .= "You should delete the entire 'setup/' directory after successful installation." . PHP_EOL;
        if (@file_put_contents($lockFilePath, $lockFileContent) !== false) {
            @chmod($lockFilePath, 0444);
            return true;
        }
        return false;
    }

    public function isInstalled(): bool {
        $lockFilePath = __DIR__ . '/installed.lock';
        $configPath = __DIR__ . '/../config.php';
        if (file_exists($lockFilePath)) {
            return true;
        }
        if (file_exists($configPath)) {
            $configContent = @file_get_contents($configPath);
            if ($configContent && strpos($configContent, '// Setup completed marker - Mindust CMS') !== false) {
                $this->createLockFile();
                return true;
            }
        }
        return false;
    }
}
?>
