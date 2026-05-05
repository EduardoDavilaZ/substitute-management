<?php 
    $list = $periods['data'] ?? []; 
    $days = ['L', 'M', 'X', 'J', 'V'];
    $schedules = $schedules ?? [];
    
    $dayNames = ['L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes'];

    $guards = [];
    foreach ($schedules as $s) {
        $guards["{$s['day']}-{$s['period_id']}"][] = $s;
    }
?>

<main class="main">
    <h1 class="text-title">Libro de Guardias</h1>

    <table class="schedule-table table-responsive mx-auto w-100">
        <thead>
            <tr>
                <th class="p-2 text-center">HORA</th>
                <?php foreach(['LUNES','MARTES','MIÉRCOLES','JUEVES','VIERNES'] as $d) echo '<th class="p-2 text-center">' . $d . '</th>'; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $p): ?>
            <?php
                $timeRange = substr($p['start_time'], 0, 5) . " - " . substr($p['end_time'], 0, 5);
                $periodText = "{$p['name']} ({$timeRange})";
            ?>
            <tr>
                <td class="period-cell text-center">
                    <strong class="d-block"><?= $p['name'] ?></strong>
                    <span>(<?= $timeRange ?>)</span>
                </td>
                
                <?php foreach ($days as $day): 
                    $key = "$day-{$p['id']}";
                    $teachers = $guards[$key] ?? [];
                    $fullInfo = ($dayNames[$day] ?? $day) . " — " . $periodText;
                ?>

                <td class="schedule-slot p-1">
                    <div class="slot-container p-1">

                        <?php for ($i = 0; $i < 2; $i++): ?>
                        <div class="schedule-slot-wrapper">

                            <?php if (isset($teachers[$i])): 
                                $t = $teachers[$i]; ?>
                                <div class="teacher-badge <?= $i === 0 ? 'slot-primary' : 'slot-success' ?> p-2">
                                    <div class="text-truncate me-2">
                                        <span><?= $t['full_name'] ?></span>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="count-pill"><?= $t['count'] ?></span>
                                        <button class="btn-edit" 
                                            data-id="<?= $t['id'] ?>" 
                                            data-day="<?= $day ?>" 
                                            data-period="<?= $p['id'] ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <button class="btn-delete" data-id="<?= $t['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                            <?php else: ?>
                                <button class="btn-add-slot p-2" 
                                    data-day="<?= $day ?>" 
                                    data-period="<?= $p['id'] ?>">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir profesor <?= $i + 1 ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php endfor; ?>
                    </div>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php
    push_css('admin/substitution_schedule.css');
    push_js('admin/substitution_schedule.js');
?>