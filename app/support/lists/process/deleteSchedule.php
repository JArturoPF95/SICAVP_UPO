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

$schedule = $_GET['id'];

$sqlDeleteDetail = "DELETE FROM admin_schedules WHERE CODE_SCHEDULE = '$schedule'";
$sqlDeleteSchedule = "DELETE FROM code_schedule WHERE CODE_NOM = '$schedule'";

if ($mysqli -> query($sqlDeleteDetail) === true && $mysqli -> query($sqlDeleteSchedule) === true) {
    $message = 'Horario eliminado correctamente';
    $icon = 'success';
} else {
    $message = 'Error eliminando horario';
    $icon = 'error';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPs</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Eliminar Horario",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../schedules.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>