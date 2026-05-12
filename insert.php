<?php
// ============================================================
// actions/insert.php  –  All POST insert handlers
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
        $nationalIdType = trim($_POST['national_id_type'] ?? '');
        $nationalIdNo   = trim($_POST['national_id_no']   ?? '');
        $wardCount    = max(0, min(10, (int)($_POST['ward_count'] ?? 1)));

        $students = [];

if (isset($_POST['students']) && is_array($_POST['students'])) {

    foreach ($_POST['students'] as $s) {

        $students[] = [
            'first'   => trim($s['first']   ?? ''),
            'last'    => trim($s['last']    ?? ''),
            'class'   => trim($s['class']   ?? ''),
            'house'   => trim($s['house']   ?? ''),
            'nhis'    => trim($s['nhis']    ?? ''),
            'dob'     => trim($s['dob']     ?? ''),
            'gender'  => trim($s['gender']  ?? ''),
            'idno'    => trim($s['idno']    ?? ''),
            'medical' => trim($s['medical'] ?? '')
        ];

    }

}

        if (!$firstName || !$lastName || !$phone) {
            jsonResponse(false, 'Please fill in all required parent fields.');
        }

        if ($wardCount > 0) {
            foreach ($students as $idx => $s) {
                if (!$s['first'] || !$s['last'] || !$s['class']) {
                    jsonResponse(false, 'Please fill in all required fields for Ward ' . ($idx + 1) . '.');
                }
            }
        }

        $allowed = ['Father', 'Mother', 'Guardian', 'Other'];
        if (!in_array($relationship, $allowed, true)) $relationship = 'Guardian';

        $pdb = getDB();

        $chk = $pdb->prepare('SELECT id FROM parents WHERE phone = ? LIMIT 1');
        $chk->execute([$phone]);
        if ($chk->fetch()) jsonResponse(false, 'A parent with this phone number is already registered.');

        $parentPhoto = isset($_FILES['parent_photo']) ? saveUploadedPhoto($_FILES['parent_photo'], 'parent') : null;

        $pdb->beginTransaction();
        try {
            $stmt = $pdb->prepare(
                'INSERT INTO parents (first_name, last_name, phone, email, address, relationship, national_id_type, national_id_no, photo_path)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $allowedIdTypes = ['Ghana Card', 'Passport', "Driver's License"];
            $stmt->execute([$firstName, $lastName, $phone,
                            $email ?: null, $address ?: null,
                            $relationship,
                            (in_array($nationalIdType, $allowedIdTypes, true) ? $nationalIdType : null),
                            $nationalIdNo ?: null,
                            $parentPhoto]);
            $parentId = (int)$pdb->lastInsertId();

            $stmtStudent = $pdb->prepare(
    'INSERT INTO students (
        parent_id,
        first_name,
        last_name,
        student_class,
        house,
        nhis_id,
        date_of_birth,
        gender,
        student_id_no,
        medical_condition,
        photo_path
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
            $stmtLink = $pdb->prepare(
                'INSERT IGNORE INTO student_parents (student_id, parent_id) VALUES (?, ?)'
            );

            $studentIds = [];
            for ($i = 0; $i < $wardCount; $i++) {
                $s = $students[$i];
                $sPhotoKey = "student_photo_{$i}";
                $studentPhoto = (isset($_FILES[$sPhotoKey]) && $_FILES[$sPhotoKey]['error'] === UPLOAD_ERR_OK)
                    ? saveUploadedPhoto($_FILES[$sPhotoKey], 'student')
                    : null;

                           $stmtStudent->execute([
                        $parentId,
    $s['first'],
    $s['last'],
    $s['class'],
    $s['house']    ?: null,
    $s['nhis']     ?: null,
    $s['dob']      ?: null,
    $s['gender']   ?: null,
    $s['idno']     ?: null,
    $s['medical']  ?: null,
    $studentPhoto
]);
                $newStudentId = (int)$pdb->lastInsertId();
                $studentIds[] = $newStudentId;

                // Link this student to the registering parent in the junction table
                $stmtLink->execute([$newStudentId, $parentId]);
            }

            $pdb->commit();
            jsonResponse(true, 'Registration successful!', [
                'parent_id'   => $parentId,
                'ward_count'  => $wardCount,
                'student_ids' => $studentIds   // ← returned so spouse can be linked
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

        $name    = trim($_POST['name']        ?? '');
        $type    = trim($_POST['event_type']  ?? 'PTA Meeting');
        $date    = trim($_POST['event_date']  ?? '');
        $time    = trim($_POST['event_time']  ?? '');
        $venue   = trim($_POST['venue']       ?? 'School premises');
        $desc    = trim($_POST['description'] ?? '');
        $adminId = (int)$_SESSION['admin_id'];

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
    // DELETE EVENT (admin)
    // ──────────────────────────────────────────────────────────
    case 'delete_event':
        session_start();
        if (empty($_SESSION['admin_id'])) jsonResponse(false, 'Unauthorised.');

        $eventId = (int)($_POST['event_id'] ?? 0);
        if (!$eventId) jsonResponse(false, 'Event ID is required.');

        // Check if event exists
        $stmt = getDB()->prepare('SELECT id, name FROM events WHERE id = ?');
        $stmt->execute([$eventId]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) jsonResponse(false, 'Event not found.');

        // Delete the event (CASCADE will handle related attendance records)
        $stmt = getDB()->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([$eventId]);
        
        jsonResponse(true, 'Event "' . $event['name'] . '" deleted successfully!');
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

        $pdb = getDB();

        $evChk = $pdb->prepare('SELECT id FROM events  WHERE id = ? LIMIT 1');
        $evChk->execute([$eventId]);
        if (!$evChk->fetch()) jsonResponse(false, 'Event not found.');

        $paChk = $pdb->prepare('SELECT id FROM parents WHERE id = ? LIMIT 1');
        $paChk->execute([$parentId]);
        if (!$paChk->fetch()) jsonResponse(false, 'Parent not found. Please register first.');

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
    // 4. ADMIN / STAFF LOGIN  (returns role for portal routing)
    // ──────────────────────────────────────────────────────────
    case 'admin_login':
        session_start();
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$username || !$password) jsonResponse(false, 'Username and password required.');

        $stmt = getDB()->prepare(
            'SELECT id, password, full_name, role FROM admin_users WHERE username = ? LIMIT 1'
        );
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['admin_id']   = $row['id'];
            $_SESSION['admin_name'] = $row['full_name'];
            $_SESSION['admin_role'] = $row['role'];
            jsonResponse(true, 'Login successful.', [
                'name' => $row['full_name'],
                'role' => $row['role']
            ]);
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

    // ──────────────────────────────────────────────────────────
    // 6. LINK EXISTING PARENT TO EXISTING STUDENT
    // ──────────────────────────────────────────────────────────
    case 'link_parent_student':
        $parentId  = (int)($_POST['parent_id']  ?? 0);
        $studentId = (int)($_POST['student_id'] ?? 0);
        if (!$parentId || !$studentId) jsonResponse(false, 'Parent and student IDs required.');

        $pdb = getDB();

        $pc = $pdb->prepare('SELECT id FROM parents  WHERE id=? LIMIT 1');
        $pc->execute([$parentId]); if (!$pc->fetch()) jsonResponse(false,'Parent not found.');

        $sc = $pdb->prepare('SELECT id FROM students WHERE id=? AND is_linked_copy=0 LIMIT 1');
        $sc->execute([$studentId]); if (!$sc->fetch()) jsonResponse(false,'Student not found.');

        // Check if already linked
        $lc = $pdb->prepare('SELECT 1 FROM student_parents WHERE student_id=? AND parent_id=? LIMIT 1');
        $lc->execute([$studentId, $parentId]);
        if ($lc->fetch()) jsonResponse(false,'This student is already linked to this parent.');

        // Insert into junction table only — no duplicate student rows
        $pdb->prepare('INSERT INTO student_parents (student_id, parent_id) VALUES (?, ?)')
            ->execute([$studentId, $parentId]);

        jsonResponse(true, 'Student linked successfully!');
        break;

    // ──────────────────────────────────────────────────────────
    // 7. SUBMIT EXEAT REQUEST (parent)
    // ──────────────────────────────────────────────────────────
    case 'submit_exeat':
        $studentId     = (int)($_POST['student_id']      ?? 0);
        $parentId      = (int)($_POST['parent_id']       ?? 0);
        $reason        = trim($_POST['reason']           ?? '');
        $departDate    = trim($_POST['departure_date']   ?? '');
        $departTime    = trim($_POST['departure_time']   ?? '');
        $expectedReturn= trim($_POST['expected_return']  ?? '');

        if (!$studentId || !$parentId) jsonResponse(false, 'Student and parent are required.');
        if (!$reason)        jsonResponse(false, 'Reason is required.');
        if (!$departDate)    jsonResponse(false, 'Departure date is required.');
        if (!$departTime)    jsonResponse(false, 'Departure time is required.');
        if (!$expectedReturn)jsonResponse(false, 'Expected return date is required.');

        $pdb = getDB();

        // Verify student is linked to this parent via junction table
        $chk = $pdb->prepare(
            'SELECT s.id FROM students s
             JOIN student_parents sp ON sp.student_id = s.id
             WHERE s.id=? AND sp.parent_id=? AND s.is_linked_copy=0 LIMIT 1'
        );
        $chk->execute([$studentId, $parentId]);
        if (!$chk->fetch()) jsonResponse(false, 'Student not found for this parent.');

        // Check for pending exeat for same student
        $dup = $pdb->prepare("SELECT id FROM exeat_requests WHERE student_id=? AND status='pending' LIMIT 1");
        $dup->execute([$studentId]);
        if ($dup->fetch()) jsonResponse(false, 'This student already has a pending exeat request.');

        $stmt = $pdb->prepare(
            'INSERT INTO exeat_requests (student_id, parent_id, reason, departure_date, departure_time, expected_return)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$studentId, $parentId, $reason, $departDate, $departTime, $expectedReturn]);
        jsonResponse(true, 'Exeat request submitted successfully!', ['exeat_id' => (int)$pdb->lastInsertId()]);
        break;

    // ──────────────────────────────────────────────────────────
    // 8. REVIEW EXEAT (domestic affairs / admin)
    // ──────────────────────────────────────────────────────────
    case 'review_exeat':
        session_start();
        if (empty($_SESSION['admin_id'])) jsonResponse(false, 'Unauthorised.');

        $exeatId      = (int)($_POST['exeat_id']      ?? 0);
        $status       = trim($_POST['status']         ?? '');
        $reviewNote   = trim($_POST['review_note']    ?? '');
        $actualReturn = trim($_POST['actual_return']  ?? '');

        if (!$exeatId) jsonResponse(false, 'Exeat ID required.');

        $allowed = ['approved','declined'];
        if (!in_array($status, $allowed, true)) jsonResponse(false, 'Invalid status.');

        $pdb = getDB();
        $stmt = $pdb->prepare(
            'UPDATE exeat_requests
             SET status=?, review_note=?, actual_return=?, reviewed_by=?, reviewed_at=NOW()
             WHERE id=? AND status=\'pending\''
        );
        $stmt->execute([
            $status,
            $reviewNote ?: null,
            ($status === 'approved' && $actualReturn) ? $actualReturn : null,
            (int)$_SESSION['admin_id'],
            $exeatId
        ]);
        if ($stmt->rowCount() === 0) jsonResponse(false, 'Exeat not found or already reviewed.');
        jsonResponse(true, 'Exeat request ' . $status . '.');
        break;

    // ──────────────────────────────────────────────────────────
    // 9. CHANGE CREDENTIALS (any staff user)
    // ──────────────────────────────────────────────────────────
    case 'change_credentials':
        session_start();
        if (empty($_SESSION['admin_id'])) jsonResponse(false, 'Unauthorised.');

        $newUsername = trim($_POST['new_username']     ?? '');
        $newPassword = trim($_POST['new_password']     ?? '');
        $curPassword = trim($_POST['current_password'] ?? '');

        if (!$curPassword) jsonResponse(false, 'Current password is required.');

        $pdb  = getDB();
        $stmt = $pdb->prepare('SELECT id, password, username FROM admin_users WHERE id=? LIMIT 1');
        $stmt->execute([(int)$_SESSION['admin_id']]);
        $row  = $stmt->fetch();
        if (!$row || !password_verify($curPassword, $row['password'])) {
            jsonResponse(false, 'Current password is incorrect.');
        }

        $updates = []; $params = [];
        if ($newUsername && $newUsername !== $row['username']) {
            // Check uniqueness
            $uq = $pdb->prepare('SELECT id FROM admin_users WHERE username=? AND id!=? LIMIT 1');
            $uq->execute([$newUsername, $row['id']]);
            if ($uq->fetch()) jsonResponse(false, 'That username is already taken.');
            $updates[] = 'username=?'; $params[] = $newUsername;
        }
        if ($newPassword) {
            if (strlen($newPassword) < 8) jsonResponse(false, 'Password must be at least 8 characters.');
            $updates[] = 'password=?'; $params[] = password_hash($newPassword, PASSWORD_BCRYPT);
        }
        if (!$updates) jsonResponse(false, 'No changes provided.');

        $params[] = $row['id'];
        $pdb->prepare('UPDATE admin_users SET ' . implode(',', $updates) . ' WHERE id=?')
            ->execute($params);
        jsonResponse(true, 'Credentials updated successfully.');
        break;

    default:
        jsonResponse(false, 'Unknown action.');
}