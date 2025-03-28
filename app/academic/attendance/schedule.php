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

$today = date('Y-m-d');
$payrollPeriodID = '';
require_once 'process/query.php';

$sql_academic_schedules;
$result_academic_schedule = $mysqli->query($sql_academic_schedules);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoario</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">

</head>

<body>
    <h4 class="mb-3">Horario de Clases</h4>
    <div class="container-fluid" style="width: 100%; height: 100%">
        <table class="table table-hover table-bordered">
            <thead>
                <tr class="text-white text-center table-primary">
                    <th scope="col">Materia</th>
                    <th scope="col">Salón</th>
                    <th scope="col">Lunes</th>
                    <th scope="col">Martes</th>
                    <th scope="col">Miércoles</th>
                    <th scope="col">Jueves</th>
                    <th scope="col">Viernes</th>
                    <th scope="col">Sábado</th>
                    <th scope="col">Domingo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_academic_schedule->num_rows > 0) {
                    while ($row_academic_schedule = $result_academic_schedule->fetch_assoc()) {
                        $schedule_event = $row_academic_schedule['EVENT'];
                        $schedule_room = $row_academic_schedule['ROOM'];
                        $schedule_start_1 = $row_academic_schedule['START_MONDAY'];
                        $schedule_start_2 = $row_academic_schedule['START_TUESDAY'];
                        $schedule_start_3 = $row_academic_schedule['START_WEDNESDAY'];
                        $schedule_start_4 = $row_academic_schedule['START_THURSDAY'];
                        $schedule_start_5 = $row_academic_schedule['START_FRIDAY'];
                        $schedule_start_6 = $row_academic_schedule['START_SATURDAY'];
                        $schedule_start_0 = $row_academic_schedule['START_SUNDAY'];
                        $schedule_end_1 = $row_academic_schedule['END_MONDAY'];
                        $schedule_end_2 = $row_academic_schedule['END_TUESDAY'];
                        $schedule_end_3 = $row_academic_schedule['END_WEDNESDAY'];
                        $schedule_end_4 = $row_academic_schedule['END_THURSDAY'];
                        $schedule_end_5 = $row_academic_schedule['END_FRIDAY'];
                        $schedule_end_6 = $row_academic_schedule['END_SATURDAY'];
                        $schedule_end_0 = $row_academic_schedule['END_SUNDAY'];
                ?>
                        <tr class="text-center">
                            <td><?php echo $schedule_event ?></td>
                            <td><?php echo $schedule_room ?></td>
                            <td><?php echo substr($schedule_start_1, 0, 8) . ' - ' . substr($schedule_end_1, 0, 8) ?></td>
                            <td><?php echo substr($schedule_start_2, 0, 8) . ' - ' . substr($schedule_end_2, 0, 8) ?></td>
                            <td><?php echo substr($schedule_start_3, 0, 8) . ' - ' . substr($schedule_end_3, 0, 8) ?></td>
                            <td><?php echo substr($schedule_start_4, 0, 8) . ' - ' . substr($schedule_end_4, 0, 8) ?></td>
                            <td><?php echo substr($schedule_start_5, 0, 8) . ' - ' . substr($schedule_end_5, 0, 8) ?></td>
                            <td><?php echo substr($schedule_start_6, 0, 8) . ' - ' . substr($schedule_end_6, 0, 8) ?></td>
                            <td><?php echo substr($schedule_start_0, 0, 8) . ' - ' . substr($schedule_end_0, 0, 8) ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>