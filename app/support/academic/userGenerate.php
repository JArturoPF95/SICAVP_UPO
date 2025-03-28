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

$message = '';
$icon = '';

//Obtenemos la información de los empleados
$sqlGetUsers = "SELECT 
ASH.PERSON_CODE_ID,
CSA.CODE_NOM
FROM academic_schedules ASH
INNER JOIN code_sesion_academic CSA ON CSA.CODE_VALUE_KEY = ASH.ACADEMIC_SESSION";
$resultGetUsers = $mysqli->query($sqlGetUsers);
if ($resultGetUsers->num_rows > 0) {
    while ($rowGetUsers = $resultGetUsers->fetch_assoc()) {
        $user = $rowGetUsers['PERSON_CODE_ID'];
        $session = $rowGetUsers['CODE_NOM'];

        //Validamos ue aún no tenga usuario
        $sqlValUser = "SELECT * FROM users WHERE USER = '$user'";
        $resultValUser = $mysqli->query($sqlValUser);
        if ($resultValUser->num_rows == 0) {
            //Insertamos
            $sqlInsertUsers = "INSERT INTO users(USER, PASS_TEMP, PASSWORD, NOM_SESSION, PAYROLL, ACCESS_LEVEL, SEPARATION_FLAG, CREATED_BY, CREATED_DATE) 
VALUES ('$user','$user','',$session, '2', '4', '0', '$user_active', NOW());";
            if ($mysqli->query($sqlInsertUsers) === true) {
                $message = 'Usuarios Docentes creados correctamente';
                $icon = 'success';
            } else {
                $message = 'Error creando usuarios Docentes';
                $icon = 'error';

                echo $sqlInsertUsers . '<br>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Docentes</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<!--body style="background-color: #010440;"-->
<body>

    <script type="text/javascript">
        swal({
            title: "Carga de Usuarios Docentes",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>