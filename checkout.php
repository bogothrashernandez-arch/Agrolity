<?php
// MANTENER ESTO EN LA PRIMERÍSIMA LÍNEA SIN ESPACIOS PREVIOS
session_start();

// Tus credenciales oficiales de Mercado Pago extraídas de tu panel de desarrollador
$public_key = "APP_USR-a9623302-3cf5-4929-bdde-67754a3cfbd8";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra | AgroRed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/main.css">
    <link rel="stylesheet" href="CSS/checkout.css">
    <link rel="stylesheet" href="CSS/estilo.css">
    <style>
        .checkout { max-width: 1200px; margin: 120px auto 50px; padding: 20px; }
        .checkout h2 { border-bottom: 2px solid #0BAE03; padding-bottom: 10px; margin-bottom: 30px; }
        .checkout-grid { display: flex; gap: 30px; flex-wrap: wrap; }
        .checkout-form { flex: 2; }
        .checkout-productos { flex: 1; background: #f7f1f0; padding: 20px; border-radius: 15px; }
        .checkout-resumen { flex: 1; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: fit-content; }
        .checkout-bloque { background: #f9f9f9; padding: 20px; border-radius: 15px; margin-bottom: 20px; }
        .checkout-bloque h3 { margin-top: 0; color: #0BAE03; }
        .checkout-bloque input, .checkout-bloque textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .checkout-bloque label { display: block; margin: 10px 0; cursor: pointer; }
        .resumen-linea { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .resumen-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px solid #0BAE03; font-weight: bold; font-size: 1.2rem; }
        .producto-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .producto-nombre { font-weight: bold; }
        .producto-precio { color: #0BAE03; }
        .carrito-vacio { text-align: center; padding: 40px; color: #888; }
        .btn-pagar { background: #0BAE03; color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; width: 100%; font-size: 1rem; margin-top: 20px; font-weight: bold; display: block; text-align: center; text-decoration: none; }
        .btn-pagar:hover { background: #099402; }
        @media (max-width: 768px) { .checkout-grid { flex-direction: column; } }

        /* Corrección quirúrgica para alinear los puntos de los métodos de pago */
        .checkout-bloque label {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important; 
            margin: 15px 0 !important;
            font-size: 1rem;
            cursor: pointer;
        }

        .checkout-bloque label input[type="radio"] {
            width: auto !important; 
            margin: 0 !important;   
            padding: 0 !important;
            cursor: pointer;
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
            <li><a href="index.php#contactanos">Contactanos</a></li>
            <li><a href="faqs.php">Preguntas Frecuentes</a></li>
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <li><a href="perfil.php">Hola, <?php echo $_SESSION['usuario_nombre']; ?></a></li>
                <li><a href="logout.php">Salir</a></li>
                <?php if ($_SESSION['usuario_rol'] == 'productor'): ?>
                    <li><a href="dashboard-campesino.php">Mi Panel</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="checkout">
    <h2>Finalizar compra</h2>
    <div class="checkout-grid">
        <section class="checkout-form">
            <div class="checkout-bloque">
                <h3>Datos del comprador</h3>
                <input type="text" id="nombre" placeholder="Nombre completo" value="Cristian Hernandez">
                <input type="email" id="email" placeholder="Correo electrónico" value="bogothrashernandez@gmail.com">
                <input type="tel" id="telefono" placeholder="Teléfono" value="3196709559">
            </div>

            <div class="checkout-bloque">
                <h3>Dirección de envío</h3>
                <input type="text" id="direccion" placeholder="Dirección" value="CRA 8B #192-32">
                <input type="text" id="ciudad" placeholder="Ciudad" value="BOGOTA D.C.">
                <textarea id="indicaciones" placeholder="Indicaciones adicionales" rows="3">Casas</textarea>
            </div>

            <div class="checkout-bloque">
                <h3>Método de pago</h3>
                <label><input type="radio" name="pago" id="pago_mp" checked> PSE / Mercado Pago</label>
                <label><input type="radio" name="pago" id="pago_ce"> Contraentrega</label>
                <label><input type="radio" name="pago" id="pago_tb"> Transferencia bancaria</label>
            </div>
        </section>

        <section class="checkout-productos">
            <h3>Productos en tu pedido</h3>
            <div id="checkout-lista">
                <p style="text-align:center; color:#888;">Cargando productos...</p>
            </div>
        </section>

        <aside class="checkout-resumen">
            <h3>Resumen del pedido</h3>
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
            
            <button id="btn-accion-checkout" class="btn-pagar">PAGAR PEDIDO</button>
            <p id="mensaje-vacio" style="display:none; color:red; text-align:center; margin-top:10px;"></p>
        </aside>
    </div>
</main>

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
        <p>©2025, <span>Agrolity</span>. All Rights Reserved</p>
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

<script>
    const usuarioLogueado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
</script>

<script src="JS/main.js"></script>
<script src="JS/checkout.js"></script>

<script>
    document.getElementById('btn-accion-checkout').addEventListener('click', function(e) {
        e.preventDefault();
        
        const totalTexto = document.getElementById('total').innerText;
        let valorLimpio = totalTexto.replace('$', '').replace(/\./g, '').trim();
        const totalNumerico = parseInt(valorLimpio, 10);
        
        const msgError = document.getElementById('mensaje-vacio');
        msgError.style.display = 'none';

        if (isNaN(totalNumerico) || totalNumerico <= 0) {
            msgError.innerText = "Añade productos para activar tu pedido.";
            msgError.style.display = 'block';
            return;
        }

        // CAPTURAR COMPONENTES DINÁMICOS DEL FORMULARIO DE ENVÍO
        const nombreForm    = document.getElementById('nombre').value;
        const emailForm     = document.getElementById('email').value;
        const telefonoForm  = document.getElementById('telefono').value;
        const direccionForm = document.getElementById('direccion').value;
        const ciudadForm    = document.getElementById('ciudad').value;
        const carritoActual = JSON.parse(localStorage.getItem('carrito')) || [];

        // --- SOLUCIÓN DE PROCESAMIENTO TRADICIONAL (CONTRAENTREGA / TRANSFERENCIA) ---
        if (!document.getElementById('pago_mp').checked) {
            this.innerText = "PROCESANDO PEDIDO...";
            this.disabled = true;

            const metodoSeleccionado = document.getElementById('pago_ce').checked ? 'Contraentrega' : 'Transferencia bancaria';

            // Extraemos los valores numéricos limpios de la pantalla para mandarlos desglosados a MySQL
            const subtotalNum = parseInt(document.getElementById('subtotal').innerText.replace('$', '').replace(/\./g, '').trim(), 10) || 0;
            const envioNum    = parseInt(document.getElementById('envio').innerText.replace('$', '').replace(/\./g, '').trim(), 10) || 0;

            // RECONSTRUIDO CON TUS CAMPOS ORIGINALES FIELMENTE
            const datosPedido = {
                nombre_entrega: nombreForm,
                email: emailForm,
                telefono: telefonoForm,
                direccion: direccionForm,
                ciudad: ciudadForm,
                subtotal: subtotalNum,
                envio: envioNum,
                total: totalNumerico,
                metodo_pago: metodoSeleccionado, 
                carrito: carritoActual 
            };

            fetch('guardar_pedido_tradicional.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosPedido)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('carrito');
                    alert("¡Pedido recibido! Procesando bajo el método tradicional seleccionado.");
                    window.location.href = "confirmacion.php";
                } else {
                    alert("Error en base de datos: " + (data.error || "Intente de nuevo."));
                    document.getElementById('btn-accion-checkout').innerText = "PAGAR PEDIDO";
                    document.getElementById('btn-accion-checkout').disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error de conexión al guardar el pedido tradicional.");
                document.getElementById('btn-accion-checkout').innerText = "PAGAR PEDIDO";
                document.getElementById('btn-accion-checkout').disabled = false;
            });
            return;
        }

        // --- PROCESAMIENTO INTEGRAL CON MERCADO PAGO ---
        this.innerText = "PROCESANDO CON PASARELA...";
        this.disabled = true;

        // GUARDAR EN LOCALSTORAGE PARA LEERLO EN RESPUESTA_PAGO.PHP AL REGRESAR
        const infoEnvioTemporal = {
            nombre: nombreForm,
            email: emailForm,
            telefono: telefonoForm,
            direccion: direccionForm,
            ciudad: ciudadForm
        };
        localStorage.setItem('datos_envio_temporal', JSON.stringify(infoEnvioTemporal));

        // CORRECCIÓN CON REVENTADOR DE CACHÉ ACTIVO (?t=Date.now)
        fetch('crear_preferencia.php?t=' + Date.now(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ total: totalNumerico })
        })
        .then(res => res.json())
        .then(data => {
            this.innerText = "PAGAR PEDIDO";
            this.disabled = false;
            
            if (data.init_point) {
                window.location.href = data.init_point;
            } else {
                msgError.innerText = data.error || "Error al generar enlace de Mercado Pago.";
                msgError.style.display = 'block';
            }
        })
        .catch(err => {
            this.innerText = "PAGAR PEDIDO";
            this.disabled = false;
            console.error(err);
            msgError.innerText = "Error de conexión con el servidor.";
            msgError.style.display = 'block';
        });
    });
</script>

<?php include 'control_inactividad.php'; ?>
</body>
</html>
