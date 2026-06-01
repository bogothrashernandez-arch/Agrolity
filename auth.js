document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('auth-form');
    const registroForm = document.getElementById('form-registro');

    // --- LOGIN ---
    /* COMENTADO PARA CRUD MYSQL: 
       Dejamos que el formulario use 'action="login.php"' en el HTML.
       Si dejamos el e.preventDefault(), el PHP nunca recibirá los datos.
    */
    /*
    if (loginForm) {
        loginForm.onsubmit = function(e) {
            e.preventDefault(); 
            const email = document.getElementById('email').value.trim();
            const pass = document.getElementById('password').value;
            const usuarios = JSON.parse(localStorage.getItem('users')) || [];
            const user = usuarios.find(u => u.email === email && u.password === pass);

            if (user) {
                localStorage.setItem('currentUser', JSON.stringify(user));
                if (user.role === 'productor') {
                    window.location.href = 'dashboard-campesino.html';
                } else {
                    window.location.href = 'index.html';
                }
            } else {
                alert("Correo o contraseña incorrectos");
            }
        };
    }
    */

    // --- REGISTRO ---
    /* COMENTADO PARA CRUD MYSQL: 
       Ahora el archivo 'registro.php' se encargará de guardar en la base de datos.
    */
    /*
    if (registroForm) {
        registroForm.onsubmit = function(e) {
            e.preventDefault();
            const nombre = document.getElementById('reg-nombre').value;
            const email = document.getElementById('reg-email').value;
            const pass = document.getElementById('reg-pass').value;
            const rol = document.getElementById('reg-rol').value;

            let usuarios = JSON.parse(localStorage.getItem('users')) || [];

            if (usuarios.find(u => u.email === email)) {
                alert("El correo ya existe");
                return;
            }

            const nuevoU = {
                id: Date.now(),
                nombre: nombre,
                email: email,
                password: pass,
                role: rol,
                pedidos: [],
                misProductos: []
            };

            usuarios.push(nuevoU);
            localStorage.setItem('users', JSON.stringify(usuarios));
            alert("Usuario creado en LocalStorage. Cambia a PHP para MySQL.");
            window.location.href = 'login.html';
        };
    }
    */
});

// --- ESTO SE MANTIENE PARA FUNCIONALIDAD DE INTERFAZ ---

function restablecerPassword(email, nuevaPassword) {
    let usuarios = JSON.parse(localStorage.getItem('users')) || [];
    const indice = usuarios.findIndex(u => u.email.toLowerCase() === email.toLowerCase().trim());

    if (indice !== -1) {
        usuarios[indice].password = nuevaPassword;
        localStorage.setItem('users', JSON.stringify(usuarios));

        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        if (currentUser && currentUser.email.toLowerCase() === email.toLowerCase().trim()) {
            localStorage.removeItem('currentUser');
        }

        alert("Contraseña actualizada localmente. Nota: Esto no afecta a MySQL.");
        window.location.href = 'login.html';
    } else {
        alert("El correo electrónico no coincide con ningún usuario en LocalStorage.");
    }
}

function confirmarRestablecer() {
    const email = prompt("Por favor, ingresa tu correo electrónico registrado:");
    
    if (email) {
        const nuevaClave = prompt("Ingresa tu nueva contraseña:");
        
        if (nuevaClave && nuevaClave.length >= 4) {
            restablecerPassword(email, nuevaClave);
        } else {
            alert("La contraseña es muy corta o no es válida.");
        }
    }
}
