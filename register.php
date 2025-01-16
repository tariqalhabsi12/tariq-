<?php
// register.php
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register (Biometrics)</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
    body {
      background:#f5f7fa; display:flex; justify-content:center; align-items:center;
      height:100vh;
    }
    .container {
      background:#fff; border-radius:8px;
      box-shadow:0 2px 10px rgba(0,0,0,0.1);
      width:320px; padding:30px; text-align:center;
    }
    h2 { margin-bottom:20px; color:#333; }
    .input-group { text-align:left; margin-bottom:15px; }
    .input-group label { display:block; margin-bottom:5px; font-weight:600; color:#555; }
    .input-group input {
      width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;
    }
    .btn {
      background:#007bff; color:#fff; padding:10px 14px;
      border:none; border-radius:4px; cursor:pointer; font-weight:600;
    }
    .btn:hover { background:#0069d9; }
    .link { display:block; margin-top:10px; color:#007bff; text-decoration:none; }
    .link:hover { text-decoration:underline; }
  </style>
</head>
<body>
<div class="container">
  <h2>Register (Biometrics)</h2>
  <form id="regForm" onsubmit="return false;">
    <div class="input-group">
      <label>Username</label>
      <input type="text" id="username" required>
    </div>
    <div class="input-group">
      <label>Email</label>
      <input type="email" id="email" required>
    </div>
    <p style="text-align:left; margin-bottom:5px; font-weight:600;">Biometric Type:</p>
    <label><input type="radio" name="bio_type" value="eye" checked> Eye ID</label><br>
    <label><input type="radio" name="bio_type" value="face"> Face ID</label><br>
    <label><input type="radio" name="bio_type" value="fingerprint"> Fingerprint</label><br><br>
    <button class="btn" id="regBtn">Register</button>
  </form>
  <a class="link" href="login.php">Already have an account? Login</a>
</div>

<!-- FaceIO modal for liveness detection -->
<div id="faceio-modal"></div>

<!-- FaceIO script -->
<script src="https://cdn.faceio.net/fio.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const faceio = new faceIO("fioa8ad1"); // Your FaceIO public ID with liveness
  const regBtn = document.getElementById("regBtn");

  regBtn.addEventListener("click", async () => {
    const username = document.getElementById("username").value.trim();
    const email    = document.getElementById("email").value.trim();
    const bioType  = document.querySelector('input[name="bio_type"]:checked').value;

    if(!username || !email){
      alert("Please fill all fields.");
      return;
    }

    let biometricId = "";
    try {
      if(bioType==='eye' || bioType==='face'){
        // FaceIO enroll (liveness detection)
        let response = await faceio.enroll({
          locale:"auto",
          payload:{ username, email }
        });
        biometricId = response.facialId;
      } else {
        // fingerprint mock
        biometricId = "fingerprint-" + Date.now();
        alert("Fingerprint enrolled (mock).");
      }

      // Save user
      let res = await fetch('save_registration.php', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ username, email, bioType, biometricId })
      });
      let data = await res.json();
      if(data.success){
        alert("Registration successful! Admin must approve your account.");
        window.location.href="login.php";
      } else {
        alert("Error: " + data.message);
      }
    } catch(err){
      console.error("Biometric enrollment failed:", err);
      alert("Biometric enrollment failed: " + err);
    }
  });
});
</script>
</body>
</html>
