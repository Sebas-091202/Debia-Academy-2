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
$usuario = $_SESSION["usuario"];
$area = $_SESSION["area"];


/* TRAER MÓDULOS DESDE BD */
$sql = "SELECT m.*, COUNT(c.id) as total_contenido
        FROM modulos m
        LEFT JOIN contenidos c ON m.id = c.id_modulo
        WHERE m.area = :area
        GROUP BY m.id
        ORDER BY m.id ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([":area"=>$area]);

$modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* AGREGAR PROGRESO */
foreach ($modulos as &$m) {

  $sql2 = "SELECT porcentaje FROM progreso 
           WHERE id_usuario = :usuario AND id_modulo = :modulo";

  $stmt2 = $conn->prepare($sql2);
  $stmt2->execute([
    ":usuario"=>$id_usuario,
    ":modulo"=>$m["id"]
  ]);

  $res = $stmt2->fetch(PDO::FETCH_ASSOC);
  $m["progreso"] = $res ? $res["porcentaje"] : 0;
}
unset($m);

/* VALIDAR SI TODO COMPLETO */
$completo = true;
foreach($modulos as $m){
  if($m['progreso'] < 100){
    $completo = false;
    break;
  }
}

/* PROGRESO GLOBAL */
$total = array_sum(array_column($modulos, 'progreso'));
$cantidad = count($modulos);
$progresoGlobal = $cantidad > 0 ? $total / $cantidad : 0;
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>DEBIA Academy</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/user.css">
</head>

<body>

  <div class="dashboard">

    <div class="header">
      <img src="../img/debia-academy-blanco.png">
      <h1><?php echo $usuario; ?> - Área: <?php echo $area; ?></h1>
    </div>

    <!-- PROGRESO GLOBAL -->
    <div class="progress-global">
      <p>Progreso general</p>
      <div class="progress-bar">
        <span style="width: <?php echo $progresoGlobal; ?>%"></span>
      </div>
    </div>

    <!-- MODULOS -->
    <div class="modules">

      <?php foreach ($modulos as $m): ?>
        <div class="card">
          <h3><?php echo $m['titulo']; ?></h3>

          <div class="bar">
            <span style="width:<?php echo $m['progreso']; ?>%"></span>
          </div>

          <a href="modulo.php?id=<?php echo $m['id']; ?>" class="btn">Ver módulo</a>
        </div>
      <?php endforeach; ?>

      <!-- EVALUACION -->
      <div class="card eval">
        <h3>Evaluación Final</h3>
        <div class="estado">

          <?php if ($completo): ?>
            <p class="estado" style="color:#22c55e;">✅ Puedes presentar la evaluación</p>
          <?php else: ?>
            <p class="estado" style="color:#facc15;">⏳ Completa todos los módulos</p>
          <?php endif; ?>
          <p>Complete todos los módulos para acceder a la evaluación.</p> 
        </div>

        </p>

        <a href="evaluacion.php"
          class="btn <?php echo !$completo ? 'disabled' : ''; ?>">
          Ir a Evaluación
        </a>
      </div>

    </div>

    <!-- LOGOUT -->
    <a href="../controller/logout.php" class="btn logout">Cerrar Sesión</a>

  </div>

</body>

</html>