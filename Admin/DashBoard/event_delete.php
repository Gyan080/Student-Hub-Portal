<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/audit_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$submittedToken = $_POST['csrf_token'] ?? '';
if (!is_string($submittedToken) || !isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

require '../db_connect.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    header('Location: events_list.php?msg=Invalid+event+ID');
    exit;
}

$stmt = $mysqli->prepare("SELECT poster FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: events_list.php?msg=Event+not+found');
    exit;
}

$mysqli->begin_transaction();
$stmt = $mysqli->prepare("DELETE FROM registrations WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt = $mysqli->prepare("DELETE FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

write_log($mysqli, $_SESSION['admin_id'], 'delete_event', 'events', $id, 'Deleted event');
$mysqli->commit();

if (!empty($event['poster'])) {
    $poster_path = __DIR__ . '/../../Uploads/posters/' . $event['poster'];
    if (file_exists($poster_path)) {
        unlink($poster_path);
    }
}

header('Location: events_list.php?msg=Event+deleted+successfully');
exit;