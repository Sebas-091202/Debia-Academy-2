<?php
require_once("../bd/conn.php");

$id_modulo = $_POST["id_modulo"];
$tipo = $_POST["tipo"];
$contenido = $_POST["contenido"];

$sql = "INSERT INTO contenidos (id_modulo, tipo, contenido)
VALUES (:m, :t, :c)";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":m"=>$id_modulo,
    ":t"=>$tipo,
    ":c"=>$contenido
]);

header("Location: ../views/admin_modulos.php");
exit;
?>