<?php
require_once("../bd/conn.php");


$id = $_POST["id"];
$tipo = $_POST["tipo"];
$contenido = $_POST["contenido"] ?? "";

// Si sube archivo
if (!empty($_FILES["archivo"]["name"])) {

    $ruta = "../uploads/";
    $nombreArchivo = time() . "_" . $_FILES["archivo"]["name"];
    $destino = $ruta . $nombreArchivo;

    move_uploaded_file($_FILES["archivo"]["tmp_name"], $destino);

    $contenido = $destino;
    
}

// UPDATE
$stmt = $conn->prepare("UPDATE contenidos SET tipo=:tipo, contenido=:contenido WHERE id=:id");
$stmt->execute([
    ":tipo" => $tipo,
    ":contenido" => $contenido,
    ":id" => $id
]);


header("Location: ".$_SERVER['HTTP_REFERER']);