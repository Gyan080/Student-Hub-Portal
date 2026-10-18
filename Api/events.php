<?php
header('Content-Type: application/json');

try {
    require __DIR__ . '/db.php';
    http_response_code(200);

    $method = $_SERVER['REQUEST_METHOD'];

    // Return one event or the full event list.
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id_value = trim((string)$_GET['id']);
            if ($id_value === '' || !ctype_digit($id_value) || (int)$id_value < 1) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid event ID', 'data' => null]);
                exit;
            }

            $id = (int)$id_value;
            $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();

            if (!$event) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Event not found', 'data' => null]);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Event found', 'data' => $event]);
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM events ORDER BY event_date DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }

        echo json_encode(['success' => true, 'message' => 'Events list', 'data' => $events]);
        exit;
    }

    // Validate the action and required ID before changing an event.
    if ($method === 'POST') {
        $action = trim($_POST['action'] ?? '');

        if ($action !== 'add' && $action !== 'update' && $action !== 'delete') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action. Use add, update, or delete', 'data' => null]);
            exit;
        }

        $id = 0;
        if ($action === 'update' || $action === 'delete') {
            $id_value = trim((string)($_POST['event_id'] ?? ''));
            if ($id_value === '' || !ctype_digit($id_value) || (int)$id_value < 1) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid event ID', 'data' => null]);
                exit;
            }
            $id = (int)$id_value;

            $stmt = $conn->prepare("SELECT event_id FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Event not found', 'data' => null]);
                exit;
            }
        }

        // Delete an event and its existing poster.
        if ($action === 'delete') {
            $stmt = $conn->prepare("SELECT poster FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();

            $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if (!empty($event['poster'])) {
                $poster_path = __DIR__ . '/../Uploads/posters/' . basename($event['poster']);
                if (file_exists($poster_path) && !unlink($poster_path)) {
                    error_log('Could not delete event poster: ' . $poster_path);
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Could not delete poster file', 'data' => null]);
                    exit;
                }
            }

            echo json_encode(['success' => true, 'message' => 'Event deleted', 'data' => null]);
            exit;
        }

        // Validate event values before inserting or updating.
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $event_date = trim($_POST['event_date'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $fee = trim($_POST['fee'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $status = trim($_POST['status'] ?? '');

        if ($title === '' || strlen($title) < 3 || strlen($title) > 150) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Title is required (3-150 characters)', 'data' => null]);
            exit;
        }
        if ($event_date === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Event date is required', 'data' => null]);
            exit;
        }
        if ($location === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Location is required', 'data' => null]);
            exit;
        }
        if (!is_numeric($fee) || (float)$fee < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Fee must be a number >= 0', 'data' => null]);
            exit;
        }
        $allowed_categories = ['Tech', 'Cultural', 'Sports', 'Workshop', 'Other'];
        if (!in_array($category, $allowed_categories, true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category must be Tech, Cultural, Sports, Workshop, or Other', 'data' => null]);
            exit;
        }
        if ($status !== 'open' && $status !== 'closed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Status must be open or closed', 'data' => null]);
            exit;
        }

        $fee_value = (float)$fee;
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, location, fee, category, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssdss", $title, $description, $event_date, $location, $fee_value, $category, $status);
            $stmt->execute();
            $new_id = $conn->insert_id;

            echo json_encode(['success' => true, 'message' => 'Event added', 'data' => ['event_id' => $new_id]]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, fee=?, category=?, status=? WHERE event_id=?");
        $stmt->bind_param("ssssdssi", $title, $description, $event_date, $location, $fee_value, $category, $status, $id);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Event updated', 'data' => ['event_id' => $id]]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed', 'data' => null]);
} catch (mysqli_sql_exception $error) {
    error_log('Events API database error: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error', 'data' => null]);
}
