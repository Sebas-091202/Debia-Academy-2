<?php
require_once("../bd/conn.php");

$titulo = $_POST["titulo"];
$area = $_POST["area"];

$sql = "INSERT INTO modulos (titulo, area) VALUES (:t, :a)";
$stmt = $conn->prepare($sql);

$stmt->execute([
    ":t"=>$titulo,
    ":a"=>$area
]);

header("Location: ../views/admin_modulos.php");
exit;
?>