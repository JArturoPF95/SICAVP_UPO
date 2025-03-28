<?php

require_once '../../../logic/conn.php';



date_default_timezone_set('America/Mexico_City');



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



$getTime = date('H:i:s');

$codeDay = date('w');

$incidence = '';



if (isset($_POST['justDate'])) {

    $getDate = $_POST['justDate'];

} else {

    $getDate = date('Y-m-d');

}

$justify_comment = $_POST['comment'];



//echo $getDate;



$timeClock = date('d/m/Y', strtotime($getDate)).' '. date('H:i:s', strtotime($getTime));



//echo $getDate . ' ' . $user_active . ' ' . $justify_comment;



$sql_valInsertJustify = "SELECT AttendanceId

FROM admin_attendance 

WHERE ATTENDANCE_DATE = '$getDate' AND NOM_ID = '$user_active' AND IN_OUT = '1' ORDER BY ATTENDANCE_DATE ASC LIMIT 1";

$result_valInsertJustify = $mysqli->query($sql_valInsertJustify);

if ($result_valInsertJustify->num_rows > 0) {

    while ($rowAtt = $result_valInsertJustify -> fetch_assoc()) {

        $pk = $rowAtt['AttendanceId'];

        $sql_updateJustify = "UPDATE admin_attendance SET COMMENTS = '$justify_comment', JUSTIFY = 'P' WHERE AttendanceId = '$pk'";

        if ($mysqli->query($sql_updateJustify) === true) {

            $title = 'Justificación Enviada';

            $message = 'Se solicitó justificación con éxito';

            //$message = $sql_updateJustify;

            $icon = 'success';

        } else {

            $title = '¡¡Error!!';

            $message = 'Se produjo un error solicitando Justificación \n Favor de intentar nuevamente';

            $icon = 'error';

        }

    }

} else {

    $sql_insertJustify = "INSERT INTO admin_attendance 

(NOM_ID, IN_OUT, ATTENDANCE_DATE, ATTENDANCE_TIME, COMMENTS, JUSTIFY) 

VALUES ('$user_active','1','$getDate','$getTime','$justify_comment','P');";

    if ($mysqli->query($sql_insertJustify) === true) {

        $title = 'Justificación Enviada';

        $message = 'Se solicitó justificación con éxito';

        $icon = 'success';

    } else {

        $title = '¡¡Error!!';

        $message = 'Se produjo un error solicitando Justificación \n Favor de intentar nuevamente';

        $icon = 'error';

    }

}







?>



<!DOCTYPE html>

<html lang="es">



<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Asistencias del día</title>

    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>

    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">

</head>



<body>

    <script type="text/javascript">

        swal({

            title: "<?php echo $title; ?>",

            text: "<?php echo $message; ?>",

            icon: "<?php echo $icon ?>",

            button: "Volver",

        }).then(function() {

            window.location = "../../last_today.php?id=<?php echo $user_active ?>";

        });

    </script>



</body>



</html>