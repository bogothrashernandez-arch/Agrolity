<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['pedido_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de pedido no recibido.']);
    exit;
}

include 'conexion.php';

$pedido_id = intval($data['pedido_id']);
$usuario_id = $_SESSION['usuario_id'];

try {
    // Validamos que el pedido pertenezca al usuario en sesión para evitar que cancelen pedidos ajenos
    $query = "UPDATE pedidos SET estado = 'Cancelado' WHERE id = ? AND usuario_id = ? AND estado = 'Pendiente'";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $pedido_id, $usuario_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo cancelar. El pedido ya cambió de estado o no te pertenece.']);
    }
    
    $stmt->close();
    $conexion->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
?>
