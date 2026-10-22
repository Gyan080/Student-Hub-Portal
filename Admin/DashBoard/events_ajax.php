<?php
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Events (AJAX) - Admin Portal</title>
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
    .event-description {
      color: #64748b;
      font-size: 0.92rem;
      margin-bottom: 16px;
    }
    .event-table .status-open {
      color: #059669;
      font-weight: 700;
    }
    .event-table .status-closed {
      color: #dc2626;
      font-weight: 700;
    }
    .event-action {
      font-weight: 700;
      background: none;
      border: none;
      cursor: pointer;
    }
    .event-edit {
      color: #1f4e79;
      margin-right: 8px;
    }
    .event-delete {
      color: #dc2626;
    }
    #status-msg {
      padding: 10px 14px;
      border-radius: 6px;
      margin-bottom: 14px;
      font-weight: 600;
      display: none;
    }
    .msg-success {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    .msg-error {
      background: #fef2f2;
      color: #b91c1c;
      border: 1px solid #fecaca;
    }
    .msg-loading {
      background: #eff6ff;
      color: #1e40af;
      border: 1px solid #bfdbfe;
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
    body.dark-mode .event-description {
      color: #b3b3b3;
    }
    body.dark-mode .event-table .status-open {
      color: #86efac;
    }
    body.dark-mode .event-table .status-closed {
      color: #fca5a5;
    }
    body.dark-mode .event-edit {
      color: #93c5fd;
    }
    body.dark-mode .event-delete {
      color: #fca5a5;
    }
    body.dark-mode #status-msg.msg-success {
      background: #163323;
      color: #bbf7d0;
      border-color: #276749;
    }
    body.dark-mode #status-msg.msg-error {
      background: #3b1d1d;
      color: #fecaca;
      border-color: #7f1d1d;
    }
    body.dark-mode #status-msg.msg-loading {
      background: #172b45;
      color: #bfdbfe;
      border-color: #1e40af;
    }
    body.dark-mode .form-field input,
    body.dark-mode .form-field select,
    body.dark-mode .form-field textarea {
      border-color: #4b5563;
    }
    body.dark-mode #f-description {
      border-color: #4b5563 !important;
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

        <h2>Events (AJAX CRUD)</h2>
        <p class="event-description">Add, edit, and delete events without page reload</p>

        <div id="status-msg" role="status"></div>

        <div class="form-card" style="margin-bottom:20px;">
          <h3 id="form-title" style="margin-bottom:12px;">Add New Event</h3>
          <form id="event-form">
            <input type="hidden" id="f-action" name="action" value="add">
            <input type="hidden" id="f-event-id" name="event_id" value="">

            <div class="form-grid">
              <div class="form-field">
                <label for="f-title"><strong>Title</strong></label>
                <input type="text" id="f-title" name="title" maxlength="150" placeholder="Event title (3-150 chars)">
              </div>
              <div class="form-field">
                <label for="f-date"><strong>Date</strong></label>
                <input type="date" id="f-date" name="event_date">
              </div>
              <div class="form-field">
                <label for="f-location"><strong>Location</strong></label>
                <input type="text" id="f-location" name="location" placeholder="e.g. Main Auditorium">
              </div>
              <div class="form-field">
                <label for="f-fee"><strong>Fee (₹)</strong></label>
                <input type="number" id="f-fee" name="fee" step="0.01" min="0" value="0">
              </div>
              <div class="form-field">
                <label for="f-category"><strong>Category</strong></label>
                <select id="f-category" name="category">
                  <option value="">-- Select --</option>
                  <option value="Tech">Tech</option>
                  <option value="Cultural">Cultural</option>
                  <option value="Sports">Sports</option>
                  <option value="Workshop">Workshop</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="form-field">
                <label for="f-status"><strong>Status</strong></label>
                <select id="f-status" name="status">
                  <option value="open">Open</option>
                  <option value="closed">Closed</option>
                </select>
              </div>
            </div>

            <div class="form-field" style="margin-top:4px;">
              <label for="f-description"><strong>Description</strong></label>
              <textarea id="f-description" name="description" rows="3" placeholder="Event description..." style="width:100%;padding:10px 14px;font-family:inherit;font-size:0.95rem;border:1px solid #cbd5e1;border-radius:6px;resize:vertical;"></textarea>
            </div>

            <div style="margin-top:14px;display:flex;gap:12px;">
              <button type="submit" id="btn-submit" class="btn btn-primary" style="padding:10px 24px;font-size:0.95rem;">Add Event</button>
              <button type="button" id="btn-cancel" class="btn btn-secondary" style="padding:10px 24px;font-size:0.95rem;display:none;">Cancel</button>
            </div>
          </form>
        </div>

        <div style="overflow-x:auto;">
          <table class="event-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date</th>
                <th>Location</th>
                <th>Fee</th>
                <th>Category</th>
                <th>Status</th>
                <th style="text-align:center;">Actions</th>
              </tr>
            </thead>
            <tbody id="events-body">
              <tr><td colspan="8" style="text-align:center;padding:24px;">Loading...</td></tr>
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
  <script>
    var API_URL = '../../Api/events.php';

    // Escape event text before adding it to the table.
    function esc(str) {
      if (str === null || str === undefined) return '';
      var div = document.createElement('div');
      div.appendChild(document.createTextNode(String(str)));
      return div.innerHTML;
    }

    // Show and hide request status messages.
    function showMsg(text, type) {
      var el = document.getElementById('status-msg');
      el.textContent = text;
      el.className = '';
      if (type === 'success') el.className = 'msg-success';
      else if (type === 'error') el.className = 'msg-error';
      else el.className = 'msg-loading';
      el.style.display = 'block';
    }

    function hideMsg() {
      document.getElementById('status-msg').style.display = 'none';
    }

    // Load and display all events from the API.
    function loadEvents(successMessage) {
      showMsg('Loading...', 'loading');
      fetch(API_URL)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          var tbody = document.getElementById('events-body');
          if (!data.success) {
            showMsg(data.message, 'error');
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:24px;">' + esc(data.message) + '</td></tr>';
            return;
          }
          if (!data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:24px;">No events found</td></tr>';
          } else {
            var html = '';
            for (var i = 0; i < data.data.length; i++) {
              var e = data.data[i];
              html += '<tr>';
              html += '<td>' + esc(e.event_id) + '</td>';
              html += '<td><strong>' + esc(e.title) + '</strong></td>';
              html += '<td>' + esc(e.event_date) + '</td>';
              html += '<td>' + esc(e.location) + '</td>';
              html += '<td>' + esc(e.fee) + '</td>';
              html += '<td>' + esc(e.category) + '</td>';
              var statusClass = e.status === 'closed' ? 'status-closed' : 'status-open';
              html += '<td class="' + statusClass + '">' + esc(e.status) + '</td>';
              html += '<td style="text-align:center;white-space:nowrap;">';
              html += '<button class="event-action event-edit" data-action="edit" data-id="' + esc(e.event_id) + '">Edit</button>';
              html += '<button class="event-action event-delete" data-action="delete" data-id="' + esc(e.event_id) + '">Delete</button>';
              html += '</td>';
              html += '</tr>';
            }
            tbody.innerHTML = html;
          }
          if (successMessage) showMsg(successMessage, 'success');
          else hideMsg();
        })
        .catch(function(err) {
          showMsg('Network error: ' + err.message, 'error');
        });
    }

    // Clear the form and return it to add mode.
    function resetForm() {
      document.getElementById('event-form').reset();
      document.getElementById('f-action').value = 'add';
      document.getElementById('f-event-id').value = '';
      document.getElementById('f-fee').value = '0';
      document.getElementById('form-title').textContent = 'Add New Event';
      document.getElementById('btn-submit').textContent = 'Add Event';
      document.getElementById('btn-cancel').style.display = 'none';
    }

    // Load one event into the form for editing.
    function editEvent(id) {
      showMsg('Loading event...', 'loading');
      fetch(API_URL + '?id=' + id)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          hideMsg();
          if (!data.success) {
            showMsg(data.message, 'error');
            return;
          }
          showMsg(data.message, 'success');
          var e = data.data;
          document.getElementById('f-action').value = 'update';
          document.getElementById('f-event-id').value = e.event_id;
          document.getElementById('f-title').value = e.title || '';
          document.getElementById('f-date').value = e.event_date || '';
          document.getElementById('f-location').value = e.location || '';
          document.getElementById('f-fee').value = e.fee || '0';
          document.getElementById('f-category').value = e.category || '';
          document.getElementById('f-status').value = e.status || 'open';
          document.getElementById('f-description').value = e.description || '';
          document.getElementById('form-title').textContent = 'Edit Event #' + e.event_id;
          document.getElementById('btn-submit').textContent = 'Update Event';
          document.getElementById('btn-cancel').style.display = 'inline-flex';
          window.scrollTo(0, 0);
        })
        .catch(function(err) {
          showMsg('Network error: ' + err.message, 'error');
        });
    }

    // Ask before deleting an event.
    function deleteEvent(id) {
      if (!confirm('Are you sure you want to delete this event?')) return;
      showMsg('Deleting...', 'loading');
      var formData = new FormData();
      formData.append('action', 'delete');
      formData.append('event_id', id);
      fetch(API_URL, { method: 'POST', body: formData })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data.success) {
            loadEvents(data.message);
          } else {
            showMsg(data.message, 'error');
          }
        })
        .catch(function(err) {
          showMsg('Network error: ' + err.message, 'error');
        });
    }

    // Handle row actions and the cancel button.
    document.getElementById('events-body').addEventListener('click', function(ev) {
      var button = ev.target;
      if (!button || button.tagName !== 'BUTTON') return;
      if (button.getAttribute('data-action') === 'edit') editEvent(button.getAttribute('data-id'));
      if (button.getAttribute('data-action') === 'delete') deleteEvent(button.getAttribute('data-id'));
    });

    document.getElementById('btn-cancel').addEventListener('click', resetForm);

    // Send form values to the API without reloading this page.
    document.getElementById('event-form').addEventListener('submit', function(ev) {
      ev.preventDefault();
      var formData = new FormData(this);
      var action = formData.get('action');
      showMsg(action === 'add' ? 'Adding...' : 'Updating...', 'loading');

      fetch(API_URL, { method: 'POST', body: formData })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data.success) {
            resetForm();
            loadEvents(data.message);
          } else {
            showMsg(data.message, 'error');
          }
        })
        .catch(function(err) {
          showMsg('Network error: ' + err.message, 'error');
        });
    });

    loadEvents();
  </script>
</body>
</html>