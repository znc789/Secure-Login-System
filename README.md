# Secure-Login-System

🔐 Secure Login System

A secure web-based authentication system developed using PHP, MySQL, HTML, CSS, and JavaScript. This project demonstrates secure authentication practices, including password hashing, session management, CSRF protection, and defense against SQL injection.

## Setup

1. Create the database:
   ```bash
   mysql -u root -p < setup.sql
   ```
2. Configure `config.php` with your MySQL credentials.
3. Deploy the project to a PHP-enabled web server.
4. Open `register.php` in your browser to create the first account.

## Files

- `config.php` — database connection settings
- `init.php` — session handling, CSRF helpers, and shared utilities
- `register.php` — user registration form and account creation
- `login.php` — login form and authentication
- `dashboard.php` — protected user dashboard
- `logout.php` — session logout
- `styles.css` — UI styling
- `setup.sql` — database creation script

## Security Features

- password hashing with `password_hash`
- prepared statements for all database queries
- CSRF token generation and validation
- session cookie settings with `HttpOnly` and `SameSite`
- session fixation protection via `session_regenerate_id`
- session timeout and user agent verification

