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



if (isset($_POST['schedule_name'])) {

    $schedule = $_POST['schedule_name'];

} else {

    $schedule = '';

}

if (isset($_POST['schedule_daytrip'])) {

    $scheduleDaytrip = $_POST['schedule_daytrip'];

} else {

    $scheduleDaytrip = '';

}



if (isset($_POST['schedule'])) {



    //Insertamos el horario en la tabla code_schedule

    $sqlInsertSchedule = "INSERT INTO code_schedule (DAYTRIP, SCHEDULE, OFICIAL_SCHEDULE, MODIFIED_BY, MODIFIED_DATE) VALUES ('$schedule','$scheduleDaytrip','N','$user_active',NOW());";

    if ($mysqli->query($sqlInsertSchedule)) {



        //Validamos último regitro insertado para obtener el código

        $getCodeSchedule = "SELECT CODE_NOM FROM code_schedule ORDER BY CODE_NOM DESC LIMIT 1";

        $resultCode = $mysqli->query($getCodeSchedule);

        if ($resultCode->num_rows > 0) {

            while ($rowCode = $resultCode->fetch_assoc()) {

                $codeNomSch = $rowCode['CODE_NOM'];

            }

        }



        //Insertamos el horario por días en admin_schedules

        foreach ($_POST['schedule'] as $index => $newSchedule) {

            $newDay = $newSchedule['day'] ?? '';

            $newStart = $newSchedule['start_time'] ?? '';

            $newEnd = $newSchedule['end_time'] ?? '';

            $newBreak = $newSchedule['break_time'] ?? '';

            

            $sqlInsert = "INSERT INTO admin_schedules (CODE_DAY, CODE_SCHEDULE, TIME_START, OUT_TIME, OFICIAL_SCHEDULE, BREAK_TIME, MODIFIED_BY, MODIFIED_DATE)

            VALUES ('$newDay', '$codeNomSch', '$newStart', '$newEnd', 'N', '$newBreak', '$user_active', NOW());";

            //echo $sqlInsert . '<br>';

            if ($mysqli->query($sqlInsert)) {

                $delete = "DELETE FROM admin_schedules WHERE TIME_START = '00:00:00'";

                if ($mysqli->query($delete)) {

                    $message = 'Nuevo Horario creado con éxito';

                    $icon = 'success';

                }

            } else {

                $message = 'Error creando de nuevo horario';

                $icon = 'error';

            }

        } //Cerramos foreach que inserta en admin_schedules



    } //Cerramos el if de insertar en code_schedule



} else {

    $message = 'Error enviando datos de nuevo horario';

    $icon = 'error';

}



?>



<!DOCTYPE html>

<html lang="es">



<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IPs</title>

    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>

    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">

</head>



<body>



    <script type="text/javascript">

        swal({

            title: "Nuevo Horario",

            text: "<?php echo $message; ?>",

            icon: "<?php echo $icon ?>",

            button: "Volver",

        }).then(function() {

            window.location = "../schedules.php?id=<?php echo $user_active ?>";

        });

    </script>



</body>



</html>