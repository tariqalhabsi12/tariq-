<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}
$originalId = $_POST['original_id'] ?? '';
$receiverName = $_POST['receiver_name'] ?? '';
?>
<!DOCTYPE html>
<html>
<head><title>Reply</title></head>
<body>
<h2>Reply to <?= htmlspecialchars($receiverName) ?></h2>
<form action="send_message.php" method="POST">
  <input type="hidden" name="receiver_name" value="<?= htmlspecialchars($receiverName) ?>">
  <label>Message:</label><br>
  <textarea name="content"></textarea><br><br>
  <button type="submit">Send Reply</button>
</form>
</body>
</html>
