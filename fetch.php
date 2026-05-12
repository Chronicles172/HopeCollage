<?php
// ============================================================
// actions/fetch.php  –  GET data endpoints (JSON)
// ============================================================

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

switch ($action) {

    // ── All events (upcoming first) ───────────────────────────
    case 'events':
        $rows = getDB()
            ->query('SELECT id, name, event_type, event_date, event_time, venue, description
                     FROM events ORDER BY event_date ASC')
            ->fetchAll();
        echo json_encode(['success' => true, 'data' => $rows]);
        break;

    // ── All parents + their students (grouped by student) ────
    case 'parents':
        // Fetch every (student, parent) pair via the junction table.
        // is_linked_copy = 0 ensures we only show real student rows.
        $rows = getDB()->query(
            'SELECT
               s.id              AS s_id,
               s.first_name      AS s_first,
               s.last_name       AS s_last,
               s.student_class,
               s.house,
               s.nhis_id,
               s.student_id_no,
               s.gender,
               s.date_of_birth,
			   s.medical_condition,
               s.photo_path      AS s_photo,
               s.registered_at   AS s_registered_at,
               p.id              AS p_id,
               p.first_name,
               p.last_name,
               p.phone,
               p.email,
               p.relationship,
               p.address,
               p.national_id_type,
               p.national_id_no,
               p.photo_path,
               p.registered_at
             FROM students s
             JOIN student_parents sp ON sp.student_id = s.id
             JOIN parents p          ON p.id          = sp.parent_id
             WHERE s.is_linked_copy = 0
             ORDER BY s.id ASC, p.registered_at ASC'
        )->fetchAll();

        // Group: studentId → { student info, parents[] }
        // Also build a flat parents list for backwards-compat fields.
        $studentMap = [];
        $parentMap  = [];   // parentId → parent record (for the flat parents[] array)

        foreach ($rows as $row) {
            $sid = $row['s_id'];
            $pid = $row['p_id'];

            if (!isset($studentMap[$sid])) {
                $studentMap[$sid] = [
                    'id'            => $sid,
                    'first_name'    => $row['s_first'],
                    'last_name'     => $row['s_last'],
                    'student_class' => $row['student_class'],
                    'house'         => $row['house'],
                    'nhis_id'       => $row['nhis_id'],
                    'student_id_no' => $row['student_id_no'],
                    'gender'        => $row['gender'],
                    'date_of_birth' => $row['date_of_birth'],
					'medical_condition' => $row['medical_condition'],
                    'photo_path'    => $row['s_photo'],
                    'registered_at' => $row['s_registered_at'],
                    'parents'       => [],
                ];
            }

            // Avoid duplicate parents for a student
            $alreadyLinked = false;
            foreach ($studentMap[$sid]['parents'] as $ep) {
                if ($ep['id'] === $pid) { $alreadyLinked = true; break; }
            }
            if (!$alreadyLinked) {
                $studentMap[$sid]['parents'][] = [
                    'id'               => $pid,
                    'first_name'       => $row['first_name'],
                    'last_name'        => $row['last_name'],
                    'phone'            => $row['phone'],
                    'email'            => $row['email'],
                    'relationship'     => $row['relationship'],
                    'address'          => $row['address'],
                    'national_id_type' => $row['national_id_type'],
                    'national_id_no'   => $row['national_id_no'],
                    'photo_path'       => $row['photo_path'],
                    'registered_at'    => $row['registered_at'],
                ];
            }

            // Also keep a flat parents map (for the old top-level parents[] endpoint consumers)
            if (!isset($parentMap[$pid])) {
                $parentMap[$pid] = [
                    'id'               => $pid,
                    'first_name'       => $row['first_name'],
                    'last_name'        => $row['last_name'],
                    'phone'            => $row['phone'],
                    'email'            => $row['email'],
                    'relationship'     => $row['relationship'],
                    'address'          => $row['address'],
                    'national_id_type' => $row['national_id_type'],
                    'national_id_no'   => $row['national_id_no'],
                    'photo_path'       => $row['photo_path'],
                    'registered_at'    => $row['registered_at'],
                    // Flat ward fields kept for backwards compat
                    's_first'          => $row['s_first'],
                    's_last'           => $row['s_last'],
                    'student_class'    => $row['student_class'],
                    'student_id_no'    => $row['student_id_no'],
                    's_photo'          => $row['s_photo'],
                    'wards'            => [],
                ];
            }
        }

        // Attach all wards to each parent (for old consumers that iterate allParents)
        foreach ($studentMap as $sid => $student) {
            foreach ($student['parents'] as $p) {
                $pid = $p['id'];
                if (isset($parentMap[$pid])) {
                    $alreadyWard = false;
                    foreach ($parentMap[$pid]['wards'] as $ew) {
                        if ($ew['id'] === $sid) { $alreadyWard = true; break; }
                    }
                    if (!$alreadyWard) {
                        $parentMap[$pid]['wards'][] = [
                          'id'                => $sid,
                          'canonical_id'      => $sid,
                          'first_name'        => $student['first_name'],
                          'last_name'         => $student['last_name'],
                          'student_class'     => $student['student_class'],
                           'house'             => $student['house'],
                          'nhis_id'           => $student['nhis_id'],
                             'student_id_no'     => $student['student_id_no'],
                           'gender'            => $student['gender'],
                          'date_of_birth'     => $student['date_of_birth'],
                          'medical_condition' => $student['medical_condition'],
                          'photo_path'        => $student['photo_path'],
                                   ];
                    }
                }
            }
        }

        // The response carries BOTH data shapes:
        //   data.students  → student-centric (each student with their parents[])
        //   data.parents   → parent-centric  (each parent with their wards[])  [legacy]
        echo json_encode([
            'success'  => true,
            'data'     => array_values($parentMap),     // legacy shape (allParents)
            'students' => array_values($studentMap),    // new shape for dashboards
        ]);
        break;

    // ── Attendance for an event ───────────────────────────────
    case 'attendance':
        $eventId = (int)($_GET['event_id'] ?? 0);
        if (!$eventId) { echo json_encode(['success' => false, 'message' => 'event_id required']); break; }

        $stmt = getDB()->prepare(
            'SELECT a.id, a.visit_type, a.signed_at, a.notes,
                    p.id AS parent_id, p.first_name, p.last_name, p.phone,
                    p.relationship, p.photo_path,
                    s.first_name AS s_first, s.last_name AS s_last, s.student_class
             FROM attendance a
             JOIN parents  p ON p.id = a.parent_id
             LEFT JOIN students s ON s.parent_id = p.id
             WHERE a.event_id = ?
             ORDER BY a.signed_at DESC'
        );
        $stmt->execute([$eventId]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    // ── Parent lookup by phone (for attendance & exeat) ───────
    case 'parent_by_phone':
        $phone = trim($_GET['phone'] ?? '');
        if (!$phone) { echo json_encode(['success' => false, 'message' => 'phone required']); break; }

        $pdb  = getDB();
        $stmt = $pdb->prepare(
            'SELECT p.id, p.first_name, p.last_name, p.phone, p.relationship, p.photo_path
             FROM parents p WHERE p.phone = ? LIMIT 1'
        );
        $stmt->execute([$phone]);
        $row = $stmt->fetch();
        if (!$row) { echo json_encode(['success' => false, 'message' => 'No parent found with that phone number.']); break; }

        // Fetch all real wards linked to this parent via the junction table
        $ws = $pdb->prepare(
            'SELECT s.id, s.first_name, s.last_name, s.student_class, s.house,
                    s.nhis_id, s.student_id_no, s.gender, s.photo_path
             FROM students s
             JOIN student_parents sp ON sp.student_id = s.id
             WHERE sp.parent_id = ? AND s.is_linked_copy = 0
             ORDER BY s.first_name ASC'
        );
        $ws->execute([$row['id']]);
        $wards = $ws->fetchAll();

        // For each ward, also fetch ALL parents so dashboards can display them
        foreach ($wards as &$ward) {
            $ps = $pdb->prepare(
                'SELECT p2.id, p2.first_name, p2.last_name, p2.phone, p2.relationship, p2.photo_path
                 FROM parents p2
                 JOIN student_parents sp2 ON sp2.parent_id = p2.id
                 WHERE sp2.student_id = ?
                 ORDER BY p2.registered_at ASC'
            );
            $ps->execute([$ward['id']]);
            $ward['parents'] = $ps->fetchAll();
        }
        unset($ward);

        $row['wards'] = $wards;
        // Backwards-compat flat fields for the first ward
        $first = $wards[0] ?? [];
        $row['s_first']       = $first['first_name']    ?? null;
        $row['s_last']        = $first['last_name']     ?? null;
        $row['student_class'] = $first['student_class'] ?? null;

        echo json_encode(['success' => true, 'data' => $row]);
        break;

    // ── Dashboard stats ───────────────────────────────────────
    case 'stats':
        $pdb = getDB();
        $totalParents  = (int)$pdb->query('SELECT COUNT(*) FROM parents')->fetchColumn();
        $totalStudents = (int)$pdb->query('SELECT COUNT(*) FROM students WHERE is_linked_copy = 0')->fetchColumn();
        $totalEvents   = (int)$pdb->query('SELECT COUNT(*) FROM events')->fetchColumn();
        $upcomingCount = (int)$pdb->query('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()')->fetchColumn();
        $pendingExeats = (int)$pdb->query("SELECT COUNT(*) FROM exeat_requests WHERE status = 'pending'")->fetchColumn();
        $offCampus     = (int)$pdb->query(
            "SELECT COUNT(*) FROM exeat_requests WHERE status = 'approved'
             AND departure_date <= CURDATE() AND (actual_return IS NULL OR actual_return > CURDATE())"
        )->fetchColumn();
        echo json_encode([
            'success' => true,
            'data' => compact('totalParents','totalStudents','totalEvents','upcomingCount','pendingExeats','offCampus')
        ]);
        break;

    // ── Search students by name or ID (for parent linking) ────
    case 'search_students':

    $q = trim($_GET['q'] ?? '');

    // Prevent empty or tiny searches
    if (strlen($q) < 2) {
        echo json_encode([
            'success' => true,
            'data'    => []
        ]);
        break;
    }

    $like = '%' . $q . '%';

    $stmt = getDB()->prepare("
        SELECT 
            s.id,
            s.first_name,
            s.last_name,
            s.student_class,
            s.student_id_no,

            p.first_name AS p_first,
            p.last_name  AS p_last,
            p.relationship

        FROM students s

        JOIN student_parents sp
            ON sp.student_id = s.id

        JOIN parents p
            ON p.id = sp.parent_id

        WHERE s.is_linked_copy = 0
          AND (
                s.first_name LIKE ?
             OR s.last_name LIKE ?
             OR CONCAT(s.first_name, ' ', s.last_name) LIKE ?
             OR s.student_id_no LIKE ?
          )

        ORDER BY s.first_name ASC
        LIMIT 10
    ");

    $stmt->execute([
        $like,
        $like,
        $like,
        $like
    ]);

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $students
    ]);

    break;

    // ── Exeat requests (filtered by status and/or gender) ─────
    case 'exeats':
        $status = trim($_GET['status'] ?? '');
        $gender = trim($_GET['gender'] ?? '');
        $allowed = ['pending','approved','declined'];

        // Join via the exeat's own parent_id (the requesting parent)
        // Then also gather all parents for that student for display
        $sql = 'SELECT e.id, e.reason, e.departure_date, e.departure_time, e.expected_return,
                       e.actual_return, e.status, e.review_note, e.created_at,
                       s.id AS student_id, s.first_name AS s_first, s.last_name AS s_last,
                       s.student_class, s.house, s.nhis_id, s.gender AS s_gender,
                       s.student_id_no, s.photo_path AS s_photo,
                       p.id AS parent_id, p.first_name AS p_first, p.last_name AS p_last,
                       p.phone AS p_phone, p.relationship AS p_relationship,
                       a.full_name AS reviewer_name
                FROM exeat_requests e
                JOIN students s    ON s.id = e.student_id
                JOIN parents  p    ON p.id = e.parent_id
                LEFT JOIN admin_users a ON a.id = e.reviewed_by
                WHERE 1=1';
        $params = [];
        if ($status && in_array($status, $allowed, true)) {
            $sql .= ' AND e.status = ?'; $params[] = $status;
        }
        if ($gender) {
            $sql .= ' AND s.gender = ?'; $params[] = $gender;
        }
        $sql .= ' ORDER BY e.created_at DESC';

        $pdb  = getDB();
        $stmt = $pdb->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Attach all parents for each student
        $stmtAllParents = $pdb->prepare(
            'SELECT p2.id, p2.first_name, p2.last_name, p2.phone, p2.relationship, p2.photo_path
             FROM parents p2
             JOIN student_parents sp ON sp.parent_id = p2.id
             WHERE sp.student_id = ?
             ORDER BY p2.registered_at ASC'
        );
        foreach ($rows as &$row) {
            $stmtAllParents->execute([$row['student_id']]);
            $row['all_parents'] = $stmtAllParents->fetchAll();
        }
        unset($row);

        echo json_encode(['success' => true, 'data' => $rows]);
        break;

    // ── Students filtered by gender (for house parents) ───────
    case 'students_by_gender':
        $gender = trim($_GET['gender'] ?? '');
        if (!$gender) { echo json_encode(['success'=>false,'message'=>'gender required']); break; }

        $pdb = getDB();

        // Fetch real students of the requested gender
        $stmt = $pdb->prepare(
            'SELECT s.id, s.first_name, s.last_name, s.student_class, s.house,
                    s.nhis_id, s.student_id_no, s.gender, s.photo_path,
                    (
                      SELECT COUNT(*) FROM exeat_requests ex
                      WHERE ex.student_id = s.id AND ex.status = \'approved\'
                        AND ex.departure_date <= CURDATE()
                        AND (ex.actual_return IS NULL OR ex.actual_return > CURDATE())
                    ) AS is_off_campus_count
             FROM students s
             WHERE s.gender = ? AND s.is_linked_copy = 0
             ORDER BY s.first_name ASC'
        );
        $stmt->execute([$gender]);
        $rows = $stmt->fetchAll();

        // For each student, fetch ALL their parents via the junction table
        $stmtParents = $pdb->prepare(
            'SELECT p.id, p.first_name, p.last_name, p.phone, p.relationship, p.photo_path
             FROM parents p
             JOIN student_parents sp ON sp.parent_id = p.id
             WHERE sp.student_id = ?
             ORDER BY p.registered_at ASC'
        );

        foreach ($rows as &$r) {
            $stmtParents->execute([$r['id']]);
            $r['parents']   = $stmtParents->fetchAll();
            // Backwards-compat flat fields (first parent)
            $fp = $r['parents'][0] ?? [];
            $r['p_first']   = $fp['first_name']  ?? null;
            $r['p_last']    = $fp['last_name']   ?? null;
            $r['p_phone']   = $fp['phone']       ?? null;
            $r['on_campus'] = ((int)$r['is_off_campus_count'] === 0);
            unset($r['is_off_campus_count']);
        }
        unset($r);

        echo json_encode(['success' => true, 'data' => $rows]);
        break;
		
		
	case 'student':

    $id = (int)($_GET['id'] ?? 0);

    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid student ID.'
        ]);
        break;
    }

    $stmt = getDB()->prepare("
        SELECT 
            id,
            first_name,
            last_name,
            student_class,
            house,
            nhis_id,
            date_of_birth,
            gender,
            student_id_no,
            photo_path
        FROM students
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode([
            'success' => false,
            'message' => 'Student not found.'
        ]);
        break;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Student loaded.',
        'data' => $student
    ]);

    break;

    // ── Full student record + all parents + exeat history ─────
    case 'student_full_data':
        $sid = (int)($_GET['student_id'] ?? 0);
        if (!$sid) { echo json_encode(['success'=>false,'message'=>'student_id required']); break; }

        $pdb  = getDB();

        // Fetch student
        $stmt = $pdb->prepare(
            'SELECT s.id, s.first_name, s.last_name, s.student_class, s.student_id_no,
                    s.gender, s.date_of_birth, s.medical_condition, s.photo_path
             FROM students s
             WHERE s.id = ? 
			 AND s.is_linked_copy = 0 
			 LIMIT 1'
        );
        $stmt->execute([$sid]);
        $student = $stmt->fetch();
        if (!$student) { echo json_encode(['success'=>false,'message'=>'Student not found.']); break; }

        // Fetch ALL parents via junction table
        $ps = $pdb->prepare(
            'SELECT p.id, p.first_name, p.last_name, p.phone, p.email,
                    p.relationship, p.address, p.photo_path
             FROM parents p
             JOIN student_parents sp ON sp.parent_id = p.id
             WHERE sp.student_id = ?
             ORDER BY p.registered_at ASC'
        );
        $ps->execute([$sid]);
        $student['parents'] = $ps->fetchAll();

        // Backwards-compat flat fields (first parent)
        $fp = $student['parents'][0] ?? [];
        $student['p_first']      = $fp['first_name']  ?? null;
        $student['p_last']       = $fp['last_name']   ?? null;
        $student['phone']        = $fp['phone']        ?? null;
        $student['email']        = $fp['email']        ?? null;
        $student['relationship'] = $fp['relationship'] ?? null;

        // Exeat history
        $ex = $pdb->prepare(
            'SELECT e.*, a.full_name AS reviewer_name
             FROM exeat_requests e
             LEFT JOIN admin_users a ON a.id = e.reviewed_by
             WHERE e.student_id = ? ORDER BY e.created_at DESC'
        );
        $ex->execute([$sid]);
        $exeats = $ex->fetchAll();

        echo json_encode(['success'=>true,'data'=>['student'=>$student,'exeats'=>$exeats]]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
