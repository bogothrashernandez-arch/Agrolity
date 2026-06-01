<?php
session_start();
include 'conexion.php';

// 1. Verificación de seguridad: si no hay sesión, al index (HOME) directamente
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// 2. EVITAR CACHÉ: Esto impide que al dar "atrás" se vea el perfil tras salir
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$id_usuario = $_SESSION['usuario_id'];
$query = "SELECT * FROM usuarios WHERE id = '$id_usuario'";
$result = mysqli_query($conexion, $query);
$datos = mysqli_fetch_assoc($result);

// 3. CONSULTA HISTORIAL MEJORADA: Si productos_comprados es NULL, muestra un texto alternativo directo
$queryPedidos = "SELECT p.*, 
                 IFNULL(GROUP_CONCAT(CONCAT(dp.cantidad, 'x ', pr.nombre) SEPARATOR '<br>'), 'Compra Directa / Tradicional') AS productos_comprados
                 FROM pedidos p
                 LEFT JOIN detalles_pedido dp ON p.id = dp.pedido_id
                 LEFT JOIN productos pr ON dp.producto_id = pr.id
                 WHERE p.usuario_id = '$id_usuario'
                 GROUP BY p.id
                 ORDER BY p.fecha DESC";
$resultPedidos = mysqli_query($conexion, $queryPedidos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - Agrolity</title>
    <link rel="stylesheet" href="CSS/estilo.css">
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    <style>
        /* ===== HEADER ORIGINAL (GRIS) ===== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #bfbaba; 
            padding: 10px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            border-bottom: 1px solid #ddd;
            box-sizing: border-box;
            height: 80px;
        }
        .logo img { height: 50px; }
        .logo a { display: inline-block; }
        .nav ul { list-style: none; display: flex; gap: 25px; margin: 0; padding: 0; align-items: center; }
        .nav a { text-decoration: none; color: black; font-weight: 500; font-family: 'Didact Gothic', sans-serif; font-size: 14px; transition: color 0.3s; }
        .nav a:hover { color: #0BAE03; }
        .user-name { color: #0BAE03 !important; font-weight: bold !important; }

        /* Icono Carrito */
        .carrito-icono { position: relative; display: flex; align-items: center; }
        .carrito-icono a { font-size: 1.3rem; text-decoration: none; color: black; }
        .carrito-contador {
            position: absolute; top: -6px; right: -10px; background-color: #0BAE03;
            color: #fff; font-size: 0.7rem; font-weight: bold; width: 18px; height: 18px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }

        /* ===== CONTENIDO PERFIL DISPUESTO EN DOS COLUMNAS ===== */
        .perfil-container { max-width: 1250px; margin: 120px auto 50px; padding: 20px; font-family: 'Didact Gothic', sans-serif; }
        
        .perfil-layout {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }

        .columna-datos {
            flex: 1;
            max-width: 400px;
        }

        .columna-historial {
            flex: 2; /* Espacio para el historial y las cosechas */
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .user-info-card, .historial-card { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); box-sizing: border-box; }
        .campo-perfil { margin-bottom: 20px; }
        .campo-perfil label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        .input-perfil { width: 100%; padding: 10px; border: 2px solid #f0f0f0; border-radius: 6px; background-color: #f9f9f9; font-family: inherit; box-sizing: border-box; }
        .input-perfil:enabled { border-color: #0BAE03; background-color: #fff; }
        
        .password-requisitos {
            display: block;
            font-size: 0.78rem;
            color: #666;
            margin-top: 5px;
            line-height: 1.2;
        }

        .btn-principal { background: #0BAE03; color: white; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; transition: 0.3s; }
        .btn-principal:hover { background: #098a02; }
        .btn-secundario { background: #6c757d; color: white; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-logout { background: #d9534f; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 20px; font-family: inherit; width: 100%; text-align: center; }

        /* Estilos Tabla de Historial */
        .tabla-pedidos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .tabla-pedidos th, .tabla-pedidos td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
            vertical-align: middle;
        }
        .tabla-pedidos th {
            background-color: #f9f9f9;
            color: #555;
            font-weight: bold;
        }

        /* Contenedor de Estado */
        .estado-col {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-start;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
            text-transform: capitalize;
        }
        .badge-pendiente {
            background-color: #fef3c7;
            color: #d97706;
        }
        .badge-cancelado {
            background-color: #ffeeef;
            color: #dc3545;
        }
        .badge-completado {
            background-color: #d1fae5;
            color: #065f46;
        }

        html, body { height: 100%; margin: 0; padding: 0; }
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .perfil-container { flex: 1; }

        @media (max-width: 950px) {
            .perfil-layout { flex-direction: column; }
            .columna-datos, .columna-historial { width: 100%; max-width: 100%; }
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
                <li><a href="perfil.php" class="user-name">Hola, <?php echo $_SESSION['usuario_nombre']; ?></a></li>
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

<main class="perfil-container">
    <h1 style="border-bottom: 2px solid #0BAE03; padding-bottom: 10px; color: #333; margin-bottom: 30px;">Mi Panel de Usuario</h1>
    
    <div class="perfil-layout">
        <div class="columna-datos">
            <section class="user-info-card">
                <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Datos Personales</h3>
                <form id="form-perfil" method="POST" action="actualizar_perfil.php">
                    <div class="campo-perfil">
                        <label>Nombre Completo:</label>
                        <input type="text" name="nombre" id="edit-nombre" class="input-perfil" value="<?php echo htmlspecialchars($datos['nombre']); ?>" disabled>
                    </div>
                    <div class="campo-perfil">
                        <label>Correo Electrónico:</label>
                        <input type="email" name="correo" id="edit-email" class="input-perfil" value="<?php echo htmlspecialchars($datos['correo']); ?>" disabled>
                    </div>
                    <div class="campo-perfil">
                        <label>Tipo de Usuario:</label>
                        <input type="text" class="input-perfil" value="<?php echo htmlspecialchars($datos['rol'] ?? 'Cliente'); ?>" disabled style="text-transform: capitalize;">
                    </div>
                    <div class="campo-perfil">
                        <label>Nueva Contraseña:</label>
                        <input type="password" name="password" id="edit-password" class="input-perfil" placeholder="Dejar en blanco para no cambiar" disabled>
                        <small class="password-requisitos">Debe incluir al menos una mayúscula, un número y un carácter especial (ej: @, $, !, #, %).</small>
                    </div>
                    <div class="botones-perfil">
                        <button type="button" id="btn-editar" class="btn-secundario">Editar Datos</button>
                        <button type="submit" id="btn-guardar" class="btn-principal" style="display: none;">Guardar Cambios</button>
                    </div>
                </form>
                <button class="btn-logout" onclick="window.location.href='logout.php'">Cerrar Sesión</button>
            </section>
        </div>

        <div class="columna-historial">
            <section class="historial-card">
                <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Historial de Pedidos</h3>
                
                <?php if (mysqli_num_rows($resultPedidos) > 0): ?>
                    <table class="tabla-pedidos">
                        <thead>
                            <tr>
                                <th>N° Pedido</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Dirección</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($pedido = mysqli_fetch_assoc($resultPedidos)): ?>
                                <tr id="pedido-fila-<?php echo $pedido['id']; ?>">
                                    <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                                    <td><?php echo date("d/m/Y", strtotime($pedido['fecha'])); ?></td>
                                    
                                    <td style="line-height: 1.4; color: #444;">
                                        <?php echo $pedido['productos_comprados']; ?>
                                    </td>
                                    
                                    <td><?php echo htmlspecialchars($pedido['direccion'] . " (" . $pedido['ciudad'] . ")"); ?></td>
                                    <td>$<?php echo number_format($pedido['total'], 0, ',', '.'); ?></td>
                                    <td>
                                        <div class="estado-col">
                                            <?php 
                                            $estado_limpio = trim(strtolower($pedido['estado']));
                                            if ($estado_limpio == 'pendiente'): 
                                            ?>
                                                <span id="badge-estado-<?php echo $pedido['id']; ?>" class="badge badge-pendiente">Pendiente</span>
                                            <?php elseif ($estado_limpio == 'cancelado'): ?>
                                                <span id="badge-estado-<?php echo $pedido['id']; ?>" class="badge badge-cancelado">Cancelado</span>
                                            <?php else: ?>
                                                <span id="badge-estado-<?php echo $pedido['id']; ?>" class="badge badge-completado"><?php echo htmlspecialchars($pedido['estado']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <?php if ($estado_limpio == 'pendiente'): ?>
                                            <button onclick="cancelarPedido(<?php echo $pedido['id']; ?>)" id="btn-cancelar-<?php echo $pedido['id']; ?>" style="background-color: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: bold; font-family: inherit;">
                                                Cancelar
                                            </button>
                                        <?php else: ?>
                                            <span style="color: #aaa; font-size: 0.8rem; font-style: italic;">Sin acciones</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #666; text-align: center; margin-top: 20px;">Aún no has realizado ningún pedido en la tienda.</p>
                <?php endif; ?>
            </section>

            <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'productor'): ?>
                <section class="historial-card" style="margin-top: 5px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Mis Cosechas Publicadas</h3>
                    
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                            <thead>
                                <tr style="background-color: #f9f9f9; border-bottom: 1px solid #eee;">
                                    <th style="padding: 12px 10px; font-weight: bold; color: #555;">Producto</th>
                                    <th style="padding: 12px 10px; font-weight: bold; color: #555;">Precio sugerido</th>
                                    <th style="padding: 12px 10px; font-weight: bold; color: #555; text-align: center;">Estado de Validación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id_campesino = mysqli_real_escape_string($conexion, $_SESSION['usuario_id']);
                                $query_prod = "SELECT nombre, precio, estado FROM productos WHERE id_productor = '$id_campesino' ORDER BY id DESC";
                                $result_prod = mysqli_query($conexion, $query_prod);

                                if (mysqli_num_rows($result_prod) > 0):
                                    while ($prod = mysqli_fetch_assoc($result_prod)):
                                        $bg_badge = '#fef3c7'; 
                                        $color_texto = '#d97706';
                                        $texto_estado = 'En Validación';
                                        
                                        if ($prod['estado'] === 'aprobado') {
                                            $bg_badge = '#d1fae5'; 
                                            $color_texto = '#065f46';
                                            $texto_estado = 'Activo en Tienda';
                                        } elseif ($prod['estado'] === 'rechazado') {
                                            $bg_badge = '#ffeeef'; 
                                            $color_texto = '#dc3545';
                                            $texto_estado = 'Rechazado';
                                        }
                                ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px 10px; font-weight: bold; color: #333;"><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                        <td style="padding: 12px 10px;">$<?php echo number_format($prod['precio'], 0, ',', '.'); ?></td>
                                        <td style="padding: 12px 10px; text-align: center;">
                                            <span style="display: inline-block; padding: 5px 12px; background-color: <?php echo $bg_badge; ?>; color: <?php echo $color_texto; ?>; border-radius: 15px; font-size: 0.8rem; font-weight: bold;">
                                                <?php echo $texto_estado; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                    <tr>
                                        <td colspan="3" style="padding: 20px; text-align: center; color: #999; font-style: italic;">Aún no has registrado ningún producto para la venta.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>
        </div>
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
function cancelarPedido(pedidoId) {
    if (!confirm("¿Estás seguro de que deseas cancelar el pedido #" + pedidoId + "?")) return;

    const boton = document.getElementById('btn-cancelar-' + pedidoId);
    const textoPrevio = boton.innerText;
    boton.innerText = "Procesando...";
    boton.disabled = true;

    fetch('cancelar_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pedido_id: pedidoId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Pedido #" + pedidoId + " cancelado correctamente.");
            
            const badge = document.getElementById('badge-estado-' + pedidoId);
            if (badge) {
                badge.innerText = "Cancelado";
                badge.className = "badge badge-cancelado";
            }
            
            boton.parentElement.innerHTML = '<span style="color: #aaa; font-size: 0.8rem; font-style: italic;">Sin acciones</span>';
        } else {
            alert("Error: " + data.error);
            boton.innerText = textoPrevio;
            boton.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error de conexión al procesar la cancelación.");
        boton.innerText = textoPrevio;
        boton.disabled = false;
    });
}
</script>

<script>
    const btnEditar = document.getElementById('btn-editar');
    const btnGuardar = document.getElementById('btn-guardar');
    const inputs = document.querySelectorAll('.input-perfil');
    const formPerfil = document.getElementById('form-perfil');
    const inputPassword = document.getElementById('edit-password');

    btnEditar.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = false);
        btnEditar.style.display = 'none';
        btnGuardar.style.display = 'inline-block';
    });

    formPerfil.addEventListener('submit', (e) => {
        const passwordValue = inputPassword.value;
        if (passwordValue.trim() !== "") {
            const tieneMayuscula   = /[A-Z]/.test(passwordValue);
            const tieneNumero      = /[0-9]/.test(passwordValue);
            const tieneEspacial    = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(passwordValue);

            if (!tieneMayuscula || !tieneNumero || !tieneEspacial) {
                e.preventDefault();
                alert("¡Contraseña poco segura!\n\nPara actualizar tu contraseña, debes cumplir con todos los requisitos:\n• Al menos una letra MAYÚSCULA.\n• Al menos un número (0-9).\n• Al menos un carácter especial (ej: @, $, #, !).");
                inputPassword.focus();
            }
        }
    });
</script>

<script>
    const usuarioLogueado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
    if (!usuarioLogueado) {
        localStorage.removeItem("carrito");
    }
</script>

<?php include 'control_inactividad.php'; ?>
<script src="JS/main.js"></script>

</body>
</html>
