<?php
// verify_otp.php
session_start();
require_once "config.php";
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$userId = $input['user_id'] ?? null;
$otp    = $input['otp'] ?? null;

if(!$userId || !$otp){
  echo json_encode(["success"=>false,"message"=>"Missing user or OTP"]);
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
  echo json_encode(["success"=>false,"message"=>"User not found"]);
  exit;
}
if($user['otp_code'] === $otp && strtotime($user['otp_expires'])>time()){
  // success => finalize login
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['username']= $user['username'];

  // clear OTP
  $clr = $pdo->prepare("UPDATE users SET otp_code=NULL, otp_expires=NULL WHERE id=?");
  $clr->execute([$userId]);

  echo json_encode(["success"=>true]);
} else {
  echo json_encode(["success"=>false,"message"=>"Invalid or expired OTP"]);
}
