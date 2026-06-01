// JS/main.js - CONTROL DE AGREGADO Y CONTADOR VISUAL

document.addEventListener('DOMContentLoaded', () => {
    // 1. PRIORIDAD MÁXIMA: Pintar el contador del carrito inmediatamente al cargar la página
    actualizarContadorVisual(); 

    // 2. CARGA DE COMPONENTES DE LA TIENDA
    const contenedor = document.getElementById('contenedor-productos');
    if (contenedor) {
        cargarProductosDinamicos();
    }

    // Ejecución segura del header con try-catch para que no congele el archivo
    try {
        if (typeof actualizarInterfazHeader === 'function') {
            actualizarInterfazHeader();
        }
    } catch (e) {
        console.warn("actualizarInterfazHeader no definida.");
    }
});

// ACTUALIZAR CONTADOR VISUAL (Fijo y visible siempre, incluso en 0)
function actualizarContadorVisual() {
    // 1. Leemos el carrito del almacenamiento o asignamos un array vacío si no hay nada
    const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
    
    // 2. Sumamos la cantidad total de artículos seleccionados
    const totalItems = carrito.reduce((acc, p) => acc + p.cantidad, 0);
    
    // 3. Capturamos todos los elementos con la clase del contador en tu HTML
    const contadores = document.querySelectorAll(".carrito-contador");
    
    // 4. Pintamos el número y nos aseguramos de que NUNCA se oculte (siempre en 'flex')
    contadores.forEach(c => {
        c.textContent = totalItems;
        c.style.display = "flex"; // Eliminamos el 'none' para que el círculo verde no desaparezca nunca
    });
}

// AÑADIR PRODUCTOS AL CARRITO
document.addEventListener("click", (e) => {
    if (e.target.classList.contains("btn-agregar-carrito")) {
        const b = e.target;
        const nuevoProducto = {
            id: b.dataset.id,
            nombre: b.dataset.nombre,
            precio: Number(b.dataset.precio),
            imagen: b.dataset.imagen,
            cantidad: 1
        };

        let carritoTemporal = JSON.parse(localStorage.getItem("carrito")) || [];
        const existe = carritoTemporal.find(p => String(p.id) === String(nuevoProducto.id));
        
        if (existe) {
            existe.cantidad++;
        } else {
            carritoTemporal.push(nuevoProducto);
        }

        localStorage.setItem("carrito", JSON.stringify(carritoTemporal));
        actualizarContadorVisual();
        alert(`¡${nuevoProducto.nombre} añadido al carrito!`);
    }
});

// RENDERIZAR EL CATÁLOGO
function cargarProductosDinamicos() {
    const contenedor = document.getElementById('contenedor-productos');
    const productos = JSON.parse(localStorage.getItem('productosGlobales')) || [];
    if (!contenedor) return;

    if (productos.length === 0) {
        contenedor.innerHTML = `<p style="grid-column: 1/-1; text-align: center;">Cargando cosechas...</p>`;
        return;
    }

    contenedor.innerHTML = "";
    productos.forEach(prod => {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
            <img src="${prod.imagen || 'img/papa-criolla-3432646_1280.jpg'}" alt="${prod.nombre}">
            <div class="card-info" style="padding: 15px;">
                <h3>${prod.nombre}</h3>
                <p class="price">$${Number(prod.precio).toLocaleString()} <span>x libra</span></p>
                <button class="btn btn-agregar-carrito" 
                    data-id="${prod.id}" data-nombre="${prod.nombre}" 
                    data-precio="${prod.precio}" data-imagen="${prod.imagen}">
                    Añadir al carrito
                </button>
            </div>`;
        contenedor.appendChild(card);
    });
}
