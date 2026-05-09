document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('substitution-calendar');

    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        weekends: false,
        aspectRatio: 3.7,
        contentHeight: 'auto',
        locale: 'es',
        timeZone: 'Europe/Madrid',
        themeSystem: 'bootstrap5',
        headerToolbar: {
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
            else event.className = 'level-default';
            
            return event;
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