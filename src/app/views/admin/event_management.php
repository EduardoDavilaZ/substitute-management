<main class="main">
    <h1 class="text-title">Gestión de eventos</h1>
    <span class="text-muted-custom">Crea y notifica eventos a las clases</span>

    <button class="btn-create right-self my-2 py-1 px-2">
        <i class="bi bi-plus-square"></i> Nuevo evento
    </button>

    <div class="row row-cols-1 row-cols-lg-2">
        <div class="col p-2">
            <article class="event-container p-2">
                <div>
                    <div>
                        <h4 class="event-label">Excursión a Londres</h4>
                        <span class="event-group">3° ESO A</span>
                        <span class="badge-status badge-green">Notificado</span>
                    </div>

                    <div>
                        <button class="btn-edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn-delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                
                <p class="event-details">
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ipsam, nulla necessitatibus a autem eos corrupti ipsa veritatis ab, cum libero enim iure? Aut esse consequatur reiciendis nam consectetur voluptas? Expedita.
                </p>

                <span class="event-date">
                    <i class="bi bi-calendar2-check"></i> 11-04-2026
                </span>
            </article>
        </div>


        <div class="col p-2">
            <article class="event-container p-2">
                <div>
                    <div>
                        <h4 class="event-label">Excursión a Malaga</h4>
                        <span class="event-group">3° ESO B</span>
                        <span class="badge-status badge-yellow">Pendiente</span>
                    </div>

                    <div>
                        <button class="btn-edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn-delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                
                <p class="event-details">
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ipsam, nulla necessitatibus a autem eos corrupti ipsa veritatis ab, cum libero enim iure? Aut esse consequatur reiciendis nam consectetur voluptas? Expedita.
                </p>

                <span class="event-date">
                    <i class="bi bi-calendar2-check"></i> 11-04-2026
                </span>
            </article>
        </div>
    </div>
</main>

<?php
    push_css('admin/event_management.css');
    push_js('admin/event_management.js');
?>