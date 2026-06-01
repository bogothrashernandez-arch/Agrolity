document.addEventListener('DOMContentLoaded', () => {
    // 1. Referencias exactas a tus elementos
    const formProducto = document.getElementById('form-producto');
    // Mantenemos la referencia inicial que tenías
    let btnSubmit = formProducto ? formProducto.querySelector('button[type="submit"]') : null;

    /**
     * Prepara el formulario para edición
     */
    window.prepararEdicion = (id, nombre, precio, desc) => {
        const campoId = document.getElementById('p-id'); 
        const campoNombre = document.getElementById('p-nombre');
        const campoPrecio = document.getElementById('p-precio');
        const campoDesc = document.getElementById('p-desc');

        // ASIGNACIÓN DE VALORES (Lo que PHP recibirá en $_POST)
        if (campoId) campoId.value = id;
        if (campoNombre) campoNombre.value = nombre;
        if (campoPrecio) campoPrecio.value = precio;
        if (campoDesc) campoDesc.value = desc;

        // Re-confirmamos la referencia al botón por si el DOM cambió
        if (!btnSubmit && formProducto) {
            btnSubmit = formProducto.querySelector('button[type="submit"]');
        }

        // Cambiamos el estilo del botón para indicar edición
        if (btnSubmit) {
            btnSubmit.innerText = "GUARDAR CAMBIOS"; //
            btnSubmit.style.backgroundColor = "#157347"; // Verde más oscuro
            
            // CAMBIO DINÁMICO DE ACCIÓN: Ahora apunta a editar_producto.php
            formProducto.action = "editar_producto.php";
        }
        
        // Subir suavemente al formulario
        if (formProducto) {
            formProducto.scrollIntoView({ behavior: 'smooth' });
        }
    };

    /**
     * Alerta de eliminación usando SweetAlert2
     */
    window.eliminarProducto = (id) => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: "Se borrará permanentemente de la base de datos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0BAE03',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `eliminar_producto.php?id=${id}`;
                }
            });
        } else {
            if (confirm("¿Estás seguro de eliminar este producto?")) {
                window.location.href = `eliminar_producto.php?id=${id}`;
            }
        }
    };

    console.log("Panel de productor vinculado correctamente.");
});
