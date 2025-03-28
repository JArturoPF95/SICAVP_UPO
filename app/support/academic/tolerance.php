<?php

require '../../logic/conn.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$message = '';
$icon = '';

$level = $_POST['level'];
$t_min = $_POST['t_minIn'];
$t_delay = $_POST['t_tolerance'];
$t_absence = $_POST['t_delay'];
$t_outMin = $_POST['t_minOut'];

$sqlGetLevel = "SELECT DISTINCT DEGREE, PROGRAM FROM academic_tolerance WHERE CODE_DEGPRO = '$level'";
$resultGetLevel = $mysqli->query($sqlGetLevel);
if ($resultGetLevel->num_rows > 0) {
    while ($rowGetLevel = $resultGetLevel->fetch_assoc()) {
        $degree = $rowGetLevel['DEGREE'];
        $program = $rowGetLevel['PROGRAM'];
    }
}


$sqlTolerance = "UPDATE academic_tolerance SET MIN_TIME = '$t_min', DELAY_CLASS = '$t_delay', MAX_CLASS = '$t_absence', MIN_END = '$t_outMin', MODIFIED_DATE = NOW(), MODIFIED_BY = '$user_active' WHERE CODE_DEGPRO = '$level'";
$sqlUpdateSchedule = "UPDATE academic_schedules SET MAX_BEFORE_CLASS = DATE_ADD(START_CLASS, INTERVAL -'$t_min' MINUTE), DELAY_CLASS = DATE_ADD(START_CLASS, INTERVAL '$t_delay' MINUTE), MAX_DELAY_CLASS = DATE_ADD(START_CLASS, INTERVAL '$t_absence' MINUTE), MIN_END_CLASS = DATE_ADD(END_CLASS, INTERVAL -'$t_outMin' MINUTE), MODIFIED_DATE = NOW(), MODIFIED_BY = '$user_active' WHERE DEGREE = '$degree' AND PROGRAM = '$program'";
if ($mysqli->query($sqlTolerance) === true && $mysqli->query($sqlUpdateSchedule) === true) {
    $message = 'Tolerancias ' . $level . ' Actualizadas con Éxito';
    $icon = 'success';
} else {
    $message = 'Error Actualizando Tolerancias de ' . $level;
    $icon = 'error';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tolerancias</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
</head>

<body>
    <script type="text/javascript">
        swal({
            title: "Generar Tiempos de Tolerancia",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>
</body>

</html>