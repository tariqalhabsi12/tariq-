<?php
// home.php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Home - Secure Messaging</title>
  <style>
    body { font-family:Arial,sans-serif; background:#f5f7fa; padding:40px; }
    h2 { color:#333; }
    a {
      display:inline-block; margin-right:10px; 
      color:#007bff; text-decoration:none; font-weight:600;
    }
    a:hover { text-decoration:underline; }
  </style>
</head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
<p>
  <a href="inbox.php">Inbox</a> |
  <a href="sent.php">Sent Messages</a> |
  <a href="send_message.php">Send a Message</a> |
  <a href="logout.php">Logout</a>
</p>
</body>
</html>
