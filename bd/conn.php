<?php

$host = "localhost";
$db = "debia_academy";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error conexión: " . $e->getMessage());
}

?>

<!-- git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/Sebas-091202/Debia-Academy-2.git
git push -u origin main -->