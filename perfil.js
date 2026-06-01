document.addEventListener('DOMContentLoaded', () => {
    // 1. OBTENER USUARIO
    const user = JSON.parse(localStorage.getItem('currentUser'));

    if (!user) {
        window.location.href = 'login.html';
        return;
    }

    // --- NUEVO: MOSTRAR SECCIÓN CAMPESINO SI TIENE EL ROL ---
    // Esto activa el cuadro que pusimos en el HTML
    const seccionCampesino = document.getElementById('seccion-campesino');
    if (user.role === 'productor' && seccionCampesino) {
        seccionCampesino.style.display = 'block';
    }
    // -------------------------------------------------------

    // 2. CARGA INICIAL DE DATOS
    const inputNombre = document.getElementById("edit-nombre");
    const inputEmail = document.getElementById("edit-email");
    const emailElem = document.getElementById('user-email');
    const nameElem = document.getElementById('user-display-name');

    // Llenar inputs de edición
    if (inputNombre) inputNombre.value = user.nombre || user.username || "";
    if (inputEmail) inputEmail.value = user.email || "";
    
    // Llenar textos del header del panel
    if (emailElem) emailElem.innerText = user.email;
    if (nameElem) nameElem.innerText = user.nombre || user.username;

    // 3. MOSTRAR PEDIDOS
    mostrarPedidos(user);

    // 4. LÓGICA DE BOTONES (EDITAR / GUARDAR)
    const btnEditar = document.getElementById("btn-editar");
    const btnGuardar = document.getElementById("btn-guardar");
    const inputsPerfil = document.querySelectorAll(".input-perfil");
    const formPerfil = document.getElementById("form-perfil");

    if (btnEditar) {
        btnEditar.addEventListener("click", () => {
            inputsPerfil.forEach(input => input.disabled = false);
            btnEditar.style.display = "none";
            btnGuardar.style.display = "inline-block";
            inputNombre.focus();
        });
    }

    if (formPerfil) {
        formPerfil.addEventListener("submit", (e) => {
            e.preventDefault();

            const nuevoNombre = inputNombre.value.trim();
            const nuevoEmail = inputEmail.value.trim();
            const nuevaPass = document.getElementById("edit-password").value;
            const confirmaPass = document.getElementById("edit-confirm-password").value;

            if (nuevoNombre === "" || nuevoEmail === "") {
                alert("⚠️ El nombre y el correo son obligatorios.");
                return;
            }

            if (nuevaPass !== "") {
                if (nuevaPass.length < 6) {
                    alert("⚠️ La nueva contraseña debe tener al menos 6 caracteres.");
                    return;
                }
                if (nuevaPass !== confirmaPass) {
                    alert("⚠️ Las contraseñas no coinciden.");
                    return;
                }
                user.password = nuevaPass;
            }

            user.nombre = nuevoNombre;
            user.email = nuevoEmail;

            localStorage.setItem('currentUser', JSON.stringify(user));

            let listaUsuarios = JSON.parse(localStorage.getItem('users')) || [];
            const index = listaUsuarios.findIndex(u => u.id === user.id || u.email === user.email);
            
            if (index !== -1) {
                listaUsuarios[index] = { ...listaUsuarios[index], ...user };
                localStorage.setItem('users', JSON.stringify(listaUsuarios));
            }

            alert("✅ Perfil y contraseña actualizados correctamente.");
            location.reload(); 
        });
    }

    // 5. BOTÓN CERRAR SESIÓN
    const btnLogout = document.getElementById("btn-logout");
    if (btnLogout) {
        btnLogout.addEventListener("click", () => {
            localStorage.removeItem("currentUser");
            localStorage.removeItem("carrito");
            localStorage.removeItem("totalCarrito");
            alert("Has cerrado sesión. ¡Vuelve pronto!");
            window.location.href = "index.html";
        });
    }
});

// FUNCIÓN PARA RENDERIZAR PEDIDOS (Sin cambios)
function mostrarPedidos(user) {
    const contenedor = document.getElementById("lista-pedidos");
    if (!contenedor) return;

    if (!user.pedidos || user.pedidos.length === 0) {
        contenedor.innerHTML = `
            <tr>
                <td colspan="4" style="text-align: center; padding: 30px; color: #666;">
                    <p>Aún no tienes pedidos realizados.</p>
                    <a href="index.html#productos" class="btn-principal" style="text-decoration: none; display: inline-block; margin-top: 10px; padding: 10px 20px; background: #0BAE03; color: white; border-radius: 5px;">
                        ¡Ir a comprar productos frescos!
                    </a>
                </td>
            </tr>
        `;
        return;
    }

    contenedor.innerHTML = "";
    [...user.pedidos].reverse().forEach(pedido => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong>${pedido.id}</strong></td>
            <td>${pedido.fecha}</td>
            <td>$${pedido.total.toLocaleString()}</td>
            <td><span class="estado-badge">${pedido.estado}</span></td>
        `;
        contenedor.appendChild(tr);
    });
}
