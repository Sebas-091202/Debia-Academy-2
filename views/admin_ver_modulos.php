<?php
session_start();
require_once("../bd/conn.php");

/*  VALIDAR ADMIN */
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "ADMIN") {
    header("Location: index_Usuario.php");
    exit;
}


// Evitar cache para que no se pueda volver atrás después de cerrar sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$id_modulo = $_GET["id"] ?? 0;

/* MODULO */
$stmt = $conn->prepare("SELECT * FROM modulos WHERE id=:id");
$stmt->execute([":id" => $id_modulo]);
$modulo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$modulo) {
    echo "Módulo no encontrado";
    exit;
}

/* CONTENIDOS */
$stmt2 = $conn->prepare("SELECT * FROM contenidos WHERE id_modulo=:id ORDER BY id ASC");
$stmt2->execute([":id" => $id_modulo]);
$contenidos = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $modulo["titulo"] ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap">

    <style>
        /* ================= BODY ================= */
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;

            background: linear-gradient(135deg, #c40c0c, #111827, #ffffff);
            background-size: 600% 600%;
            animation: grad 20s infinite;

            display: flex;
            justify-content: center;
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

        /* ================= CONTAINER ================= */
        .container {
            width: 90%;
            max-width: 1000px;
            margin-top: 30px;

            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);

            padding: 30px;
            border-radius: 20px;

            color: white;
        }

        /* ================= HEADER ================= */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-back {
            padding: 8px 14px;
            background: #2563eb;
            border-radius: 8px;
            color: white;
            text-decoration: none;
        }

        /* ================= BLOQUES ================= */
        .bloque {
            margin-top: 20px;
            padding: 20px;

            background: rgba(0, 0, 0, 0.4);
            border-radius: 15px;

            transition: 0.3s;
        }

        .bloque:hover {
            transform: translateY(-3px);
        }

        /* ================= CONTENIDO ================= */
        iframe {
            border-radius: 10px;
            margin-top: 10px;
        }

        /* ================= BOTONES ================= */
        .edit-box {
            margin-top: 15px;
        }

        .delete-box {
            margin-top: 10px;
            display: flex;

        }

        /* MEJOR BOTÓN */
        .btn-delete {
            background: #dc2626;
            padding: 8px 14px;
            border-radius: 8px;
            border: none;
            color: white;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #991b1b;
        }

        button {
            padding: 8px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            color: white;
        }

        .btn-edit {
            background: #16a34a;
        }

        .btn-delete {
            background: #dc2626;
        }

        .btn-edit:hover {
            background: #15803d;
        }

        .btn-delete:hover {
            background: #991b1b;
        }

        /* INPUT EDIT */
        .input-edit {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: none;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <h2>📚 <?= $modulo["titulo"] ?></h2>
            <a href="admin_Modulos.php" class="btn-back">← Volver</a>
        </div>

        <?php if (empty($contenidos)): ?>
            <p style="color:#f87171;">❌ Este módulo no tiene contenido</p>
        <?php endif; ?>

        <?php foreach ($contenidos as $c): ?>

            <div class="bloque">

                <!-- TEXTO -->
                <?php if ($c["tipo"] == "texto"): ?>
                    <p><?= nl2br($c["contenido"]) ?></p>
                <?php endif; ?>

                <!-- VIDEO -->
                <?php if ($c["tipo"] == "video"): ?>
                    <iframe width="100%" height="400"
                        src="<?= $c["contenido"] ?>"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>
                <?php endif; ?>


                <!-- ARCHIVO -->
                <?php if ($c["tipo"] == "archivo"): ?>

                    <iframe src="<?= $c["contenido"] ?>#toolbar=1"
                        width="100%" height="400">
                    </iframe>
                <?php elseif (preg_match('/\.(jpg|jpeg|png|gif)$/i', $c["contenido"])): ?>
                    <img src="<?= $c["contenido"] ?>" style="width:100%;">

                <?php endif; ?>

                <!-- ACCIONES -->
                <!-- ========= EDICIÓN ========= -->
                <div class="edit-box">

                    <form action="../controller/editar_contenido.php"
                        method="POST"
                        enctype="multipart/form-data">


                        <!-- SELECT TIPO -->
                        <select name="tipo" class="input-edit">
                            <option value="texto" <?= $c["tipo"] == "texto" ? "selected" : "" ?>>Texto</option>
                            <option value="video" <?= $c["tipo"] == "video" ? "selected" : "" ?>>Video</option>
                            <option value="archivo" <?= $c["tipo"] == "archivo" ? "selected" : "" ?>>Archivo</option>
                        </select>


                        <input type="hidden" name="id" value="<?= $c["id"] ?>">

                        <textarea name="contenido" class="input-edit"
                            placeholder="Editar contenido (texto o link)"><?=
                            ($c["tipo"] == "texto" || $c["tipo"] == "video") ? $c["contenido"] : ''
                            ?></textarea>

                        <input type="file" name="archivo" class="input-edit">

                        <button class="btn-edit">Actualizar</button>

                    </form>

                </div>


                <!-- ========= ELIMINAR ========= -->
                <div class="delete-box">

                    <form action="../controller/eliminar_contenido.php" method="POST">
                        <input type="hidden" name="id" value="<?= $c["id"] ?>">
                        <button class="btn-delete">Eliminar</button>
                    </form>

                </div>


            </div>

        <?php endforeach; ?>

    </div>
<script>
document.querySelectorAll('select[name="tipo"]').forEach(select => {
    select.addEventListener('change', function () {
        const form = this.closest('form');
        const textarea = form.querySelector('textarea');
        const fileInput = form.querySelector('input[type="file"]');

        if (this.value === 'archivo') {
            textarea.style.display = 'none';
            fileInput.style.display = 'block';
        } else {
            textarea.style.display = 'block';
            fileInput.style.display = 'none';
        }
    });
});
</script>
</body>

</html>