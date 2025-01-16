<?php
// send_message.php
session_start();
require_once "config.php";
require_once "includes/functions.php"; // include our encryption helpers

if(!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Ensure uploads folder for attachments
$uploadDir = __DIR__ . "/uploads/";
if(!is_dir($uploadDir)){
  mkdir($uploadDir, 0777, true);
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  $receiverName = trim($_POST['receiver_name'] ?? '');
  $message      = trim($_POST['message'] ?? '');
  $senderId     = $_SESSION['user_id'];

  if($receiverName && $message){
    // find receiver
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
    $stmt->execute([$receiverName]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if($r){
      $receiverId = $r['id'];
      $attachmentPath = null;

      // handle file
      if(!empty($_FILES['attachment']['name'])){
        $fileName   = basename($_FILES['attachment']['name']);
        $targetFile = $uploadDir . $fileName;
        if(move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)){
          $attachmentPath = "uploads/" . $fileName;
        } else {
          $error = "Failed to upload attachment.";
        }
      }

      if(!isset($error)){
        // Encrypt the message
        $encryptedText = encryptMessage($message);

        // Insert
        $ins = $pdo->prepare("
          INSERT INTO messages (sender_id, receiver_id, encrypted_text, attachment_path)
          VALUES (?,?,?,?)
        ");
        $ins->execute([$senderId, $receiverId, $encryptedText, $attachmentPath]);
        $success = "Message sent to $receiverName!";
      }
    } else {
      $error = "User not found: $receiverName";
    }
  } else {
    $error = "Please fill all fields.";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Send Message</title>
  <style>
    body { font-family:Arial,sans-serif; background:#f5f7fa; padding:40px; }
    .container { background:#fff; padding:20px; border-radius:8px; max-width:500px; margin:auto; }
    .msg { font-weight:600; margin-bottom:10px; }
    .msg.success { color:green; }
    .msg.error { color:red; }
    label { display:block; margin-bottom:5px; font-weight:600; }
    input[type="text"], textarea, input[type="file"] {
      width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; margin-bottom:10px;
    }
    button {
      background:#007bff; color:#fff; padding:10px 16px;
      border:none; border-radius:4px; cursor:pointer;
    }
    button:hover { background:#0069d9; }
  </style>
</head>
<body>
<div class="container">
  <h2>Send a Message</h2>
  <?php if(!empty($success)): ?>
    <p class="msg success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>
  <?php if(!empty($error)): ?>
    <p class="msg error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Receiver Username:</label>
    <input type="text" name="receiver_name" required>

    <label>Message:</label>
    <textarea name="message" required></textarea>

    <label>Attachment (optional):</label>
    <input type="file" name="attachment">

    <button type="submit">Send</button>
  </form>
  <p><a href="home.php">Back to Home</a></p>
</div>
</body>
</html>
