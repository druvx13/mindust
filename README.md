## IMPORTANT Notice!!!

> [!CAUTION]
> This repository has many security vulnerabilities that's why i am archiving it. But you can still use it if you want according to the [license](./LICENSE)


> **⚠️ Found a bug or issue?**  
> If you encounter or find any error, **do not hesitate to report it in the [Issues](../../issues) section**.  
> This helps me identify and fix problems more effectively. Thank you!

---

# **Mindust**  
![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg?style=flat&logo=php)  
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg?style=flat&logo=mysql)  
![License](https://img.shields.io/badge/License-GPL--3.0-green.svg?style=flat)  
![Status](https://img.shields.io/badge/Status-Active-brightgreen.svg?style=flat)  

**Mindust** is a minimalist PHP + MySQL blogging system that delivers essential features without the complexity of major platforms. Ideal for personal blogs, writers, and developers who seek simplicity and control.  

---

## ✨ **Features**  

✔ **Post Management** – Create, edit, and delete blog posts.  
✔ **Password-Protected Post Creation** – A password is required to publish posts.  
✔ **Built-in Comment System** – Enables user interactions through comments.  
✔ **Thumbnail Support** – Each post supports a single image.  
✔ **Floating Music Button** – Plays one audio file from `/music/`.  
✔ **Archive Page** – View posts by date.  
✔ **Contact Page** – Includes a simple contact form.  
✔ **Customizable Footer** – Easily modify copyright information.  
✔ **Mobile-Responsive** – Clean, user-friendly layout.  

---

## 🛠 **Tech Stack**  

![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg?style=flat&logo=php)  
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg?style=flat&logo=mysql)  
![HTML](https://img.shields.io/badge/HTML5-FF5733.svg?style=flat&logo=html5)  
![CSS](https://img.shields.io/badge/CSS3-2962FF.svg?style=flat&logo=css3)  
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E.svg?style=flat&logo=javascript)  

---

## 🚀 **Installation**  

### 1️⃣ Clone the repository  

```bash
git clone https://github.com/druvx13/mindust.git
cd mindust
```

### 2️⃣ Set up the database  

- Create a MySQL database named **`mindust`**.  
- Import the SQL file from the **`db/`** folder using phpMyAdmin or the MySQL CLI.  

### 3️⃣ Configure the application  

Edit **`config.php`** with your database credentials:  

```php
$host = 'localhost';
$dbname = 'mindust';
$username = 'your_username';
$password = 'your_password';
```

### 4️⃣ Set the post creation password  

Modify **`create_post.php`** to set a password for publishing posts:  

```php
if ($password !== 'your-password') {
    $errors[] = 'Incorrect password.';
}
```

> **Note:** This password must be entered when creating a post via the form in `index.php`.  

### 5️⃣ Run the project locally  

- Move the project to your local server directory (`htdocs` or `www`).  
- Open `http://localhost/mindust/index.php` in your browser.  

---

## 📂 **Project Structure**  

```plaintext
mindust/
├── db/               # Database schema
├── uploads/          # Post thumbnails
├── music/            # Music files for floating player
├── index.php         # Homepage + post creation form
├── post.php          # Full post view
├── create_post.php   # Admin post creation handler
├── archive.php       # Archive page
├── contact.php       # Contact form
├── config.php        # Database configuration
├── copyright.php     # Editable footer
└── README.md         # Documentation
```

---

## 🎵 **Media & Music Usage**  

- **Thumbnails**:  
  - Each post supports one image stored in `/uploads/`.  

- **Floating Music Button**:  
  - Plays an audio file from `/music/`.  
  - To change the track, replace or rename the file and update references where needed.  

---

## 🎨 **Customization**  

- **Comments** – Already integrated, no plugin required.  
- **Footer** – Modify `copyright.php`.  
- **Post Creation Access** – Change the password in `create_post.php`.  

---

## 📜 **License**  

This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.  
See the [LICENSE](./LICENSE) file for full details.  

---

## 🤝 **Contributing**  

[![PRs Welcome](https://img.shields.io/badge/PRs-Welcome-brightgreen.svg?style=flat)](https://github.com/druvx13/mindust/pulls)  
Contributions are welcome! Fork the repo, make improvements, and submit a pull request.  

---
