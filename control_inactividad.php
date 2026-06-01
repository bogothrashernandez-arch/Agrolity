<style>
/* CSS Agregado para que no se rompa la estructura abajo en el footer */
.modal-inactividad {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* Fondo oscuro transparente */
    z-index: 999999; /* Asegura que flote por encima de TODO */
    display: none; /* TU CÓDIGO JS SE ENCARGARÁ DE CAMBIARLO A 'FLEX' */
    justify-content: center;
    align-items: center;
}

.modal-contenido {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    width: 90%;
    text-align: center;
    font-family: 'Didact Gothic', sans-serif;
}

.modal-contenido h3 {
    color: #d9534f; /* Color sutil de advertencia */
    margin-top: 0;
    margin-bottom: 12px;
}

.modal-contenido p {
    color: #333333;
    font-size: 1rem;
    margin-bottom: 20px;
}

.btn-seguir-conectado {
    background-color: #28a745; /* Verde Agrolity */
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-seguir-conectado:hover {
    background-color: #218838;
}
</style>

<div id="alerta-inactividad" class="modal-inactividad">
    <div class="modal-contenido">
        <h3>Sesión por Expirar</h3>
        <p>Tu sesión está a punto de expirar por inactividad. ¿Deseas seguir conectado?</p>
        <button class="btn-seguir-conectado" onclick="seguirConectado()">Sí, seguir conectado</button>
    </div>
</div>

<script>
// Tiempos configurados en milisegundos
const TIEMPO_ALERTA = 14 * 60 * 1000;  // 14 minutos para mostrar la advertencia
const TIEMPO_CIERRE = 15 * 60 * 1000;  // 15 minutos en total para cerrar la sesión

// Declaración global de las variables de control de tiempo
let timeoutAlerta;
let timeoutCierre;

// Inicializar los temporizadores de inmediato al cargar el archivo en la página
resetTimer();

// Detectar cualquier interacción física del usuario para reiniciar el contador
window.onload = resetTimer;
window.onmousemove = resetTimer;
window.onmousedown = resetTimer; 
window.ontouchstart = resetTimer;
window.onclick = resetTimer;     
window.onkeydown = resetTimer;   

/**
 * Función encargada de reiniciar y calcular los tiempos de inactividad
 */
function resetTimer() {
    // Si la alerta visual ya está activa en pantalla, congelamos el reinicio
    if (document.getElementById('alerta-inactividad').style.display === 'flex') {
        return;
    }
    
    // Limpiar cualquier temporizador que estuviera corriendo previamente
    clearTimeout(timeoutAlerta);
    clearTimeout(timeoutCierre);
    
    // Programar el disparo de la alerta a los 14 minutos de inactividad
    timeoutAlerta = setTimeout(mostrarAlerta, TIEMPO_ALERTA);
    
    // Programar la redirección forzada al minuto 15 de inactividad
    timeoutCierre = setTimeout(cerrarSesionAutomatica, TIEMPO_CIERRE);
}

/**
 * Muestra el modal de advertencia visual en la pantalla
 */
function mostrarAlerta() {
    document.getElementById('alerta-inactividad').style.display = 'flex';
}

/**
 * Se ejecuta al hacer clic en "Sí, seguir conectado".
 * Hace una petición asíncrona en segundo plano para mantener la sesión PHP viva.
 */
function seguirConectado() {
    // Ocultar el cuadro de la alerta inmediatamente
    document.getElementById('alerta-inactividad').style.display = 'none';
    
    // Enviar una señal silenciosa al servidor para actualizar la sesión en backend
    fetch('renovar_sesion.php')
        .then(() => {
            // Una vez renovado con éxito en el servidor, reiniciar los contadores locales
            resetTimer();
        })
        .catch(err => {
            console.error("Error al renovar la sesión en el servidor:", err);
        });
}

/**
 * Redirecciona al usuario al script encargado de destruir las variables de sesión
 */
function cerrarSesionAutomatica() {
    window.location.href = 'logout.php?motivo=inactividad';
}
</script>
