<?php
header('Content-Type: application/json');
session_start();

// Require a logged-in student for event browsing and registration.
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] ?? '') !== 'student' || !isset($_SESSION['student_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in as a student', 'data' => null]);
    exit;
}

$transactionStarted = false;

try {
    require __DIR__ . '/db.php';

    // Return the open events stored in the database.
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $studentId = (int)$_SESSION['student_id'];
        $stmt = $conn->prepare("SELECT e.event_id AS id, e.title, COALESCE(LOWER(e.category), 'other') AS category, IF(e.event_date = '0000-00-00', '', e.event_date) AS date, e.location AS venue, e.description, e.fee, (r.registration_id IS NOT NULL) AS registered
                                FROM events e
                                LEFT JOIN registrations r ON r.event_id = e.event_id AND r.student_id = ?
                                WHERE e.status = 'open'
                                ORDER BY e.event_date");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $events = [];
        while ($event = $result->fetch_assoc()) {
            $events[] = $event;
        }

        echo json_encode(['success' => true, 'message' => 'Events list', 'data' => $events]);
        exit;
    }

    // Validate the requested event before creating a registration.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $eventIdValue = trim((string)($_POST['event_id'] ?? ''));
        if ($eventIdValue === '' || !ctype_digit($eventIdValue) || (int)$eventIdValue < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid event ID', 'data' => null]);
            exit;
        }

        $eventId = (int)$eventIdValue;
        $studentId = (int)$_SESSION['student_id'];
        $stmt = $conn->prepare("SELECT title FROM events WHERE event_id = ? AND status = 'open'");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $event = $stmt->get_result()->fetch_assoc();
        if (!$event) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Event not found or closed', 'data' => null]);
            exit;
        }

        $stmt = $conn->prepare("SELECT registration_id FROM registrations WHERE student_id = ? AND event_id = ?");
        $stmt->bind_param("ii", $studentId, $eventId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'You are already registered for this event', 'data' => null]);
            exit;
        }

        // Save the registration and corresponding audit entry together.
        $conn->begin_transaction();
        $transactionStarted = true;
        $stmt = $conn->prepare("INSERT INTO registrations (student_id, event_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $studentId, $eventId);
        $stmt->execute();
        $registrationId = $conn->insert_id;

        $studentName = $_SESSION['name'] ?? $_SESSION['username'];
        $studentUid = $_SESSION['username'];
        $action = 'student_event_register';
        $targetTable = 'registrations';
        $details = 'Student ' . $studentName . ' (' . $studentUid . ') registered for event: ' . $event['title'];
        $stmt = $conn->prepare("INSERT INTO audit_logs (admin_id, action, target_table, target_id, details) VALUES (NULL, ?, ?, ?, ?)");
        $stmt->bind_param("ssis", $action, $targetTable, $registrationId, $details);
        $stmt->execute();
        $conn->commit();
        $transactionStarted = false;

        echo json_encode(['success' => true, 'message' => 'Registration successful', 'data' => ['registration_id' => $registrationId]]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed', 'data' => null]);
} catch (mysqli_sql_exception $error) {
    if ($transactionStarted) {
        $conn->rollback();
    }
    error_log('Student events API database error: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error', 'data' => null]);
}
