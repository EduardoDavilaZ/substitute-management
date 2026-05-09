<?php 
    $events = $events ?? [];
?>

<main class="main">
    <h1 class="text-title">Gestión de eventos</h1>
    <span class="text-muted-custom">Crea y notifica eventos a las clases</span>

    <button class="btn-create right-self my-2 py-1 px-2">
        <i class="bi bi-plus-square"></i> Nuevo evento
    </button>

    <div class="row row-cols-1 row-cols-lg-2">
        <?php foreach($events as $event): ?>
            <div class="col p-2">
                <article class="event-container p-2">
                    <div>
                        <div>
                            <h4 class="event-label"><?= htmlspecialchars($event['title']) ?></h4>

                            <?php if (!empty($event['affected_classes'])): ?>
                                <?php foreach($event['affected_classes'] as $class): ?>
                                    <span class="event-group"><?= htmlspecialchars($class['code']) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="event-group text-muted">Global</span>
                            <?php endif; ?>

                        </div>

                        <div>
                            <button class="btn-edit" data-id="<?= $event['class_id'] ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn-delete" data-id="<?= $event['class_id'] ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="event-details">
                        <?= nl2br(htmlspecialchars($event['description'])) ?>
                    </p>

                    <span class="event-date">
                        <i class="bi bi-calendar2-check"></i> 
                        <?php 
                            $start = date('d-m-Y', strtotime($event['start_date']));
                            $end   = date('d-m-Y', strtotime($event['end_date']));

                            if ($start === $end) {
                                echo $start;
                            } else {
                                echo $start . " <i class='bi bi-arrow-right px-1'></i> " . $end;
                            }
                        ?>
                    </span>
                </article>
            </div>
        <?php endforeach ?>
    </div>
</main>

<?php
    push_css('admin/event_management.css');
    push_js('admin/event_management.js');
?>