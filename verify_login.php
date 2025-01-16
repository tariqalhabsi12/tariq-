<?php
// verify_login.php
session_start();
require_once "config.php";
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$username = trim($input['username'] ?? '');
$bioType  = $input['bioType'] ?? '';
$bioId    = trim($input['bioId'] ?? '');

if (!$username || !$bioType || !$bioId) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, email, status FROM users 
                       WHERE username=? AND biometric_type=? AND biometric_id=?");
$stmt->execute([$username, $bioType, $bioId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found or mismatch"]);
    exit;
}
if ($user['status'] !== 'approved') {
    echo json_encode(["success" => false, "message" => "Account not approved by admin"]);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$otpExpires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
$upd = $pdo->prepare("UPDATE users SET otp_code=?, otp_expires=? WHERE id=?");
$upd->execute([$otp, $otpExpires, $user['id']]);

// Attempt to send email
$to      = $user['email'];
$subject = "Your OTP Code";
$message = "Hello,\n\nYour OTP code is: $otp\nThis code is valid for 10 minutes.\n\nRegards,\nYourApp Team";
$headers = "From: noreply@yourdomain.com\r\n" .
           "Reply-To: noreply@yourdomain.com\r\n" .
           "X-Mailer: PHP/" . phpversion();

// Uncomment this mail line in production (make sure mail() is working on your host)
if (@mail($to, $subject, $message, $headers)) {
    // mail() returned true, but doesn't guarantee actual delivery
    echo json_encode(["success" => true, "user_id" => $user['id']]);
} else {
    // If mail() fails or is disabled, let the user know
    echo json_encode(["success" => false, "message" => "OTP generation ok, but failed to send email. Check server mail config."]);
}
