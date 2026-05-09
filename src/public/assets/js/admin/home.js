$(document).ready(function () {
    const date = new Date();

    const dateFormat = new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date).replace(/\//g, '-');
    const dayWeek = date.toLocaleDateString('es-ES', { weekday: 'long' });
    let dayWeekM = dayWeek.charAt(0).toUpperCase() + dayWeek.slice(1);

    $('#dateToday').text("Semana del " + dateFormat + " " + dayWeekM);

    $.ajax({
        url: BASE_URL + "dashboard/obtain-dashboard-data",
        type: 'GET',
        dataType: 'json',
        success: function (respuesta) {
            console.log(respuesta);
            $('#totalTeachers').text(respuesta["active_teachers"]);
            $('#totalSubstitutions').text(respuesta["today_shifts"]);
            $('#totalClasses').text(respuesta["active_classes"]);
            $('#totalAbsences').text(respuesta["today_absences"]);
            respuesta["pending_guards"].forEach(guard => {
                $('#pending-substitutions').append(`
                    <div class="col p-2">
                        <div class="pending-group p-2">
                            <div class="px-2">
                                <span class="day">${guard.dia} - ${guard.class_name}</span>
                            </div>
                            <div class="icon-alert center">
                                <i class="bi bi-exclamation-circle fs-5"></i>
                            </div>
                        </div>
                    </div>`);
            });
            respuesta["teacher_absences"].forEach(teacher => {

                const avatarHtml = teacher.profile_img_path
                    ? `<img src="${ASSETS_URL + 'img/' + teacher.profile_img_path}" class="rounded-circle object-fit-cover" style="width: 80px; height: 80px;">`
                    : `<i class="bi bi-person-circle text-secondary" style="font-size: 80px;"></i>`;

                $('#teachers_absences').append(`
                    <div class=" col-6 col-md-4 col-lg-3 p-2">
                        <div class="kpi-card h-100 border-0 shadow-sm text-center p-3">
                            <div class="d-flex justify-content-center mb-2">
                                ${avatarHtml}
                            </div>
                            <div class="card-body p-0">
                                <p class="card-text fw-bold mb-0 text-truncate" style="font-size: 0.9rem;">
                                    ${teacher.full_name}
                                </p>
                            </div>
                        </div>
                    </div>`);
            });
            const substitutionsWeekly = respuesta["weekly_substitutions"];

            const ctx = document.getElementById('weekly-substitutions-chart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
                    datasets: [{
                        label: 'Sustituciones',
                        data: substitutionsWeekly.map(d => d.total_sustituciones),
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
        },
        error: function (xhr, status, error) {
            console.error('Error en la llamada:', error);
        }
    });
});
