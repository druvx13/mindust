
---

# **Mindust**

**Mindust** is a lightweight PHP + MySQL-based blogging system. It delivers core blogging functionality in a simple, elegant interface — without the bloat of major platforms. Ideal for small personal blogs, writers, and devs who want control over their blog infrastructure.

---

## **Features**

- **Post Management**: Create, edit, and delete blog posts.
- **Media Support (Thumbnail only)**:  
  Each post supports **one thumbnail image**. Uploaded files are saved in the `uploads/` folder and linked to the post using the path:
  ```
  /uploads/your-image-name.jpg
  ```
- **Inbuilt Comment System**:  
  Users can comment on posts directly, enabling interaction and discussion.

- **Floating Music Button**:  
  A simple floating music button is included to play a **single music file** from the `/music` folder.

  > To change the track:  
  Rename your preferred file as required and update the filename reference manually **on each page** where the player is included.  
  The default music file is already included and is copyright-free.

- **Responsive Design**: Mobile-friendly layout with clean aesthetics.
- **Archive Page**: View posts organized by archive dates.
- **Contact Page**: Visitors can send you messages through a built-in contact form.
- **Customizable Copyright**

---

## **Technologies Used**

- PHP (Server-side logic)
- MySQL (Database)
- HTML, CSS & JavaScript (Frontend)
- Minimal third-party dependencies

---

## **Installation**

1. **Clone this repository:**

```bash
git clone https://github.com/druvx13/mindust.git
cd mindust
```

2. **Set up the database:**

- Create a MySQL database named `mindust`.
- Import the provided SQL schema from the `db/` folder (e.g., via phpMyAdmin or MySQL CLI).

3. **Configure the application:**

Edit `config.php` with your database credentials:

```php
$host = 'localhost';
$dbname = 'mindust';
$username = 'your_username';
$password = 'your_password';
```

4. **Run the project locally:**

- Place the project in your local server directory (`htdocs` or `www`).
- Open `http://localhost/mindust/index.php` in your browser.

---

## **Project Structure**

```
mindust/
├── db/                   # SQL schema and migration files
├── uploads/              # Stores uploaded post thumbnails
├── music/                # Music files (used by floating player)
├── index.php             # Homepage
├── post.php              # Displays a full post
├── create_post.php       # Admin post creation page
├── archive.php           # Archive list
├── contact.php           # Contact form
├── copyright.php         # License footer (editable)
├── config.php            # Database configuration
└── README.md             # This documentation
```

---

## **How Media Works**

- Only **one thumbnail** image per post is supported.
- The uploaded image gets stored in `/uploads/` and linked as:
  ```
  /uploads/image-name.jpg
  ```
- Ensure filenames are unique to avoid overwriting.

---

## **Music Button Usage**

- Located at the bottom corner, this button plays music from `/music`.
- To change the audio:
  - Replace the existing file or rename your file to match.
  - Manually update the filename on each page where it's included.

> **Note**: A copyright-free music file is already provided.

---

## **Customizations**

- **Comments**: Already enabled — no plugin needed.
- **Copyright**
  - Located in `copyright.php`.
  - You can freely update the footer information there.

---

## **License**

This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.  
See the [LICENSE](./LICENSE) file for full terms.

---

## **Contributions**

Feel free to fork, modify, and submit pull requests. Suggestions and improvements are welcome!

---
