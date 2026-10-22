<?php
$menu = [
    ['Dashboard', 'admin_dashboard.php', true],
    ['Manage Students', 'students_manage.php', true],
    ['Events', 'events_list.php', true],
    ['Events (AJAX)', 'events_ajax.php', true],
    ['Manage Admins', 'admins_manage.php', is_super_admin()],
    ['Audit Log', 'audit_log.php', is_super_admin()],
    ['Logout', '../../logout.php', true]
];
?>
<nav class="n-links">
  <?php foreach ($menu as $item): ?>
    <?php if ($item[2]): ?>
      <a href="<?php echo htmlspecialchars($item[1], ENT_QUOTES, 'UTF-8'); ?>" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === $item[1] ? ' active' : ''; ?>"><?php echo htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8'); ?></a>
    <?php endif; ?>
  <?php endforeach; ?>
</nav>
