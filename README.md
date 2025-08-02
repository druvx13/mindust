# Mindust - A Minimalist PHP Blogging System

![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg?style=flat&logo=mysql)
![License](https://img.shields.io/badge/License-GPL--3.0-green.svg?style=flat)

**Mindust** is a lightweight, minimalist blogging system built with PHP and MySQL. It's designed for personal blogs, writers, and developers who appreciate simplicity, control, and a retro-modern aesthetic.

> **⚠️ Security Warning & Archived Status**
>
> This project is **archived** and **NOT recommended for production use without significant security hardening**. It contains known security vulnerabilities, including but not limited to:
>
> *   **Hardcoded Default Password**: The default password for post creation (`ChangeMeImmediately123!` in `create_post.php`) **MUST** be changed immediately after installation.
> *   **Basic Input Sanitization**: While some `htmlspecialchars` is used, the project may still be vulnerable to XSS and other injection attacks.
> *   **SQL Injection**: Review all database queries for potential SQL injection vulnerabilities if you intend to use or modify this code.
> *   **No CSRF Protection**: Forms lack CSRF tokens, making them vulnerable to cross-site request forgery.
>
> If you choose to use or learn from this code, do so with caution and at your own risk. It is primarily intended as a learning example or a base for a heavily modified, secured version.



> [!WARNING]
> **⚠️ सुरक्षा चेतावनी एवं संग्रहित स्थिति**
>
> यह परियोजना **संग्रहित** है और **बिना महत्वपूर्ण सुरक्षा सुदृढ़ीकरण के व्यावसायिक प्रयोग हेतु अनुशंसित नहीं है**। इसमें निम्नलिखित सहित परंतु इन्हीं तक सीमित नहीं, ज्ञात सुरक्षा कमज़ोरियाँ शामिल हैं:
>
> * **हार्डकोडेड डिफ़ॉल्ट पासवर्ड**: पोस्ट निर्माण हेतु डिफ़ॉल्ट पासवर्ड (`create_post.php` में `ChangeMeImmediately123!`) को स्थापना के तुरंत पश्चात् **परिवर्तित करना आवश्यक है**।
> * **मूलभूत इनपुट शुद्धिकरण**: यद्यपि कुछ स्थानों पर `htmlspecialchars` का उपयोग किया गया है, फिर भी यह परियोजना XSS तथा अन्य इंजेक्शन आक्रमणों के प्रति संवेदनशील हो सकती है।
> * **एसक्यूएल इंजेक्शन**: यदि आप इस कोड का उपयोग या संशोधन करना चाहते हैं, तो सभी डेटाबेस क्वेरीज़ में एसक्यूएल इंजेक्शन कमज़ोरियों की जाँच अवश्य करें।
> * **सीएसआरएफ़ सुरक्षा का अभाव**: फ़ॉर्म्स में सीएसआरएफ़ टोकन्स का अभाव है, जिससे क्रॉस-साइट रिक्वेस्ट फॉर्जरी के आक्रमण का ख़तरा रहता है।
>
> यदि आप इस कोड का उपयोग या अध्ययन करना चुनते हैं, तो सावधानीपूर्वक और अपने जोखिम पर करें। यह मुख्यतः एक शैक्षिक उदाहरण अथवा भारी संशोधन एवं सुरक्षित संस्करण हेतु आधार के रूप में निर्मित की गई है।


> [!WARNING]
> **⚠️ حِفاظت کی چِتاؤنی اور مُہر لَگا ہُوا حال**
>
> یہ کام **مُہر لَگا دِیا گیا ہے** اور **بِنا سَخت حِفاظتی اِنتِظام کِیے استِعمال کَرنے کی سِفارِش نہیں کی جاتی**۔ اِس میں حِفاظت کے مَعلوم خَطرے ہیں، جِن میں شامِل ہیں:
>
> * **مُہر بَند رازی نمبر**: پوسٹ بنانے کا رازی نمبر (`ChangeMeImmediately123!` فائل `create_post.php` میں) **فوراً بَدلنا ضَروری ہے**۔
> * **بُنیادی داخِلہ صفائی**: کُچھ جگہ `htmlspecialchars` کا استِعمال ہُوا ہے، مَگر پھر بھی یہ کوڈ XSS اور دوسرے زہریلے حَملوں کا شِکار ہو سَکتا ہے۔
> * **SQL میں زہر گھولنا**: اگر آپ اِس کوڈ کو استِعمال یا سُدھارنا چاہتے ہیں، تو سارے ڈیٹا بیس کے سوالات میں SQL زہر گھولنے کے خَطرے کی جانچ کریں۔
> * **CSRF سے حِفاظت نہیں**: فارمز میں CSRF کے خاص نشان نہیں ہیں، اِس لیے اِن پر ویب سائٹ کے باہر سے آنے والے جھوٹے حُکم کا حَملہ ہو سَکتا ہے۔
>
> اگر آپ اِس کوڈ کو استِعمال یا سیکھنے کا فیصلہ کریں، تو بہُت ہوشیاری سے کریں اور **اپنی ذِمّہ داری پر**۔ یہ کام اصل میں صرف سیکھنے کے نمونے یا پھر اِس قسم کے سنسْکارن کی بنیاد ہے جِس میں بہُت زیادہ سُدھار اور حِفاظتی قَدم اُٹھائے جائیں۔

## ✨ Features

*   **Simple Post Management**: Create, view, and (implicitly) manage blog posts.
*   **Password-Protected Post Creation**: Basic password protection for publishing new content.
*   **Built-in Comment System**: Allows readers to engage with posts.
*   **Thumbnail Support**: Each post can feature a main image.
*   **Markdown Support**: Write posts using Markdown for easy formatting (rendered client-side).
*   **Floating Music Player**: A non-intrusive music player for ambient background audio.
*   **Archive Page**: Browse all posts chronologically.
*   **Contact Page**: A simple contact form to receive messages.
*   **Customizable Footer & Legal Page**: Easily modify copyright and legal information.
*   **Responsive Design**: A clean, user-friendly layout that adapts to different screen sizes.

## 🛠 Tech Stack

*   **Backend**: PHP (7.4+)
*   **Database**: MySQL (5.7+)
*   **Frontend**: HTML5, Tailwind CSS (via CDN), Vanilla JavaScript, Font Awesome (via CDN), Marked.js (for Markdown rendering, via CDN)

## 🚀 Installation

Follow these steps to set up Mindust on your local server or web host that supports PHP and MySQL.

### 1. Prerequisites

*   A web server (e.g., Apache, Nginx) with PHP 7.4 or higher.
*   MySQL 5.7 or higher (or MariaDB equivalent).
*   Access to a MySQL database management tool (e.g., phpMyAdmin, MySQL CLI).
*   Git (optional, for cloning).

### 2. Clone the Repository

Clone this repository to your local machine or server:

```bash
git clone https://github.com/druvx13/mindust.git
cd mindust
```
Alternatively, download the ZIP file and extract it.

### 3. Database Setup

1.  **Create a Database**:
    *   Using your MySQL management tool, create a new database. For example, `mindust_db`.
    *   Ensure the database uses `utf8mb4` character set for full Unicode support.
2.  **Import the SQL Schema**:
    *   Import the `db/database.sql` file into your newly created database. This will create the necessary tables (`posts`, `comments`, `messages`).

    Example using MySQL CLI:
    ```bash
    mysql -u your_username -p your_database_name < db/database.sql
    ```
    Replace `your_username` and `your_database_name` accordingly.

### 4. Application Configuration

1.  **Configure Database Connection**:
    *   Rename or copy `config.php.example` to `config.php` (if `config.php` doesn't exist with placeholders).
    *   Edit `config.php` and update the following database credentials:
        ```php
        $host = 'YOUR_DB_HOST';         // e.g., 'localhost'
        $dbname = 'YOUR_DB_NAME';       // e.g., 'mindust_db'
        $username = 'YOUR_DB_USERNAME'; // e.g., 'root' or your specific DB user
        $password = 'YOUR_DB_PASSWORD'; // Your database password
        ```

2.  **‼️ Change Post Creation Password (CRITICAL SECURITY STEP)**:
    *   Open `create_post.php`.
    *   Locate the line: `if ($password !== 'ChangeMeImmediately123!') {`
    *   **Change `'ChangeMeImmediately123!'` to a strong, unique password that you will remember.** This password is required to publish new posts.
    *   **Failure to change this default password poses a significant security risk.**

### 5. Directory Permissions (If Applicable)

Ensure the `uploads/` directory is writable by your web server. This is where post thumbnails will be stored.
```bash
chmod -R 755 uploads/ # Or 775 depending on your server setup
# You might also need to set ownership: chown -R www-data:www-data uploads/
```

### 6. Run the Project

*   Move the entire `mindust` project folder to your web server's document root (e.g., `htdocs/`, `www/`, `public_html/`).
*   Open your web browser and navigate to the project's URL (e.g., `http://localhost/mindust/` or `http://yourdomain.com/mindust/`).

## ⚙️ Usage

*   **Homepage (`index.php`)**: Displays the latest posts, a sidebar with author info and random quotes, and a "New Post" button.
*   **Creating Posts**:
    *   Click the "New Post" button on the homepage.
    *   Fill in the title, category, content (Markdown is supported), and upload a thumbnail.
    *   Enter the **post creation password** you set in `create_post.php`.
    *   Click "Publish".
*   **Viewing Posts (`post.php`)**: Click "Read More" on any post to view its full content and comments.
*   **Commenting**: Visitors can add comments to posts.
*   **Archive (`archive.php`)**: Lists all published posts.
*   **Contact (`contact.php`)**: A form for visitors to send messages (stored in the `messages` table).
*   **Legal (`copyright.php`)**: Displays copyright and terms of use information.

## 📂 Project Structure

```
mindust/
├── assets/
│   ├── css/
│   │   └── style.css         # Main stylesheet
│   └── js/
│       └── main.js           # Main JavaScript file
├── db/
│   ├── database.sql          # MySQL database schema
│   └── .gitkeep              # Ensures db directory is tracked by Git
├── includes/
│   ├── footer.php            # Site footer
│   ├── head.php              # HTML head section (meta, CSS links)
│   ├── header.php            # Site header and navigation
│   ├── mobile_menu.php       # Mobile navigation menu
│   ├── music_toggle.php      # Floating music player
│   ├── post_modal.php        # Modal for creating new posts
│   └── sidebar.php           # Sidebar content
├── music/
│   ├── Heavenly - Aakash Gandhi.mp3  # Default music file
│   └── .gitkeep              # Ensures music directory is tracked
├── uploads/                  # Directory for post thumbnails (must be writable)
│   ├── default.jpg           # Default thumbnail if none uploaded
│   └── .gitkeep              # Ensures uploads directory is tracked
├── archive.php               # Displays all posts
├── config.php                # Database and application configuration
├── contact.php               # Contact form page
├── copyright.php             # Legal/copyright information page
├── create_post.php           # Handles new post creation (server-side)
├── index.php                 # Homepage
├── LICENSE                   # Project license (GPL-3.0)
├── post.php                  # Displays a single post and comments
└── README.md                 # This file
```

## 🎨 Customization

*   **Appearance**:
    *   Modify CSS variables and styles in `assets/css/style.css`.
    *   Tailwind CSS utility classes are used throughout the HTML; you can adjust these directly in the PHP files or includes.
*   **Content**:
    *   **Site Title/Logo**: Change in `includes/header.php` (the "Mind Dust" text).
    *   **About the Writer/Social Links**: Edit `includes/sidebar.php`.
    *   **Random Quotes**: Modify the `$quotes_array` in `index.php` (for initial server-side quote) and the `quotes` array in `assets/js/main.js` (for client-side updates).
    *   **Footer Text**: Update `includes/footer.php`.
    *   **Legal Information**: Edit `copyright.php`.
*   **Music**:
    *   To change the music track, replace `music/Heavenly - Aakash Gandhi.mp3` with your desired MP3 file.
    *   Update the `<source src="...">` path in `includes/music_toggle.php` if you change the filename or location.
*   **Post Categories**: Modify the `<option>` values in the select dropdown within `includes/post_modal.php`.

## 🤝 Contributing

While this project is archived, contributions for learning purposes or significant security improvements might be considered. If you wish to contribute:

1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/YourImprovement`).
3.  Make your changes.
4.  Commit your changes (`git commit -m 'Add some YourImprovement'`).
5.  Push to the branch (`git push origin feature/YourImprovement`).
6.  Open a Pull Request.

Please ensure any contributions address security concerns if applicable and maintain the minimalist philosophy of the project.

## 📜 License

This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.
See the [LICENSE](./LICENSE) file for full details.

---

*Originally created by druvx13. Maintained and refactored for clarity and structural improvements.*
