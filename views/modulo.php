<?php
session_start();
require_once("../bd/conn.php");

/* VERIFICAR SESIÓN */
if (!isset($_SESSION["id"]) || !isset($_SESSION["usuario"]) || !isset($_SESSION["area"])) {
    header("Location: index_Login.php");
    exit;
}
/* VALIDAR ROL DESPUÉS */
if ($_SESSION['rol'] !== 'USUARIO') {
    header("Location: ../views/index_Login.php");
    exit;
}

// Evitar cache para que no se pueda volver atrás después de cerrar sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$id_usuario = $_SESSION["id"];
$area = $_SESSION["area"];
$id_modulo = $_GET["id"] ?? 1;

/* ✅ VALIDAR MODULO */
$sql = "SELECT * FROM modulos WHERE id = :id AND area = :area";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ":id" => $id_modulo,
    ":area" => $area
]);

$modulo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$modulo) {
    echo "Acceso no permitido";
    exit;
}

/* ✅ CONTENIDO */
$sql2 = "SELECT * FROM contenidos WHERE id_modulo = :id ORDER BY id ASC";
$stmt2 = $conn->prepare($sql2);
$stmt2->execute([":id" => $id_modulo]);
$contenidos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

/* ✅ PROGRESO */
$sql3 = "SELECT porcentaje FROM progreso 
         WHERE id_usuario = :u AND id_modulo = :m";

$stmt3 = $conn->prepare($sql3);
$stmt3->execute([
    ":u" => $id_usuario,
    ":m" => $id_modulo
]);

$res = $stmt3->fetch(PDO::FETCH_ASSOC);
$porcentaje = $res ? $res["porcentaje"] : 0;

$titulo = $modulo["titulo"];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;

            background: linear-gradient(135deg, #c40c0c, #111827, #ffffff);
            background-size: 600% 600%;
            animation: grad 20s infinite;

            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        @keyframes grad {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            width: 900px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            padding: 30px;
            border-radius: 20px;
            color: white;
        }

        h1 {
            text-align: center;
        }

        a {
            color: white;
            text-decoration: none;
        }

        /* BLOQUES */
        .bloque {
            margin-top: 20px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 15px;
        }

        /* BARRA */
        .bar {
            margin-top: 20px;
            height: 12px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #22c55e, #4ade80);
        }

        /* BOTONES */
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 20px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        button:disabled {
            background: gray;
        }

        .volver {
            margin-top: 15px;
            display: inline-block;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1><?= $titulo ?></h1>

        <?php if (empty($contenidos)): ?>
            <p style="color:#f87171;">❌ Este módulo aún no tiene contenido</p>
        <?php endif; ?>

        <?php foreach ($contenidos as $c): ?>

            <!-- TEXTO -->
            <?php if ($c["tipo"] == "texto"): ?>
                <div class="bloque">
                    <?= nl2br($c["contenido"]) ?>
                </div>
            <?php endif; ?>

            <!-- VIDEO -->
            <?php if ($c["tipo"] == "video"): ?>
                <div class="bloque">
                    <iframe width="100%" height="400"
                        src="<?= $c["contenido"] ?>"
                        frameborder="0" allowfullscreen>
                    </iframe>
                </div>
            <?php endif; ?>

            <!-- ARCHIVOS -->
            <?php if ($c["tipo"] == "archivo" || $c["tipo"] == "pdf"): ?>

                <div class="bloque">

                    <?php
                    $archivo = $c["contenido"];

                    if (strpos($archivo, ".pdf") !== false): ?>

                        <iframe src="<?= $archivo ?>#toolbar=1"
                            width="100%" height="500">
                        </iframe>

                    <?php elseif (preg_match('/\.(jpg|png|jpeg|gif)$/i', $archivo)): ?>

                        <img src="<?= $archivo ?>"
                            style="width:100%; border-radius:10px;">

                    <?php else: ?>

                        <a href="<?= $archivo ?>" target="_blank">Abrir archivo</a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        <?php endforeach; ?>

        <!-- PROGRESO -->
        <div class="bar">
            <span style="width:<?= $porcentaje ?>%"></span>
        </div>

        <p><?= $porcentaje ?>% completado</p>

        <!-- BOTÓN -->
        <?php if ($porcentaje < 100): ?>
            <form action="../controller/guardar_progreso.php" method="post">
                <input type="hidden" name="id_modulo" value="<?= $id_modulo ?>">
                <button>Marcar como completado</button>
            </form>
        <?php else: ?>
            <button disabled>Módulo completado</button>
        <?php endif; ?>

        <a href="index_Usuario.php" class="volver">← Volver</a>

    </div>

</body>

</html>