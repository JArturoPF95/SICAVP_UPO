<?php

require '../../../logic/conn.php';
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

date_default_timezone_set('America/Mexico_City');
$today = date('Y-m-d');
$codeDay = date('w');
$time = date('H:i:s');

$justify_title = '';
$justify_message = '';
$justify_icon = '';

$classID = $_POST['classID'];
$comment = $_POST['comment'];

$sql_valAttendance = "SELECT ACT.AttendanceId, ACT.TINC FROM academic_attendance ACT
WHERE ACADEMIC_ID = '$user_active' AND ACT.CODE_DAY = '$codeDay' AND ACT.ATTENDANCE_DATE = '$today' 
AND ACT.SCHEDULE_ID = '$classID' AND ACT.IN_OUT = 1;";
$result_valAttendance = $mysqli->query($sql_valAttendance);
if ($result_valAttendance->num_rows > 0) {
    while ($rowValAtt = $result_valAttendance->fetch_assoc()) {
        $aca_attendance_id = $rowValAtt['AttendanceId'];
        $sql_update_justify = "UPDATE academic_attendance SET JUSTIFY = 'P', COMMENT = '$comment' WHERE AttendanceId = '$aca_attendance_id'";
        if ($mysqli->query($sql_update_justify) === true) {
            $justify_title = 'Justificación de retraso';
            $justify_message = 'Justificación Solicitada con éxito';
            $justify_icon = 'success';
        } else {
            $justify_title = 'Justificación de retraso';
            $justify_message = 'Error solicitando justificación. \n Favor de intentar nuevamente.';
            $justify_icon = 'error';
        }
    }
} else {
    $sql_schedule = "SELECT * FROM academic_schedules WHERE PK = '$classID'";
    $result_schedule = $mysqli->query($sql_schedule);
    if ($result_schedule->num_rows > 0) {
        while ($rowSchedule = $result_schedule->fetch_assoc()) {
            $scheduleID = $rowSchedule['PK'];
            $scheduleSesion = $rowSchedule['ACADEMIC_SESSION'];
            $scheduleDegree = $rowSchedule['DEGREE'];
            $scheduleProgram = $rowSchedule['PROGRAM'];
            $scheduleCurriculum = $rowSchedule['CURRICULUM'];
            $scheduleRoom = $rowSchedule['ROOM'];
            $scheduleEvent = $rowSchedule['EVENT'];
            $scheduleStart = $rowSchedule['START_CLASS'];
            $scheduleEnd = $rowSchedule['END_CLASS'];

            $sql_insert_justify = "INSERT INTO academic_attendance 
(SCHEDULE_ID, ACADEMIC_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, SESSION, DEGREE, PROGRAM, CURRICULUM, ROOM_ID, EVENT_ID, START_CLASS, END_CLASS, TINC, IN_OUT, CLASS_SUMMARY, JUSTIFY, COMMENT) 
VALUES ('$scheduleID', '$user_active','$codeDay','$today','$time','$scheduleSesion','$scheduleDegree','$scheduleProgram',
'$scheduleCurriculum','$scheduleRoom','$scheduleEvent','$scheduleStart','$scheduleEnd', 8, 1, '-', 'P','$comment')";
            if ($mysqli->query($sql_insert_justify) === true) {
                $justify_title = 'Justificación de retraso';
                $justify_message = 'Justificación Solicitada con éxito';
                $justify_icon = 'success';
            } else {
                $justify_title = 'Justificación de retraso';
                $justify_message = 'Error solicitando justificación. \n Favor de intentar nuevamente.';
                $justify_icon = 'error';
            }
        }
    } else {
        $justify_title = 'No fue posible Justificar';
        $justify_message = 'No se encontró información de esa clase';
        $justify_icon = 'warning';
    }
}

?>

<DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Asistencias del día</title>
        <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
        <link rel="stylesheet" href="../../../../static/css/bootstrap.css">

    </head>

    <body>

        <script type="text/javascript">
            swal({
                title: "<?php echo $justify_title; ?>",
                text: "<?php echo $justify_message; ?>",
                icon: "<?php echo $justify_icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "../../last_today.php?id=<?php echo $user_active ?>";
            });
        </script>

    </body>

    </html>