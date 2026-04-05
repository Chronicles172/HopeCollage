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

    // ── All parents + their students (grouped) ───────────────
    case 'parents':
        $rows = getDB()->query(
            'SELECT p.id, p.first_name, p.last_name, p.phone, p.email,
                    p.relationship, p.photo_path, p.registered_at,
                    s.id AS s_id, s.first_name AS s_first, s.last_name AS s_last,
                    s.student_class, s.student_id_no, s.photo_path AS s_photo
             FROM parents p
             LEFT JOIN students s ON s.parent_id = p.id
             ORDER BY p.registered_at DESC, s.id ASC'
        )->fetchAll();

        // Group students under each parent
        $parents = [];
        foreach ($rows as $row) {
            $pid = $row['id'];
            if (!isset($parents[$pid])) {
                $parents[$pid] = [
                    'id'           => $row['id'],
                    'first_name'   => $row['first_name'],
                    'last_name'    => $row['last_name'],
                    'phone'        => $row['phone'],
                    'email'        => $row['email'],
                    'relationship' => $row['relationship'],
                    'photo_path'   => $row['photo_path'],
                    'registered_at'=> $row['registered_at'],
                    // keep first ward fields at top level for backwards compat
                    's_first'      => $row['s_first'],
                    's_last'       => $row['s_last'],
                    'student_class'=> $row['student_class'],
                    'student_id_no'=> $row['student_id_no'],
                    's_photo'      => $row['s_photo'],
                    'wards'        => [],
                ];
            }
            if ($row['s_id']) {
                $parents[$pid]['wards'][] = [
                    'id'           => $row['s_id'],
                    'first_name'   => $row['s_first'],
                    'last_name'    => $row['s_last'],
                    'student_class'=> $row['student_class'],
                    'student_id_no'=> $row['student_id_no'],
                    'photo_path'   => $row['s_photo'],
                ];
            }
        }
        echo json_encode(['success' => true, 'data' => array_values($parents)]);
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

    // ── Parent lookup by phone (for attendance sign-in) ───────
    case 'parent_by_phone':
        $phone = trim($_GET['phone'] ?? '');
        if (!$phone) { echo json_encode(['success' => false, 'message' => 'phone required']); break; }

        $stmt = getDB()->prepare(
            'SELECT p.id, p.first_name, p.last_name, p.phone, p.relationship, p.photo_path,
                    s.first_name AS s_first, s.last_name AS s_last, s.student_class
             FROM parents p
             LEFT JOIN students s ON s.parent_id = p.id
             WHERE p.phone = ? LIMIT 1'
        );
        $stmt->execute([$phone]);
        $row = $stmt->fetch();
        if ($row) echo json_encode(['success' => true, 'data' => $row]);
        else      echo json_encode(['success' => false, 'message' => 'No parent found with that phone number.']);
        break;

    // ── Dashboard stats ───────────────────────────────────────
    case 'stats':
        $pdb = getDB();
        $totalParents  = (int)$pdb->query('SELECT COUNT(*) FROM parents')->fetchColumn();
        $totalStudents = (int)$pdb->query('SELECT COUNT(*) FROM students')->fetchColumn();
        $totalEvents   = (int)$pdb->query('SELECT COUNT(*) FROM events')->fetchColumn();
        $upcomingCount = (int)$pdb->query('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()')->fetchColumn();
        echo json_encode([
            'success' => true,
            'data' => compact('totalParents','totalStudents','totalEvents','upcomingCount')
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
