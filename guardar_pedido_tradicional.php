<?php
session_start();
header('Content-Type: application/json');

// 1. Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado en la sesión.']);
    exit;
}

// 2. Leer los datos del checkout
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos en el servidor.']);
    exit;
}

// 3. Conexión a la base de datos
if (!file_exists('conexion.php')) {
    echo json_encode(['success' => false, 'error' => 'El archivo conexion.php no existe en esta ruta.']);
    exit;
}
include 'conexion.php'; 

// Validar que la variable de conexión exista
if (!isset($conexion)) {
    echo json_encode(['success' => false, 'error' => 'La variable $conexion no está definida en tu conexion.php.']);
    exit;
}

$usuario_id     = $_SESSION['usuario_id'];
$nombre_entrega = isset($data['nombre_entrega']) ? $data['nombre_entrega'] : '';
$email          = isset($data['email']) ? $data['email'] : '';
$telefono       = isset($data['telefono']) ? $data['telefono'] : '';
$direccion      = isset($data['direccion']) ? $data['direccion'] : '';
$ciudad         = isset($data['ciudad']) ? $data['ciudad'] : ''; 
$subtotal       = isset($data['subtotal']) ? intval($data['subtotal']) : 0;
$envio          = isset($data['envio']) ? intval($data['envio']) : 0;
$total          = isset($data['total']) ? intval($data['total']) : 0;
$fecha_actual   = date('Y-m-d H:i:s');

// -------------------------------------------------------------------------
// REGLA DEFINITIVA: TODO SE GUARDA COMO COMPLETADO (VERDE) AL INSTANTE
// -------------------------------------------------------------------------
$estado = 'Completado'; 
// -------------------------------------------------------------------------

// Extraer el carrito enviado desde el frontend JS
$carrito = isset($data['carrito']) ? $data['carrito'] : [];

try {
    // Desactivar temporalmente el autocommit para procesar todo como una transacción segura
    $conexion->begin_transaction();

    // Consulta SQL usando la columna correcta 'ciudad'
    $query = "INSERT INTO pedidos (usuario_id, nombre_entrega, email, telefono, direccion, ciudad, subtotal, envio, total, estado, fecha) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
              
    $stmt = $conexion->prepare($query);
    
    if (!$stmt) {
        $conexion->rollback();
        echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta SQL: ' . $conexion->error]);
        exit;
    }
    
    $stmt->bind_param(
        "isssssiiiss", 
        $usuario_id, 
        $nombre_entrega, 
        $email, 
        $telefono, 
        $direccion, 
        $ciudad, 
        $subtotal, 
        $envio, 
        $total, 
        $estado, 
        $fecha_actual
    );
    
    if ($stmt->execute()) {
        // CAPTURAR EL ID DEL PEDIDO GENERADO
        $pedido_id = $conexion->insert_id;
        
        // GUARDAR LOS PRODUCTOS EN DETALLES_PEDIDO
        if (!empty($carrito) && is_array($carrito)) {
            $query_detalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt_detalle = $conexion->prepare($query_detalle);
            
            if (!$stmt_detalle) {
                $conexion->rollback();
                echo json_encode(['success' => false, 'error' => 'Error al preparar detalles del pedido: ' . $conexion->error]);
                exit;
            }

            foreach ($carrito as $item) {
                $producto_id     = intval($item['id']);
                $cantidad        = intval($item['cantidad']);
                $precio_unitario = intval($item['precio']);

                $stmt_detalle->bind_param("iiii", $pedido_id, $producto_id, $cantidad, $precio_unitario);
                $stmt_detalle->execute();
            }
            $stmt_detalle->close();
        }

        // Si todo salió bien, guardar permanentemente en la base de datos
        $conexion->commit();
        echo json_encode(['success' => true]);

    } else {
        $conexion->rollback();
        echo json_encode(['success' => false, 'error' => 'Error de MySQL al insertar pedido: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conexion->close();

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['success' => false, 'error' => 'Excepción del sistema: ' . $e->getMessage()]);
}
?>
