<?php
session_start();
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda - Agrolity</title>
    <link rel="stylesheet" href="CSS/estilo.css">
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    
    <style>
        /* ===== ESTILOS ESPECÍFICOS PARA FAQS ===== */
        .faqs-container { 
            max-width: 1000px; 
            margin: 120px auto 50px; 
            padding: 20px; 
            font-family: 'Didact Gothic', sans-serif; 
        }
        .titulo-verde { 
            color: #0BAE03; 
            text-align: center; 
            font-size: 2.5rem; 
            margin-bottom: 10px; 
        }
        .faq-seccion { 
            margin-bottom: 40px; 
        }
        .faq-seccion h2 { 
            border-bottom: 2px solid #0BAE03; 
            padding-bottom: 10px; 
            color: #333; 
            margin-bottom: 20px; 
        }
        .faq-item { 
            border-bottom: 1px solid #eee; 
        }
        .faq-pregunta { 
            width: 100%; 
            padding: 15px; 
            text-align: left; 
            background: none; 
            border: none; 
            font-family: inherit; 
            font-size: 1.1rem; 
            cursor: pointer; 
            display: flex; 
            justify-content: space-between; 
        }
        .faq-respuesta { 
            padding: 0 15px; 
            max-height: 0; 
            overflow: hidden; 
            transition: max-height 0.3s ease-out; 
        }
        .faq-item.active .faq-respuesta { 
            max-height: 500px; 
            padding-bottom: 15px; 
        }
        .faq-item.active .faq-pregunta { 
            color: #0BAE03; 
            font-weight: bold; 
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

            <li class="carrito-icono">
                <a href="carrito.php">🛒<span class="carrito-contador">0</span></a>
            </li>
        </ul>
    </nav>
</header>

<main class="faqs-container">
    <h1 class="titulo-verde">Centro de Ayuda - FAQs</h1>
    
    <section class="faq-seccion">
        <h2>🛒 Sobre Compras</h2>
        <div class="faq-item">
            <button class="faq-pregunta">1. ¿Necesito registrarme para comprar? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí. Para finalizar una compra es necesario crear una cuenta e iniciar sesión.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">2. ¿Cómo agrego un producto al carrito? <span>+</span></button>
            <div class="faq-respuesta"><p>Ingresa a la sección Productos, selecciona el producto y haz clic en "Añadir al carrito".</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">3. ¿Puedo eliminar productos del carrito? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí. En la vista del carrito puedes eliminar productos antes de proceder al pago.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">4. ¿Puedo modificar la cantidad de productos? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí. Puedes aumentar o disminuir la cantidad desde el carrito antes de confirmar la compra.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">5. ¿Cómo sé que mi pedido fue confirmado? <span>+</span></button>
            <div class="faq-respuesta"><p>El sistema mostrará un mensaje de confirmación y enviará un correo electrónico con el resumen del pedido.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">6. ¿Puedo cancelar un pedido después de pagarlo? <span>+</span></button>
            <div class="faq-respuesta"><p>Dependerá del estado del pedido. Si aún no ha sido despachado, podrás solicitar la cancelación desde tu perfil o contactando soporte.</p></div>
        </div>
    </section>

    <section class="faq-seccion">
        <h2>🚚 Envíos y Entregas</h2>
        <div class="faq-item">
            <button class="faq-pregunta">7. ¿Cuánto tiempo tarda la entrega? <span>+</span></button>
            <div class="faq-respuesta"><p>El tiempo estimado depende de la ubicación, pero generalmente es entre 24 y 72 horas.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">8. ¿Hacen envíos a todo el país? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí, siempre que exista cobertura logística en la zona del usuario. Por lo pronto solo se trabaja la zona de Cundinamarca.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">9. ¿Puedo programar la fecha de entrega? <span>+</span></button>
            <div class="faq-respuesta"><p>En algunos casos sí, dependiendo del productor y la disponibilidad del producto.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">10. ¿Qué pasa si mi pedido llega incompleto o en mal estado? <span>+</span></button>
            <div class="faq-respuesta"><p>Debes reportarlo dentro de las primeras 24 horas desde la sección Contáctanos o soporte.</p></div>
        </div>
    </section>

    <section class="faq-seccion">
        <h2>💳 Pagos</h2>
        <div class="faq-item">
            <button class="faq-pregunta">11. ¿Qué medios de pago acepta Agrolity? <span>+</span></button>
            <div class="faq-respuesta"><p>Transferencias bancarias, tarjetas débito/crédito y otros medios electrónicos disponibles en la plataforma.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">12. ¿Es seguro pagar en la plataforma? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí. La plataforma utiliza protocolo HTTPS y pasarelas de pago seguras.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">13. ¿Puedo pagar contra entrega? <span>+</span></button>
            <div class="faq-respuesta"><p>Dependerá del productor y la zona de entrega.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">14. ¿Dónde puedo ver el historial de mis pagos? <span>+</span></button>
            <div class="faq-respuesta"><p>En la sección Mi Perfil dentro del historial de pedidos.</p></div>
        </div>
    </section>

    <section class="faq-seccion">
        <h2>👤 Cuenta de Usuario</h2>
        <div class="faq-item">
            <button class="faq-pregunta">15. ¿Cómo recupero mi contraseña? <span>+</span></button>
            <div class="faq-respuesta"><p>Haz clic en Acceder y luego en "¿Olvidaste tu contraseña?" para recibir un enlace de recuperación.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">16. ¿Puedo actualizar mis datos personales? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí. Desde Mi Perfil puedes modificar tu información.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">17. ¿Puedo eliminar mi cuenta? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí. Puedes solicitar la eliminación desde tu perfil o mediante soporte.</p></div>
        </div>
    </section>

    <section class="faq-seccion">
        <h2>🚜 Productores (Dashboard Campesino)</h2>
        <div class="faq-item">
            <button class="faq-pregunta">18. ¿Cómo me registro como productor? <span>+</span></button>
            <div class="faq-respuesta"><p>Debes crear una cuenta y solicitar el rol de productor para habilitar el Dashboard Campesino.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">19. ¿Cómo publico un producto para vender? <span>+</span></button>
            <div class="faq-respuesta"><p>Desde el Dashboard Campesino, selecciona "Ingresar producto", completa la información y guarda los cambios.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">20. ¿Cómo gestiono mis ganancias? <span>+</span></button>
            <div class="faq-respuesta"><p>En el Dashboard podrás ver el resumen de ventas, pagos recibidos y ganancias acumuladas.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">21. ¿Puedo editar o eliminar productos publicados? <span>+</span></button>
            <div class="faq-respuesta"><p>Sí. Desde el panel de gestión de productos puedes actualizarlos o eliminarlos.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">22. ¿Cómo recibo el dinero de mis ventas? <span>+</span></button>
            <div class="faq-respuesta"><p>El sistema transfiere los pagos según las políticas establecidas por la plataforma.</p></div>
        </div>
    </section>

    <section class="faq-seccion">
        <h2>🔐 Seguridad y Soporte</h2>
        <div class="faq-item">
            <button class="faq-pregunta">23. ¿Qué hago si detecto actividad sospechosa en mi cuenta? <span>+</span></button>
            <div class="faq-respuesta"><p>Cambia tu contraseña inmediatamente y contacta soporte.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">24. ¿Cómo contacto al equipo de Agrolity? <span>+</span></button>
            <div class="faq-respuesta"><p>Desde la sección Contáctanos puedes enviar un mensaje directo.</p></div>
        </div>
        <div class="faq-item">
            <button class="faq-pregunta">25. ¿Qué hago si la página no carga correctamente? <span>+</span></button>
            <div class="faq-respuesta"><p>Verifica tu conexión a internet o actualiza el navegador.</p></div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="footer-top">
        <div class="footer-left">
            <a href="index.php"><img src="img/Logo1.png" alt="Agrolity Logo" class="footer-logo"></a>
            <p>Del campo a tu vida, cultivamos bienestar y sembramos oportunidades. Porque el agua nos une, las personas nos inspiran y juntos cosechamos futuro.</p>
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

<script>
    document.querySelectorAll('.faq-pregunta').forEach(boton => {
        boton.addEventListener('click', () => {
            const item = boton.parentElement;
            item.classList.toggle('active');
            const span = boton.querySelector('span');
            span.textContent = item.classList.contains('active') ? '-' : '+';
        });
    });
</script>
<script src="JS/main.js"></script>
<?php include 'control_inactividad.php'; ?>
</body>
</html>
