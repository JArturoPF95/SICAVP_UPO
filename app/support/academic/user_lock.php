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

$sqlUserLock = "UPDATE users SET PASSWORD = '7RicN6.i047hW8pOr', PASS_TEMP = '$idUser', SEPARATION_FLAG = '1', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE USER = '$idUser'";
if ($mysqli -> query($sqlUserLock) === TRUE) {
    $return_message = 'Docente Bloqueado con éxito';
    $return_icon = 'success';
} else {
    $return_message = 'Error Bloqueando Usuario';
    $return_icon = 'error';
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <title>Bloqueo</title>
</head>
<body>

    <script type="text/javascript">
        swal({
            title: "Bloquear Usuario",
            text: "<?php echo $return_message; ?>",
            icon: "<?php echo $return_icon ?>",
            button: "Volver",
          }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>
</html>