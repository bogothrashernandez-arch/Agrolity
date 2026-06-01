<?php
// logout.php - CIERRE DE SESIÓN SEGURO Y LIMPIEZA TOTAL
session_start();

// 1. Destruimos las variables de sesión en el servidor PHP
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrando Sesión...</title>
</head>
<body>
    <script>
        // Limpieza absoluta en el cliente
        localStorage.clear(); 
        // Una vez limpio, redirigimos al usuario al inicio de la tienda
        window.location.href = "index.php";
    </script>
</body>
</html>
