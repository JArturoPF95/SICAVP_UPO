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

$today = date('Y-m-d');
$day = date('w');
$time = date('H:i:s');

$id_class = $_POST['class_id'];
$teacher = $_POST['id_teacher'];
$summary = $_POST['summary'];

$substitute_message = '';
$substitute_icon = '';

$sql_getClass = "SELECT DISTINCT
    ACS.ACADEMIC_SESSION, ACS.DEGREE, ACS.SECTION, ACS.PROGRAM,
    ACS.PROGRAM, ACS.CURRICULUM, ACS.CURRICULUM, ACS.BUILDING, ACS.ROOM, 
    ACS.CODE_DAY, ACS.GENERAL_ED, ACS.EVENT, ACS.START_CLASS, ACS.END_CLASS
FROM academic_schedules ACS
INNER JOIN academic_calendar ACL ON ACL.ACADEMIC_YEAR = ACS.ACADEMIC_YEAR AND ACL.ACADEMIC_TERM = ACS.ACADEMIC_TERM AND ACL.ACADEMIC_SESSION = ACS.ACADEMIC_SESSION
    WHERE ACS.PK = '$id_class'";
$result_getClass = $mysqli -> query($sql_getClass);
if ($result_getClass -> num_rows > 0) {
    while ($rowGetClass = $result_getClass -> fetch_assoc()) {
        $session = $rowGetClass['ACADEMIC_SESSION'];
        $degree = $rowGetClass['DEGREE'];
        $program = $rowGetClass['PROGRAM'];
        $curriculum = $rowGetClass['CURRICULUM'];
        $room = $rowGetClass['ROOM'];
        $event = $rowGetClass['EVENT'];
        $startC = $rowGetClass['START_CLASS'];
        $endC = $rowGetClass['END_CLASS'];
    }
}

$sql_insertSubstitution = "INSERT INTO academic_attendance(SCHEDULE_ID, ACADEMIC_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, SESSION, DEGREE, PROGRAM, CURRICULUM, ROOM_ID, EVENT_ID, START_CLASS, END_CLASS, TINC, IN_OUT, CLASS_SUMMARY, JUSTIFY) 
    VALUES ('$id_class', '$teacher', '$day', '$today', '$time', '$session', '$degree', '$program', '$curriculum', '$room','$event','$startC', '$endC', 11, 1, '$summary', 'S')";
if ($mysqli -> query($sql_insertSubstitution) === true) {
    $substitute_icon = 'success';
    $substitute_message = 'Profesor sustituto colocado con éxito';
} else {
    $substitute_icon = 'error';
    $substitute_message = 'Error colocando profesor sustituto';
}

?>

<!DOCTYPE html>
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
            title: "Profesor Sustituto",
            text: "<?php echo $substitute_message; ?>",
            icon: "<?php echo $substitute_icon ?>",
            button: "Volver",
          }).then(function() {
            window.location = "../../teachers.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>
</html> 