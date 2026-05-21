<?php
require_once("../bd/conn.php");

$id = $_POST["id"];
$contenido = $_POST["contenido"] ?? null;

$ruta = null;

/* ✅ SI ENVÍA ARCHIVO */
if(isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] == 0){

    $nombre = time() . "_" . $_FILES["archivo"]["name"];
    $ruta = "../uploads/" . $nombre;

    move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta);
}

/* ✅ DECIDIR QUÉ GUARDAR */

if($ruta){
    // Si subió archivo → reemplaza
    $sql = "UPDATE contenidos SET contenido=:c WHERE id=:id";
    $data = $ruta;
} else {
    // Si no → usa texto
    $sql = "UPDATE contenidos SET contenido=:c WHERE id=:id";
    $data = $contenido;
}

$stmt = $conn->prepare($sql);
$stmt->execute([
    ":c"=>$data,
    ":id"=>$id
]);

header("Location: ".$_SERVER['HTTP_REFERER']);