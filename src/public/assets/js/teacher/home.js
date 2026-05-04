document.addEventListener('DOMContentLoaded', () => {
    function actualizarReloj() {
        const ahora = new Date();
        
        const fechaTexto = ahora.toLocaleDateString('es-ES', { 
            weekday: 'long', day: 'numeric', month: 'long' 
        });

        const horaTexto = ahora.toLocaleTimeString('es-ES', {
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });

        document.getElementById('fecha').textContent = fechaTexto;
        document.getElementById('reloj').textContent = horaTexto;
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();
});