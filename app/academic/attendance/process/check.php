<?php

require_once '../../../logic/conn.php';
date_default_timezone_set('America/Mexico_City');

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$payrollPeriodID = '';

$message_check = '';
$incidence = '';
$delays = 0;

$getTime = date('H:i:s');
$today = date('Y-m-d');
$code_day = date('w'); //Obtenemos la clave del día (0-6)

$idClass = $_POST['id_class'];
$session = $_POST['session'];
$degree = $_POST['degree'];
$program = $_POST['program'];
$curriculum = $_POST['curriculum'];
$event = $_POST['event'];
$room = $_POST['room'];
$check = $_POST['check'];
$start = $_POST['c_start'];
$end = $_POST['c_end'];
$summary = $_POST['summary'];

$sql_get_incidence = "SELECT
DISTINCT
PK, CODE_DAY, ASH.ACADEMIC_SESSION, DEGREE, PROGRAM, CURRICULUM, EVENT, START_CLASS, END_CLASS, ROOM, MAX_DELAY_CLASS, MIN_END_CLASS, DELAY_CLASS, MAX_BEFORE_CLASS
FROM academic_schedules ASH
INNER JOIN academic_calendar CAL ON CAL.ACADEMIC_YEAR = ASH.ACADEMIC_YEAR AND CAL.ACADEMIC_TERM = ASH.ACADEMIC_TERM
WHERE ASH.PK = '$idClass' AND CAL.START_DATE <= '$today' AND CAL.END_DATE >= '$today'";
$result_get_incidence = $mysqli->query($sql_get_incidence);
if ($result_get_incidence->num_rows > 0) {
    while ($row_get_incidence = $result_get_incidence->fetch_assoc()) {
        $incidence_time_pk = $row_get_incidence['PK'];
        $incidence_time_start = $row_get_incidence['START_CLASS'];
        $incidence_time_end = $row_get_incidence['END_CLASS'];
        $incidence_time_absence = $row_get_incidence['MAX_DELAY_CLASS'];
        $incidence_time_before = $row_get_incidence['MIN_END_CLASS'];
        $incidence_time_room = $row_get_incidence['ROOM'];
        $incidence_time_event = $row_get_incidence['EVENT'];
        $incidence_time_curriculum = $row_get_incidence['CURRICULUM'];
        $incidence_time_program = $row_get_incidence['PROGRAM'];
        $incidence_time_degree = $row_get_incidence['DEGREE'];
        $incidence_time_delay = $row_get_incidence['DELAY_CLASS'];
        $incidence_time_before = $row_get_incidence['MAX_BEFORE_CLASS'];

        if ($check == 1) {
            if ($getTime <= $incidence_time_delay && $getTime >= $incidence_time_before) {
                $incidence = 7;
            } elseif ($getTime > $incidence_time_delay && $getTime <= $incidence_time_absence) {
                require_once 'query.php';
                //Validamos los retardos y faltas por retardos de la quincena en cuestión
                $sql_countDelays;
                $result_countDelays = $mysqli->query($sql_countDelays);
                if ($result_countDelays->num_rows > 0) {
                    while ($row_countDelay = $result_countDelays->fetch_assoc()) {
                        $delay_incidence = $row_countDelay['TINC'];

                        $delays++;

                        if ((($delays + 1) % 3) == 0) {
                            $incidence = 2;
                            //echo $check . 'Aplica Falta por Retardos ' . $getTime . '<br>';
                        } else {
                            $incidence = 8;
                            //echo $check . 'Aplica Retardo ' . $getTime . '<br>';
                        }
                    }
                    //Si está en horario de retardo y no tiene retardos antes le pone el primero
                } else {
                    $incidence = 8;
                }
            } elseif ($getTime > $incidence_time_absence) {
                $incidence = 1;
            }
        } else {
            $incidence = '0';
        }

        $sql_insert_check = "INSERT INTO academic_attendance (SCHEDULE_ID, ACADEMIC_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, SESSION, DEGREE, PROGRAM, 
CURRICULUM, ROOM_ID, EVENT_ID, START_CLASS, END_CLASS, TINC, IN_OUT, CLASS_SUMMARY) 
VALUES ('$incidence_time_pk', '$user_active','$code_day','$today','$getTime','$session','$degree','$program','$curriculum','$room',
'$event', '$start', '$end', '$incidence','$check', '$summary')";
        if ($mysqli->query($sql_insert_check) === true) {
            if ($check == 1) {
                $message_check = 'Clase Registrada con éxito';
                $icon_check = 'success';
            } else {
                $message_check = 'Salida Registrada con éxito';
                $icon_check = 'success';
            }
        } else {
            $message_check = 'No se pudo Registrar';
            $icon_check = 'error';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">

</head>

<body>
    <script type="text/javascript">
        swal({
            title: "Registro",
            text: "<?php echo $message_check; ?>",
            icon: "<?php echo $icon_check ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../../last_today.php?id=<?php echo $user_active ?>";
        });
    </script>
</body>

</html>