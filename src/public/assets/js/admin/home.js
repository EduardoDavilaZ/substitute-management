const ctx = document.getElementById('weekly-substitutions-chart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
        datasets: [{
            label: 'Sustituciones',
            data: [8, 2, 1, 7, 4],
            backgroundColor: '#0F4C81',
            borderRadius: 4,
            barThickness: 20
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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
                    font: { size: 10 }
                },
                offset: true
            },
            y: {
                beginAtZero: true,
                grid: {
                    drawTicks: false,
                    drawOnChartArea: true,
                    color: 'rgba(72, 187, 120, 0.2)'
                },
                ticks: {
                    stepSize: 2,
                    font: { size: 10 }
                }
            }
        }
    }
});