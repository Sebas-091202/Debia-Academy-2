<?php
session_start();
require_once("../bd/conn.php");

$id_usuario = $_SESSION["id"];
$id_modulo = $_POST["id_modulo"];

$sql = "INSERT INTO progreso (id_usuario, id_modulo, porcentaje)
VALUES (:usuario, :modulo, 100)
ON DUPLICATE KEY UPDATE porcentaje = 100";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":usuario"=>$id_usuario,
    ":modulo"=>$id_modulo
]);

header("Location: ../views/index_Usuario.php");
exit;
?>