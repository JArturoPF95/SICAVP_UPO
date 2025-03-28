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

$payrollPeriodID = '';

$sqlScheduleWeek = "SELECT * FROM assigned_schedule WHERE NOW() BETWEEN START_DATE AND END_DATE AND ID_NOM = '$user_active'";
        $resultWeek = $mysqli -> query($sqlScheduleWeek);
        if ($resultWeek -> num_rows > 0) {
            while ($rowWeek = $resultWeek -> fetch_assoc()) {
                $selectedWeek = $rowWeek['ASSIGNMENT_ID'];
            }
        } else {
            $selectedWeek = '0';
        }
require 'process/query_attendance.php';

$alert = '
<div class="alert alert-warning my-3 d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>No cuenta con horario asignado esta semana</div>
</div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assignedScheduleID'])) {
        $selectedWeek = $_POST['assignedScheduleID'];
    } else {
        $selectedWeek = 0;
    }
    

}


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
    <div class="container-fluid" style="width: 85%; height: 100%">
        <h4 class="mb-3">Horario</h4>

        <div class="row">
        <div class="col">
                <form class="row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <input hidden type="text" name="flag" id="" value="1">
                    <div class="col-sm-4">
                        <label class="visually-hidden" for="specificSizeSelect">Semana</label>
                        <select class="form-select" id="specificSizeSelect" name="assignedScheduleID">
                            <option selected>Semana</option>
                            <?php
                            $sqlAssignedSchedule = "SELECT * FROM assigned_schedule WHERE END_DATE >= now() AND ID_NOM = '$user_active'";
                            $resultAssignedSchedule = $mysqli->query($sqlAssignedSchedule);
                            if ($resultAssignedSchedule->num_rows > 0) {
                                while ($rowAssignedSchedule = $resultAssignedSchedule->fetch_assoc()) {
                                    $id = $rowAssignedSchedule['ASSIGNMENT_ID'];
                                    $start_date = $rowAssignedSchedule['START_DATE'];
                                    $end_date = $rowAssignedSchedule['END_DATE'];
                            ?>
                                    <option value="<?php echo $id ?>"><?php echo date('d/m/Y', strtotime($start_date)) . ' al ' . date('d/m/Y', strtotime($end_date)) ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Seleccionar</button>
                </form>
            </div>
        </div>
        <div class="row">
                <?php

                /** Horario */

                $sql_schedule = "SELECT * FROM assigned_schedule ASH
                    LEFT OUTER JOIN admin_schedules ADS ON ADS.CODE_SCHEDULE = ASH.SCHEDULE
                    INNER JOIN code_days DYS ON DYS.CODE_DAY = ADS.CODE_DAY
                    INNER JOIN calendar CAL ON CAL.WEEK = ASH.WEEK AND CAL.YEAR = ASH.YEAR AND ADS.CODE_DAY = CAL.CODE_DAY
                    WHERE ASH.ID_NOM = '$user_active' AND ADS.TIME_START != '00:00:00' AND ASSIGNMENT_ID = '$selectedWeek';";

                $result_schedule = $mysqli->query($sql_schedule);
                if ($result_schedule->num_rows > 0) {
                ?>
                
        <table class="table my-3 table-hover table-bordered">
            <thead>
                <tr class="text-white text-center table-primary">
                    <th scope="col">Día</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Entrada</th>                    
                    <th scope="col">Tiempo <br> de Comida</th>
                    <th scope="col">Salida</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while ($row_schedule = $result_schedule->fetch_assoc()) {
                        $schedule_day = $row_schedule['NAME_DAY'];
                        $schedule_date = date('d/m/Y', strtotime($row_schedule['CALENDAR_DATE']));
                        $schedule_checkIn = substr($row_schedule['TIME_START'], 0, 8);
                        $schedule_end = substr($row_schedule['OUT_TIME'], 0, 8);

                        $hours = floor($row_schedule['BREAK_TIME'] / 60);
                        $minutes = $row_schedule['BREAK_TIME'] % 60;

                        $schedule_break = sprintf("%02d:%02d:%02d", $hours, $minutes, 0);
                ?>
                        <tr class="text-center">
                            <td><?php echo $schedule_day ?></td>
                            <td><?php echo $schedule_date ?></td>
                            <td><?php echo $schedule_checkIn ?></td>
                            <td><?php echo $schedule_break ?></td>
                            <td><?php echo $schedule_end ?></td>
                        </tr>
                <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
                } else {
                    echo $alert;
                }
                ?>
                </div>
    </div>
</body>

</html>