<?php

/** Para que el usuario de mantenimiento cargue masivamente las vacaciones asignadas por la empresa
 * Semana Santa o Navidad
 */
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

$antiquity_years = 0;
$today = date('Y-m-d');
$term = date('Y');
$vacations_message = '';
$vacations_icon = '';
$takenDays = 0;
$lawDays = 0;


if (isset($_POST['startDate'])) {
    $start_date = $_POST['startDate'];
} else {
    $start_date = '';
}

if (isset($_POST['endDate'])) {
    $end_date = $_POST['endDate'];
} else {
    $end_date = '';
}

if (isset($_POST['comment'])) {
    $comment = $_POST['comment'];
} else {
    $comment = '';
}

$sqlGetPrevDays = "SELECT DISTINCT PREV_DAYS FROM code_vacation";
$resultPrevDays = $mysqli->query($sqlGetPrevDays);
if ($resultPrevDays->num_rows > 0) {
    while ($rowPrevDays = $resultPrevDays->fetch_assoc()) {
        $prevDays = $rowPrevDays['PREV_DAYS'];

        $daysPrevRequest = (strtotime($start_date) - strtotime($today)) / (60 * 60 * 24);

        if ($daysPrevRequest >= $prevDays) {
            $sqlAntiquity = "SELECT ID_NOM, ANTIQUITY, SUPERVISOR_ID FROM employed WHERE STATUS = 'A'";
            $resultAntiquity = $mysqli->query($sqlAntiquity);
            if ($resultAntiquity->num_rows > 0) {
                while ($rowAntiquity = $resultAntiquity->fetch_assoc()) {
                    $idnom = $rowAntiquity['ID_NOM'];
                    $antiquity = $rowAntiquity['ANTIQUITY'];
                    $supervisor = $rowAntiquity['SUPERVISOR_ID'];

                    //Obtenemos la antiguedad
                    $antiquity_years = (strtotime($today) - strtotime($antiquity)) / (60 * 60 * 24 * 365);


                    if ($antiquity_years >= 1) {
                        require 'query.php';
                        $sql_getCalendarDays; //Mandamos llamar la consulta que valida los días que tomará
                        $resultDays = $mysqli->query($sql_getCalendarDays);
                        if ($resultDays->num_rows > 0) {
                            while ($rowDays = $resultDays->fetch_assoc()) {
                                $first_day = $rowDays['START_DATE'];
                                $last_day = $rowDays['END_DATE'];
                                $days = $rowDays['DAYS_REQ'];

                                //Prueba

                                //if ($days > 6) { 

                                //Validamos días de ley
                                $sqlValLawDays = "SELECT DAYS_BY_LAW FROM code_vacation WHERE $antiquity_years BETWEEN MIN_YEARS AND MAX_YEARS";
                                $resultValLawDays = $mysqli->query($sqlValLawDays);
                                if ($resultValLawDays->num_rows > 0) {
                                    while ($rowValLawDays = $resultValLawDays->fetch_assoc()) {
                                        $lawDays = $rowValLawDays['DAYS_BY_LAW'];

                                        //Validamos los días ya ocupados
                                        $sqlValLeftDays = "SELECT ID_NOM, SUM(DAYS_REQUESTED) DAYS FROM vacation_request
                                            WHERE REQUEST_TERM = '2024' AND ID_NOM = '$idnom'";
                                        $resultValLeftDays = $mysqli->query($sqlValLeftDays);
                                        if ($resultValLeftDays->num_rows > 0) {
                                            while ($rowValLeftDays = $resultValLeftDays->fetch_assoc()) {
                                                $takenDays = $rowValLeftDays['DAYS'];

                                                $leftDays = $lawDays - $takenDays;

                                                //echo $idnom . ' - ' . $antiquity_years . ' - ' . $lawDays . ' - ' . $takenDays . ' - ' . $days . ' - ' . $leftDays . '<br>';

                                                if ($days <= $leftDays) {

                                                    //Termina prueba

                                                //} else {

                                                    //Inserta los días
                                                    $sqlInsertDays = "INSERT INTO vacation_request(ID_NOM, REQUEST_TERM, REQUEST_DATE, START_DATE, END_DATE, DAYS_REQUESTED, AUTHORIZATION_FLAG, IMMEDIATE_BOSS, COMMENTS, CREATED_BY, CREATED_DATE) 
VALUES ('$idnom','$term','$today','$first_day','$last_day','$days','P','$supervisor','$comment', '$user_active',NOW())";
                                                    if ($mysqli->query($sqlInsertDays) === true) {
                                                        $vacations_message = 'Vacaciones enviadas con éxito';
                                                        $vacations_icon = 'success';
                                                    } else {
                                                        $vacations_message = 'Error enviando vacaciones';
                                                        $vacations_icon = 'error';
                                                    } //Cierra if de insert

                                                } else {

                                                    $vacations_message = 'Favor de Enviar Menor Cantidad de Días';
                                                    $vacations_icon = 'warning';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } //Cierra If si cumple antiguedad

                }
            }
        } else {
            $vacations_message = 'Las vacaciones deben solicitarse con ' . $prevDays . ' de anticipación';
            $vacations_icon = 'warning';
        } //Cierra if de días de anticipación
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Días de Descanso</title>
    <script src="../../../../static/js/popper.min.js"></script>
    <script src="../../../../static/js/bootstrap.min.js"></script>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <script type="text/javascript">
        swal({
            title: "Vacaciones <?php echo $comment ?>",
            text: "<?php echo $vacations_message; ?>",
            icon: "<?php echo $vacations_icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../vacations.php?id=<?php echo $user_active ?>&opcion=2";
        });
    </script>
</body>

</html>