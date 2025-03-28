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

$idIP = $_POST['idIP'];
$ip = $_POST['ip'];
$sesion = $_POST['sesion'];
$message = '';
$icon = '';

//echo $idIP . ' ' . $ip . ' ' . $sesion;

$sqlUdate = "UPDATE code_ip SET IP = '$ip', CODE_SESION = '$sesion', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE ipID = '$idIP'";
if ($mysqli->query($sqlUdate)) {
    $message = 'IP Actualizada con Éxito';
    $icon = 'success';
} else {
    $message = 'Error Actualizando IP';
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
            title: "Catálogo de IPs",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../ip.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>