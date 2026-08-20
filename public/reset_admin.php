<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_twoone_kopi";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die($conn->connect_error);
}

$password = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE username='admin'");
$stmt->bind_param("s", $password);
$stmt->execute();

echo "Password admin berhasil direset.";