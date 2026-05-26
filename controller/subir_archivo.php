<?php
require_once("../bd/conn.php");

header('Content-Type: application/json');

try {

    // 🔥 LIMPIAR BUFFER
    while(ob_get_level()) ob_end_clean();

    $id_modulo = $_POST["id_modulo"] ?? null;
    $tipo = $_POST["tipo"] ?? null;
    $contenido = $_POST["contenido"] ?? null;

    if(!$id_modulo || !$tipo){
        throw new Exception("Faltan datos");
    }

    $ruta = null;

    if(isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] == 0){
        $nombre = time()."_".$_FILES["archivo"]["name"];
        $ruta = "../uploads/".$nombre;
        move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta);
    }

    $data = $ruta ? $ruta : $contenido;

    $stmt = $conn->prepare("
        INSERT INTO contenidos (id_modulo, tipo, contenido)
        VALUES (:m,:t,:c)
    ");

    $stmt->execute([
        ":m"=>$id_modulo,
        ":t"=>$tipo,
        ":c"=>$data
    ]);

    $mod = $conn->prepare("SELECT titulo FROM modulos WHERE id=:id");
    $mod->execute([":id"=>$id_modulo]);
    $m = $mod->fetch();

    echo json_encode([
        "tipo" => "success",
        "mensaje" => "✅ Contenido creado en: ".$m["titulo"]
    ]);

    exit;

} catch(Exception $e){

    echo json_encode([
        "tipo"=>"error",
        "mensaje"=>"❌ ".$e->getMessage()
    ]);

    exit;
}
