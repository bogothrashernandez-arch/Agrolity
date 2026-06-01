<?php
session_start();

// Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Variables críticas del usuario en sesión
$usuario_id = $_SESSION['usuario_id'];
$nombre_usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : "Usuario Agrolity";
$email_usuario = isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : "correo@agrolity.com";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico de Compra | Agrolity</title>
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    <style>
        .procesando-container {
            max-width: 700px;
            margin: 80px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            font-family: 'Didact Gothic', sans-serif;
        }
        .consola-error {
            background: #222;
            color: #7fff00;
            padding: 15px;
            border-radius: 8px;
            text-align: left;
            font-family: monospace;
            white-space: pre-wrap;
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #0BAE03;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h2 { color: #333; text-align: center; }
    </style>
</head>
<body>

<main class="procesando-container">
    <div id="icono-proceso" class="loader"></div>
    <h2 id="titulo-proceso">Analizando inserción en Base de Datos...</h2>
    <p style="text-align:center;">Procesando y registrando de forma segura los artículos de tu compra.</p>
    
    <div id="debug-info">
        <strong>Respuesta del Servidor (guardar_pedido_tradicional.php):</strong>
        <div id="consola" class="consola-error">Esperando respuesta del backend...</div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Recuperar el carrito activo y los datos temporales guardados
    const carritoActual = JSON.parse(localStorage.getItem('carrito')) || [];
    const envioTemporal = JSON.parse(localStorage.getItem('datos_envio_temporal')) || {};
    
    if (carritoActual.length === 0) {
        document.getElementById('consola').innerText = "⚠️ El carrito está vacío o la página fue recargada. Redirigiendo a tu perfil.";
        setTimeout(() => { window.location.href = "perfil.php"; }, 2000);
        return;
    }

    // Calcular montos basados en el carrito real del comprador
    let subtotalNum = 0;
    carritoActual.forEach(item => {
        subtotalNum += (parseInt(item.precio, 10) * parseInt(item.cantidad, 10));
    });
    
    const envioNum = 5000; 
    const totalNum = subtotalNum + envioNum;

    // 2. Construir el objeto inyectando dinámicamente tu ID de sesión
    const datosPedido = {
        usuario_id: "<?php echo $usuario_id; ?>", // Amarra el pedido a tu cuenta real
        nombre_entrega: envioTemporal.nombre || "<?php echo $nombre_usuario; ?>", 
        email: envioTemporal.email || "<?php echo $email_usuario; ?>",
        telefono: envioTemporal.telefono || "3196709559",
        direccion: envioTemporal.direccion || "Dirección de entrega",
        ciudad: envioTemporal.ciudad || "BOGOTA D.C.", 
        subtotal: subtotalNum,
        envio: envioNum,
        total: totalNum,
        metodo_pago: "Mercado Pago", 
        carrito: carritoActual // El array mapeado con cada producto individual
    };

    // 3. Enviarlo mediante FETCH a tu backend de procesamiento
    fetch('guardar_pedido_tradicional.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosPedido)
    })
    .then(res => res.text()) // Captura la respuesta en bruto ante cualquier error oculto de PHP
    .then(textoOriginal => {
        document.getElementById('icono-proceso').className = "";
        
        try {
            const data = JSON.parse(textoOriginal);
            if (data.success) {
                // ¡Éxito! Limpiamos las variables temporales y de carrito
                localStorage.removeItem('carrito');
                localStorage.removeItem('datos_envio_temporal');
                
                document.getElementById('icono-proceso').innerHTML = "✅";
                document.getElementById('consola').style.color = "#7fff00";
                document.getElementById('consola').innerText = "¡Éxito total! Tu compra se registró correctamente.\nRedirigiendo a confirmación...";
                
                setTimeout(() => { window.location.href = "confirmacion.php"; }, 2500);
            } else {
                document.getElementById('icono-proceso').innerHTML = "❌";
                document.getElementById('consola').style.color = "#ff4500";
                document.getElementById('consola').innerText = "La base de datos rechazó los parámetros:\n" + (data.error || JSON.stringify(data));
            }
        } catch (e) {
            document.getElementById('icono-proceso').innerHTML = "❌";
            document.getElementById('consola').style.color = "#ff4500";
            document.getElementById('consola').innerText = "El backend devolvió una respuesta ilegible o un error de PHP:\n\n" + textoOriginal;
        }
    })
    .catch(err => {
        document.getElementById('icono-proceso').className = "";
        document.getElementById('consola').innerText = "Fallo de comunicación de red: " + err.message;
    });
});
</script>

</body>
</html>
