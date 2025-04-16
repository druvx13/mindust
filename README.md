
---

# **Mindust**

**Mindust** is a lightweight, PHP + MySQL-based blogging system that offers essential blogging features in a clean, minimalist interface. Perfect for personal blogs, writers, and developers who prefer simplicity and control without the overhead of large platforms.

---

## **Key Features**

- **Post Management**: Easily create, edit, and delete blog posts.
- **Thumbnail Support**:  
  Each post supports a single thumbnail image, stored in the `uploads/` directory.
- **Built-in Comment System**:  
  Enables direct user interaction via comments.
- **Floating Music Button**:  
  Plays one audio file from the `/music` folder. The default track is copyright-free.
- **Responsive Design**:  
  Mobile-friendly layout with clean aesthetics.
- **Archive Page**:  
  View posts organized by date.
- **Contact Page**:  
  Includes a functional contact form for visitor messages.
- **Editable Footer**:  
  Fully customizable copyright.

---

## **Technology Stack**

- PHP (Backend)
- MySQL (Database)
- HTML, CSS, JavaScript (Frontend)
- Minimal third-party dependencies

---

## **Installation Guide**

1. **Clone the repository**

   ```bash
   git clone https://github.com/druvx13/mindust.git
   cd mindust
   ```

2. **Set up the database**

   - Create a MySQL database named `mindust`.
   - Import the SQL file from the `db/` directory using phpMyAdmin or the MySQL CLI.

3. **Configure the application**

   Update `config.php` with your database credentials:

   ```php
   $host = 'localhost';
   $dbname = 'mindust';
   $username = 'your_username';
   $password = 'your_password';
   ```

4. **Run the project locally**

   - Move the project to your server directory (`htdocs` or `www`).
   - Visit `http://localhost/mindust/index.php` in your browser.

---

## **Directory Structure**

```
mindust/
├── db/               # Database schema
├── uploads/          # Post thumbnails
├── music/            # Audio file for the music button
├── index.php         # Homepage
├── post.php          # Full post view
├── create_post.php   # Admin post creation
├── archive.php       # Archive page
├── contact.php       # Contact form
├── config.php        # DB configuration
├── copyright.php     # Footer content
└── README.md         # Documentation
```

---

## **Media & Music Usage**

- **Thumbnails**:  
  - One image per post, stored in `/uploads/`.
  - Use unique filenames to prevent overwriting.

- **Music Button**:  
  - Audio file must reside in `/music/`.
  - To change the track, replace or rename the file and update references on pages where it’s used.

---

## **Customization Tips**

- **Comments**: Pre-integrated, no plugin required.
- **Footer**: Update `copyright.php`.

---

## **License**

Licensed under the **GNU General Public License v3.0 (GPL-3.0)**.  
See [LICENSE](./LICENSE) for full terms.

---

## **Contributing**

Contributions are welcome! Fork the repository, make improvements, and submit a pull request.

---
