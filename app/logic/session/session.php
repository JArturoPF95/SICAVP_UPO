<?php

require '../conn.php';

//Obtenemos IP
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}


if (!empty($_POST)) {
    $usuario = mysqli_real_escape_string($mysqli, $_POST['usuario']);
    $password = mysqli_real_escape_string($mysqli, $_POST['password']);
    $error = '';

    //Obtenemos la información del usuario
    $sql_user = "SELECT
            DISTINCT
            USR.*, 
            IF(USR.PAYROLL = 1 AND USR.ACCESS_LEVEL != '3', 
                CONCAT(SUBSTRING(EMP.GOVERNMENT_ID,9,2),SUBSTRING(EMP.GOVERNMENT_ID,7,2),SUBSTRING(EMP.GOVERNMENT_ID,5,2)),
                IF(USR.PAYROLL = 2 AND USR.ACCESS_LEVEL = 4, ASH.PERSON_CODE_ID, USR.PASS_TEMP)) TEMP,
            IF(USR.PAYROLL = 1 AND USR.ACCESS_LEVEL != '3',
                CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX),
                IF(USR.PAYROLL = 2 AND USR.ACCESS_LEVEL = 4, 
                    CONCAT(ASH.NAME,' ',ASH.LAST_NAME,' ',ASH.LAST_NAME_PREFIX),
                    IF(USR.ACCESS_LEVEL = 3, CONCAT('ADMINISTRADOR ',USR.USER),
                    CONCAT('SUPERVISOR DOCENTE ',USR.USER)
                    ))) USER_NAME
        FROM users USR
        LEFT OUTER JOIN employed EMP ON EMP.ID_NOM = USR.USER
        LEFT OUTER JOIN academic_schedules ASH ON ASH.PERSON_CODE_ID = USR.USER
        WHERE USR.USER = '$usuario'";

    $result_user = $mysqli->query($sql_user);
    $rows = $result_user->num_rows;

    if ($rows > 0) {

        $row = $result_user->fetch_assoc();
        if ($row['ACCESS_LEVEL'] == '1' OR $row['ACCESS_LEVEL'] == '2' OR $row['ACCESS_LEVEL'] == '4') {
            $temp = $row['TEMP'];
        } else {
            $temp = 'ABCD1234';
        }
        $pass_temp = $row['PASS_TEMP'];
        $pass = $row['PASSWORD'];

        //Validamos la IP desde donde ingresa (Si se solicita no permitiría acceso desde una ubicación no registrada en catálogo)
        /*$sqlValIp = "SELECT * FROM code_ip WHERE IP = '$ip'";
        $resultIP = $mysqli -> query($sqlValIp);
        if ($resultIP -> num_rows > 0) {*/

        if ($row['SEPARATION_FLAG'] != 1) { //Validamos una posible baja            

            if ( $password === $pass_temp && $pass === '' ) {  //Valida si tiene la contraseña temporal

                $user = $row['USER'];
                echo '<script type="text/javascript">window.location.href="update_password.php?u_12345=' . $user . '"</script>';

                //echo $user.' '.$password.' - '.$pass.' - '.$pass_temp;

            } elseif (password_verify($password, $pass)) {   //Validamos la contraseña si ya la modificó

                session_start();
                $_SESSION['usuario'] = $row['USER'];
                $_SESSION['access_lev'] = $row['ACCESS_LEVEL'];
                $_SESSION['user_name'] = $row['USER_NAME'];
                $_SESSION['payroll'] = $row['PAYROLL'];
                $_SESSION['session'] = $row['NOM_SESSION'];
                header("location: ../../index.php");
            } else {

                $error = 1;
                header("Location:../../../index.php?error=$error");
                exit();
            } //cierra if de validación de accesos

        } else {
            $error = 3;
            header("Location:../../../index.php?error=$error");
            exit();
        }

        /*} else {
            $error = 4;
            header("Location:../../../index.php?error=$error");
            exit();
        } //Termina if de ubicación*/
    } else {

        $error = 2;
        header("Location:../../../index.php?error=$error");
        exit();
    } //Cierra if de query

} else {
    header("Location:../../../");
    exit();
} //Cierra el if del empty
