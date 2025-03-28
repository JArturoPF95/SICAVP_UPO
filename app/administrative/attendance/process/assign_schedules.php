<?php



require '../../../logic/conn.php';

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



if (isset($_POST['assign'])) {



    $action = $_POST['action'];



    foreach ($_POST['assign'] as $index => $assign) {

        $schMonth = $assign['smonth'] ?? '';
        $schEmp = $assign['semp'] ?? '';
        $schYear = $assign['syear'] ?? '';

        $schWeek = $assign['sweek'] ?? '';

        $schempID = $assign['empID'] ?? '';

        $schempNm = $assign['empNm'] ?? '';

        $schsDate = $assign['sDate'] ?? '';

        $scheDate = $assign['eDate'] ?? '';

        $schnSche = $assign['nsche'] ?? '';

        $schnAsID = $assign['assign'] ?? '';



        if ($action == 'insert') { //Crea nuevos registros



            if ($schnAsID == '') {  

                $insert = "INSERT INTO assigned_schedule(ID_NOM, NAME, YEAR, WEEK, START_DATE, END_DATE, SCHEDULE, ASSIGNED_BY, ASSIGNED_DATE) 

                VALUES ('$schempID','$schempNm','$schYear','$schWeek','$schsDate','$scheDate','$schnSche','$user_active',NOW());";



                try {

                    //echo $insert .'<br>';

                    if ($mysqli->query($insert)) {

                        $message = 'Horarios asignados con éxito';

                        $icon = 'success';

                    } else {

                        $message = 'Error asignando horarios';

                        $icon = 'error';

                    }

                } catch (\Throwable $th) {                    

                    $message = 'Ya existe asignación para este mes.';

                    $icon = 'error';

                }

            }

        } else { //Actualiza registros ya existentes



            if ($schnAsID != '') {

                $update = "UPDATE assigned_schedule SET SCHEDULE = '$schnSche' WHERE ASSIGNMENT_ID = '$schnAsID';";

                //echo $update.'<br>';

                if ($mysqli->query($update)) {

                    $message = 'Horarios asignados actualizados con éxito';

                    $icon = 'success';

                } else {

                    $message = 'Error actualizando horarios asignados';

                    $icon = 'error';

                }

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

    <title>Registro de Asistencia</title>

    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>

    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">

</head>



<body>

    <script type="text/javascript">

        swal({

            title: "Asignación de Horarios",

            text: "<?php echo $message; ?>",

            icon: "<?php echo $icon ?>",

            button: "Volver",

        }).then(function() {

            window.location = "../assign_schedules.php?id=<?php echo $user_active ?>&m=<?php echo $schMonth ?>&e=<?php echo $schEmp ?>";

        });

    </script>

</body>



</html>