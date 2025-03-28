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
$separation_message = '';
$separation_icon = '';
$separationDate = date('Y-m-d');

$sql_employedSeparation = "UPDATE employed SET STATUS = 'I', SEPARATION_DATE = '$separationDate', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE ID_NOM = '$idUser'";
$sql_userSeparation = "UPDATE users SET SEPARATION_FLAG = '1', PASSWORD = 'IV6OHJ.KQixrNhinWCsJbq', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE USER = '$idUser'";
if ($mysqli->query($sql_employedSeparation) === TRUE && $mysqli->query($sql_userSeparation)) {
    $separation_message = 'Usuario Bloqueado con éxito';
    $separation_icon = 'success';
} else {
    $separation_message = 'Error Bloqueando usuario';
    $separation_icon = 'error';
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
            title: "Bloqueo de Usuario",
            text: "<?php echo $separation_message; ?>",
            icon: "<?php echo $separation_icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../admin_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>