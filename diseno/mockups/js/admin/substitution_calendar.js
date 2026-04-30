document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('substitution-calendar');

    if (!calendarEl) return;

    const mockEvents = [
        {
            "title": "Sustitución: 1º ESO A",
            "start": "2026-05-18",
            "absent_teacher": "María García López",
            "substitute_teacher": "Ricardo Darín",
            "class_name": "1º ESO A",
            "status": "CONFIRMADO"
        },
        {
            "title": "Sustitución: 2º BACH B",
            "start": "2026-05-18",
            "absent_teacher": "Julián Besteiro",
            "substitute_teacher": "Pendiente de asignar",
            "class_name": "2º BACH B",
            "status": "PENDIENTE"
        },
        {
            "title": "Sustitución: SM1A",
            "start": "2026-05-19",
            "absent_teacher": "Ana Belén",
            "substitute_teacher": "Luis Tosar",
            "class_name": "Sistemas Microinformáticos (1º)",
            "status": "CONFIRMADO"
        },
        {
            "title": "Sustitución: 4º ESO C",
            "start": "2026-05-19",
            "absent_teacher": "Carlos Alcántara",
            "substitute_teacher": "Inma Cuesta",
            "class_name": "4º ESO C",
            "status": "CANCELADO"
        },
        {
            "title": "Sustitución: 3º PRIM",
            "start": "2026-05-20",
            "absent_teacher": "Lucía Jiménez",
            "substitute_teacher": "Alberto San Juan",
            "class_name": "3º Primaria",
            "status": "CONFIRMADO"
        },
        {
            "title": "Sustitución: ASIR 2",
            "start": "2026-05-20",
            "absent_teacher": "Roberto Álamo",
            "substitute_teacher": "Pendiente de asignar",
            "class_name": "Admin. Sistemas Informáticos (2º)",
            "status": "PENDIENTE"
        },
        {
            "title": "Sustitución: 1º BACH A",
            "start": "2026-05-21",
            "absent_teacher": "Elena Anaya",
            "substitute_teacher": "Javier Cámara",
            "class_name": "1º BACH A",
            "status": "CONFIRMADO"
        },
        {
            "title": "Sustitución: 2º ESO D",
            "start": "2026-05-21",
            "absent_teacher": "Antonio de la Torre",
            "substitute_teacher": "Bárbara Lennie",
            "class_name": "2º ESO D",
            "status": "CONFIRMADO"
        },
        {
            "title": "Sustitución: DAW 1",
            "start": "2026-05-22",
            "absent_teacher": "Raúl Arévalo",
            "substitute_teacher": "Pendiente de asignar",
            "class_name": "Desarrollo Aplicaciones Web (1º)",
            "status": "PENDIENTE"
        },
        {
            "title": "Sustitución: 6º PRIM B",
            "start": "2026-05-22",
            "absent_teacher": "Carmen Machi",
            "substitute_teacher": "Paco León",
            "class_name": "6º Primaria B",
            "status": "CONFIRMADO"
        }
    ];

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        weekends: false,
        aspectRatio: 3.7,
        contentHeight: 'auto',
        locale: 'es',
        timeZone: 'UTC',
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
        // events: function(info, successCallback, failureCallback) {
        //     $.ajax({
        //         url: "../ ObtainCalendar",
        //         type: "GET",
        //         dataType: 'json',
        //         success: function (response) {
        //             successCallback(response);
        //         },
        //         error: function () {
        //             swal("Error", "No se pudieron cargar los eventos", "error");
        //         }
        //     });
        // },
        events: mockEvents,
        eventDataTransform: function(event) {
            const name = event.class_name.toUpperCase();
            if (name.includes('ESO')) event.className = 'level-eso';
            else if (name.includes('BACH')) event.className = 'level-high-school';
            else if (name.includes('PRIM')) event.className = 'level-primary';
            else if (name.includes('DAW') || name.includes('ASIR') || name.includes('SISTEMAS') || name.includes('SM')) event.className = 'level-vocational';
            else event.className = 'level-default';
            return event;
        },
        eventMouseEnter: function (info) {
            let props = info.event.extendedProps;
            let $popUp = $(`
                <div class="event-tooltip p-3">
                    <div class="mb-2">
                        <small class="text-muted d-block uppercase" style="font-size: 0.7rem; font-weight: bold;">CLASE</small>
                        <h6 class="mb-0 fw-bold text-primary">${props.class_name}</h6>
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