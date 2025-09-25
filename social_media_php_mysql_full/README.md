Social Media PHP + MySQL (No frameworks) - Ready project

How to run:
1. Place the contents of this folder in your web server root (e.g., XAMPP htdocs/social_media_php_mysql).
2. Import sql/schema.sql into MySQL:
   - mysql -u root -p < sql/schema.sql
   or use phpMyAdmin to import.
3. Edit config.php DB credentials if needed.
4. Make sure 'uploads/' directory is writable by the web server.
5. Ensure PHP extensions: mysqli, fileinfo, gd (optional) are enabled.
6. Visit http://localhost/social_media_php_mysql/register.php to create an account.

Files:
- config.php : DB connection, session, csrf
- functions.php : helpers + upload helper
- index.php : feed and create post
- register.php, login.php, logout.php
- post.php : single post + comments
- profile.php, edit_profile.php
- like.php : toggle like
- uploads/ : uploaded files
- assets/ : css/js
- sql/schema.sql : database schema

This is a minimal but working starter. For production, add stronger validation, input sanitization, rate-limiting, and CSRF improvements.
