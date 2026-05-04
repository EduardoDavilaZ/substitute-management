<main class="main">
    <h1 class="text-title">Panel de administración</h1>
    <span class="text-muted-custom">Semana del 20-04-2026 - Lunes</span>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">
        <div class="col p-2">
            <div class="row row-cols-2 g-0 kpi-card">
                <div class="col  p-2">
                    <h5 class="kpi-label">Profesores</h5>
                    <h3 class="kpi-value">49</h3>
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
                    <h3 class="kpi-value">4</h3>
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
                    <h3 class="kpi-value">67</h3>
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
                    <h3 class="kpi-value">2</h3>
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


    <div class="row">
        <div class="col col-md-8 p-2">
            <div class="kpi-card p-2">
                <span class="kpi-label">Sustituciones Semanales</span>
                <div>
                    <canvas id="weekly-substitutions-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="col col-md-4 p-2">
            <div class="kpi-card p-2">
                <span class="kpi-label">Guardias pendientes</span>
                
                <div class="row row-cols-1" id="pending-substitutions">
                    <div class="col p-2">
                        <div class="pending-group p-2">
                            <div class="px-2">
                                <span class="day">Lunes a 5ta hora</span>
                            </div>
                            <div class="icon-alert center">
                                <i class="bi bi-exclamation-circle fs-5"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col p-2">
                        <div class="pending-group p-2">
                            <div class="px-2">
                                <span class="day">Lunes a 6ta hora</span>
                            </div>
                            <div class="icon-alert center">
                                <i class="bi bi-exclamation-circle fs-5"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col p-2">
                        <div class="pending-group p-2">
                            <div class="px-2">
                                <span class="day">Martes a 1ra hora</span>
                            </div>
                            <div class="icon-alert center">
                                <i class="bi bi-exclamation-circle fs-5"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col p-2">
                        <div class="pending-group p-2">
                            <div class="px-2">
                                <span class="day">Martes a 4ta hora</span>
                            </div>
                            <div class="icon-alert center">
                                <i class="bi bi-exclamation-circle fs-5"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col p-2">
                        <div class="pending-group p-2">
                            <div class="px-2">
                                <span class="day">Viernes a 2da hora</span>
                            </div>
                            <div class="icon-alert center">
                                <i class="bi bi-exclamation-circle fs-5"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col p-2">
                        <div class="pending-group p-2">
                            <div class="px-2">
                                <span class="day">Viernes a 3ra hora</span>
                            </div>
                            <div class="icon-alert center">
                                <i class="bi bi-exclamation-circle fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 p-2">
        <div class="kpi-card p-2">
            <span class="kpi-label">Ausencias de la semana</span>
            <div class="row row-cols-3 row-cols-md-6 row-cols-lg-12">
                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>

                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>
                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>

                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>

                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>

                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>

                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>

                <div class="col p-2 center ">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>

                <div class="col p-2 center">
                    <div class="icon-user kpi-icon center">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
    push_css('admin/home.css');
    push_js('admin/home.js');
?>