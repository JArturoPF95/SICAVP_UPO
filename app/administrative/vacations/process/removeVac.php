<?php

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

require '../../../logic/conn.php';

$idVac = $_GET['id'];
$message = '';
$icon = '';

$sqlDelete = "DELETE FROM vacation_request WHERE requestId = '$idVac'";
if ($mysqli -> query($sqlDelete) === true) {
    $message = 'Solicitud Eliminada';
    $icon = 'success';
} else {
    $message = 'Error Eliminando Solicitud';
    $icon = 'error';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solcitar Vacaciones</title>
    <script src="../../../../static/js/popper.min.js"></script>
    <script src="../../../../static/js/bootstrap.min.js"></script>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../../static/css/styles/tables.css">
</head>

<body>
    <div class="container">
        <script type="text/javascript">
            swal({
                title: "Eliminar Solicitud",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon; ?>",
                button: "Volver",
            }).then(function() {
                window.location = "../request_days.php?id=<?php echo $user_active ?>";
            });
        </script>
    </div>
</body>

</html>