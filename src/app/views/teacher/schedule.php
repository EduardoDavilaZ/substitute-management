<?php
    include_once(VIEWS_PATH . '/layouts/teacher/menu.php');

    $periods = $periods ?? [];
    $schedule = $schedule ?? [];
    $days = [
        'L' => 'Lunes',
        'M' => 'Martes',
        'X' => 'Miercoles',
        'J' => 'Jueves',
        'V' => 'Viernes',
    ];

    $scheduleBySlot = [];
    foreach ($schedule as $entry) {
        $scheduleBySlot[$entry['period_id']][$entry['day']] = $entry;
    }
?>

<main class="main teacher-section-page">
    <div class="container-fluid px-0">
        <section class="teacher-panel mx-auto">
            <div class="teacher-panel-header">
                <div class="teacher-panel-icon">
                    <i class="bi bi-calendar-week"></i>
                </div>

                <div>
                    <h1 class="teacher-panel-title">Mi horario</h1>
                    <p class="teacher-panel-subtitle mb-0">
                        Consulta tus clases y horas de guardia semanales.
                    </p>
                </div>
            </div>

            <?php if (empty($periods)): ?>
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <h2>No hay periodos configurados</h2>
                    <p class="mb-0">El horario se mostrara cuando existan periodos lectivos.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive teacher-table-wrap schedule-table-wrap">
                    <table class="table teacher-table schedule-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <?php foreach ($days as $dayName): ?>
                                    <th><?= htmlspecialchars($dayName) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($periods as $period): ?>
                                <tr>
                                    <th class="period-cell">
                                        <strong><?= htmlspecialchars($period['name']) ?></strong>
                                        <span>
                                            <?= substr($period['start_time'], 0, 5) ?>
                                            -
                                            <?= substr($period['end_time'], 0, 5) ?>
                                        </span>
                                    </th>

                                    <?php foreach (array_keys($days) as $day): ?>
                                        <?php $slot = $scheduleBySlot[$period['id']][$day] ?? null; ?>

                                        <td>
                                            <?php if ($slot === null): ?>
                                                <span class="free-slot">Libre</span>
                                            <?php elseif ($slot['class_code'] === null): ?>
                                                <div class="schedule-slot guard-slot">
                                                    <i class="bi bi-shield-check"></i>
                                                    <strong>Guardia</strong>
                                                </div>
                                            <?php else: ?>
                                                <div class="schedule-slot class-slot">
                                                    <strong><?= htmlspecialchars($slot['class_code']) ?></strong>
                                                    <span><?= htmlspecialchars($slot['class_name']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php
    push_css('teacher/teacher_sections.css');
?>
