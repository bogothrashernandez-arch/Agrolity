let slideIndex = 0;
const slides = document.querySelectorAll(".slide");
const dots = document.querySelectorAll(".dot");

function mostrarSlide(n) {
    slides.forEach(s => s.classList.remove("active"));
    dots.forEach(d => d.classList.remove("active"));
    
    slideIndex = (n + slides.length) % slides.length;
    
    slides[slideIndex].classList.add("active");
    dots[slideIndex].classList.add("active");
}

function cambiarSlide(n) { mostrarSlide(slideIndex + n); }
function irASlide(n) { mostrarSlide(n); }

// Auto-play cada 5 segundos
setInterval(() => cambiarSlide(1), 5000);
