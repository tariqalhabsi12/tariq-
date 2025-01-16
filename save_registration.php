<?php
// save_registration.php
require_once "config.php";
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$username   = trim($input['username'] ?? '');
$email      = trim($input['email'] ?? '');
$bioType    = $input['bioType'] ?? 'eye';
$biometricId= trim($input['biometricId'] ?? '');

if(!$username || !$email || !$biometricId) {
  echo json_encode(["success"=>false,"message"=>"Missing fields"]);
  exit;
}

// check duplicates
$stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
$stmt->execute([$username,$email]);
if($stmt->rowCount()>0) {
  echo json_encode(["success"=>false,"message"=>"Username or email already taken"]);
  exit;
}

// Insert => status = pending
try {
  $ins = $pdo->prepare("
    INSERT INTO users (username,email,biometric_type,biometric_id,status)
    VALUES (?,?,?,?, 'pending')
  ");
  $ins->execute([$username,$email,$bioType,$biometricId]);
  echo json_encode(["success"=>true]);
} catch(Exception $e) {
  echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
