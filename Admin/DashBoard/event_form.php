<?php
require_once __DIR__ . '/auth_check.php';
require '../db_connect.php';
require_once __DIR__ . '/audit_helper.php';

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = ($edit_id > 0);
$errors = [];

$title = '';
$description = '';
$event_date = '';
$location = '';
$fee = '0';
$category = '';
$status = 'open';
$old_poster = '';

if ($is_edit) {
    $stmt = $mysqli->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    if (!$event) {
        header("Location: events_list.php?msg=Event+not+found");
        exit;
    }
    $title = $event['title'];
    $description = $event['description'];
    $event_date = $event['event_date'];
    $location = $event['location'];
    $fee = $event['fee'];
    $category = $event['category'];
    $status = $event['status'];
    $old_poster = $event['poster'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $is_edit = ($edit_id > 0);

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $fee = trim($_POST['fee'] ?? '0');
    $category = trim($_POST['category'] ?? '');
    $status = trim($_POST['status'] ?? 'open');

    $old_poster = trim($_POST['old_poster'] ?? '');

    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen($title) < 3 || strlen($title) > 150) {
        $errors[] = 'Title must be between 3 and 150 characters.';
    }

    if ($event_date === '') {
        $errors[] = 'Event date is required.';
    }

    if ($location === '') {
        $errors[] = 'Location is required.';
    }

    if (!is_numeric($fee)) {
        $errors[] = 'Fee must be a valid number.';
    } elseif ((float)$fee < 0) {
        $errors[] = 'Fee cannot be negative.';
    }

    $allowed_categories = ['Tech', 'Cultural', 'Sports', 'Workshop', 'Other'];
    if (!in_array($category, $allowed_categories)) {
        $errors[] = 'Category must be one of: Tech, Cultural, Sports, Workshop, Other.';
    }

    if ($status !== 'open' && $status !== 'closed') {
        $errors[] = 'Status must be open or closed.';
    }

    $poster_filename = $old_poster;

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['poster']['error'] !== 0) {
            $errors[] = 'Poster upload error. Please try again.';
        } else {

            if ($_FILES['poster']['size'] > 2097152) {
                $errors[] = 'Poster file is too large. Maximum size is 2 MB.';
            }

            $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed_ext)) {
                $errors[] = 'Poster must be a JPG, JPEG, PNG, or WEBP image.';
            }

            if (empty($errors) || (!in_array('Poster must be a JPG, JPEG, PNG, or WEBP image.', $errors) && !in_array('Poster file is too large. Maximum size is 2 MB.', $errors))) {
                $image_check = @getimagesize($_FILES['poster']['tmp_name']);
                if ($image_check === false) {
                    $errors[] = 'The uploaded file is not a valid image.';
                }
            }

            if (empty($errors)) {
                $upload_dir = __DIR__ . '/../../Uploads/posters/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['poster']['tmp_name'], $destination)) {
                    if ($old_poster !== '' && file_exists($upload_dir . $old_poster)) {
                        unlink($upload_dir . $old_poster);
                    }
                    $poster_filename = $new_filename;
                } else {
                    $errors[] = 'Failed to save the poster file.';
                }
            }
        }
    }

    if (empty($errors)) {
        $fee_val = (float)$fee;

        if ($is_edit) {
            $stmt = $mysqli->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, fee=?, category=?, status=?, poster=? WHERE event_id=?");
            $stmt->bind_param("ssssdsssi", $title, $description, $event_date, $location, $fee_val, $category, $status, $poster_filename, $edit_id);
            $stmt->execute();
            write_log($mysqli, $_SESSION['admin_id'], 'update_event', 'events', $edit_id, 'Updated event: ' . $title);
            header("Location: events_list.php?msg=Event+updated+successfully");
            exit;
        } else {
            $stmt = $mysqli->prepare("INSERT INTO events (title, description, event_date, location, fee, category, status, poster) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssdsss", $title, $description, $event_date, $location, $fee_val, $category, $status, $poster_filename);
            $stmt->execute();
            write_log($mysqli, $_SESSION['admin_id'], 'add_event', 'events', $mysqli->insert_id, 'Added event: ' . $title);
            header("Location: events_list.php?msg=Event+added+successfully");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo $is_edit ? 'Edit Event' : 'Add Event'; ?> - Admin Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../adminStyle.css">
  <link rel="stylesheet" href="../admin-dark-mode.css">
  <style>
    .form-field textarea {
      width: 100%;
      padding: 10px 14px;
      font-family: inherit;
      font-size: 0.95rem;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      background-color: #ffffff;
      outline: none;
      resize: vertical;
      min-height: 80px;
    }
    .form-field textarea:focus {
      border-color: #1f4e79;
    }
    .error-box {
      background: #fef2f2;
      color: #b91c1c;
      border: 1px solid #fecaca;
      border-radius: 6px;
      padding: 12px 16px;
      margin-bottom: 16px;
      font-weight: 600;
    }
    .error-box ul {
      margin: 6px 0 0 18px;
      padding: 0;
    }
    .current-poster {
      margin-top: 6px;
    }
    .current-poster img {
      width: 120px;
      height: auto;
      border-radius: 4px;
      border: 1px solid #e2e8f0;
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
      <section style="margin-top:20px;max-width:700px;margin-left:auto;margin-right:auto;">

        <div style="margin-bottom:16px;">
          <h2><?php echo $is_edit ? 'Edit Event' : 'Add New Event'; ?></h2>
          <p style="color:#64748b;font-size:0.92rem;">
            <?php echo $is_edit ? 'Update the event details below' : 'Fill in the details to create a new event'; ?>
          </p>
        </div>

        <?php if (!empty($errors)): ?>
          <div class="error-box">
            Errors:
            <ul>
              <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="form-card">
          <form method="POST" action="event_form.php<?php echo $is_edit ? '?id=' . $edit_id : ''; ?>" enctype="multipart/form-data">

            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
            <input type="hidden" name="old_poster" value="<?php echo htmlspecialchars($old_poster, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-grid">

              <div class="form-field">
                <label for="title"><strong>Title</strong></label>
                <input type="text" id="title" name="title" maxlength="150"
                       value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Event title (3-150 chars)">
              </div>

              <div class="form-field">
                <label for="event_date"><strong>Date</strong></label>
                <input type="date" id="event_date" name="event_date"
                       value="<?php echo htmlspecialchars($event_date, ENT_QUOTES, 'UTF-8'); ?>">
              </div>

              <div class="form-field">
                <label for="location"><strong>Location</strong></label>
                <input type="text" id="location" name="location"
                       value="<?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="e.g. Main Auditorium">
              </div>

              <div class="form-field">
                <label for="fee"><strong>Fee (₹)</strong></label>
                <input type="number" id="fee" name="fee" step="0.01" min="0"
                       value="<?php echo htmlspecialchars($fee, ENT_QUOTES, 'UTF-8'); ?>">
              </div>

              <div class="form-field">
                <label for="category"><strong>Category</strong></label>
                <select id="category" name="category">
                  <option value="">-- Select Category --</option>
                  <?php
                  $cats = ['Tech', 'Cultural', 'Sports', 'Workshop', 'Other'];
                  foreach ($cats as $cat):
                  ?>
                    <option value="<?php echo $cat; ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                      <?php echo $cat; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-field">
                <label for="status"><strong>Status</strong></label>
                <select id="status" name="status">
                  <option value="open" <?php echo ($status === 'open') ? 'selected' : ''; ?>>Open</option>
                  <option value="closed" <?php echo ($status === 'closed') ? 'selected' : ''; ?>>Closed</option>
                </select>
              </div>
            </div>

            <div class="form-field" style="margin-top:4px;">
              <label for="description"><strong>Description</strong></label>
              <textarea id="description" name="description" placeholder="Event description..."><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="form-field" style="margin-top:4px;">
              <label for="poster"><strong>Poster Image</strong> (JPG, PNG, WEBP — max 2 MB)</label>
              <input type="file" id="poster" name="poster" accept=".jpg,.jpeg,.png,.webp">
              <?php if ($old_poster !== ''): ?>
                <div class="current-poster">
                  <small>Current poster:</small><br>
                  <img src="../../Uploads/posters/<?php echo htmlspecialchars($old_poster, ENT_QUOTES, 'UTF-8'); ?>" alt="Current poster">
                </div>
              <?php endif; ?>
            </div>

            <div style="margin-top:18px;display:flex;gap:12px;">
              <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:0.95rem;">
                <?php echo $is_edit ? 'Update Event' : 'Add Event'; ?>
              </button>
              <a href="events_list.php" class="btn btn-secondary" style="padding:10px 24px;font-size:0.95rem;">Cancel</a>
            </div>

          </form>
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