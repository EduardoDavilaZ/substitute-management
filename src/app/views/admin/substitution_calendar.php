<main class="main">
    <h1 class="text-title text-center">Calendario de sustituciones</h1>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-2">
                <div class="calendar-legend h-50 mt-5">
                    <h6 class="fw-bold mb-4 text-primary-dark small uppercase letter-spacing">Leyenda</h6>
                    <div class="legend-item mb-3">
                        <span class="dot" style="background: #f39c12;"></span> PRIMARIA
                    </div>
                    <div class="legend-item mb-3">
                        <span class="dot" style="background: var(--primary);"></span> ESO
                    </div>
                    <div class="legend-item mb-3">
                        <span class="dot" style="background: var(--success);"></span> BACHILLERATO
                    </div>
                    <div class="legend-item mb-3">
                        <span class="dot" style="background: #8e44ad;"></span> GRADO MEDIO
                    </div>
                    <div class="legend-item mb-3">
                        <span class="dot" style="background: #b1242b;"></span> GRADO SUPERIOR
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card border-0 shadow-sm overflow-hidden mt-5" style="border-radius: 20px;">
                    <div id="substitution-calendar"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
    push_css('admin/substitution_calendar.css');
    push_js('admin/substitution_calendar.js');
?>