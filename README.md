# SchoolConnect — Setup Guide
## XAMPP / phpMyAdmin Installation

### 1. Folder Structure
Copy the entire `schoolconnect/` folder into your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\schoolconnect\
├── index.php               ← Main application (all pages)
├── config/
│   └── db.php              ← Database connection & helpers
├── actions/
│   ├── insert.php          ← POST handlers (register, events, attendance, login)
│   └── fetch.php           ← GET/JSON data endpoints
├── database/
│   └── schoolconnect.sql   ← Run this in phpMyAdmin
└── uploads/                ← Create this folder, chmod 755
```

### 2. Create the `uploads/` folder
```bash
mkdir C:\xampp\htdocs\schoolconnect\uploads
```
Make sure it is **writable** by the web server.

### 3. Import the Database
1. Open **http://localhost/phpmyadmin**
2. Click **Import** (top menu)
3. Choose `database/schoolconnect.sql`
4. Click **Go**

This creates the `schoolconnect` database with all tables and seeds sample events.

### 4. Configure DB credentials (if needed)
Edit `config/db.php` — change `DB_USER` / `DB_PASS` if your MySQL has a password:
```php
define('DB_USER', 'root');
define('DB_PASS', '');   // ← set your password here
```

### 5. Open the App
Visit: **http://localhost/schoolconnect/**

---

## Default Admin Login
| Username | Password    |
|----------|-------------|
| admin    | Admin@1234  |

> Change this immediately in phpMyAdmin by running:
> ```sql
> UPDATE admin_users SET password = '$2y$12$YOUR_BCRYPT_HASH' WHERE username = 'admin';
> ```
> Generate a new hash with PHP: `echo password_hash('YourNewPass', PASSWORD_BCRYPT);`

---

## How the QR Code Works
The Admin Dashboard has a **QR Code** button that generates a QR pointing to:
```
http://localhost/schoolconnect/?page=attendance
```
Parents scan this and are taken directly to the **Attendance & Visitation** sign-in page.

---

## Security Features
- All database queries use **PDO prepared statements** (SQL injection prevention)
- File uploads are validated by **MIME type** (not extension), max 5 MB
- Admin routes check **PHP session** (`$_SESSION['admin_id']`)
- Passwords stored as **bcrypt hashes** (`password_hash` / `password_verify`)
- Duplicate phone number check on registration

---

## Pages
| Page | URL | Description |
|------|-----|-------------|
| Home | `/?page=home` | Landing page with upcoming events & stats |
| Register | `/?page=register` | Parent + student registration with photos |
| Attendance | `/?page=attendance` | Self sign-in by phone number |
| Admin | `/?page=admin` | Protected dashboard (login required) |
