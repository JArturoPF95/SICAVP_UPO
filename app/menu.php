<?php

//urls de menús
$urlMenu = [

    "ultimo_regsitro" => "administrative/today.php?id=$user_active",
    "ultima_clase" => "academic/today.php?id=$user_active",

    //Asistencias
    "asistencias" => "administrative/attendance/records.php?id=$user_active",
    "miHorario" => "administrative/attendance/schedule.php?id=$user_active",
    "asignarHorario" => "administrative/attendance/assign_schedules.php?id=$user_active",
    "historialClases" => "academic/attendance/records.php?id=$user_active",
    "misClases" => "academic/attendance/schedule.php?id=$user_active",

    //Vacaciones
    "solicitarVacaciones" => "administrative/vacations/request_days.php?id=$user_active",
    "autorizarVacaciones" => "administrative/vacations/authorizations.php?id=$user_active",

    //Reportes
    "justificarRetardos" => "administrative/reports/justify_delays.php?id=$user_active",
    "periodoNomina" => "administrative/reports/payroll_period.php?id=$user_active",
    "justificarDocentes" => "academic/reports/events.php?id=$user_active",
    "clasesPeriodo" => "academic/reports/payroll_period.php?id=$user_active",

    //Mantenimientio
    "catalogos" => "support/lists.php?id=$user_active",
    "usuarios" => "support/admin_users.php?id=$user_active",
    "docentes" => "support/academic_users.php?id=$user_active",
    "registrarAnticipadas" => "support/anticipated_record.php?id=$user_active",
    "vacacionesAnticipadas" => "support/anticipated_vacations.php?id=$user_active"

];



//Mpodulos SideNav
$attendance_administrative = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseAtte" role="button" aria-expanded="false" aria-controls="collapseAtte">
<i class="bi bi-calendar-day-fill"></i> &nbsp; Asistencias
</a>
</p>
<div class="collapse" id="collapseAtte">
<div class="list-group list-group-flush">
<a href="?opcion=asistencias" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Historial</b></a>
<a href="?opcion=miHorario" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Horario</b></a>
</div>
</div>
';

$attendance_academic = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseAtteS" role="button" aria-expanded="false" aria-controls="collapseAtteS">
<i class="bi bi-calendar-day-fill"></i> &nbsp; Clases Docente
</a>
</p>
<div class="collapse" id="collapseAtteS">
<div class="list-group list-group-flush">
<a href="?opcion=historialClases" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Historial de Clases</b></a>
<a href="?opcion=misClases" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Horario de Clases</b></a>
</div>
</div>
';

$vacations = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseVacations" role="button" aria-expanded="false" aria-controls="collapseVacations">
<i class="bi bi-luggage-fill"></i> &nbsp Vacaciones
</a>
</p>
<div class="collapse" id="collapseVacations">
<div class="list-group list-group-flush">
<a href="?opcion=solicitarVacaciones" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Solicitar Vacaciones</b></a>
</div>
</div>
';

$vacations_supervisor = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseVacationsSuper" role="button" aria-expanded="false" aria-controls="collapseVacationsSuper">
<i class="bi bi-luggage-fill"></i> &nbsp Vacaciones
</a>
</p>
<div class="collapse" id="collapseVacationsSuper">
<div class="list-group list-group-flush">
<a href="?opcion=solicitarVacaciones" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Solicitar Vacaciones</b></a>
<a href="?opcion=autorizarVacaciones" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Aprobar Solicitudes</b></a>
</div>
</div>
';

$reports_administrative = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" href="?opcion=periodoNomina" role="button" aria-expanded="false" >
<i class="bi bi-file-earmark-spreadsheet-fill"></i> &nbsp; Reporte Nómina
</a>
</p>
';

$justify_administrative = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" href="?opcion=justificarRetardos" role="button" aria-expanded="false">
<i class="bi bi-journal-check"></i> &nbsp; Justificaciones
</a>
</p>
';

$reports_academic = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" href="?opcion=clasesPeriodo" role="button" aria-expanded="false">
<i class="bi bi-file-earmark-spreadsheet-fill"></i> &nbsp; Reporte Docente
</a>
</p>
';

$justify_academic = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" href="?opcion=justificarDocentes" role="button" aria-expanded="false">
<i class="bi bi-journal-check"></i> &nbsp; Justificaciones
</a>
</p>
';

$support = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseSupport" role="button" aria-expanded="false" aria-controls="collapseSupport">
<i class="bi bi-pc-display"></i> &nbsp; Administración
</a>
</p>
<div class="collapse" id="collapseSupport">
<div class="list-group list-group-flush">
<a href="?opcion=catalogos" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Catálogos</b></a>
<a href="?opcion=usuarios" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Administrativos</b></a>
<!--a href="?opcion=docentes" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Docentes</b></a-->
</div>
</div>
';

$supportAnticipated = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseVacationAnt" role="button" aria-expanded="false" aria-controls="collapseVacationAnt">
<i class="bi bi-luggage-fill"></i> &nbsp; Vacaciones Anticipadas
</a>
</p>
<div class="collapse" id="collapseVacationAnt">
<div class="list-group list-group-flush">
<a href="?opcion=registrarAnticipadas" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Captura Vacaciones Anticipadas</b></a>
<a href="?opcion=vacacionesAnticipadas" type="button" class="list-group-item list-group-item-action list-group-item-primary"><b>Vacaciones Anticipadas Registradas</b></a>
</div>
</div>
';

$schedules_assign = '
<p class="d-inline-flex gap-1">
<a class="btn btn-primary" href="?opcion=asignarHorario" role="button" aria-expanded="false">
<i class="bi bi-clock-fill"></i> &nbsp; Asignar Horarios
</a>
</p>
';

//Sidebars por nivel de acceso
$sidebar_suport = '
<ul class="nav flex-column">
<li class="nav-item">
' . $support . '   
</li>
<li class="nav-item">
' . $supportAnticipated . '   
</li>
</ul>';

$sidebar_super_admin = '
<ul class="nav flex-column">
<li class="nav-item">
' . $attendance_administrative . '
</li>
<li class="nav-item">
' . $vacations_supervisor . '   
</li>
<li class="nav-item">
' . $justify_administrative . '
</li>
<li class="nav-item">
' . $reports_administrative . '   
</li>
<li class="nav-item">
' . $schedules_assign . '   
</li>
</ul>';

$sidebar_super_school = '
<ul class="nav flex-column">
<li class="nav-item">
' . $reports_academic . '   
</li>
<li class="nav-item">
' . $justify_academic . '   
</li>
</ul>';

$sidebar_emp_admin = '
<ul class="nav flex-column">
<li class="nav-item">
' . $attendance_administrative . '
</li>
<li class="nav-item">
' . $vacations . '   
</li>
</ul>';

$sidebar_emp_school = '
<ul class="nav flex-column">
<li class="nav-item">
' . $attendance_academic . '
</li>
</ul>';

//Validación de Sideav por tipo de usuario y rol

switch ($user_access) {
    case '1':
        $sidebar = $sidebar_emp_admin;
        break;
    case '2':
        $sidebar = $sidebar_super_admin;
        break;
    case '3':
        $sidebar = $sidebar_suport;
        break;
    case '4':
        $sidebar = $sidebar_emp_school;
        break;
    case '5':
        $sidebar = $sidebar_super_school;
        break;
    default:
        $sidebar = '';
        break;
}

