# Social Media Platform (PHP + MySQL)

A simple social media web application built with **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript** — without using any frameworks.  
Users can create accounts, share posts, like, comment, and interact with each other in a minimal but functional environment.

---

## 🚀 Features

- User authentication (register, login, logout, session management).
- User profile with editable display name, bio, and avatar.
- Create, edit, and delete posts (with image/video upload).
- Like and comment on posts.
- Post tagging system for categorization.
- Optional: follow/unfollow users.
- Optional: notifications for likes, comments, and new followers.
- Explore trending content.
- Secure with CSRF tokens, input sanitization, and prepared SQL statements.
- Clean responsive UI with modern CSS.

---

## 🛠️ Tech Stack

- **Backend:** PHP (no frameworks, raw PHP)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Styling:** Custom CSS (responsive and modern design)

---

## 📂 Project Structure

social_media_php_mysql/
│── assets/
│ ├── style.css # Main CSS styles
│ └── script.js # Client-side scripts (if any)
│
│── uploads/ # User uploaded images/videos
│── config.php # Database connection & helpers
│── functions.php # Common reusable functions
│── index.php # Homepage / feed
│── login.php # Login form
│── register.php # User registration
│── logout.php # Logout
│── profile.php # User profile page
│── edit_profile.php # Profile editing
│── post.php # Single post view
│── new_post.php # Create a new post
│── like.php # Handle likes
│── comment.php # Handle comments
│── explore.php # Trending/explore page
│── notifications.php # User notifications (optional)
│── database.sql # Database schema (import into MySQL)


---

## ⚙️ Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/social_media_php_mysql.git

Import the database:

Open phpMyAdmin or MySQL CLI.

Create a database:

CREATE DATABASE social_media;


Import the database.sql file into it.

Configure the database connection in config.php:

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'social_media';


Start your PHP server (e.g., with XAMPP or MAMP) and place the project in htdocs.

Visit in your browser:

http://localhost/social_media_php_mysql/

🔐 Security Notes

All SQL queries use prepared statements (SQL injection protection).

CSRF tokens are implemented for form submissions.

Passwords should be hashed using password_hash() and verified with password_verify().
