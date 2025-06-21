# Mindust - A Minimalist PHP Blogging System (CMS Phase 1)

![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg?style=flat&logo=mysql)
![License](https://img.shields.io/badge/License-GPL--3.0-green.svg?style=flat)

**Mindust** is a lightweight, minimalist blogging system built with PHP and MySQL, now with foundational CMS capabilities. It's designed for personal blogs, writers, and developers who appreciate simplicity and control.

> **⚠️ Security Warning & Development Status**
>
> This project, while enhanced with basic CMS features and security improvements like CSRF protection, is still primarily a learning tool or a base for further development. **It is NOT recommended for production use on a live, untrusted internet environment without a thorough security audit and further hardening.**
>
> Key improvements made:
> *   **Admin Authentication**: Secure login for administrators.
> *   **Post Management**: Admins can create, edit, and delete posts.
> *   **CSRF Protection**: Implemented on all forms.
>
> However, always assume vulnerabilities may exist. If you choose to use or learn from this code, do so with caution and at your own risk.

## ✨ Features

*   **Admin Area**:
    *   Secure admin login (`admin_login.php`).
    *   Admin dashboard (`admin_dashboard.php`).
    *   Full post management: Create, Edit, List, and Delete posts (`admin_manage_posts.php`).
*   **Public Frontend**:
    *   Displays blog posts with thumbnails and Markdown rendering.
    *   Built-in comment system per post.
    *   Floating music player.
    *   Archive page, Contact page, Legal page.
    *   Responsive design.
*   **Security**:
    *   CSRF protection on all forms.
    *   Password hashing for admin accounts.
    *   Basic input sanitization and output encoding.

## 🛠 Tech Stack

*   **Backend**: PHP (7.4+)
*   **Database**: MySQL (5.7+) / MariaDB
*   **Frontend**: HTML5, Tailwind CSS (via CDN), Vanilla JavaScript, Font Awesome (via CDN), Marked.js (for Markdown rendering, via CDN)

## 🚀 Installation & Setup

### 1. Prerequisites

*   Web server (Apache, Nginx, etc.) with PHP 7.4+ (ensure `pdo_mysql` extension is enabled).
*   MySQL 5.7+ or MariaDB.
*   Database management tool (phpMyAdmin, MySQL CLI, etc.).
*   Git (for cloning).

### 2. Clone Repository

```bash
git clone https://github.com/druvx13/mindust.git
cd mindust
```
Or download and extract the ZIP.

### 3. Database Setup

1.  **Create Database**: Create a new database (e.g., `mindust_cms_db`) using `utf8mb4` character set and `utf8mb4_unicode_ci` collation.
2.  **Import Schema**: Import `db/database.sql`. This creates `posts`, `comments`, `messages`, and the new `admins` table.
    ```bash
    mysql -u your_username -p your_database_name < db/database.sql
    ```

### 4. Application Configuration

1.  **`config.php`**:
    *   If `config.php` doesn't exist, copy or rename `config.php.example` to `config.php`.
    *   Edit `config.php` with your database credentials:
        ```php
        $host = 'YOUR_DB_HOST';
        $dbname = 'YOUR_DB_NAME';
        $username = 'YOUR_DB_USERNAME';
        $password = 'YOUR_DB_PASSWORD';
        ```
2.  **Create Initial Admin User (CRITICAL)**:
    *   Navigate to the project root in your command line (terminal).
    *   Run the setup script: `php create_admin_setup.php`
    *   Follow the prompts to create your admin username, email, and password.
    *   **‼️ IMPORTANT: Delete the `create_admin_setup.php` file from your server immediately after creating the admin user.** Leaving this script accessible is a major security risk.

### 5. File Structure & Permissions

*   **Admin Files**: It's highly recommended to move the admin-related files (`admin_login.php`, `admin_dashboard.php`, `admin_manage_posts.php`, `admin_logout.php`) into a dedicated `admin/` subdirectory within your project root for better organization and potential additional security (e.g., via web server configuration). If you do this, you'll need to adjust paths in `require_once`, `include`, and `header("Location: ...")` calls accordingly.
    *   *Note: The agent created these files in the root due to sandbox limitations during development. Manual relocation is advised.*
*   **`uploads/` Directory**: Ensure this directory is writable by your web server for post thumbnails.
    ```bash
    # Example permissions (adjust as needed for your server environment)
    # If admin files are moved to admin/
    # mkdir admin
    # mv admin_*.php admin/
    chmod -R 755 uploads/
    # chown -R www-data:www-data uploads/ # If needed
    ```

### 6. Run the Project

*   Deploy the `mindust` project folder to your web server's document root.
*   Access via browser (e.g., `http://localhost/mindust/`).
*   Access the admin area (e.g., `http://localhost/mindust/admin_login.php` or `http://localhost/mindust/admin/login.php` if moved).

## ⚙️ CMS Usage

### Admin Area

1.  **Login**: Access `admin_login.php` (or `admin/login.php`) and log in with the credentials created during setup.
2.  **Dashboard (`admin_dashboard.php`)**: Provides an overview and navigation to management sections.
3.  **Manage Posts (`admin_manage_posts.php`)**:
    *   **List Posts**: View all existing posts with options to View (public link), Edit, or Delete.
    *   **Create Post**: Click "Create New Post". Fill in the title, category, content (Markdown supported), and optionally upload a thumbnail.
    *   **Edit Post**: Click "Edit" for a specific post. Modify details and thumbnail as needed.
    *   **Delete Post**: Click "Delete" and confirm. This removes the post and its associated thumbnail (if not the default image). Comments are deleted via database cascade.

### Public Site

*   **Homepage (`index.php`)**: Displays latest posts. "Create Post (Admin)" link now directs to the admin area.
*   **Post View (`post.php`)**: Full post content and comment section.
*   Other pages (`archive.php`, `contact.php`, `copyright.php`) function as before.

## 📂 Updated Project Structure (Recommended)

```
mindust/
├── admin/                    # RECOMMENDED: Move admin_*.php files here
│   ├── login.php             # (was admin_login.php)
│   ├── dashboard.php         # (was admin_dashboard.php)
│   ├── manage_posts.php      # (was admin_manage_posts.php)
│   └── logout.php            # (was admin_logout.php)
├── assets/
│   ├── css/
│   │   ├── style.css         # Main public stylesheet
│   │   └── admin_style.css   # Admin area specific styles
│   └── js/
│       └── main.js           # Main JavaScript
├── db/
│   ├── database.sql          # Updated MySQL schema (includes admins table)
│   └── .gitkeep
├── includes/
│   ├── footer.php
│   ├── head.php
│   ├── header.php
│   ├── mobile_menu.php
│   ├── music_toggle.php
│   ├── post_modal.php        # (Now likely unused by index.php)
│   ├── sidebar.php
│   ├── csrf_helper.php       # CSRF token functions
│   ├── admin_header_inc.php  # Admin area header/nav
│   └── admin_footer_inc.php  # Admin area footer
├── music/
│   └── Heavenly - Aakash Gandhi.mp3
│   └── .gitkeep
├── uploads/                  # (Must be writable)
│   ├── default.jpg
│   └── .gitkeep
├── archive.php
├── config.php                # Database configuration
├── contact.php
├── copyright.php
├── create_admin_setup.php    # SCRIPT TO BE DELETED AFTER USE
├── create_post.php           # Now handles submissions from admin form
├── index.php
├── LICENSE
├── post.php
└── README.md                 # This file
```
*(Files like `admin_login.php`, `admin_dashboard.php`, etc. were created in root by the agent due to sandbox `mkdir` issues. Manually move them to an `admin/` directory for better structure and update paths accordingly.)*

## 🎨 Customization

(Similar to previous version, but note new `admin_style.css` and admin includes)

*   **Appearance**:
    *   Public: `assets/css/style.css`.
    *   Admin: `assets/css/admin_style.css`.
*   **Content**:
    *   Public: `includes/header.php`, `includes/sidebar.php`, `includes/footer.php`, `copyright.php`.
    *   Admin: `includes/admin_header_inc.php`, `includes/admin_footer_inc.php`.
*   **Music & Categories**: Same as before.

## 🛡 Security Considerations (Recap)

*   **DELETE `create_admin_setup.php` AFTER USE.**
*   Regularly update admin passwords.
*   Keep PHP and MySQL versions up-to-date.
*   Implement further hardening if deploying publicly: more robust input validation, file upload checks, HTTPS, web server security configurations, etc.
*   CSRF protection is now implemented. Review and ensure its robustness for any new forms.

## 🤝 Contributing & License

(Same as previous version)

---

*Originally created by druvx13. Significantly refactored and enhanced by AI for CMS functionality, security, and clarity.*
