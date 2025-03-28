<?php
require_once '../../logic/conn.php';

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

$send_flag = 0;
$year = date('Y');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $send_flag = $_POST['flag'];
    if (isset($_POST['payrollCode'])) {
        $payrollPeriodID = $_POST['payrollCode'];
    } else {
        $payrollPeriodID = '';
    }
    require_once 'process/query.php';

    $sql_get_attendance;
    $result_attendance = $mysqli->query($sql_get_attendance);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">

</head>

<body>
    <h4 class="mb-3">Historial Clases</h4>
    <div class="container-fluid" style="height: 100%; width: 100%;">
        <form class="row gx-3 gy-2 mb-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <input hidden type="text" name="flag" id="" value="1">
            <div class="col-sm-5">
                <select class="form-select" id="specificSizeSelect" name="payrollCode">
                    <option selected disabled value="0">Seleccionar Periodo de Nómina</option>
                    <?php
                    $sqlValPayrollPeriod = "SELECT * FROM payroll_period WHERE YEAR = '$year'";
                    $resultValPayrollPeriod = $mysqli->query($sqlValPayrollPeriod);
                    if ($resultValPayrollPeriod->num_rows > 0) {
                        while ($rowPayrollPeriod = $resultValPayrollPeriod->fetch_assoc()) {
                    ?>
                            <option value="<?php echo $rowPayrollPeriod['ID'] ?>"><?php echo $rowPayrollPeriod['CODE'] . ' ' . $rowPayrollPeriod['DESCRIPTION'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Seleccionar</button>
            </div>
        </form>
        <?php
        if ($send_flag == '1') {
        ?>
            <table class="table table-primary table-hover table-bordered table-sm">
                <thead class="text-white text-center">
                    <tr>
                        <th scope="col">Fecha</th>
                        <th scope="col">Día</th>
                        <th scope="col">Carrera</th>
                        <th scope="col">Modalidad</th>
                        <th scope="col">Materia</th>
                        <th scope="col">Aula</th>
                        <th scope="col">Estatus</th>
                        <th scope="col">Hora Entrada</th>
                        <th scope="col">Hora Salida</th>
                        <th scope="col">Justificada</th>
                    </tr>
                </thead>
                <tbody class="table-light">
                    <?php

                    if ($result_attendance->num_rows > 0) {
                        while ($row_attendance = $result_attendance->fetch_assoc()) {
                            $attendance_day = $row_attendance['NAME_DAY'];
                            $attendance_date = $row_attendance['ATTENDANCE_DATE'];
                            $attendance_program = $row_attendance['PROGRAM'];
                            $attendance_curriculum = $row_attendance['CURRICULUM'];
                            $attendance_event = $row_attendance['EVENT_ID'];
                            $attendance_room = $row_attendance['ROOM_ID'];
                            $attendance_incidence = $row_attendance['DESCRIP_TINC'];
                            $attendance_checkIn = $row_attendance['CHECK_IN'];
                            $attendance_checkOut = $row_attendance['CHECK_OUT'];
                            $attendance_justify = $row_attendance['JUSTIFY'];
                    ?>

                            <tr class="text-center">
                                <td><?php echo date("d/m/Y", strtotime($attendance_date)) ?></td>
                                <td><?php echo $attendance_day ?></td>
                                <td><?php echo $attendance_curriculum ?></td>
                                <td><?php echo $attendance_program ?></td>
                                <td><?php echo $attendance_event ?></td>
                                <td><?php echo $attendance_room ?></td>
                                <td><?php echo $attendance_incidence ?></td>
                                <td><?php echo substr($attendance_checkIn, 0, 8) ?></td>
                                <td><?php echo substr($attendance_checkOut, 0, 8)  ?></td>
                                <td>
                                    <?php
                                    //Enviamos un ícono dependiendo si fue justificado
                                    if ($attendance_justify == 'Y') {
                                        echo '<i class="bi-check-circle-fill" style="color: green;"></i>';
                                    } elseif ($attendance_justify == 'N') {
                                        echo '<i class="bi-x-circle-fill" style="color: red;"></i>';
                                    } else {
                                        echo '';
                                    }
                                    ?>

                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
        ?>
    </div>
</body>

</html>