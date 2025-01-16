<?php
// admin/admin_panel.php
session_start();
require_once "../config.php";

// If you have an admin login, check it here (omitted for brevity)

if (isset($_POST['action'], $_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE users SET status='approved' WHERE id=?");
        $stmt->execute([$userId]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$userId]);
    }
}

$stmt = $pdo->query("SELECT * FROM users WHERE status='pending'");
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <style>
    body { font-family: Arial,sans-serif; margin:20px; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th,td { border:1px solid #ccc; padding:10px; }
    th { background:#eee; }
  </style>
</head>
<body>
<h1>Admin Panel - Pending Registrations</h1>

<?php if($pending): ?>
  <table>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Biometric</th>
      <th>Actions</th>
    </tr>
    <?php foreach($pending as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['username']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= htmlspecialchars($u['biometric_type']) ?></td>
      <td>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
          <button name="action" value="approve">Approve</button>
        </form>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
          <button name="action" value="reject">Reject</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php else: ?>
  <p>No pending users.</p>
<?php endif; ?>
</body>
</html>
