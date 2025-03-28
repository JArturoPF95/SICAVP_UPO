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

$idUser = $_GET['id'];
$reset_message = '';
$reset_icon = '';

//Obtiene la contraseña temporal para su nuevo acceso
$sqlGetPassTemp = "SELECT 
    ACCESS_LEVEL, CONCAT(SUBSTRING(GOVERNMENT_ID,9,2),SUBSTRING(GOVERNMENT_ID,7,2),SUBSTRING(GOVERNMENT_ID,5,2)) BDAY
FROM users 
LEFT OUTER JOIN employed ON users.USER = employed.ID_NOM
WHERE USER = '$idUser'";
$resultPassTemp = $mysqli->query($sqlGetPassTemp);
if ($resultPassTemp->num_rows > 0) {
    while ($rowPT = $resultPassTemp->fetch_assoc()) {
        if ($rowPT['ACCESS_LEVEL'] == '1' OR $rowPT['ACCESS_LEVEL'] == '2') {
            $passNew = $rowPT['BDAY'];
        } elseif ($rowPT['ACCESS_LEVEL'] == '3' OR $rowPT['ACCESS_LEVEL'] == '5') {
            $passNew = 'ABCD1234';
        }
    }
}

$sql_userReset = "UPDATE users SET PASSWORD = '', PASS_TEMP = '$passNew', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE USER = '$idUser' AND SEPARATION_FLAG = '0'";
if ($mysqli->query($sql_userReset) === TRUE) {
    $reset_message = 'Contraseña reseteada con éxito' .$passNew;
    $reset_icon = 'success';
} else {
    $reset_message = 'Error reseteando contraseña';
    $reset_icon = 'error';
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
            title: "Contraseña Reseteada",
            text: "<?php echo $reset_message; ?>",
            icon: "<?php echo $reset_icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../admin_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>