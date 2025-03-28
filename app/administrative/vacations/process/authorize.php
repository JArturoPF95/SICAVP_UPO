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

$message_authorization = '';
$icon_vacation = '';
$idReq = $_POST['vacationId'];
$authorization = $_POST['authorization'];
$comment = $_POST['comment'];

//echo $idReq . ' ' . $authorization . ' ' . $comment;

$sql_authorizeDays = "UPDATE vacation_request SET AUTHORIZATION_FLAG = '$authorization', COMMENTS = '$comment', AUTHORIZED_BY = '$user_active', AUTHORIZED_DATE = NOW() WHERE requestId = '$idReq'";
if ($mysqli->query($sql_authorizeDays) === true) {
    if ($authorization == 1) {
        $message_authorization = 'Vacaciones Autorizadas con éxito.';
        $icon_authorization = 'success';
    } else {
        $message_authorization = 'Vacaciones Rechazadas con éxito.';
        $icon_authorization = 'success';
    }
} else {
    $message_authorization = 'No se pudo actualizar registro \n Favor de intentar nuevamente';
    $icon_authorization = 'error';
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
                title: "Autorización",
                text: "<?php echo $message_authorization; ?>",
                icon: "<?php echo $icon_authorization; ?>",
                button: "Volver",
            }).then(function() {
                window.location = "../authorizations.php?id=<?php echo $user_active ?>";
            });
        </script>
    </div>
</body>

</html>