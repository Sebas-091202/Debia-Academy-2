<?php
session_start();
require_once("../bd/conn.php");

/*  VALIDAR SESIÓN */
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "ADMIN") {
    header("Location: index_Usuario.php");
    exit;
}


// Evitar cache para que no se pueda volver atrás después de cerrar sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*  PAGINACIÓN */
$porPagina = 6;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) $pagina = 1;

$inicio = ($pagina - 1) * $porPagina;

/* TOTAL */
$totalSql = "SELECT COUNT(*) FROM modulos";
$totalModulos = $conn->query($totalSql)->fetchColumn();
$totalPaginas = ceil($totalModulos / $porPagina);

/*  MODULOS */
$sql = "SELECT * FROM modulos ORDER BY id DESC LIMIT :inicio, :limite";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":inicio", (int)$inicio, PDO::PARAM_INT);
$stmt->bindValue(":limite", (int)$porPagina, PDO::PARAM_INT);
$stmt->execute();

$modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_Modulos.css">
</head>

<body>

    <div class="dashboard">

        <!-- HEADER -->
        <div class="header">
            <h2>⚙️ Panel Administración</h2>

            <div class="menu" onclick="toggleMenu()">
                ⚙️
                <div class="dropdown" id="menu">
                    <a href="#">Configuración</a>
                    <a href="../controller/logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>

        <!-- CREAR MODULO -->
        <div class="card">

            <h3>Crear Módulo</h3>

            <form action="../controller/crear_modulo.php" method="POST">

                <input type="text" name="titulo" placeholder="Título módulo" required>

                <select name="area" required>
                    <option value="">Área</option>
                    <option>Investigaciones Privadas</option>
                    <option>Visitas Domiciliarias</option>
                    <option>Poligrafía</option>
                    <option>Validación Académica</option>
                    <option>Asesoría Jurídica</option>
                </select>

                <button class="btn">Crear módulo</button>

            </form>

        </div>

        <!-- SUBIR CONTENIDO -->
        <div class="card">

            <h3>Subir contenido</h3>

            <form action="../controller/subir_archivo.php"
                method="POST"
                enctype="multipart/form-data">

                <select name="id_modulo" required>
                    <option value="">Seleccionar módulo</option>

                    <?php foreach ($modulos as $m): ?>
                        <option value="<?= $m["id"] ?>">
                            <?= $m["titulo"] ?> (<?= $m["area"] ?>)
                        </option>
                    <?php endforeach; ?>

                </select>

                <select name="tipo" required>
                    <option value="texto">Texto</option>
                    <option value="video">Video</option>
                    <option value="archivo">Archivo</option>
                </select>

                <textarea name="contenido" placeholder="Texto o link video"></textarea>

                <input type="file" name="archivo">

                <button class="btn">Subir contenido</button>

            </form>

        </div>

        <!-- LISTA MODULOS -->
        <div class="card">

            <h3>📚 Módulos creados</h3>

            <div class="mod-list">

                <?php foreach ($modulos as $m): ?>
                    <a href="admin_ver_modulos.php?id=<?= $m["id"] ?>" class="mod-card">

                        <div class="mod-title">
                            📚 <?= $m["titulo"] ?>
                        </div>

                        <div class="mod-area">
                            <?= $m["area"] ?>
                        </div>

                    </a>
                <?php endforeach; ?>

            </div>

            <!-- PAGINACIÓN -->
            <div class="paginacion">

                <?php if ($pagina > 1): ?>
                    <a href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>"
                        class="<?= ($pagina == $i) ? 'activo-pagina' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
                <?php endif; ?>

            </div>

        </div>

    </div>

    <script>
        function toggleMenu() {
            let m = document.getElementById("menu");
            m.style.display = m.style.display === "block" ? "none" : "block";
        }
    </script>

</body>

</html>