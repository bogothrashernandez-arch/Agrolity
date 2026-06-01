<?php
include 'conexion.php';

// Variable para mensajes de error o éxito
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Limpieza de datos
    $nombre   = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo   = mysqli_real_escape_string($conexion, $_POST['correo']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $rol      = mysqli_real_escape_string($conexion, $_POST['rol']);
    
    // 2. Encriptación (Seguridad)
    $password_encriptada = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 3. Verificación de correo duplicado
    $check_email = mysqli_query($conexion, "SELECT correo FROM usuarios WHERE correo = '$correo'");
    
    if (mysqli_num_rows($check_email) > 0) {
        $mensaje = "error_duplicado";
    } else {
        // 4. Inserción
        $sql = "INSERT INTO usuarios (nombre, correo, password, rol, telefono) 
                VALUES ('$nombre', '$correo', '$password_encriptada', '$rol', '$telefono')";

        if (mysqli_query($conexion, $sql)) {
            $mensaje = "exito";
        } else {
            $mensaje = "error_db";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Agrolity</title>
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Didact Gothic', sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .registro-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .registro-box h2 { color: #0BAE03; margin-bottom: 20px; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        .btn-registrar { width: 100%; padding: 12px; border: none; border-radius: 8px; background: #0BAE03; color: white; font-weight: bold; cursor: pointer; font-size: 1em; }
        .links { margin-top: 15px; font-size: 0.9em; }
        .links a { color: #0BAE03; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="registro-box">
        <h2>Regístrate en Agrolity</h2>
        <form action="registro.php" method="POST">
            <div class="input-group">
                <input type="text" name="nombre" placeholder="Nombre completo" required>
            </div>
            <div class="input-group">
                <input type="email" name="correo" placeholder="Correo electrónico" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="input-group">
                <input type="text" name="telefono" placeholder="Teléfono" required>
            </div>
            <div class="input-group">
                <select name="rol" required>
                    <option value="" disabled selected>Selecciona tu rol</option>
                    <option value="cliente">Cliente (Comprador)</option>
                    <option value="productor">Productor (Campesino)</option>
                </select>
            </div>
            <button type="submit" class="btn-registrar">Crear Cuenta</button>
        </form>
        <div class="links">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>

    <?php if($mensaje == "exito"): ?>
        <script>
            Swal.fire('¡Éxito!', 'Te has registrado correctamente.', 'success').then(() => {
                window.location.href = 'login.php';
            });
        </script>
    <?php elseif($mensaje == "error_duplicado"): ?>
        <script>Swal.fire('Error', 'El correo ya existe.', 'error');</script>
    <?php elseif($mensaje == "error_db"): ?>
        <script>Swal.fire('Error', 'Problema con la base de datos.', 'error');</script>
    <?php endif; ?>

</body>
</html>
