<?php
include 'conexion.php';
session_start();

// 1. Verificamos que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// 2. Verificamos que se haya recibido un ID por la URL
if (isset($_GET['id'])) {
    $id_producto = mysqli_real_escape_string($conexion, $_GET['id']);
    $id_productor = $_SESSION['usuario_id'];

    // 3. Ejecutamos la eliminación
    // Agregamos id_productor en el WHERE por seguridad (que solo el dueño pueda borrarlo)
    $sql = "DELETE FROM productos WHERE id = '$id_producto' AND id_productor = '$id_productor'";

    if (mysqli_query($conexion, $sql)) {
        // Redirigimos de vuelta al panel con un mensaje de éxito
        echo "<script>
                alert('Producto eliminado correctamente.'); 
                window.location.href='dashboard-campesino.php';
              </script>";
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
} else {
    // Si no hay ID, simplemente regresamos
    header("Location: dashboard-campesino.php");
}
?>
