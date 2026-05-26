<?php
session_start();
require_once("../bd/conn.php");

try {

    $id_modulo = $_POST["id_modulo"] ?? null;
    $tipo = $_POST["tipo"] ?? null;
    $contenido = $_POST["contenido"] ?? null;

    /* VALIDAR */
    if(!$id_modulo || !$tipo){
        throw new Exception("Datos incompletos");
    }

    /* OBTENER NOMBRE DEL MÓDULO */
    $stmtMod = $conn->prepare("SELECT titulo FROM modulos WHERE id = :id");
    $stmtMod->execute([":id"=>$id_modulo]);
    $mod = $stmtMod->fetch(PDO::FETCH_ASSOC);

    $nombreModulo = $mod ? $mod["titulo"] : "Módulo";

    /* INSERTAR CONTENIDO */
    $sql = "INSERT INTO contenidos (id_modulo, tipo, contenido)
            VALUES (:m, :t, :c)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":m"=>$id_modulo,
        ":t"=>$tipo,
        ":c"=>$contenido
    ]);

    /* MENSAJE ÉXITO */
    $_SESSION["mensaje"] = "✅ Contenido creado correctamente en: <b>$nombreModulo</b>";
    $_SESSION["tipo"] = "success";

} catch (Exception $e) {

    /* ERROR */
    $_SESSION["mensaje"] = "❌ Error: no se pudo crear el contenido";
    $_SESSION["tipo"] = "error";
}

/* REDIRECCIÓN */
header("Location: ../views/admin_modulos.php");
exit;
?>
