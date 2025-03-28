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

$codeValueKey = $_POST['codePwC'];
$codeNomSesion = $_POST['idNom2001'];

$message = '';
$icon = '';

$sqlUpdateSesion = "UPDATE code_sesion_academic SET CODE_NOM = '$codeNomSesion' WHERE CODE_VALUE_KEY = '$codeValueKey'";
if ($mysqli->query($sqlUpdateSesion) === true) {
    $message = 'Clave de Nom2001 Actualizada con éxito';
    $icon = 'success';
} else {
    $message = 'Error Actualizando Clave de Nom2001';
    $icon = 'error';
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Actualizar Campus",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>