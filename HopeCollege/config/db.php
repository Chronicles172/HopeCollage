<?php
// ============================================================
// config/db.php  –  Database connection (PDO, XAMPP defaults)
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'schoolconnect');
define('DB_USER', 'root');
define('DB_PASS', '');          // Change if you set a MySQL password
define('DB_CHARSET', 'utf8mb4');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');   // writable folder
define('UPLOAD_URL', 'uploads/');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,   // real prepared statements
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
        }
    }
    return $pdo;
}

// ── Helper: send JSON response and exit ──────────────────────
function jsonResponse(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

// ── Helper: sanitise uploaded image ──────────────────────────
function saveUploadedPhoto(array $file, string $prefix = 'photo'): ?string {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);

    if (!isset($allowed[$mime])) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;   // 5 MB max

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

    $filename = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $dest     = UPLOAD_DIR . $filename;

    return move_uploaded_file($file['tmp_name'], $dest) ? UPLOAD_URL . $filename : null;
}
