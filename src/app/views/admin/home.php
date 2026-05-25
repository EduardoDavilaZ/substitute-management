<main class="main">
    <h1 class="text-title">Panel de administración</h1>
    <span id="dateToday" class="text-muted-custom"></span> 

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">
        <div class="col p-2">
            <div class="row row-cols-2 g-0 kpi-card">
                <div class="col  p-2">
                    <h5 class="kpi-label">Profesores</h5>
                    <h3 id="totalTeachers" class="kpi-value"></h3>
                    <small class="text-muted-custom">Registrados activos</small>
                </div>
                <div class="col center">
                    <div class="icon-people kpi-icon center">
                        <i class="bi bi-people fs-2 icon-thin"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col  p-2">
            <div class=" row row-cols-2 g-0 kpi-card">
                <div class="col  p-2">
                    <h5 class="kpi-label">Guardias de hoy</h5>
                    <h3 id="totalSubstitutions" class="kpi-value"></h3> 
                    <small class="text-muted-custom">Registrados activos</small>
                </div>
                <div class="col center">
                    <div class="icon-shield kpi-icon center">
                        <i class="bi bi-shield fs-2 icon-thin"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col  p-2">
            <div class=" row row-cols-2 g-0 kpi-card">
                <div class="col  p-2">
                    <h5 class="kpi-label">Clases activas</h5>
                    <h3 id="totalClasses"class="kpi-value"></h3> 
                    <small class="text-muted-custom">Registrados activos</small>
                </div>
                <div class="col center">
                    <div class="icon-book kpi-icon center">
                        <i class="bi bi-book fs-2 icon-thin"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col p-2">
            <div class="row row-cols-2 g-0 kpi-card">
                <div class="col  p-2">
                    <h5 class="kpi-label">Ausencias</h5>
                    <h3 id="totalAbsences" class="kpi-value"></h3>
                    <small class="text-muted-custom">Registrados activos</small>
                </div>
                <div class="col center">
                    <div class="icon-calendar kpi-icon center">
                        <i class="bi bi-calendar fs-2 icon-thin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!------------------------------------------------ Sustituciones Semanales ---------------------------------------------------->
    <div class="row">
        <div class="col col-md-8 p-2">
            <div class="kpi-card p-2">
                <span class="section-title">Sustituciones Semanales</span>
                <div>
                    <canvas id="weekly-substitutions-chart"></canvas>
                </div>
            </div>
        </div>
<!------------------------------------------------ Guardias pendientes ---------------------------------------------------->
        <div class="col col-md-4 p-2">
            <div class="kpi-card p-2">
                <span class="section-title">Guardias pendientes</span>
                
                <div class="row row-cols-1" id="pending-substitutions">
                </div>
            </div>
        </div>
    </div>
    <!------------------------------------------------ Ausencias de la semana ---------------------------------------------------->
    <div class="row row-cols-1 p-2">
        <div class="kpi-card p-2">
            <span class="section-title">Ausencias de la semana</span>
            <div id="teachers_absences" class="row row-cols-3 row-cols-md-6 row-cols-lg-12">
            </div>
        </div>
    </div>
</main>

<?php
    push_css('admin/home.css');
    push_js('admin/home.js');
?>