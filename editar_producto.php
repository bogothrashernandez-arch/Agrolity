<?php
include 'conexion.php';
session_start();

// 1. Verificamos sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capturamos los datos del formulario
    $id_producto = mysqli_real_escape_string($conexion, $_POST['id_producto']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $precio = mysqli_real_escape_string($conexion, $_POST['precio']);
    $desc   = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $imagen = mysqli_real_escape_string($conexion, $_POST['imagen']);
    $id_productor = $_SESSION['usuario_id'];

    // 3. Ejecutamos la actualización
    // Solo actualiza si el producto pertenece al productor logueado
    $sql = "UPDATE productos 
            SET nombre='$nombre', descripcion='$desc', precio='$precio', imagen='$imagen' 
            WHERE id='$id_producto' AND id_productor='$id_productor'";

    if (mysqli_query($conexion, $sql)) {
        echo "<script>
                alert('¡Producto actualizado correctamente!'); 
                window.location.href='dashboard-campesino.php';
              </script>";
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>
