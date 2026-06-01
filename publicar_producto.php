<?php
session_start();
include 'conexion.php';

// Verificar que el usuario esté logueado y sea productor
if (!isset($_SESSION['usuario_id'])) {
    die("Sesión no iniciada.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_productor = $_SESSION['usuario_id'];
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $precio = mysqli_real_escape_string($conexion, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    
    // VALIDAR: El nombre y precio son obligatorios
    if (empty($nombre) || empty($precio)) {
        header("Location: dashboard-campesino.php?error=1&msg=Faltan datos obligatorios");
        exit();
    }
    
    $nombre_final_archivo = null;
    $error_imagen = false;

    // Procesar la imagen SOLO si el usuario seleccionó una
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        $nombre_original = $_FILES['imagen']['name'];
        $tamano_imagen = $_FILES['imagen']['size'];
        $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
        
        // 🔒 SEGURIDAD EXTRA 1: Limitar peso máximo a 2MB para cuidar el almacenamiento
        $max_size = 2 * 1024 * 1024; 
        if ($tamano_imagen > $max_size) {
            header("Location: dashboard-campesino.php?error=1&msg=La imagen es muy pesada. Máximo 2MB");
            exit();
        }

        // Validar extensión visual
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $extensiones_permitidas)) {
            header("Location: dashboard-campesino.php?error=1&msg=Formato no permitido. Use JPG, PNG o GIF");
            exit();
        }

        // 🔒 SEGURIDAD EXTRA 2: Validar el Tipo MIME interno real del archivo
        // Esto frena en seco a hackers que renombran un archivo .php peligroso a .jpg
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipo_mime_real = finfo_file($finfo, $ruta_temporal);
        finfo_close($finfo);

        $mimes_seguros = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($tipo_mime_real, $mimes_seguros)) {
            header("Location: dashboard-campesino.php?error=1&msg=El contenido del archivo no es una imagen válida");
            exit();
        }
        
        // Crear nombre único seguro
        $nombre_final_archivo = time() . "_" . $id_productor . "." . $extension;
        $ruta_destino = "Uploads/" . $nombre_final_archivo;

        if (!move_uploaded_file($ruta_temporal, $ruta_destino)) {
            $error_imagen = true;
            header("Location: dashboard-campesino.php?error=1&msg=Error al subir la imagen");
            exit();
        }
    }
    
    // 🔒 SEGURIDAD EXTRA 3: Insertar obligando al campo 'estado' a ser 'pendiente'
    // Se mapea con tu columna id_productor exacta
    $query = "INSERT INTO productos (nombre, descripcion, precio, imagen, id_productor, estado) 
              VALUES ('$nombre', '$descripcion', '$precio', " . ($nombre_final_archivo ? "'$nombre_final_archivo'" : "NULL") . ", '$id_productor', 'pendiente')";

    if (mysqli_query($conexion, $query)) {
        // Redirección exitosa modificada para que tu frontend sepa que está en revisión
        header("Location: dashboard-campesino.php?subido=true&msg=¡Cosecha enviada! Estará en validación por seguridad.");
        exit();
    } else {
        echo "Error en la base de datos: " . mysqli_error($conexion);
    }
}
?>
