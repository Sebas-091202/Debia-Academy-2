<?php
session_start();
require_once("../bd/conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = $_POST["nombre"];
    $tipo = $_POST["tipo_documento"];
    $numero = $_POST["numero_identificacion"];
    $correo = $_POST["correo"];
    $rol = $_POST["rol"];
    $area = ($rol === "ADMIN") ? null : $_POST["area"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);

    try {

        /*  VALIDAR DUPLICADOS */
        $check = $conn->prepare("
            SELECT * FROM usuarios 
            WHERE correo = :correo 
            OR numero_identificacion = :numero
        ");

        $check->execute([
            ":correo" => $correo,
            ":numero" => $numero
        ]);

        $existe = $check->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            echo "<script>alert('Correo o documento ya registrado'); window.location.href='../views/index_Login.php';</script>";
            exit;
        }

        /*  VALIDAR ADMIN */
        $stmt = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='ADMIN'");
        $existeAdmin = $stmt->fetchColumn() > 0;

        /*  ASIGNAR ROL */
        if (!$existeAdmin) {
            $rol = "ADMIN";
        } else {
            $rol = "USUARIO";
        }

        /*  INSERT */
        $sql = "INSERT INTO usuarios 
        (nombre, tipo_documento, numero_identificacion, correo, area, contrasena, rol)
        VALUES (:nombre, :tipo, :numero, :correo, :area, :contrasena, :rol)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ":nombre" => $nombre,
            ":tipo" => $tipo,
            ":numero" => $numero,
            ":correo" => $correo,
            ":area" => $area,
            ":contrasena" => $contrasena,
            ":rol" => $rol
        ]);

        /*  CREAR SESIÓN (CLAVE) */
        $_SESSION["id"] = $conn->lastInsertId();
        $_SESSION["usuario"] = $nombre;
        $_SESSION["area"] = $area;
        $_SESSION["rol"] = $rol;

        /*  REDIRECCIÓN */
        if ($rol === "ADMIN") {
            header("Location: ../views/admin_modulos.php");
        } else {
            header("Location: ../views/index_Usuario.php");
        }
        exit;
    } catch (PDOException $e) {

        echo "<script>alert('Error en registro'); window.location.href='../views/index_Login.php';</script>";
    }
}

?>
