<?php

require_once '../../../logic/conn.php';

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

$message_val = '';
$icon_check = '';
$title = 'Justificación Validada';

$justified = $_POST['justify_flag'];
$attendance = $_POST['id'];

echo $attendance . ' ' . $justified;

if ($justified == 'Y') {
    $sql_valJustify = "UPDATE academic_attendance SET JUSTIFY = '$justified', TINC = 7 WHERE AttendanceId = '$attendance'";
    if ($mysqli->query($sql_valJustify)) {
        $message_val = 'Justificado';
        $icon_check = 'success';
    } else {
        $message_val = 'Error Justificando \n Favor de intentar de nuevo';
        $icon_check = 'error';
    }
} else {
    $sql_valJustify = "UPDATE academic_attendance SET JUSTIFY = '$justified' WHERE AttendanceId = '$attendance'";
    if ($mysqli->query($sql_valJustify)) {
        $message_val = 'No Justificado';
        $icon_check = 'warning';
    } else {
        $message_val = 'Error Justificando \n Favor de intentar de nuevo';
        $icon_check = 'error';
    }
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
            title: "<?php echo $title; ?>",
            text: "<?php echo $message_val; ?>",
            icon: "<?php echo $icon_check ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../events.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>