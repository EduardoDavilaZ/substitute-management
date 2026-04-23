<main class="main">
    <h1 class="text-title">Cuadrante semanal</h1>
    <?php
    
    $res_select = [
        ['day' => 'L', 'period_id' => 1, 'full_name' => 'Pedro López', 'substitution_counter' => 6],
        ['day' => 'L', 'period_id' => 1, 'full_name' => 'Ana Martínez', 'substitution_counter' => 15],
        ['day' => 'M', 'period_id' => 2, 'full_name' => 'Juan Rivas', 'substitution_counter' => 8],
        ['day' => 'X', 'period_id' => 1, 'full_name' => 'Marta Sánchez', 'substitution_counter' => 12],
        ['day' => 'X', 'period_id' => 1, 'full_name' => 'Roberto Cano', 'substitution_counter' => 9],
        ['day' => 'V', 'period_id' => 5, 'full_name' => 'Beatriz Peña', 'substitution_counter' => 5],
    ];

    $guardias_indexadas = [];
    foreach ($res_select as $fila) {
        $key = $fila['day'] . '-' . $fila['period_id'];
        $guardias_indexadas[$key][] = [
            'nombre' => $fila['full_name'],
            'conteo' => $fila['substitution_counter']
        ];
    }
    $periodos_info = [
        1 => '1ª hora (8:15-9:10)',
        2 => '2ª hora (9:10-10:05)',
        3 => '3ª hora (10:05-11:00)',
        4 => 'Recreo (11:00-11:30)',
        5 => '4ª hora (11:30-12:25)',
        6 => '5ª hora (12:25-13:20)',
        7 => '6ª hora (13:20-14:15)'
    ];

    $dias_config = ['Lunes' => 'L', 'Martes' => 'M', 'Miércoles' => 'X', 'Jueves' => 'J', 'Viernes' => 'V'];
    ?>

    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 table-guardias">
                    <thead class="table-light">
                        <tr>
                            <th class="p-3 text-muted">HORA</th>
                            <?php foreach ($dias_config as $nombre => $letra): ?>
                                <th class="p-3 text-center"><?php echo $nombre; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($periodos_info as $p_id => $p_texto): ?>
                            <tr <?php echo (strpos($p_texto, 'Recreo') !== false) ? 'class="bg-light-subtle"' : ''; ?>>
                                <td class="p-3 hora-col">
                                    <?php echo $p_texto; ?>
                                </td>
                                <?php foreach ($dias_config as $nombre => $letra): 
                                    $key = $letra . '-' . $p_id;
                                    $profes = isset($guardias_indexadas[$key]) ? $guardias_indexadas[$key] : [];
                                ?>
                                <td class="p-2" style="min-width: 200px;">
                                    <div class="d-flex flex-column gap-1">
                                        
                                        <?php if (isset($profes[0])): ?>
                                            <div class="teacher-slot slot-blue">
                                                <i class="bi bi-person-fill me-1"></i>
                                                <span class="text-truncate" style="max-width: 90px;"><?php echo $profes[0]['nombre']; ?></span>
                                                <span class="badge-count bg-blue-badge"><?php echo $profes[0]['conteo']; ?></span>
                                                <div class="ms-1 d-flex gap-1">
                                                    <button class="btn-slot-action"><i class="bi bi-pencil-square"></i></button>
                                                    <button class="btn-slot-action text-danger"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn btn-add"><i class="bi bi-plus"></i> Añadir P1</button>
                                        <?php endif; ?>

                                        <?php if (isset($profes[1])): ?>
                                            <div class="teacher-slot slot-emerald">
                                                <i class="bi bi-person-fill me-1"></i>
                                                <span class="text-truncate" style="max-width: 90px;"><?php echo $profes[1]['nombre']; ?></span>
                                                <span class="badge-count bg-emerald-badge"><?php echo $profes[1]['conteo']; ?></span>
                                                <div class="ms-1 d-flex gap-1">
                                                    <button class="btn-slot-action"><i class="bi bi-pencil-square"></i></button>
                                                    <button class="btn-slot-action text-danger"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn btn-add"><i class="bi bi-plus"></i> Añadir P2</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>