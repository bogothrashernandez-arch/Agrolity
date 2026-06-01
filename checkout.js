// JS/checkout.js - PROCESAMIENTO EXCLUSIVO DE LA PÁGINA DE PAGO

document.addEventListener("DOMContentLoaded", () => {
    // Si no hay sesión iniciada en PHP, vaciamos el carrito local por seguridad
    if (typeof usuarioLogueado === 'undefined' || usuarioLogueado === false) {
        localStorage.removeItem("carrito");
    }

    const carritoInstancia = JSON.parse(localStorage.getItem("carrito")) || [];
    
    // Cálculos económicos básicos del pedido
    const subtotal = carritoInstancia.reduce((acc, p) => acc + (Number(p.precio) * p.cantidad), 0);
    const envio = subtotal > 0 ? 5000 : 0;
    const totalFinal = subtotal + envio;

    const subtotalEl = document.getElementById("subtotal");
    const envioEl = document.getElementById("envio");
    const totalEl = document.getElementById("total");

    if (subtotalEl) subtotalEl.textContent = `$${subtotal.toLocaleString('es-CO')}`;
    if (envioEl) envioEl.textContent = `$${envio.toLocaleString('es-CO')}`;
    if (totalEl) totalEl.textContent = `$${totalFinal.toLocaleString('es-CO')}`;

    // Renderizar la lista lateral de productos
    mostrarProductosCheckout(carritoInstancia);

    // Escuchador del botón para enviar datos a la base de datos
    const btnConfirmar = document.getElementById("btn-confirmar");
    if (btnConfirmar) {
        btnConfirmar.addEventListener("click", () => {
            procesarConfirmacion(carritoInstancia);
        });
    }
});

function mostrarProductosCheckout(listaProductos) {
    const contenedor = document.getElementById("checkout-lista");
    if (!contenedor) return;

    contenedor.innerHTML = "";
    if (listaProductos.length === 0) {
        contenedor.innerHTML = "<p style='text-align:center; color:#888;'>El pedido está vacío.</p>";
        return;
    }

    listaProductos.forEach(p => {
        const div = document.createElement("div");
        div.className = "checkout-item";
        div.style.marginBottom = "10px";
        div.innerHTML = `
            <p><strong>${p.nombre}</strong> (x${p.cantidad})</p>
            <p style="font-size: 0.9rem; color: #555;">Subtotal: $${(p.precio * p.cantidad).toLocaleString('es-CO')}</p>
            <hr style="border:0; border-top: 1px solid #eee;">
        `;
        contenedor.appendChild(div);
    });
}

function procesarConfirmacion(carritoActual) {
    if (typeof usuarioLogueado === 'undefined' || usuarioLogueado === false) {
        alert("❌ ¡Atención! Para realizar el pedido debe estar registrado o haber iniciado sesión.");
        window.location.href = "login.php"; 
        return;
    }
    
    if (carritoActual.length === 0) {
        alert("Tu carrito está vacío. No puedes realizar un pedido.");
        return;
    }

    const nombreInput    = document.getElementById('nombre');
    const emailInput     = document.getElementById('email');
    const telefonoInput  = document.getElementById('telefono');
    const direccionInput = document.getElementById('direccion');
    const ciudadInput    = document.getElementById('ciudad');

    if (!nombreInput || !emailInput || !telefonoInput || !direccionInput || !ciudadInput) {
        alert("Error de estructura en el formulario.");
        return;
    }

    if (!nombreInput.value.trim() || !emailInput.value.trim() || !telefonoInput.value.trim() || !direccionInput.value.trim() || !ciudadInput.value.trim()) {
        alert("Por favor, completa todos los campos de envío.");
        return;
    }

    const subtotal = carritoActual.reduce((acc, p) => acc + (Number(p.precio) * p.cantidad), 0);
    const envio    = 5000; 
    const total    = subtotal + envio;

    const datosPedido = {
        nombre: nombreInput.value.trim(),
        email: emailInput.value.trim(),
        telefono: telefonoInput.value.trim(),
        direccion: direccionInput.value.trim(),
        ciudad: ciudadInput.value.trim(),
        subtotal: subtotal,
        envio: envio,
        total: total,
        carrito: carritoActual
    };

    fetch('guardar_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosPedido)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert("✅ " + data.message);
            localStorage.removeItem("carrito"); // Se limpia el carrito al comprar con éxito
            window.location.href = 'perfil.php'; 
        } else {
            alert("❌ Error del sistema: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error crítico:", error);
        alert("Hubo un problema de conexión al procesar el pedido.");
    });
}
