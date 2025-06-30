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
*   **CSRF Protection**: Safeguards against Cross-Site Request Forgery attacks.
*   **Password Hashing**: Uses modern password hashing techniques for admin accounts.
*   **Input Sanitization & Output Encoding**: Basic measures to prevent XSS and other injection attacks.
*   **Prepared Statements**: SQL queries use prepared statements to prevent SQL injection.

## 🛠 Tech Stack

*   **Backend**: PHP (7.4 or newer recommended)
*   **Database**: MySQL (5.7 or newer) / MariaDB (10.2 or newer)
*   **Frontend**: HTML5, Tailwind CSS (via CDN), Vanilla JavaScript
*   **Libraries**: Font Awesome (icons, via CDN), Marked.js (Markdown rendering, via CDN)

## 🚀 Installation & Setup

### 1. Prerequisites
*   A web server (e.g., Apache, Nginx) with PHP 7.4+ installed and the `pdo_mysql` extension enabled.
*   MySQL 5.7+ or MariaDB 10.2+.
*   A database management tool (e.g., phpMyAdmin, MySQL CLI).
*   Git (optional, for cloning the repository).

### 2. Clone or Download
```bash
git clone https://github.com/druvx13/mindust.git
cd mindust
```
Alternatively, download the ZIP archive and extract it to your desired location.

### 3. Database Setup
1.  **Create a Database**: Using your database management tool, create a new database (e.g., `mindust_db`). Ensure it uses `utf8mb4` character set and `utf8mb4_unicode_ci` collation for full Unicode support.
2.  **Import Schema**: Import the `db/database.sql` file into your newly created database. This script will set up the required tables (`admins`, `posts`, `categories`, `comments`, `messages`).
    ```bash
    mysql -u YOUR_USERNAME -p YOUR_DATABASE_NAME < db/database.sql
    ```
    Replace `YOUR_USERNAME` and `YOUR_DATABASE_NAME` accordingly.

### 4. Application Configuration
1.  **Configure `config.php`**:
    *   If `config.php` does not exist at the root of the project, rename or copy `config.php.example` to `config.php`.
    *   Open `config.php` and update the database credentials with your settings:
        ```php
        $host = 'YOUR_DB_HOST';        // e.g., 'localhost'
        $dbname = 'YOUR_DB_NAME';      // e.g., 'mindust_db'
        $username = 'YOUR_DB_USERNAME';  // e.g., 'root'
        $password = 'YOUR_DB_PASSWORD';  // Your database password
        ```
2.  **Create Initial Admin User (CRITICAL)**:
    *   Navigate to the project root in your command line terminal.
    *   Run the setup script using PHP CLI:
        ```bash
        php create_admin_setup.php
        ```
    *   Follow the on-screen prompts to create your first admin username, email, and password.
    *   **‼️ IMPORTANT: For security reasons, you MUST delete the `create_admin_setup.php` file from your server immediately after successfully creating the admin user. Leaving this script accessible poses a significant security risk.**

### 5. Directory Permissions
*   Ensure the `uploads/` directory in the project root is writable by your web server. This directory is used for storing post thumbnails.
    ```bash
    # Example (adjust for your server environment if needed):
    chmod -R 755 uploads/
    # You might also need to set ownership, e.g., for Apache:
    # chown -R www-data:www-data uploads/
    ```

### 6. Deployment & Access
*   Deploy the entire `mindust` project folder to your web server's document root (e.g., `htdocs`, `www`, `public_html`).
*   **Public Site**: Access the blog via your browser (e.g., `http://localhost/mindust/` or `http://yourdomain.com/`).
*   **Admin Area**: Access the admin login page at `http://localhost/mindust/admin/admin_login.php` (or `http://yourdomain.com/admin/admin_login.php`).

## ⚙️ CMS Usage

### Admin Area
After logging into the admin area (`/admin/admin_login.php`):

1.  **Dashboard (`admin_dashboard.php`)**:
    *   Provides an overview of site statistics (total posts, comments, etc.).
    *   Offers quick links to key management sections.

2.  **Manage Posts (`admin_manage_posts.php`)**:
    *   **List Posts**: View all blog posts with options to View (public link), Edit, or Delete.
    *   **Create Post**: Click "Create New Post" (or navigate via `?action=create`). Fill in the title, select a category, write content using Markdown, and optionally upload a thumbnail image.
    *   **Edit Post**: Modify existing post details, content, category, and thumbnail.
    *   **Delete Post**: Remove a post and its associated thumbnail (if not the default image). Comments associated with the post are also deleted (via database cascade).

3.  **Manage Categories (`admin_manage_categories.php`)**:
    *   **List Categories**: View all categories, their slugs, and post counts.
    *   **Create Category**: Add new categories. A URL-friendly "slug" will be automatically generated.
    *   **Edit Category**: Update category names. Slugs are regenerated if the name changes significantly.
    *   **Delete Category**: Remove categories. Deletion is prevented if a category is currently assigned to any posts.

4.  **Manage Users (`admin_manage_users.php`)**:
    *   **List Users**: View all admin users, their roles, and status.
    *   **Create User**: Add new admin users, assign roles (currently 'admin'), and set their status (active/inactive).
    *   **Edit User**: Update user details like username, email, role, and status.
    *   **Delete User**: Remove admin users. Safety checks prevent self-deletion and deletion of the last active admin.

### Public Site
*   **Homepage (`index.php`)**: Displays the latest blog posts.
*   **Post View (`post.php?id=...`)**: Shows the full content of a single post and its comment section.
*   **Archive (`archive.php`)**: Lists all posts, typically in reverse chronological order.
*   **Contact (`contact.php`)**: A page for visitor inquiries (form submissions are stored in the `messages` table).
*   **Legal (`copyright.php`)**: Displays copyright or other legal information.

## 📂 Project Structure

```
mindust/
├── admin/                    # Admin area files
│   ├── admin_dashboard.php
│   ├── admin_login.php
│   ├── admin_logout.php
│   ├── admin_manage_categories.php
│   ├── admin_manage_posts.php
│   └── admin_manage_users.php
├── assets/                   # CSS, JavaScript, etc.
│   ├── css/
│   │   ├── style.css         # Main public stylesheet
│   │   └── admin_style.css   # Admin area specific styles
│   └── js/
│       └── main.js           # Main JavaScript
├── db/                       # Database-related files
│   ├── database.sql          # MySQL schema
│   └── .gitkeep
├── includes/                 # PHP include files (headers, footers, helpers)
│   ├── footer.php
│   ├── head.php
│   ├── header.php
│   ├── mobile_menu.php
│   ├── music_toggle.php
│   ├── sidebar.php
│   ├── csrf_helper.php       # CSRF token functions
│   ├── admin_header_inc.php  # Admin area header/navigation
│   └── admin_footer_inc.php  # Admin area footer
├── music/                    # Music files for the player
│   ├── Heavenly - Aakash Gandhi.mp3
│   └── .gitkeep
├── uploads/                  # User-uploaded files (e.g., post thumbnails)
│   ├── default.jpg           # Default thumbnail
│   └── .gitkeep
├── .gitignore
├── archive.php
├── config.php                # Database & application configuration
├── config.php.example        # Example configuration file
├── contact.php
├── copyright.php
├── create_admin_setup.php    # SCRIPT TO BE DELETED AFTER INITIAL SETUP
├── index.php                 # Homepage
├── LICENSE                   # Project license (GPL-3.0)
├── post.php                  # Single post view
└── README.md                 # This file
```

## 🎨 Customization

*   **Appearance**:
    *   Public site styles: `assets/css/style.css`
    *   Admin area styles: `assets/css/admin_style.css`
*   **Layout & Static Content**:
    *   Public site header: `includes/header.php`
    *   Public site footer: `includes/footer.php`
    *   Public site sidebar: `includes/sidebar.php`
    *   Admin area header/navigation: `includes/admin_header_inc.php`
    *   Admin area footer: `includes/admin_footer_inc.php`
    *   Static pages like `copyright.php` can be directly edited.
*   **Music Player**: Change the MP3 file in the `music/` directory and update the path in `includes/music_toggle.php` if necessary.
*   **Categories for Posts**: Categories are managed via the admin area. The initial set of categories in `db/database.sql` can be modified or expanded upon.

## 🛡 Security Considerations

*   **Delete `create_admin_setup.php`**: This is critical. Delete this file immediately after creating your initial admin user.
*   **Strong Admin Credentials**: Use strong, unique passwords for all admin accounts.
*   **Regular Updates**: Keep your PHP, MySQL, and web server software up-to-date to patch known vulnerabilities.
*   **File Permissions**: Ensure file and directory permissions are set correctly. Core files should not be writable by the web server user where possible, except for necessary directories like `uploads/`.
*   **HTTPS**: Deploy Mindust over HTTPS to encrypt data in transit.
*   **Further Hardening (for public-facing sites)**:
    *   Implement more comprehensive input validation and output encoding.
    *   Add rate limiting for login attempts.
    *   Consider a Web Application Firewall (WAF).
    *   Regularly review server and application logs for suspicious activity.
    *   Conduct security audits if deploying in a production environment.

## 🤝 Contributing

Contributions, bug reports, and feature requests are welcome! Please feel free to open an issue or submit a pull request on the project's GitHub repository.

## 📜 License

Mindust is open-source software licensed under the [GPL-3.0 license](https://www.gnu.org/licenses/gpl-3.0.html).

---

*Originally created by druvx13. Significantly refactored and enhanced for CMS functionality, security, and clarity.*
