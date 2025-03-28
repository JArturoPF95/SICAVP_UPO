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

$year = date('Y');
$today = date('Y-m-d');
$message = '';
$icon = '';
$vacation = '-';

$vacationsTipe = $_GET['id'];

if ($vacationsTipe == 'SS') {
    $vacation = 'Semana Santa';
} else {
    $vacation = 'Navidad - Fin de Año';
}

$sqlValDate = "SELECT  ADDDATE(REQUEST_DATE, INTERVAL 15 DAY) REQUEST_DATE FROM vacation_request WHERE COMMENTS = '$vacation' AND REQUEST_TERM = '$year'";
$resultValDate = $mysqli -> query($sqlValDate);
if($resultValDate -> num_rows > 0){
    while ($rowValDays = $resultValDate -> fetch_assoc()) {
        $requestDate = $rowValDays['REQUEST_DATE'];

        if ($requestDate <= $today) {
            $message = 'Ya no se encuentra en tiempo de eliminar este periodo vacacional';
            $icon = 'error';
        } else {
            $sqlDeleteVacations = "DELETE FROM vacation_request WHERE COMMENTS = '$vacation' AND REQUEST_TERM = '$year'";
            if ($mysqli -> query($sqlDeleteVacations) === true) {
                $message = 'Vacaciones Eliminadas Correctamente';
                $icon = 'success';
            } else {
                $message = 'Error eliminando Vacaciones. Intente nuevamente';
                $icon = 'error';
            }
        }

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
            title: "Eliminar <?php echo $vacation ?>",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../vacations.php?id=<?php echo $user_active ?>";
        });
    </script>
</body>

</html>