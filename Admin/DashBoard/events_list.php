<?php
require_once __DIR__ . '/auth_check.php';
require '../db_connect.php';

$msg = isset($_GET['msg']) ? trim($_GET['msg']) : '';

$sql = "SELECT event_id, title, event_date, location, category, status, poster FROM events ORDER BY event_date DESC";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Events - Admin Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../adminStyle.css">
  <link rel="stylesheet" href="../admin-dark-mode.css">
  <style>
    .event-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
    }
    .event-table th, .event-table td {
      padding: 12px 14px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.92rem;
    }
    .event-table th {
      background-color: #f8fafc;
      font-weight: 700;
      color: #334155;
    }
    .event-table tr:hover {
      background-color: #f8fafc;
    }
    .poster-thumb {
      width: 60px;
      height: 40px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #e2e8f0;
    }
    .status-open {
      color: #059669;
      font-weight: 700;
    }
    .status-closed {
      color: #dc2626;
      font-weight: 700;
    }
    .event-description {
      color: #64748b;
      font-size: 0.92rem;
    }
    .event-notice {
      padding: 12px 16px;
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      border-radius: 6px;
      margin-bottom: 16px;
      font-weight: 600;
    }
    .event-empty {
      text-align: center;
      padding: 24px;
      color: #64748b;
      font-weight: 600;
    }
    .no-poster {
      color: #64748b;
      font-size: 0.8rem;
    }
    .event-action {
      font-weight: 700;
      text-decoration: none;
    }
    .event-edit {
      color: #1f4e79;
      margin-right: 12px;
    }
    .event-delete {
      color: #dc2626;
    }
    body.dark-mode .event-table {
      background: #1e1e1e;
      border-color: #3a3a3a;
    }
    body.dark-mode .event-table th {
      background-color: #292929;
      color: #f5f5f5;
      border-color: #3a3a3a;
    }
    body.dark-mode .event-table td {
      color: #e5e5e5;
      border-color: #3a3a3a;
    }
    body.dark-mode .event-table tr:hover {
      background-color: #272727;
    }
    body.dark-mode .event-table .status-open {
      color: #86efac;
    }
    body.dark-mode .event-table .status-closed {
      color: #fca5a5;
    }
    body.dark-mode .event-description,
    body.dark-mode .no-poster,
    body.dark-mode .event-empty {
      color: #b3b3b3;
    }
    body.dark-mode .event-notice {
      background: #163323;
      color: #bbf7d0;
      border-color: #276749;
    }
    body.dark-mode .event-edit {
      color: #93c5fd;
    }
    body.dark-mode .event-delete {
      color: #fca5a5;
    }
  </style>
</head>
<body>
  <div class="page-wrapper">

    <header class="header-bar">
      <div class="h-brand">
        <a href="../../index.html" class="b-link">
          <img src="../../Images/Screenshot 2026-07-14 215638.png" alt="Portal Logo" class="b-logo">
          <span class="b-name">Student Portal</span>
        </a>
      </div>
      <?php require __DIR__ . '/menu.php'; ?>
    </header>

    <main>
      <section style="margin-top:20px;">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
          <div>
            <h2>Event Management</h2>
            <p class="event-description">View, add, edit, and delete campus events</p>
          </div>
          <a href="event_form.php" class="btn btn-primary" style="padding:10px 20px;font-size:0.95rem;">+ Add New Event</a>
        </div>

        <?php if ($msg !== ''): ?>
          <div class="event-notice">
            <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <div style="overflow-x:auto;">
          <table class="event-table">
            <thead>
              <tr>
                <th>Poster</th>
                <th>Title</th>
                <th>Date</th>
                <th>Location</th>
                <th>Category</th>
                <th>Status</th>
                <th style="text-align:center;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows === 0): ?>
                <tr>
                  <td colspan="7" class="event-empty">No events found</td>
                </tr>
              <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td>
                      <?php if (!empty($row['poster'])): ?>
                        <img src="../../Uploads/posters/<?php echo htmlspecialchars($row['poster'], ENT_QUOTES, 'UTF-8'); ?>"
                             alt="Poster" class="poster-thumb">
                      <?php else: ?>
                        <span class="no-poster">No image</span>
                      <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['event_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                      <span class="<?php echo ($row['status'] === 'closed') ? 'status-closed' : 'status-open'; ?>">
                        <?php echo htmlspecialchars($row['status'] ?? 'open', ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    </td>
                    <td style="text-align:center;white-space:nowrap;">
                      <a class="event-action event-edit" href="event_form.php?id=<?php echo (int)$row['event_id']; ?>">Edit</a>
                      <a href="event_delete.php?id=<?php echo (int)$row['event_id']; ?>"
                         onclick="return confirm('Are you sure you want to delete this event?');"
                         class="event-action event-delete">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </section>
    </main>

    <footer class="f-bar" style="margin-top:auto;">
      <p class="f-note">Admin Dashboard · CHARUSAT Portal</p>
    </footer>
  </div>
  <script src="../../dark-mode.js"></script>
</body>
</html>