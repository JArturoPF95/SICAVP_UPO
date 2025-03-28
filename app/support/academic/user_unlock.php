<?php
date_default_timezone_set('America/Mexico_City');
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

$idUser = $_GET['id'];
$return_message = '';
$return_icon = '';

$sqlUserReturn = "UPDATE users SET SEPARATION_FLAG = '0', PASSWORD = '', PASS_TEMP = '$idUser', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE USER = '$idUser'";
if ($mysqli->query($sqlUserReturn) === TRUE) {
    $return_message = 'Desbloqueo de Docente con éxito';
    $return_icon = 'success';
} else {
    $return_message = 'Error Desbloqueando Docente';
    $return_icon = 'error';
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencias del día</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Desbloqueo de Docente",
            text: "<?php echo $return_message; ?>",
            icon: "<?php echo $return_icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>