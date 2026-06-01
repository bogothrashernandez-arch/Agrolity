let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

document.addEventListener("DOMContentLoaded", () => {
    actualizarInterfazCarrito();
});

// Escuchar clics en toda la app
document.addEventListener("click", (e) => {
    // BOTÓN AGREGAR (En index.html)
    if (e.target.classList.contains("btn-agregar-carrito")) {
        const b = e.target;
        const nuevoProducto = {
            id: b.dataset.id,
            nombre: b.dataset.nombre,
            precio: Number(b.dataset.precio),
            imagen: b.dataset.imagen,
            cantidad: 1
        };

        const existe = carrito.find(p => p.id === nuevoProducto.id);
        if (existe) {
            existe.cantidad++;
        } else {
            carrito.push(nuevoProducto);
        }
        
        actualizarInterfazCarrito();
        if(window.Swal) Swal.fire({ title: '¡Añadido!', icon: 'success', timer: 800, showConfirmButton: false });
    }

    // BOTÓN ELIMINAR (En carrito.html)
    if (e.target.classList.contains("btn-eliminar") || e.target.closest(".btn-eliminar")) {
        const btn = e.target.classList.contains("btn-eliminar") ? e.target : e.target.closest(".btn-eliminar");
        const idEliminar = btn.dataset.id;
        carrito = carrito.filter(p => p.id !== idEliminar);
        actualizarInterfazCarrito();
    }
});

function actualizarInterfazCarrito() {
    localStorage.setItem("carrito", JSON.stringify(carrito));
    
    // 1. Actualizar numerito del icono 🛒
    const contadores = document.querySelectorAll(".carrito-contador");
    const totalItems = carrito.reduce((acc, p) => acc + p.cantidad, 0);
    contadores.forEach(c => c.textContent = totalItems);

    // 2. Dibujar lista si estamos en carrito.html
    const listaHTML = document.getElementById("lista-carrito");
    if (!listaHTML) return; // Si no estamos en la página del carrito, termina aquí.

    listaHTML.innerHTML = "";

    if (carrito.length === 0) {
        listaHTML.innerHTML = "<h2>Tu carrito está vacío</h2>";
        actualizarTotales(0);
        return;
    }

    carrito.forEach(p => {
        const item = document.createElement("div");
        item.className = "producto-carrito"; // CLASE DE TU CSS
        item.innerHTML = `
            <img src="${p.imagen}" alt="${p.nombre}">
            <div class="producto-info">
                <h3>${p.nombre}</h3>
                <p class="precio">$${p.precio.toLocaleString()}</p>
            </div>
            <div class="producto-cantidad">
                <input type="number" value="${p.cantidad}" min="1" readonly>
            </div>
            <div class="producto-subtotal">
                <p>$${(p.precio * p.cantidad).toLocaleString()}</p>
            </div>
            <button class="btn-eliminar" data-id="${p.id}">
                Eliminar
            </button>
        `;
        listaHTML.appendChild(item);
    });

    const subtotal = carrito.reduce((acc, p) => acc + (p.precio * p.cantidad), 0);
    actualizarTotales(subtotal);
}

function actualizarTotales(subtotal) {
    const envio = subtotal > 0 ? 5000 : 0;
    const total = subtotal + envio;

    if(document.getElementById("subtotal")) document.getElementById("subtotal").textContent = `$${subtotal.toLocaleString('es-CO')}`;
    if(document.getElementById("envio")) document.getElementById("envio").textContent = `$${envio.toLocaleString('es-CO')}`;
    if(document.getElementById("total")) document.getElementById("total").textContent = `$${total.toLocaleString('es-CO')}`;

    const btnProceder = document.getElementById("btn-proceder");
    if (btnProceder) {
        if (total > 0) {
            btnProceder.style.pointerEvents = "auto";
            btnProceder.style.opacity = "1";
        } else {
            // Si el carrito está realmente vacío, impedimos el clic de forma segura con CSS
            btnProceder.style.pointerEvents = "none";
            btnProceder.style.opacity = "0.5";
        }
    }
}
