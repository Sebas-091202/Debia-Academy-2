<?php
require_once("../bd/conn.php");

/* ✅ DETECTAR ERROR DE TAMAÑO */
if(empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0){
    die("⚠️ El archivo es demasiado grande. Reduce su tamaño o aumenta el límite.");
}

/* ✅ VALIDAR */
$id_modulo = $_POST["id_modulo"] ?? null;
$tipo = $_POST["tipo"] ?? null;
$contenido = $_POST["contenido"] ?? null;

if(!$id_modulo || !$tipo){
    die("Error: faltan datos obligatorios");
}

$ruta = null;

/* ✅ SUBIR */
if(isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] == 0){

    $nombre = time()."_".$_FILES["archivo"]["name"];
    $ruta = "../uploads/".$nombre;

    move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta);
}

$data = $ruta ? $ruta : $contenido;

/* ✅ INSERT */
$sql = "INSERT INTO contenidos (id_modulo,tipo,contenido)
        VALUES (:m,:t,:c)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ":m"=>$id_modulo,
    ":t"=>$tipo,
    ":c"=>$data
]);

header("Location: ../views/admin_Modulos.php");