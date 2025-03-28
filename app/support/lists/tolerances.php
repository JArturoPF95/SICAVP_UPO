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

$schedule = '';
$t_minIn = '';
$t_delay = '';
$t_absence = '';
$t_lock = '';
$t_minOut = '';
$send = '0';

$message = '';
$icon = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $schedule = $_POST['schedule'];
    $t_minIn = $_POST['t_minIn'];
    $t_delay = $_POST['t_delay'];
    $t_absence = $_POST['t_absence'];
    $t_lock = $_POST['t_lock'];
    $t_minOut = $_POST['t_minOut'];
    $send = $_POST['send'];

    $sqlUpdateTolerances = "UPDATE code_schedule SET 
T_MIN_TIME_START = '$t_minIn', T_DELAY_TIME_START = '$t_delay', 
T_AUSENCE_TIME = '$t_absence', T_LOCK_TIME = '$t_lock', 
T_MIN_TIME_OUT = '$t_minOut' WHERE CODE_NOM = '$schedule'";
    $sqlUpdateAdminTolerances = "UPDATE admin_schedules SET 
MIN_TIME_START = DATE_ADD(TIME_START, INTERVAL -'$t_minIn' MINUTE), 
DELAY_TIME_START = DATE_ADD(TIME_START, INTERVAL '$t_delay' MINUTE), 
AUSENCE_TIME = DATE_ADD(TIME_START, INTERVAL '$t_absence' MINUTE), 
LOCK_IN_TIME = DATE_ADD(TIME_START, INTERVAL '$t_lock' MINUTE),
MIN_TIME_OUT = DATE_ADD(OUT_TIME, INTERVAL -'$t_minOut' MINUTE), 
MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE CODE_SCHEDULE  = '$schedule'";
    if ($mysqli->query($sqlUpdateTolerances) === true && $mysqli->query($sqlUpdateAdminTolerances) === true) {
        $message = 'Tiempo de Tolerancia Cargado con éxito';
        $icon = 'success';
    } else {
        $message = 'Error Cargando Tiempo de Tolerancia';
        $icon = 'error';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tolerancias</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
</head>

<body>
    <!--Header con buscador de usuario y opciónes para alta-->
    <header>
        <div class="px-3 py-1 border-bottom">
            <div class="px-3 mb-3">
                <div class="container d-flex flex-wrap justify-content-end">

                    <div class="text-end">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <h4 class="my-3">Tolerancias</h4>
    <div class="col-md-11 col-lg-11 d-flex justify-content-center py-2 px-5">
        <form class="row g-3" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <input hidden type="text" name="send" value="1">
            <div class="col-md-6">
                <label for="inputState" class="form-label">Horario</label>
                <select id="inputState" class="form-select" name="schedule">
                    <?php
                    $sqlValSchedules = "SELECT * FROM code_schedule";
                    $resultValSchedules = $mysqli->query($sqlValSchedules);
                    if ($resultValSchedules->num_rows > 0) {
                        while ($rowValSchedules = $resultValSchedules->fetch_assoc()) {
                    ?>
                            <option value="<?php echo $rowValSchedules['CODE_NOM'] ?>"><?php echo $rowValSchedules['DAYTRIP'] . ' -- ' . $rowValSchedules['SCHEDULE'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="inputEmail4" class="form-label">Puede checar antes:</label>
                <input name="t_minIn" type="text" class="form-control" id="inputEmail4" required>
            </div>
            <div class="col-md-3">
                <label for="inputPassword4" class="form-label">Minutos de Tolerancia</label>
                <input name="t_delay" type="text" class="form-control" id="inputPassword4" required>
            </div>
            <div class="col-md-4">
                <label for="inputEmail4" class="form-label">Minutos con Retardo</label>
                <input name="t_absence" type="text" class="form-control" id="inputEmail4" required>
            </div>
            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Minutos con Falta</label>
                <input name="t_lock" type="text" class="form-control" id="inputPassword4" required>
            </div>
            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Puede salir antes:</label>
                <input name="t_minOut" type="text" class="form-control" id="inputPassword4" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>

    <?php
    if ($send == '1') {
    ?>
        <script type="text/javascript">
            swal({
                title: "Generar Tiempos de Tolerancia",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "tolerances.php?id=<?php echo $user_active ?>";
            });
        </script>
    <?php
    }
    ?>
</body>

</html>