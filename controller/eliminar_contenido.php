<?php
session_start();
require_once("../bd/conn.php");

/*  VALIDACIÓN SEGURIDAD */
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "ADMIN") {
    header("Location: ../views/index_Login.php");
    exit;
}

/*  VALIDAR PETICIÓN */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {

    $id = $_POST["id"];

    /*  OBTENER CONTENIDO */
    $stmt = $conn->prepare("SELECT contenido FROM contenidos WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {

        $ruta = $data["contenido"];

        /*  ELIMINAR ARCHIVO (SI EXISTE) */
        if (!empty($ruta)) {

            // Solo borrar archivos dentro de uploads
            if (strpos($ruta, "../uploads/") !== false && file_exists($ruta)) {
                unlink($ruta);
            }
        }

        /*  ELIMINAR REGISTRO */
        $stmt2 = $conn->prepare("DELETE FROM contenidos WHERE id = :id");
        $stmt2->execute([":id" => $id]);
    }

    /*  REDIRECCIÓN SEGURA */
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../views/admin_Ver_Modulos.php");
    }

    exit;
}