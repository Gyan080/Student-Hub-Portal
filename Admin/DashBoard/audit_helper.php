<?php
function write_log($conn, $admin_id, $action, $table, $target_id, $details) {
    $stmt = $conn->prepare("INSERT INTO audit_logs (admin_id, action, target_table, target_id, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $admin_id, $action, $table, $target_id, $details);
    $stmt->execute();
}
