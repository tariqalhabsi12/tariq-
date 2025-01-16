<?php
// sent.php
session_start();
require_once "config.php";
require_once "includes/functions.php"; // decryptMessage()

if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
  SELECT m.id, m.receiver_id, m.encrypted_text, m.attachment_path, m.sent_at,
         u.username AS receiver_name
  FROM messages m
  JOIN users u ON m.receiver_id = u.id
  WHERE m.sender_id = ?
  ORDER BY m.sent_at DESC
");
$stmt->execute([$userId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Sent Messages</title>
  <style>
    body { font-family:Arial,sans-serif; background:#f5f7fa; padding:40px; }
    .message {
      background:#fff; margin-bottom:20px; padding:20px; border-radius:6px;
      box-shadow:0 1px 4px rgba(0,0,0,0.1);
    }
    .meta { font-size:0.9em; color:#666; margin-bottom:10px; }
    a { color:#007bff; text-decoration:none; }
    a:hover { text-decoration:underline; }
  </style>
</head>
<body>
<h2>Sent Messages</h2>
<?php if($messages): ?>
  <?php foreach($messages as $msg):
    $decrypted = decryptMessage($msg['encrypted_text']);
  ?>
  <div class="message">
    <div class="meta">
      To: <?= htmlspecialchars($msg['receiver_name']) ?> |
      Sent: <?= $msg['sent_at'] ?>
    </div>
    <div><?= htmlspecialchars($decrypted) ?></div>
    <?php if(!empty($msg['attachment_path'])): ?>
      <p>Attachment: <a href="<?= htmlspecialchars($msg['attachment_path']) ?>" target="_blank">Download</a></p>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>No sent messages yet.</p>
<?php endif; ?>

<p><a href="home.php">Back to Home</a></p>
</body>
</html>
