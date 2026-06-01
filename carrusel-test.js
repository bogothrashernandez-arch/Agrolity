// Variable global para el índice
let testIndex = 0;

function mostrarTestimonio(n) {
    // Seleccionamos los elementos dentro de la función para asegurar que existan
    const testSlides = document.querySelectorAll(".testimonio-slide");
    const testDots = document.querySelectorAll(".dot-test");

    if (testSlides.length === 0) return;

    // 1. Limpiar clases activas
    testSlides.forEach(slide => slide.classList.remove("active"));
    testDots.forEach(dot => dot.classList.remove("active"));
    
    // 2. Calcular nuevo índice (maneja números negativos para la flecha izquierda)
    testIndex = (n + testSlides.length) % testSlides.length;
    
    // 3. Mostrar el nuevo testimonio
    testSlides[testIndex].classList.add("active");
    if (testDots[testIndex]) {
        testDots[testIndex].classList.add("active");
    }
}

// Funciones vinculadas a los botones onclick del HTML
function cambiarTestimonio(n) { 
    mostrarTestimonio(testIndex + n); 
}

function irATestimonio(n) { 
    mostrarTestimonio(n); 
}

// Iniciar carrusel automático
setInterval(() => {
    cambiarTestimonio(1);
}, 8000);

console.log("Carrusel de testimonios activado correctamente.");
