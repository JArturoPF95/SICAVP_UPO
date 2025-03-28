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

$message = '';
$icon = '';

if(isset($_POST['prevDays'])){
    $prevDays = $_POST['prevDays'];
} else {
    $prevDays = '';
}

$sqlUpdateDays = "UPDATE code_vacation SET PREV_DAYS = '$prevDays'";
if ($mysqli -> query($sqlUpdateDays) === true) {
    $message = 'Mínimo de Días para Solicitar \n Vacaciones Actualizado';
    $icon = 'success';
} else {
    $message = 'No fue posible actualizar información. \n Intentar nuevamente';
    $icon = 'error';
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Días de Descanso</title>
    <script src="../../../../static/js/popper.min.js"></script>
    <script src="../../../../static/js/bootstrap.min.js"></script>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <script type="text/javascript">
        swal({
            title: "Días Previos",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../vacations.php?id=<?php echo $user_active ?>";
        });
    </script>
</body>

</html>