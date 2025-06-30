# Mindust - A Minimalist PHP Blogging System & CMS

[![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue.svg?style=flat&logo=php)](https://www.php.net/)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7+-orange.svg?style=flat&logo=mysql)](https://www.mysql.com/)
[![License: GPL-3.0](https://img.shields.io/badge/License-GPL--3.0-green.svg?style=flat)](https://www.gnu.org/licenses/gpl-3.0.html)

**Mindust** is a lightweight, minimalist blogging system and Content Management System (CMS) built with PHP and MySQL. It's designed for personal blogs, writers, and developers who value simplicity, control, and a clean foundation for further customization.

> **⚠️ Security Warning & Development Status**
>
> This project, while featuring CMS capabilities and security measures like CSRF protection and password hashing, is primarily intended as a learning tool or a base for further development. **It is NOT recommended for production use on a live, untrusted internet environment without a thorough security audit and additional hardening measures.**
>
> Always assume potential vulnerabilities may exist. Use or learn from this code with caution and at your own risk.

## ✨ Features

### Admin Area (`/admin/`)
*   **Secure Admin Login**: Robust authentication for administrators.
*   **Admin Dashboard**: Central hub for site management with quick stats.
*   **Post Management**: Full CRUD (Create, Read, Update, Delete) capabilities for blog posts, including thumbnail uploads and Markdown support.
*   **Category Management**: Organize posts with CRUD operations for categories, including automatic slug generation.
*   **User Management**: Manage admin users with CRUD operations, role assignment (currently 'admin'), and status control (active/inactive). Includes safeguards against self-deletion and deleting the last admin.
*   **CSRF Protection**: Implemented on all forms performing state-changing actions.

### Public Frontend
*   **Dynamic Post Display**: Shows blog posts with thumbnails, summaries, and categories.
*   **Markdown Rendering**: Posts are rendered from Markdown to HTML.
*   **Built-in Comment System**: Per-post comment functionality.
*   **Floating Music Player**: Ambient background music toggle.
*   **Static Pages**: Includes Archive, Contact, and Legal (Copyright) pages.
*   **Responsive Design**: Adapts to various screen sizes.

### Security
*   **CSRF Protection**: Safeguards against Cross-Site Request Forgery attacks (including the setup wizard).
*   **Password Hashing**: Uses modern password hashing techniques for admin accounts.
*   **Input Sanitization & Output Encoding**: Basic measures to prevent XSS and other injection attacks.
*   **Prepared Statements**: SQL queries use prepared statements to prevent SQL injection.
*   **Setup Wizard Security**: The web setup wizard includes checks for prior installation and prompts for deletion of the `setup/` directory post-installation.

## 🛠 Tech Stack

*   **Backend**: PHP (7.4 or newer recommended)
*   **Database**: MySQL (5.7 or newer) / MariaDB (10.2 or newer)
*   **Frontend**: HTML5, Tailwind CSS (via CDN), Vanilla JavaScript
*   **Libraries**: Font Awesome (icons, via CDN), Marked.js (Markdown rendering, via CDN)

## 🚀 Installation & Setup

### 1. Prerequisites
*   A web server (e.g., Apache, Nginx) with PHP 7.4+ installed. Ensure the `pdo_mysql` extension is enabled.
*   MySQL 5.7+ or MariaDB 10.2+.
*   A database management tool (e.g., phpMyAdmin, MySQL CLI) for initial database creation.
*   Web browser for the web-based setup wizard.
*   Git (optional, for cloning the repository).

### 2. Clone or Download
```bash
git clone https://github.com/druvx13/mindust.git
cd mindust
```
Alternatively, download the ZIP archive and extract it to your web server's document root or a subdirectory.

### 3. Initial Setup (Web Interface)
Mindust now features a web-based setup wizard.

1.  **Create a Database (Manual Step)**:
    *   Using your database management tool (e.g., phpMyAdmin), create an empty database for Mindust (e.g., `mindust_db`).
    *   Ensure it uses `utf8mb4` character set and `utf8mb4_unicode_ci` collation for full Unicode support.
    *   **Note**: The web setup wizard will create the necessary tables within this database. You do not need to import `db/database.sql` manually if using the web setup.

2.  **Navigate to the Setup Wizard**:
    *   Deploy the `mindust` project files to your web server.
    *   Open your web browser and navigate to the `setup/` directory of your Mindust installation. For example:
        *   If Mindust is in the root: `http://yourdomain.com/setup/`
        *   If Mindust is in a subdirectory (e.g., `mindust`): `http://yourdomain.com/mindust/setup/`

3.  **Follow the On-Screen Instructions**:
    *   **Step 0: Welcome & Prerequisites**: The wizard will check for basic server requirements (PHP version, PDO extension, file permissions for `config.php` and `uploads/`). Resolve any critical issues reported before proceeding.
    *   **Step 1: Database Configuration**: Enter your database host, database name (created in step 3.1), username, and password. The wizard will test the connection.
    *   **Step 2: Admin User Creation**: Create your primary administrator account by providing a username, email, and password.
    *   **Step 3: Finalization**: The wizard will attempt to:
        *   Write the `config.php` file with your database details. If this fails due to permissions, it will provide the content for you to manually create the file in the project root.
        *   Create the necessary database tables (from `db/database.sql`).
        *   Insert your admin user details into the database.

4.  **CRITICAL - Delete Setup Directory**:
    *   After successful installation, the wizard will prompt you.
    *   **‼️ For security reasons, you MUST delete the entire `setup/` directory from your server immediately. Leaving this directory accessible poses a significant security risk.**
    *   The setup wizard also attempts to create an `installed.lock` file within the `setup/` directory to prevent re-runs, but deleting the directory is the most secure action.

### 4. Directory Permissions (Post-Setup Check)
*   Ensure the `uploads/` directory in the project root is writable by your web server. This is where post thumbnails will be stored. The setup wizard attempts to check/create this, but verify if issues arise.
    ```bash
    # Example (adjust for your server environment if needed):
    chmod -R 755 uploads/
    # chown -R www-data:www-data uploads/ # If needed
    ```

### 5. Access Your Site
*   **Public Site**: Access the blog via your browser (e.g., `http://localhost/mindust/` or `http://yourdomain.com/`).
*   **Admin Area**: Access the admin login page at `http://localhost/mindust/admin/admin_login.php` (or `http://yourdomain.com/admin/admin_login.php`).

*(The old `create_admin_setup.php` CLI script is no longer the primary setup method and will be removed or deprecated in future versions.)*

## ⚙️ CMS Usage
(This section remains largely the same as before, as admin functionalities haven't changed, only paths if they were incorrect in the previous README version.)

### Admin Area
After logging into the admin area (`/admin/admin_login.php`):
... (keep existing content for Dashboard, Manage Posts, Manage Categories, Manage Users) ...

### Public Site
... (keep existing content) ...

## 📂 Project Structure
(Update to include `setup/` directory and remove `create_admin_setup.php` from prominent display)
```
mindust/
├── admin/                    # Admin area files
│   ├── admin_dashboard.php
│   ├── admin_login.php
│   └── ... (other admin files)
├── assets/                   # CSS, JavaScript, etc.
├── db/                       # Database-related files
│   └── database.sql          # MySQL schema (used by setup wizard)
├── includes/                 # PHP include files
├── music/                    # Music files
├── setup/                    # NEW: Web-based installation wizard
│   ├── index.php
│   ├── SetupHelper.php
│   └── view_*.php
├── uploads/                  # User-uploaded files (thumbnails)
├── .gitignore
├── archive.php
├── config.php                # Generated by setup wizard
├── config.php.example
├── contact.php
├── copyright.php
├── index.php                 # Homepage
├── LICENSE
├── post.php
└── README.md                 # This file
```
*(Note: `create_admin_setup.php` is being replaced by the web setup wizard in the `setup/` directory.)*

## 🎨 Customization
(This section remains largely the same)
...

## 🛡 Security Considerations
*   **DELETE `setup/` DIRECTORY**: This is the most critical post-installation step. Delete the entire `setup/` directory immediately after successful installation.
*   **Strong Admin Credentials**: Use strong, unique passwords for all admin accounts.
*   **Regular Updates**: Keep your PHP, MySQL, and web server software up-to-date.
*   **File Permissions**: Ensure appropriate file permissions. Core files should not be writable by the web server user except for necessary directories like `uploads/`.
*   **HTTPS**: Deploy Mindust over HTTPS.
*   **Further Hardening**: For public-facing sites, consider comprehensive input validation, rate limiting, WAF, regular log reviews, and security audits.

## 🤝 Contributing
(This section remains the same)
...

## 📜 License
(This section remains the same)
...

---

*Originally created by druvx13. Significantly refactored and enhanced.*
