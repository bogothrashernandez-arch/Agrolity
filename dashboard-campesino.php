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

$nombre_usuario = $_SESSION['usuario_nombre'];
$id_productor = $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Campesino - Agrolity</title>
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="CSS/estilo.css"> 
    <style>
        body {
            background: url('img/dash.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Didact Gothic', sans-serif;
            margin: 0;
            padding: 0;
        }

        .main-content { margin-top: 80px; }

        .banner-madera {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('img/tabla.jpg') no-repeat center center;
            background-size: cover;
            width: 100%;
            padding: 60px 0;
            text-align: center;
            color: white;
            border-bottom: 5px solid #0BAE03;
        }

        .banner-madera h1 {
            font-size: 3rem;
            margin: 0;
            text-shadow: 3px 3px 10px rgba(0,0,0,0.8);
        }

        .content-wrapper {
            max-width: 950px;
            margin: -50px auto 60px;
            padding: 0 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.6);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width { grid-column: span 2; }

        input, textarea {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            font-family: 'Didact Gothic', sans-serif;
        }

        /* Estilo para el input de archivo */
        .input-file-container {
            grid-column: span 2;
            background: #f8f9fa;
            padding: 15px;
            border: 2px dashed #0BAE03;
            border-radius: 8px;
            text-align: center;
        }

        .btn-publicar {
            grid-column: span 2;
            background: #0BAE03;
            color: white;
            border: none;
            padding: 18px;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-publicar:hover { background: #088e02; transform: scale(1.02); }

        .section-title {
            margin-top: 45px;
            border-left: 8px solid #0BAE03;
            padding-left: 15px;
            color: #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th { background: #f9f9f9; padding: 15px; text-align: left; border-bottom: 2px solid #0BAE03; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        
        .img-miniatura {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .btn-logout-header {
            background: #d9534f;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-family: 'Didact Gothic', sans-serif;
        }

        /* Pequeños Badges para la tabla de control */
        .badge-estado {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>
    
    <header class="header">
        <div class="logo">
            <img src="img/Logo1.png" alt="Agrolity">
        </div>
        <nav class="nav">
            <ul>
                <li><a href="index.php">Ver Sitio</a></li>
                <li><button class="btn-logout-header" onclick="location.href='logout.php'">Cerrar Sesión</button></li>
            </ul>
        </nav>
    </header>

    <div class="main-content">
        <section class="banner-madera">
            <h1>MI PANEL DE VENTAS</h1>
            <p style="font-size: 1.3rem; margin-top: 10px;">¡Bienvenido de nuevo, <?php echo $nombre_usuario; ?>!</p>
        </section>

        <div class="content-wrapper">
            <div class="glass-card">
                
                <?php if (isset($_GET['subido']) && $_GET['subido'] === 'true'): ?>
                    <div style="background-color: #e0f2fe; color: #0369a1; padding: 18px; border-radius: 8px; margin-bottom: 25px; font-weight: bold; border-left: 6px solid #0284c7; line-height: 1.4; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        🌾 ¡Cosecha registrada con éxito!<br>
                        <span style="font-weight: normal; font-size: 0.95rem; color: #0c4a6e;">Tu producto ha entrado en fase de validación. Tan pronto como el administrador revise la información y la imagen, se publicará automáticamente en la tienda. Puedes realizar el seguimiento en la tabla de abajo.</span>
                    </div>
                <?php endif; ?>

                <h3 style="margin-top: 0; color: #0BAE03; font-size: 1.5rem;">Publicar nueva cosecha</h3>
                
                <form action="publicar_producto.php" method="POST" enctype="multipart/form-data" id="form-producto" class="form-grid">
                    <input type="hidden" name="id_producto" id="p-id">
                    
                    <input type="text" name="nombre" id="p-nombre" placeholder="¿Qué vas a vender hoy? (ej. Papa Sabanera)" class="full-width" required>
                    
                    <input type="number" name="precio" id="p-precio" placeholder="Precio por Kilo ($)" required step="0.01">
                    
                    <input type="text" name="descripcion" id="p-desc" placeholder="Breve descripción (opcional)">

                    <div class="input-file-container">
                        <label for="p-img" style="display: block; margin-bottom: 5px; color: #555;">Subir foto del producto:</label>
                        <input type="file" name="imagen" id="p-img" accept="image/*" class="full-width">
                    </div>
                    
                    <button type="submit" class="btn-publicar">SUBIR AL MERCADO</button>
                </form>

                <h3 class="section-title">Tus productos en el mercado</h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Producto</th>
                                <th>Precio/Kg</th>
                                <th>Descripción</th>
                                <th style="text-align: center;">Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-body">
                            <?php
                            $query = "SELECT * FROM productos WHERE id_productor = '$id_productor' ORDER BY id DESC";
                            $result = mysqli_query($conexion, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $ruta_img = !empty($row['imagen']) ? "Uploads/".$row['imagen'] : "img/sin-foto.jpg";
                                    $precio_formateado = number_format($row['precio'], 0, ',', '.');

                                    // Lógica visual para la columna de estado interna
                                    $estado_raw = strtolower($row['estado'] ?? 'pendiente');
                                    if ($estado_raw === 'aprobado') {
                                        $badge_html = "<span class='badge-estado' style='background:#d1fae5; color:#065f46;'>Activo</span>";
                                    } elseif ($estado_raw === 'rechazado') {
                                        $badge_html = "<span class='badge-estado' style='background:#fee2e2; color:#991b1b;'>Rechazado</span>";
                                    } else {
                                        $badge_html = "<span class='badge-estado' style='background:#fef3c7; color:#92400e;'>En Revisión</span>";
                                    }

                                    echo "<tr>
                                            <td><img src='{$ruta_img}' class='img-miniatura'></td>
                                            <td><strong>{$row['nombre']}</strong></td>
                                            <td>$ {$precio_formateado}</td>
                                            <td>{$row['descripcion']}</td>
                                            <td style='text-align: center;'>{$badge_html}</td>
                                            <td>
                                                <button onclick=\"prepararEdicion({$row['id']}, '{$row['nombre']}', {$row['precio']}, '{$row['descripcion']}')\" style='color:#0BAE03; background:none; border:none; cursor:pointer; font-weight:bold; margin-right:10px;'>Editar</button>
                                                <button onclick=\"eliminarProducto({$row['id']})\" style='color:#d9534f; background:none; border:none; cursor:pointer; font-weight:bold;'>Eliminar</button>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center; padding:20px;'>No tienes productos registrados.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="JS/productor.js"></script>
    <?php include 'control_inactividad.php'; ?>
</body>
</html>
