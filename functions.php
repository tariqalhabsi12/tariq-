<?php
// includes/functions.php
function encryptMessage($plaintext, $secretKey="CHANGE_THIS_SECRET") {
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    $iv       = openssl_random_pseudo_bytes($ivLength);
    $cipher   = openssl_encrypt($plaintext, 'AES-256-CBC', $secretKey, 0, $iv);
    // Store IV + cipher together, base64-encoded
    return base64_encode($iv . "::" . $cipher);
}

function decryptMessage($encrypted, $secretKey="CHANGE_THIS_SECRET") {
    $data = base64_decode($encrypted);
    list($iv, $cipher) = explode("::", $data, 2);
    $plaintext = openssl_decrypt($cipher, 'AES-256-CBC', $secretKey, 0, $iv);
    return $plaintext;
}
