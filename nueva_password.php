<?php
// Forzar reporte de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexion.php';

$email_usuario = "";
if (isset($_GET['email'])) {
    $email_usuario = htmlspecialchars($_GET['email']);
}

// Procesar el cambio de contraseña cuando se envíe el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_clave'])) {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo_usuario']);
    $clave_plana = $_POST['nueva_clave'];

    // Validación extra en el servidor por seguridad
    if (!preg_match('/[A-Z]/', $clave_plana) || !preg_match('/[0-9]/', $clave_plana) || !preg_match('/[^A-Za-z0-9]/', $clave_plana)) {
        echo "<script>alert('La contraseña no cumple con los requisitos de seguridad establecidos.'); window.history.back();</script>";
        exit();
    }

    // Encriptar la nueva contraseña con el método seguro password_hash
    $clave_encriptada = password_hash($clave_plana, PASSWORD_DEFAULT);

    // Actualizar en la base de datos
    $actualizar = "UPDATE usuarios SET password = '$clave_encriptada' WHERE correo = '$correo'";
    
    if (mysqli_query($conexion, $actualizar)) {
        echo "<script>alert('Contraseña actualizada con éxito. Ya puedes iniciar sesión.'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al actualizar la contraseña: " . mysqli_error($conexion) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Agrolity</title>
    <link rel="stylesheet" href="CSS/estilo.css">
    <style>
        body {
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        .box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 420px;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        h2 { color: #0BAE03; margin-bottom: 10px; font-size: 1.8rem; }
        .email-text {
            margin-bottom: 25px;
            color: #333;
            font-size: 1rem;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0 5px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        input:focus {
            border-color: #0BAE03;
            outline: none;
        }
        /* Texto de sugerencia idéntico al de tu panel de usuario original */
        .sugerencia-texto {
            text-align: left;
            font-size: 0.8rem;
            color: #777;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        .btn {
            width: 100%;
            background: #0BAE03;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #099302;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="box">
            <h2>Asignar Nueva Contraseña</h2>
            <p class="email-text">Escribe tu nueva clave para la cuenta: <br><strong><?php echo $email_usuario; ?></strong></p>
            
            <form action="nueva_password.php" method="POST">
                <input type="hidden" name="correo_usuario" value="<?php echo $email_usuario; ?>">
                
                <input type="password" name="nueva_clave" placeholder="Escribe tu nueva contraseña" required 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}"
                       title="Debe incluir al menos una mayúscula, un número y un carácter especial.">
                
                <div class="sugerencia-texto">
                    Debe incluir al menos una mayúscula, un número y un carácter especial (ej: @, $, !, #, %).
                </div>

                <button type="submit" class="btn">Guardar Nueva Contraseña</button>
            </form>
        </div>
    </div>
</body>
</html>
