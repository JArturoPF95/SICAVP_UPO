<?php

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

$idNom = $_POST['idnom'];
$sesion = $_POST['sesion'];
$access = $_POST['access'];
$passTemp = 'ABCD1234';

$userIcon = '';
$userMessage = '';

try{

    $insertUser = "INSERT INTO users (USER, PASS_TEMP, PASSWORD, NOM_SESSION, PAYROLL, ACCESS_LEVEL, SEPARATION_FLAG) 
    VALUES ('$idNom', '$passTemp', '', '$sesion', 1, '$access', 0)";
    if ($mysqli->query($insertUser) === TRUE) {
        $userIcon = 'success';
        $userMessage = 'Usuario Creado con Éxito';
    } else {
        $userIcon = 'error';
        $userMessage = 'Error Creando Usuario';
    }
} catch (mysqli_sql_exception $ex) {
    $userIcon = 'warning';
    $userMessage = 'Error Creando Usuario. Intentar de nuevo';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Alta de Usuario",
            text: "<?php echo $userMessage; ?>",
            icon: "<?php echo $userIcon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../admin_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>