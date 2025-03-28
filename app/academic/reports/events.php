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
    $user_sesion = $_SESSION['session'];
}

$today = '';
$codeDay = '';
$payrollCode  = '';

date_default_timezone_set('America/Mexico_City');
$send_flag = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['program'])) {
        $program = $_POST['program'];
    } else {
        $program = '-';
    }
    $send_flag = $_POST['flagSend'];
    $selectedDay = $_POST['dateSelected'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../../static/img/logo1.jpg" type="image/x-icon">
    <title>Clases</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">

</head>

<body>
    <div class="container-fluid">

        <form class="row g-3 my-1" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <input hidden type="text" name="flagSend" id="" value="1">
            <div class="col-auto">
                <input class="form-control" type="date" name="dateSelected" aria-label="default input example" value="<?php echo date('Y-m-d') ?>">
            </div>
            <div class="col-auto">
                <select class="form-select" aria-label="Default select example" name="program" required>
                    <?php
                    $sqlProgram = "SELECT DISTINCT PROGRAM FROM academic_schedules 
LEFT OUTER JOIN code_sesion_academic ON CODE_VALUE_KEY = ACADEMIC_SESSION WHERE CODE_NOM = '$user_sesion'";
                    $resultProgram = $mysqli->query($sqlProgram);
                    if ($resultProgram->num_rows > 0) {
                        while ($rowProgram = $resultProgram->fetch_assoc()) {
                    ?>
                            <option value="<?php echo $rowProgram['PROGRAM'] ?>"><?php echo $rowProgram['PROGRAM'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Seleccionar</button>
            </div>
        </form>

        <div class="row">

            <?php

            if ($send_flag == 1) {
                require 'process/query_reports.php';
                $sql_teacher_attendance;
                $result_teacherAttendance = $mysqli->query($sql_teacher_attendance);

                if ($result_teacherAttendance->num_rows > 0) {
            ?>
                    <table class="table table-primary table-hover table-bordered table-sm">
                        <thead class="text-white text-center">
                            <tr>
                                <th scope="col">Campus</th>
                                <th scope="col">Docente</th>
                                <th scope="col">Carrera</th>
                                <th scope="col">Aula</th>
                                <th scope="col">Materia</th>
                                <th scope="col">Tema</th>
                                <th scope="col">Estatus</th>
                                <th scope="col">Entrada</th>
                                <th scope="col">Salida</th>
                                <th scope="col">Justificación</th>
                                <th scope="col"></th>

                            </tr>
                        </thead>
                        <tbody class="table-light">
                            <?php
                            while ($rowTeachersAttendance = $result_teacherAttendance->fetch_assoc()) {

                            ?>
                                <tr class="text-center">
                                    <form action="process/update_attendance.php" method="post">
                                        <input hidden type="text" class="form-control" id="validationCustom01" name="id" value="<?php echo $rowTeachersAttendance['AttendanceId'] ?>">
                                        <td><?php echo $rowTeachersAttendance['LONG_DESC'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['NAME'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['CURRICULUM'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['ROOM_ID'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['EVENT_ID'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['CLASS_SUMMARY'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['INCIDENCE'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['IN_TIME'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['OUT_TIME'] ?></td>
                                        <td><?php echo $rowTeachersAttendance['COMMENT'] ?></td>
                                        <td>
                                            <?php
                                            if ($rowTeachersAttendance['JUSTIFY'] == 'P') {
                                            ?>
                                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                                    <button type="submit" name="justify_flag" value="Y" class="btn btn-success btn-sm">Sí</button>
                                                    &nbsp;
                                                    <button type="submit" name="justify_flag" value="N" class="btn btn-danger btn-sm">No</button>
                                                </div>
                                            <?php
                                            } else {
                                                if ($rowTeachersAttendance['JUSTIFY'] == 'Y') {
                                                    echo 'Autorizado';
                                                } elseif ($rowTeachersAttendance['JUSTIFY'] == 'N') {
                                                    echo 'Rechazado';
                                                } elseif ($rowTeachersAttendance['JUSTIFY'] == 'S') {
                                                    echo 'Suplente';
                                                } else {
                                                    echo '-';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </form>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
            <?php
                }
            }
            ?>
        </div>

    </div>

</body>

</html>