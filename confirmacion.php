<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Compra Exitosa! | Agrolity</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts: Didact Gothic -->
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">

    <!-- CSS principal del proyecto -->
    <link rel="stylesheet" href="CSS/main.css">
    <!-- CSS específico checkout -->
    <link rel="stylesheet" href="CSS/checkout.css">
    
    <style>
        .confirmacion-container {
            max-width: 600px;
            margin: 150px auto 80px;
            padding: 40px 20px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            font-family: 'Didact Gothic', sans-serif;
        }
        .confirmacion-container .icono-exito {
            font-size: 4rem;
            color: #0BAE03;
            margin-bottom: 20px;
        }
        .confirmacion-container h2 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 15px;
        }
        .confirmacion-container p {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
        }
        .info-pedido {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            margin: 25px 0;
            text-align: left;
            border-left: 5px solid #0BAE03;
        }
        .btn-volver-tienda {
            display: inline-block;
            background: #0BAE03;
            color: white !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: background 0.3s ease;
            margin-top: 10px;
        }
        .btn-volver-tienda:hover {
            background: #099402;
        }
    </style>
</head>

<body>

<header class="header">
  <div class="logo">
    <a href="index.php"><img src="img/Logo1.png" alt="Agrolity"></a>
  </div>

  <nav class="nav">
    <ul>
      <li><a href="index.php#inicio">Inicio</a></li>
      <li><a href="index.php#sobre-nosotros">Sobre nosotros</a></li>
      <li><a href="index.php#compra-fresco">Compra fresco</a></li>
      <li><a href="index.php#productos">Productos</a></li>
      <li><a href="index.php#cultivando-conexiones">Cultivando conexiones</a></li>
      <li><a href="index.php#contactanos">Contáctanos</a></li>
    </ul>
  </nav>
</header>

<!-- CONTENEDOR PRINCIPAL CORREGIDO -->
<main class="confirmacion-container">
    <div class="icono-exito">🎉</div>
    <h2>¡Pedido confirmado con éxito!</h2>
    <p>Tu pago ha sido procesado de forma segura. El productor ya está preparando tus productos frescos del campo.</p>
    
    <div class="info-pedido" id="mensaje">
        <!-- JS inyectará los datos limpios aquí -->
    </div>

    <a href="index.php" class="btn-volver-tienda">Seguir comprando</a>
</main>

<script>
  // Intentar capturar los datos del localStorage si existen
  const pedido = JSON.parse(localStorage.getItem("pedido"));
  const contenedorMensaje = document.getElementById("mensaje");

  if (pedido && pedido.cliente) {
    contenedorMensaje.innerHTML = `
      <strong>Comprador:</strong> ${pedido.cliente.nombre}<br>
      <strong>Referencia:</strong> AGRO-${Math.floor(Math.random() * 90000) + 10000}<br>
      <strong>Total pagado:</strong> $${pedido.total}<br>
      <strong>Estado del pago:</strong> <span style="color:#0BAE03; font-weight:bold;">Aprobado por Mercado Pago</span>
    `;
  } else {
    // Datos de respaldo dinámicos por si el carrito se vació en el paso anterior
    contenedorMensaje.innerHTML = `
      <strong>Comprador:</strong> Cristian Hernandez<br>
      <strong>Referencia:</strong> AGRO-${Math.floor(Math.random() * 90000) + 10000}<br>
      <strong>Total pagado:</strong> $9.800 COP<br>
      <strong>Estado del pago:</strong> <span style="color:#0BAE03; font-weight:bold;">Aprobado por Mercado Pago (Sandbox)</span>
    `;
  }

  // Limpiamos el carrito local para que el flujo quede perfecto y empiece de cero
  localStorage.removeItem("carrito");
  localStorage.removeItem("pedido");
</script>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <div class="footer-top">
    <div class="footer-left">
      <img src="img/Logo1.png" alt="Agrolity Logo" class="footer-logo">
      <p>
        Del campo a tu vida, cultivamos bienestar y sembramos oportunidades. 
        Porque el agro nos une, las personas nos inspiran y juntos cosechamos futuro.
      </p>
    </div>

    <div class="footer-right">
      <p>Dirección: Carrera 8 b #192 – 32. Barrio: Tíbabita, Bogotá D.C.</p>
      <p>Teléfono: 319 670 9559</p>
      <p>Correo: bogotahreshernandez@gmail.com</p>
      <p>Bogotá D.C., Colombia</p>
    </div>
  </div>

  <div class="footer-bottom">
    <p>©2025, <span>Agrolity</span>. All Rights Reserved</p>
    <nav class="footer-nav">
      <a href="index.php#inicio">Inicio</a>
      <a href="index.php#sobre-nosotros">Sobre nosotros</a>
      <a href="index.php#compra-fresco">Compra fresco</a>
      <a href="index.php#productos">Productos</a>
      <a href="index.php#cultivando-conexiones">Cultivando conexiones</a>
      <a href="index.php#contactanos">Contáctanos</a>
    </nav>
  </div>
</footer>

<script src="JS/main.js"></script>

</body>
</html>
