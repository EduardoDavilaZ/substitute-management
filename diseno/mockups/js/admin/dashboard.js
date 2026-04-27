const ctx = document.getElementById('weekly-substitutions-chart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'], // 5 días de la semana
        datasets: [{
            label: 'Sustituciones',
            data: [8, 2, 1, 7, 4], // Tus datos
            backgroundColor: '#0F4C81', // Color sólido para las barras
            borderRadius: 4,           // Un poco de redondeo arriba
            barThickness: 20           // Grosor de las barras
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, // Permite que use el alto del div padre
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                grid: {
                    drawTicks: false,
                    drawOnChartArea: false,
                    color: '#0F4C81'
                },
                ticks: {
                    font: { size: 10 } // Etiquetas de días más pequeñas
                },
                offset: true
            },
            y: {
                beginAtZero: true,
                grid: {
                    drawTicks: false,
                    drawOnChartArea: true,
                    color: 'rgba(72, 187, 120, 0.2)' // Bajamos opacidad a las líneas horizontales
                },
                ticks: {
                    stepSize: 2, // Para que no salgan decimales si los datos son bajos
                    font: { size: 10 }
                }
            }
        }
    }
});