# SchoolConnect

A PHP/MySQL school management platform connecting parents, students, and staff.

---

## Quick Start

1. Copy the `schoolconnect/` folder into your XAMPP `htdocs` directory.
2. Import `database/schoolconnect.sql` into phpMyAdmin (creates and seeds the database).
3. Visit `http://localhost/schoolconnect/`.

**Upgrading an existing install?** Run `database/migration_v2.sql` instead.

---

## Default Credentials

| Role                     | Username             | Password           | Login Page         |
|--------------------------|----------------------|--------------------|--------------------|
| Admin                    | `admin`              | `Admin@1234`       | `admin.php`        |
| Head of Domestic Affairs | `domestic_affairs`   | `Domestic@1234`    | `domestic.php`     |
| Male House Parent        | `houseparent_male`   | `HouseParent@1234` | `houseparent.php`  |
| Female House Parent      | `houseparent_female` | `HouseParent@1234` | `houseparent.php`  |

> **Change all passwords immediately after first login** using the Account Settings tab in each portal.

---

## Pages & Portals

| URL                  | Who Uses It       | Description                                        |
|----------------------|-------------------|----------------------------------------------------|
| `index.php`          | Everyone          | Home page with stats, events, and registry         |
| `register.php`       | Parents           | Register parent + ward(s)                          |
| `attendance.php`     | Parents           | Sign attendance for events / walk-in visits        |
| `exeat.php`          | Parents           | Submit an off-campus permission request            |
| `admin.php`          | Admin             | Full dashboard: parents, events, attendance        |
| `domestic.php`       | Domestic Affairs  | Review/approve exeat requests; view all students   |
| `houseparent.php`    | House Parents     | Gender-filtered student view; on-campus count      |

---

## Exeat Workflow

1. **Parent** fills out the Exeat form (`exeat.php`) — departure date/time, reason, expected return.
2. **Head of Domestic Affairs** logs into `domestic.php`, reviews the request, sets a confirmed return date, and approves or declines.
3. **House Parents** see the real-time on-campus count for their gender on `houseparent.php` (total students minus those with approved active exeats).

---

## Tech Stack

- **Backend:** PHP 7.4+ with PDO (MySQL)
- **Database:** MySQL / MariaDB (XAMPP-compatible)
- **Frontend:** Vanilla JS, CSS custom properties
- **No frameworks required**
