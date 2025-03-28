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

$description = '';
$payroll = '';
$message = '';
$icon = '';

$payroll = $_POST['payroll'];
$description = $_POST['name'];

$sqlInsertAccess = "INSERT INTO code_accesslevels (PAYROLL, LEVEL_DESCRIPTION, CREATED_BY, CREATED_DATE) VALUES ('$payroll','$description','$user_active', NOW())";
if ($mysqli->query($sqlInsertAccess) === true) {
    $title = 'Nivel de Acceso';
    $message = 'Nivel de Acceso Registrado Correctamente';
    $icon = 'success';
} else {
    $title = 'Nivel de Acceso';
    $message = 'Error Creando Nivel de Acceso';
    $icon = 'error';
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Típos de Nómina</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "<?php echo $title ?>",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../payroll.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>