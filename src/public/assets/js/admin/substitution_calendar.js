document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('substitution-calendar');

    if (!calendarEl) return;

    const isMobile = window.innerWidth < 768;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobile ? 'listMonth' : 'dayGridMonth',
        weekends: false,
        aspectRatio: isMobile ? 0.85 : 3.7,
        contentHeight: 'auto',
        locale: 'es',
        timeZone: 'Europe/Madrid',
        themeSystem: 'bootstrap5',
        headerToolbar: isMobile ? {
            left: 'prev,next',
            center: 'title',
            right: 'today,listMonth,dayGridMonth'
        } : {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            list: 'Agenda'
        },
        dayMaxEvents: true,
        displayEventTime: false,
        editable: false,
        windowResize: function () {
            const mobile = window.innerWidth < 768;
            calendar.setOption('aspectRatio', mobile ? 0.85 : 3.7);
            calendar.setOption('headerToolbar', mobile ? {
                left: 'prev,next',
                center: 'title',
                right: 'today,listMonth,dayGridMonth'
            } : {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            });

            if (mobile && calendar.view.type === 'dayGridMonth') {
                calendar.changeView('listMonth');
            } else if (!mobile && calendar.view.type === 'listMonth') {
                calendar.changeView('dayGridMonth');
            }
        },
        events: function (info, successCallback, failureCallback) {
            $.ajax({
                url: BASE_URL + "substitution/get-substitutions",
                type: "GET",
                dataType: 'json',
                success: function (response) {
                    successCallback(response);
                },
                error: function () {
                    swal("Error", "No se pudieron cargar los eventos", "error");
                }
            });
        },
        eventDataTransform: function (event) {
            if (!event.class) return event;
            const stage = event.stage;
            if (stage === 'ESO') event.className = 'level-eso';
            else if (stage === 'BACH') event.className = 'level-high-school';
            else if (stage === 'PRIM') event.className = 'level-primary';
            else if (stage === 'CFGM') event.className = 'level-midlle-grade';
            else if (stage === 'CFGS') event.className = 'level-higher-grade';
            else if (stage === 'INF') event.className = 'level-childish-grade';
            else event.className = 'level-default';
            
            return event;
        },
        eventClick: function (info) {
            const mobile = window.innerWidth < 768;
            if (mobile) {
                let props = info.event.extendedProps;
                swal({
                    title: "Detalles de la Sustitución",
                    text: `Clase: ${props.class || 'N/A'}\nProfesor Sustituido: ${props.absent_teacher || 'N/A'}\nSustituto: ${props.substitute_teacher || 'N/A'}`,
                    icon: "info",
                    button: "Cerrar"
                });
            }
        },
        eventMouseEnter: function (info) {
            let props = info.event.extendedProps;
            let $popUp = $(`
                <div class="event-tooltip p-3">
                    <div class="mb-2">
                        <small class="text-muted d-block uppercase" style="font-size: 0.7rem; font-weight: bold;">CLASE</small>
                        <h6 class="mb-0 fw-bold text-primary">${props.class}</h6>
                    </div>
                    <hr class="my-2">
                    <div class="small">
                        <div class="mb-2">
                            <strong class="text-dark">Profesor sustituido:</strong>
                            <div class="text-secondary">${props.absent_teacher}</div>
                        </div>
                        <div>
                            <strong class="text-dark">Sustituto:</strong>
                            <div class="text-secondary">${props.substitute_teacher}</div>
                        </div>
                    </div>
                </div>
            `);

            $('body').append($popUp);

            $popUp.css({
                top: (info.jsEvent.pageY + 20) + "px",
                left: (info.jsEvent.pageX + 20) + "px"
            });
        },
        eventMouseLeave: function () {
            $(".event-tooltip").remove();
        }
    });
    calendar.render();
});