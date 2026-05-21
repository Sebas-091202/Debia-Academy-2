<?php
require '../bd/conn.php';

/* VERIFICAR SI YA EXISTE ADMIN */
$stmt = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='ADMIN'");
$existeAdmin = $stmt->fetchColumn() > 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEBIA Academy</title>

    <link rel="stylesheet" href="../css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

</head>

<body>

    <div class="container">

        <!-- IZQUIERDA -->
        <div class="left">
            <img src="../img/debia-academy-blanco.png" class="logo">
            <h2>DEBIA Academy</h2>
            <p>Capacitación profesional segura</p>
        </div>

        <!-- DERECHA -->
        <div class="right">

            <!-- LOGIN -->
            <form id="loginForm" class="form active" action="../controller/login.php" method="POST">

                <h2>Iniciar Sesión</h2>

                <div class="input-group">
                    <input type="email" name="correo" required placeholder=" ">
                    <label>Correo</label>
                </div>

                <div class="input-group password">
                    <input type="password" name="contrasena" id="loginPass" required placeholder=" ">
                    <label>Contraseña</label>
                    <i class='bx bx-show togglePass' onclick="togglePassword('loginPass', this)"></i>
                </div>

                <button type="submit">Ingresar</button>
                <div class="switch" onclick="toggle()">Crear cuenta</div>

            </form>

            <!-- REGISTRO -->
            <form id="registerForm" class="form" action="../controller/register.php" method="POST">

                <h2>Registro</h2>

                <div class="input-group">
                    <input type="text" name="nombre" required placeholder=" ">
                    <label>Nombre completo</label>
                </div>

                <div class="input-group">
                    <select name="tipo_documento" required>
                        <option value="" disabled selected placeholder=" "></option>
                        <option>Cédula</option>
                        <option>Pasaporte</option>
                    </select>
                    <label>Tipo documento</label>
                </div>

                <div class="input-group">
                    <input type="number" name="numero_identificacion" required placeholder=" ">
                    <label>Número identificación</label>
                </div>

                <div class="input-group">
                    <input type="email" name="correo" required placeholder=" ">
                    <label>Correo</label>
                </div>

                <div class="input-group">
                    <select name="rol" id="rolSelect" required>
                        <option value="" disabled selected placeholder=" "></option>

                        <option value="USUARIO">Usuario</option>

                        <!-- SOLO SI NO EXISTE ADMIN -->
                        <?php if (!$existeAdmin): ?>
                            <option value="ADMIN">Administrador</option>
                        <?php endif; ?>

                    </select>

                    <label>Rol</label>
                </div>

                <div class="input-group">
                    <select name="area" required>
                        <option value="" disabled selected placeholder=" "></option>
                        <option>Investigaciones Privadas</option>
                        <option>Visitas Domiciliarias</option>
                        <option>Poligrafía</option>
                        <option>Validación Académica</option>
                        <option>Asesoría Jurídica</option>
                    </select>
                    <label>Área</label>
                </div>

                <div class="input-group password">
                    <input type="password" name="contrasena" id="registerPass" required placeholder=" ">
                    <label>Contraseña</label>
                    <i class='bx bx-show togglePass' onclick="togglePassword('registerPass', this)"></i>
                </div>

                <button type="submit">Registrarse</button>

                <div class="switch" onclick="toggle()">Ya tengo cuenta</div>

            </form>

        </div>

    </div>

    <script src="../js/login.js"></script>

</body>

</html>