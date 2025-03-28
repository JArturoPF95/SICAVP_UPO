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

$users_message = '';
$users_icon = '';

$sqlTemp = "SELECT EMP.ID_NOM ID_NOM, CONCAT(SUBSTRING(EMP.GOVERNMENT_ID,9,2),SUBSTRING(EMP.GOVERNMENT_ID,7,2),SUBSTRING(EMP.GOVERNMENT_ID,5,2)) GOVERNMENT_ID, 
    IF( (SELECT DISTINCT EM2.SUPERVISOR_ID FROM employed EM2 WHERE EM2.SUPERVISOR_ID = EMP.ID_NOM) IS NULL, '1', '2')  ACCESS_LEVEL,
    NOM_SESSION, IF(STATUS = 'A', 0, 1) SEPARATION_FLAG FROM employed EMP";
$resultTemp = $mysqli -> query($sqlTemp);
if ($resultTemp->num_rows > 0) {
    while ($rowTemp = $resultTemp->fetch_assoc()) {

        $user = $rowTemp['ID_NOM'];
        $passtemp = $rowTemp['GOVERNMENT_ID'];
        $session = $rowTemp['NOM_SESSION'];
        $access = $rowTemp['ACCESS_LEVEL'];
        $separation = $rowTemp['SEPARATION_FLAG'];

        //echo $user . ' ' . $passtemp . ' ' . $session . ' ' . $access  . ' ' . $separation . '<br>';


        $mysqli->begin_transaction();

        try {
            $sqlValUser = "SELECT * FROM users WHERE USER = '$user'";
            $resultValUser = $mysqli->query($sqlValUser);
        
            if ($resultValUser->num_rows == 0) {
                $sqlInsertUsers = "INSERT INTO users (USER, PASS_TEMP, PASSWORD, NOM_SESSION, PAYROLL, ACCESS_LEVEL, SEPARATION_FLAG) 
                VALUES ('$user','$passtemp','','$session',1,'$access','$separation');";
                
                if ($mysqli->query($sqlInsertUsers) === true) {
                    // Usuario insertado correctamente
                    //$users_message = 'Usuario ' . $user . ' creado correctamente';
                    $users_message = 'Usuarios creado correctamente';
                    $users_icon = 'success';
                } else {
                    // Error en el insert
                    throw new Exception("Error en Insert: " . $mysqli->error);
                }
            }
        
            // Confirmamos la transacción
            $mysqli->commit();
        
        } catch (Exception $e) {
            // Si ocurre un error, cancela la transacción
            $mysqli->rollback();
            $users_message = $e->getMessage();
            $users_icon = 'error';
        }
        
        // Mensajes de éxito o error
        //echo $users_message;
        


    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creación de Usuarios</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Creación de Usuarios",
            text: "<?php echo $users_message; ?>",
            icon: "<?php echo $users_icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../admin_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>