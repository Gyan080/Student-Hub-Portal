<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Admin_login.html');
    exit;
}

require '../db_connect.php';

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

header('Location: events_list.php?msg=Event+deleted+successfully');
exit;