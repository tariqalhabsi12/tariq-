<?php
// login.php
session_start();
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login (Biometric + OTP)</title>
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
      background:#28a745; color:#fff; padding:10px 14px;
      border:none; border-radius:4px; cursor:pointer; font-weight:600;
    }
    .btn:hover { background:#218838; }
    .link { display:block; margin-top:10px; color:#007bff; text-decoration:none; }
    .link:hover { text-decoration:underline; }
    #otpSection { display:none; margin-top:15px; }
  </style>
</head>
<body>
<div class="container">
  <h2>Login (Biometric + OTP)</h2>
  <form id="loginForm" onsubmit="return false;">
    <div class="input-group">
      <label>Username</label>
      <input type="text" id="username" required>
    </div>
    <p style="text-align:left; margin-bottom:5px; font-weight:600;">Biometric:</p>
    <label><input type="radio" name="bio_type" value="eye" checked> Eye ID</label><br>
    <label><input type="radio" name="bio_type" value="face"> Face ID</label><br>
    <label><input type="radio" name="bio_type" value="fingerprint"> Fingerprint</label><br><br>
    <button class="btn" id="loginBtn">Biometric Login</button>
  </form>

  <div id="otpSection">
    <p>OTP sent via email. Enter it below:</p>
    <input type="text" id="otpInput" placeholder="6-digit code" style="width:100%; margin-bottom:10px;">
    <button class="btn" id="otpBtn">Verify OTP</button>
  </div>

  <a class="link" href="register.php">Don't have an account? Register</a>
</div>

<!-- FaceIO modal for liveness detection -->
<div id="faceio-modal"></div>

<!-- FaceIO script -->
<script src="https://cdn.faceio.net/fio.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const faceio = new faceIO("fioa8ad1"); // your Public ID
  const loginBtn = document.getElementById("loginBtn");
  const otpSection = document.getElementById("otpSection");
  const otpBtn = document.getElementById("otpBtn");
  let tempUserId = null;

  // Step 1: Biometric
  loginBtn.addEventListener("click", async () => {
    const username = document.getElementById("username").value.trim();
    const bioType  = document.querySelector('input[name="bio_type"]:checked').value;

    if(!username) {
      alert("Enter username.");
      return;
    }

    let bioId = "";
    try {
      if(bioType==='eye' || bioType==='face') {
        // FaceIO authenticate
        let resp = await faceio.authenticate({ locale:"auto" });
        bioId = resp.facialId;
      } else {
        // fingerprint mock
        bioId = "fingerprint-12345";
        alert("Fingerprint recognized (mock).");
      }

      // call verify_login.php
      let res = await fetch('verify_login.php', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ username, bioType, bioId })
      });
      let data = await res.json();
      if(data.success) {
        tempUserId = data.user_id;
        alert("Biometric recognized! OTP sent to email.");
        otpSection.style.display = "block";
      } else {
        alert("Login failed: " + data.message);
      }
    } catch(err) {
      console.error("Biometric error:", err);
      alert("Biometric login failed: " + err);
    }
  });

  // Step 2: OTP
  otpBtn.addEventListener("click", async () => {
    const otpVal = document.getElementById("otpInput").value.trim();
    if(!otpVal) {
      alert("Enter the OTP code.");
      return;
    }
    let res = await fetch('verify_otp.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ user_id: tempUserId, otp: otpVal })
    });
    let data = await res.json();
    if(data.success) {
      alert("Login successful!");
      window.location.href="home.php";
    } else {
      alert("OTP error: " + data.message);
    }
  });
});
</script>
</body>
</html>
