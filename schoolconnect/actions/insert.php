<?php
// ============================================================
// actions/insert.php  –  All POST insert handlers
// Protected against SQL injection via PDO prepared statements
// ============================================================

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$action = $_POST['action'] ?? '';

switch ($action) {

    // ──────────────────────────────────────────────────────────
    // 1. REGISTER PARENT + MULTIPLE STUDENTS
    // ──────────────────────────────────────────────────────────
    case 'register_parent':
        $firstName    = trim($_POST['first_name']    ?? '');
        $lastName     = trim($_POST['last_name']     ?? '');
        $phone        = trim($_POST['phone']         ?? '');
        $email        = trim($_POST['email']         ?? '');
        $address      = trim($_POST['address']       ?? '');
        $relationship = trim($_POST['relationship']  ?? 'Guardian');
        $wardCount    = max(1, min(10, (int)($_POST['ward_count'] ?? 1)));

        // collect students array from POST
        $students = [];
        for ($i = 0; $i < $wardCount; $i++) {
            $s = $_POST['students'][$i] ?? [];
            $students[] = [
                'first'  => trim($s['first']  ?? ''),
                'last'   => trim($s['last']   ?? ''),
                'class'  => trim($s['class']  ?? ''),
                'dob'    => trim($s['dob']    ?? ''),
                'gender' => trim($s['gender'] ?? ''),
                'idno'   => trim($s['idno']   ?? ''),
            ];
        }

        // Validate parent fields
        if (!$firstName || !$lastName || !$phone) {
            jsonResponse(false, 'Please fill in all required parent fields.');
        }

        // Validate each ward
        foreach ($students as $idx => $s) {
            if (!$s['first'] || !$s['last'] || !$s['class']) {
                jsonResponse(false, 'Please fill in all required fields for Ward ' . ($idx + 1) . '.');
            }
        }

        $allowed = ['Father', 'Mother', 'Guardian', 'Other'];
        if (!in_array($relationship, $allowed, true)) $relationship = 'Guardian';

        $pdb = getDB();

        // Check duplicate phone
        $chk = $pdb->prepare('SELECT id FROM parents WHERE phone = ? LIMIT 1');
        $chk->execute([$phone]);
        if ($chk->fetch()) jsonResponse(false, 'A parent with this phone number is already registered.');

        // Upload parent photo
        $parentPhoto = isset($_FILES['parent_photo']) ? saveUploadedPhoto($_FILES['parent_photo'], 'parent') : null;

        $pdb->beginTransaction();
        try {
            // Insert parent
            $stmt = $pdb->prepare(
                'INSERT INTO parents (first_name, last_name, phone, email, address, relationship, photo_path)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$firstName, $lastName, $phone,
                            $email ?: null, $address ?: null,
                            $relationship, $parentPhoto]);
            $parentId = (int)$pdb->lastInsertId();

            // Insert each student/ward
            $stmt2 = $pdb->prepare(
                'INSERT INTO students (parent_id, first_name, last_name, student_class, date_of_birth, gender, student_id_no, photo_path)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            for ($i = 0; $i < $wardCount; $i++) {
                $s = $students[$i];
                // Upload student photo if provided
                $sPhotoKey = "student_photo_{$i}";
                $studentPhoto = (isset($_FILES[$sPhotoKey]) && $_FILES[$sPhotoKey]['error'] === UPLOAD_ERR_OK)
                    ? saveUploadedPhoto($_FILES[$sPhotoKey], 'student')
                    : null;

                $stmt2->execute([
                    $parentId,
                    $s['first'], $s['last'], $s['class'],
                    $s['dob']    ?: null,
                    $s['gender'] ?: null,
                    $s['idno']   ?: null,
                    $studentPhoto
                ]);
            }

            $pdb->commit();
            jsonResponse(true, 'Registration successful!', [
                'parent_id'  => $parentId,
                'ward_count' => $wardCount
            ]);
        } catch (Exception $e) {
            $pdb->rollBack();
            jsonResponse(false, 'Registration failed. Please try again.');
        }
        break;

    // ──────────────────────────────────────────────────────────
    // 2. CREATE EVENT (admin)
    // ──────────────────────────────────────────────────────────
    case 'create_event':
        session_start();
        if (empty($_SESSION['admin_id'])) jsonResponse(false, 'Unauthorised.');

        $name      = trim($_POST['name']        ?? '');
        $type      = trim($_POST['event_type']  ?? 'PTA Meeting');
        $date      = trim($_POST['event_date']  ?? '');
        $time      = trim($_POST['event_time']  ?? '');
        $venue     = trim($_POST['venue']       ?? 'School premises');
        $desc      = trim($_POST['description'] ?? '');
        $adminId   = (int)$_SESSION['admin_id'];

        if (!$name || !$date) jsonResponse(false, 'Event name and date are required.');

        $allowed = ['PTA Meeting', 'Visitation Day', 'Sports Day', 'Open Day', 'Other'];
        if (!in_array($type, $allowed, true)) $type = 'Other';

        $stmt = getDB()->prepare(
            'INSERT INTO events (name, event_type, event_date, event_time, venue, description, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $type, $date, $time ?: null, $venue, $desc ?: null, $adminId]);
        jsonResponse(true, 'Event created successfully!', ['event_id' => (int)getDB()->lastInsertId()]);
        break;

    // ──────────────────────────────────────────────────────────
    // 3. SIGN ATTENDANCE / VISITATION
    // ──────────────────────────────────────────────────────────
    case 'sign_attendance':
        $eventId   = (int)($_POST['event_id']   ?? 0);
        $parentId  = (int)($_POST['parent_id']  ?? 0);
        $visitType = trim($_POST['visit_type']  ?? 'Event Attendance');
        $notes     = trim($_POST['notes']       ?? '');

        if (!$eventId || !$parentId) jsonResponse(false, 'Event and parent are required.');

        $allowed = ['Event Attendance', 'Visitation', 'Walk-in'];
        if (!in_array($visitType, $allowed, true)) $visitType = 'Event Attendance';

        $pdb  = getDB();

        // verify event & parent exist
        $evChk = $pdb->prepare('SELECT id FROM events  WHERE id = ? LIMIT 1');
        $evChk->execute([$eventId]);
        if (!$evChk->fetch()) jsonResponse(false, 'Event not found.');

        $paChk = $pdb->prepare('SELECT id FROM parents WHERE id = ? LIMIT 1');
        $paChk->execute([$parentId]);
        if (!$paChk->fetch()) jsonResponse(false, 'Parent not found. Please register first.');

        // upsert – ignore duplicate
        $stmt = $pdb->prepare(
            'INSERT IGNORE INTO attendance (event_id, parent_id, visit_type, notes)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$eventId, $parentId, $visitType, $notes ?: null]);

        if ($stmt->rowCount() === 0) {
            jsonResponse(false, 'You have already signed in for this event.');
        }
        jsonResponse(true, 'Attendance recorded successfully!');
        break;

    // ──────────────────────────────────────────────────────────
    // 4. ADMIN LOGIN
    // ──────────────────────────────────────────────────────────
    case 'admin_login':
        session_start();
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$username || !$password) jsonResponse(false, 'Username and password required.');

        $stmt = getDB()->prepare('SELECT id, password, full_name FROM admin_users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $row  = $stmt->fetch();

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['admin_id']   = $row['id'];
            $_SESSION['admin_name'] = $row['full_name'];
            jsonResponse(true, 'Login successful.', ['name' => $row['full_name']]);
        } else {
            jsonResponse(false, 'Invalid username or password.');
        }
        break;

    // ──────────────────────────────────────────────────────────
    // 5. ADMIN LOGOUT
    // ──────────────────────────────────────────────────────────
    case 'admin_logout':
        session_start();
        session_destroy();
        jsonResponse(true, 'Logged out.');
        break;

    default:
        jsonResponse(false, 'Unknown action.');
}
