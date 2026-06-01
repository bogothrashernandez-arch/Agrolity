<?php
include 'conexion.php';
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito de compras | AgroRed</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/estilo.css">
  <link rel="stylesheet" href="CSS/carrito.css">
  <style>
    /* Estilos adicionales para el carrito */
    .carrito-item {
        display: flex;
        gap: 20px;
        padding: 20px;
        border-bottom: 1px solid #eee;
        align-items: center;
        background: white;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .item-imagen {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .item-info {
        flex: 1;
    }

    .item-info h4 {
        margin: 0 0 5px 0;
        font-size: 1.1rem;
    }

    .item-precio {
        color: #0BAE03;
        font-weight: bold;
        margin: 5px 0;
    }

    .item-cantidad {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
    }

    .btn-cantidad {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s;
    }

    .btn-cantidad:hover {
        background: #0BAE03;
        color: white;
        border-color: #0BAE03;
    }

    .cantidad-valor {
        font-size: 1.1rem;
        font-weight: bold;
        min-width: 30px;
        text-align: center;
    }

    .btn-eliminar {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 5px;
        transition: transform 0.3s;
    }

    .btn-eliminar:hover {
        transform: scale(1.1);
    }

    .item-subtotal {
        color: #333;
        font-weight: bold;
        margin: 5px 0 0 0;
    }

    .carrito-vacio {
        text-align: center;
        padding: 60px;
        background: white;
        border-radius: 10px;
        font-size: 1.2rem;
        color: #666;
    }

    .carrito-productos {
        background: #f7f1f0;
        padding: 20px;
        border-radius: 15px;
    }

    
  </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <a href="index.php"><img src="img/Logo1.png" alt="Agrolity Logo"></a>
    </div>
    <nav class="nav">
        <ul>
            <li><a href="index.php#inicio">Inicio</a></li>
            <li><a href="index.php#sobre-nosotros">Sobre nosotros</a></li>
            <li><a href="index.php#compra-fresco">Compra fresco</a></li>
            <li><a href="index.php#productos">Productos</a></li>
            <li><a href="index.php#cultivando-conexiones">Cultivando conexiones</a></li>
            <li><a href="index.php#contactanos">Contáctanos</a></li>
            <li><a href="faqs.php">Preguntas Frecuentes</a></li>
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <li><a href="perfil.php">Hola, <?php echo $_SESSION['usuario_nombre']; ?></a></li>
                <li><a href="logout.php">Salir</a></li>
                <?php if ($_SESSION['usuario_rol'] == 'productor'): ?>
                    <li><a href="dashboard-campesino.php">Mi Panel</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="login.php">Acceder</a></li>
            <?php endif; ?>
            <li class="carrito-icono"><a href="carrito.php">🛒<span class="carrito-contador">0</span></a></li>
        </ul>
    </nav>
</header>

<section class="carrito-banner">
  <div class="banner-content">
    <h1>Tu carrito de compras</h1>
    <p>¡Hola! Gestiona tus productos seleccionados aquí.</p>
  </div>
</section>

<main class="carrito container">
  <section class="carrito-productos" id="lista-carrito">
      <p style="text-align:center; color:#888; padding: 20px;">Cargando tus productos...</p>
  </section>

  <aside class="carrito-resumen">
    <h3>Resumen de compra</h3>
    <div class="resumen-linea">
      <span>Subtotal</span>
      <span id="subtotal">$0</span>
    </div>
    <div class="resumen-linea">
      <span>Envío</span>
      <span id="envio">$0</span>
    </div>
    <div class="resumen-total">
      <span>Total</span>
      <strong id="total">$0</strong>
    </div>
    <a href="checkout.php" id="btn-proceder" class="btn-proceder">
      Proceder al pago
    </a>
  </aside>
</main>

<script src="JS/carrito.js"></script>

<footer class="footer">
    <div class="footer-top">
        <div class="footer-left">
            <a href="index.php"><img src="img/Logo1.png" alt="Agrolity Logo" class="footer-logo"></a>
            <p>Del campo a tu vida, cultivamos bienestar y sembramos oportunidades. Porque el agro nos une, las personas nos inspiran y juntos cosechamos futuro.</p>
        </div>
        <div class="footer-right">
            <p>Dirección: Carrera 8 b #192 – 32. Barrio: Tibidabo, Bogotá D.C.</p>
            <p>Teléfono: 319 670 9559</p>
            <p>Correo: bogotahreshernandez@gmail.com</p>
            <p>Bogotá D.C., Colombia</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>©2025, Agrolity. All Rights Reserved</p>
        <nav class="footer-nav">
            <a href="index.php#inicio">Inicio</a>
            <a href="index.php#sobre-nosotros">Sobre nosotros</a>
            <a href="index.php#compra-fresco">Compra fresco</a>
            <a href="index.php#productos">Productos</a>
            <a href="index.php#cultivando-conexiones">Cultivando conexiones</a>
            <a href="index.php#contactanos">Contáctanos</a>
            <a href="faqs.php">Preguntas Frecuentes</a>
        </nav>
    </div>
</footer>

<script src="JS/carrito.js"></script>
<script src="JS/main.js"></script>
<script>
// JavaScript para manejar las cantidades
document.addEventListener('DOMContentLoaded', function() {
    // Aumentar cantidad
    document.querySelectorAll('.btn-aumentar').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            fetch('actualizar_cantidad.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'index=' + index + '&accion=aumentar'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
            });
        });
    });
    
    // Disminuir cantidad
    document.querySelectorAll('.btn-disminuir').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            fetch('actualizar_cantidad.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'index=' + index + '&accion=disminuir'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
            });
        });
    });
    
    // Eliminar producto
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('¿Eliminar este producto del carrito?')) {
                const index = this.getAttribute('data-index');
                fetch('actualizar_cantidad.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'index=' + index + '&accion=eliminar'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Función única para procesar cambios
    function actualizarCarrito(index, accion) {
        const formData = new URLSearchParams();
        formData.append('index', index);
        formData.append('accion', accion);

        fetch('actualizar_cantidad.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recargamos para que PHP calcule los nuevos subtotales y totales
                location.reload(); 
            } else {
                alert("Error al actualizar el carrito");
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Eventos para los botones
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-aumentar')) {
            actualizarCarrito(e.target.getAttribute('data-index'), 'aumentar');
        }
        if (e.target.classList.contains('btn-disminuir')) {
            actualizarCarrito(e.target.getAttribute('data-index'), 'disminuir');
        }
        if (e.target.classList.contains('btn-eliminar')) {
            if (confirm('¿Deseas eliminar este producto?')) {
                actualizarCarrito(e.target.getAttribute('data-index'), 'eliminar');
            }
        }
    });
});
</script>
<?php include 'control_inactividad.php'; ?>
</body>
</html>
