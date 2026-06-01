<?php
// guardar_pedido.php

// 1. Asegurar que manejamos respuestas JSON y sesiones activas
header('Content-Type: application/json');
session_start();
include 'conexion.php';

// 2. Verificar que el usuario tenga sesión iniciada
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No has iniciado sesión o tu sesión expiró.']);
    exit();
}

// 3. Capturar y decodificar el JSON enviado desde el frontend (JS)
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Validar que los datos llegaron correctamente
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos válidos en el pedido.']);
    exit();
}

$id_usuario     = $_SESSION['usuario_id'];
$nombre_entrega = mysqli_real_escape_string($conexion, $data['nombre']);
$email          = mysqli_real_escape_string($conexion, $data['email']);
$telefono       = mysqli_real_escape_string($conexion, $data['telefono']);
$direccion      = mysqli_real_escape_string($conexion, $data['direccion']);
$ciudad         = mysqli_real_escape_string($conexion, $data['ciudad']);
$subtotal       = floatval($data['subtotal']);
$envio          = floatval($data['envio']);
$total          = floatval($data['total']);
$carrito        = $data['carrito']; // Es el arreglo de productos

// Validar que el carrito no venga vacío
if (empty($carrito)) {
    echo json_encode(['status' => 'error', 'message' => 'El carrito está vacío.']);
    exit();
}

// 4. INICIAR TRANSACCIÓN DE BASE DE DATOS
// Esto asegura que si algo falla a mitad de camino, no se guarde un pedido incompleto.
mysqli_begin_transaction($conexion);

try {
    // A. Insertar los datos generales en la tabla 'pedidos' (Guardando como Completado)
    $queryPedido = "INSERT INTO pedidos (usuario_id, nombre_entrega, email, telefono, direccion, ciudad, subtotal, envio, total, estado) 
                    VALUES ('$id_usuario', '$nombre_entrega', '$email', '$telefono', '$direccion', '$ciudad', '$subtotal', '$envio', '$total', 'completado')";
    
    if (!mysqli_query($conexion, $queryPedido)) {
        throw new Exception("Error al registrar los datos generales del pedido.");
    }

    // B. Obtener el ID que se le asignó automáticamente a este pedido
    $id_pedido_nuevo = mysqli_insert_id($conexion);

    // C. Recorrer el carrito para registrar cada producto en 'detalles_pedido'
    foreach ($carrito as $item) {
        $producto_id    = mysqli_real_escape_string($conexion, $item['id']);
        $cantidad       = intval($item['cantidad']);
        $precio_unitario= floatval($item['precio']);

        $queryDetalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) 
                        VALUES ('$id_pedido_nuevo', '$producto_id', '$cantidad', '$precio_unitario')";
        
        if (!mysqli_query($conexion, $queryDetalle)) {
            throw new Exception("Error al registrar el producto ID: $producto_id en los detalles.");
        }
    }

    // Si todo se ejecutó sin errores, guardamos los cambios de forma permanente
    mysqli_commit($conexion);
    
    // Devolvemos éxito al JavaScript
    echo json_encode(['status' => 'success', 'message' => '¡Pedido registrado con éxito en la base de datos!']);

} catch (Exception $e) {
    // Si algo falló, deshacemos todo para evitar datos basura corruptos
    mysqli_rollback($conexion);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit();
?>
