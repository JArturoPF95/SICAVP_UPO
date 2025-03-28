<?php



require_once '../../../logic/conn.php';



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



$message_val = '';

$icon_check = '';

$justify = '';

$title = 'Justificación Validada';

$date = '';



if (isset($_POST['attendance'])) {



    foreach ($_POST['attendance'] as $pk => $attendance) {

        $attendancePk = $attendance['pk'] ?? '';
        $attendancePayroll = $attendance['ppd'] ?? '';

        $justified = $attendance['justify'] ?? '';



        if ($justified === 'Y') {

            $justify = '07';

            $sql_valJustify = "UPDATE admin_attendance SET JUSTIFY = '$justified', JUSTIFIED_BY = '$user_active', JUSTIFIED_DATE = NOW(), TINC = '$justify' WHERE AttendanceId = '$attendancePk'";

            if ($mysqli -> query($sql_valJustify)) {

                $message_val = 'Solicitudes validadas';

                $icon_check = 'success';

            } else {

                $message_val = 'Error Justificando \n Favor de intentar de nuevo';

                $icon_check = 'error';

            }

        } else {

            $message_val = 'Solicitudes validadas';

            $icon_check = 'success';

        }

        

        

    }

}









$url = "../justify_delays.php?id=$user_active&pp=$attendancePayroll";



?>



<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>

    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">

    <title>Asistencias del día</title>

</head>

<body>



    <script type="text/javascript">

        swal({

            title: "<?php echo $title; ?>",

            text: "<?php echo $message_val; ?>",

            icon: "<?php echo $icon_check ?>",

            button: "Volver",

          }).then(function() {

            window.location = "<?php echo $url ?>";

        });

    </script>



</body>

</html>