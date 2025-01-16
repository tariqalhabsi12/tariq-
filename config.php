<?php
/**
 * config.php
 * Database connection details
 */

$host = "localhost";         // or Hostinger's server name
$db_name = "u236048864_tariq";    // your database name
$db_user = "u236048864_tariq";     // your database username
$db_pass = "!@Tariq123"; // your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
