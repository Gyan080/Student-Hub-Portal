<?php
require_once __DIR__ . '/auth_check.php';
require '../db_connect.php';
require_once __DIR__ . '/audit_helper.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

if (!empty($event['poster'])) {
    $poster_path = __DIR__ . '/../../Uploads/posters/' . $event['poster'];
    if (file_exists($poster_path)) {
        unlink($poster_path);
    }
}

$stmt = $mysqli->prepare("DELETE FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

write_log($mysqli, $_SESSION['admin_id'], 'delete_event', 'events', $id, 'Deleted event');
header('Location: events_list.php?msg=Event+deleted+successfully');
exit;