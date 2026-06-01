<?php
// 1. FORZAR A PHP A MOSTRAR ERRORES EN PANTALLA
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'conexion.php';

// 2. CARGAR PHPMAILER DESDE LA CARPETA QUE PEGASTE EN HTDOCS
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// === BLOQUE 1: PROCESAR SOLICITUD DE RECUPERACIÓN DE CONTRASEÑA ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion_recuperar'])) {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo_recuperar']);

    // Verificar si el correo existe en la base de datos
    $consulta = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP de Brevo
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ab8bab001@smtp-brevo.com'; 
            $mail->Password   = // TODO: Colocar clave de API aquí.; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // REGLA CLAVE: Omitir la validación estricta de certificados SSL en localhost
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Remitente y Destinatario
            $mail->setFrom('bogothrashernandez@gmail.com', 'Agrolity Soporte');
            $mail->addAddress($correo);

            // Contenido Estilizado del Correo (HTML)
            $mail->isHTML(true);
            $mail->Subject = 'Restablecer Contraseña - Agrolity';
            $mail->Body    = "
                <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; border: 1px solid #eee; padding: 20px; border-radius: 8px;'>
                    <h2 style='color: #0BAE03; text-align: center;'>Recuperación de Contraseña</h2>
                    <p>Hola,</p>
                    <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en <strong>Agrolity</strong>.</p>
                    <p>Para continuar con el proceso y asignar una nueva clave, por favor haz clic en el siguiente enlace:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='http://localhost/Agrolity/nueva_password.php?email=" . urlencode($correo) . "' 
                           style='background-color: #0BAE03; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 5px; display: inline-block;'>
                           Restablecer Mi Contraseña
                        </a>
                    </div>
                    <p style='color: #666; font-size: 0.9rem;'>Si tú no realizaste esta solicitud, puedes ignorar este correo de forma segura.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px;'>
                    <p style='font-size: 0.8rem; color: #999; text-align: center;'>© 2026 Agrolity. Del campo a tu vida.</p>
                </div>
            ";

            // Enviar el correo electrónico
            if($mail->send()) {
                echo "<script>alert('Se ha enviado un enlace de recuperación a: $correo'); window.location.href='login.php';</script>";
                exit();
            }

        } catch (Exception $e) {
            echo "El mensaje no pudo ser enviado. Error de Mailer: {$mail->ErrorInfo}";
            exit();
        }
    } else {
        echo "<script>alert('El correo electrónico ingresado no está registrado en el sistema.'); window.location.href='login.php';</script>";
        exit();
    }
}

// === BLOQUE 2: PROCESAR INICIO DE SESIÓN TRADICIONAL ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['correo']) && isset($_POST['password'])) {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $password = $_POST['password'];

    $consulta = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            
            if (isset($usuario['correo'])) {
                $_SESSION['usuario_correo'] = $usuario['correo'];
            }

            if ($usuario['rol'] == 'productor') {
                header("Location: dashboard-campesino.php");
            } else {
                header("Location: index.php");
            }
            exit(); 
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('El correo no está registrado'); window.location.href='login.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agrolity</title>
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/estilo.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding-top: 50px; 
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        /* Contenedor adaptado para centrar el Logo */
        .login-logo-container {
            margin-bottom: 25px;
            display: inline-block;
            text-decoration: none;
        }
        .login-logo {
            max-width: 210px; /* Tamaño ideal para que destaque la marca en el cuadro */
            height: auto;
            display: block;
            margin: 0 auto;
            transition: transform 0.3s ease;
        }
        .login-logo:hover {
            transform: scale(1.04); /* Pequeña animación fluida al pasar el cursor */
        }
        .login-box h2 { color: #0BAE03; margin-bottom: 20px; margin-top: 0px; }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .btn-login {
            width: 100%;
            background: #0BAE03;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
        }
        .toggle-link {
            margin-top: 15px;
            display: block;
            font-size: 0.9rem;
            color: #555;
        }
        .olvido-pass {
            text-align: center;
            margin-top: 15px;
        }
        .olvido-pass a {
            color: #0BAE03;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box" id="auth-container">
            
            <a href="index.php" class="login-logo-container" title="Volver al Inicio">
                <img src="img/Logo1.png" alt="Logo Agrolity" class="login-logo">
            </a>

            <h2 id="auth-title">Iniciar Sesión</h2>
            
            <form action="login.php" method="POST" id="auth-form">
                <input type="email" name="correo" id="email" placeholder="Correo electrónico" required>
                <input type="password" name="password" id="password" placeholder="Contraseña" required>
                <button type="submit" class="btn-login" id="btn-text">Ingresar</button>
            </form>
            
            <form id="form-recuperar-oculto" action="login.php" method="POST" style="display: none;">
                <input type="hidden" name="accion_recuperar" value="1">
                <input type="hidden" name="correo_recuperar" id="correo_recuperar_hidden">
            </form>

            <div class="olvido-pass">
                <a href="javascript:void(0)" onclick="confirmarRestablecer()">¿Olvidaste tu contraseña?</a>
            </div>
            <p class="toggle-link">
                ¿No tienes cuenta? <a href="registro.php" style="color: #0BAE03; text-decoration: none; font-weight: bold;">Regístrate aquí</a>
            </p>
        </div>
    </div>
    <script src="JS/main.js"></script>
    <script>
        function confirmarRestablecer() {
            const correoIngresado = document.getElementById('email').value;
            
            if (correoIngresado.trim() !== "") {
                document.getElementById('correo_recuperar_hidden').value = correoIngresado;
                document.getElementById('form-recuperar-oculto').submit();
            } else {
                alert("Por favor, escribe tu correo electrónico en el campo 'Correo electrónico' de arriba para poder restablecer la contraseña.");
                document.getElementById('email').focus();
            }
        }
    </script>
</body>
</html>
