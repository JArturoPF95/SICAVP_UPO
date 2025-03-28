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
    $user_sesion = $_SESSION['session'];
}

$program = '';
$codeDay = '';
$today = '';
$selectedDay = '';
$payrollCode = '';

$send_flag = 0;
$year = date('Y');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $send_flag = $_POST['flag'];
    if (isset($_POST['payrollCode'])) {
        $payrollCode = $_POST['payrollCode'];
    } else {
        $payrollCode = '0';
    }

    require 'process/query_reports.php';

    $sqlTeachersReport;
    $resultTeacherReport = $mysqli->query($sqlTeachersReport);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Quincenal</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <h4 class="mb-3">Reporte Periodo de Nómina Docente</h4>
    <div class="container"> <!-- Div Principal-->
        <div class="row my-2">
            <form class="row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                <input hidden type="text" name="flag" id="" value="1">
                <div class="col-sm-5">
                    <select class="form-select" id="specificSizeSelect" name="payrollCode">
                        <option selected disabled value="0">Seleccionar Periodo</option>
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
                <div class="col-auto">
                    <a href="process/downloadPayrollPeriod.php?id=<?php echo $payrollCode ?>" type="button" class="btn btn-secondary">Descargar</a>
                </div>
            </form>
        </div>
        <div class="row my-2">

            <?php

            if ($send_flag == 1) {
                if ($resultTeacherReport->num_rows > 0) {
            ?>
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="text-center table-primary">
                            <th class="text-white fs-6 fw-bold">Fecha</th>
                            <th class="text-white fs-6 fw-bold">Periodo</th>
                            <th class="text-white fs-6 fw-bold">Campus</th>
                            <th class="text-white fs-6 fw-bold">Día</th>
                            <th class="text-white fs-6 fw-bold">ID Docente</th>
                            <th class="text-white fs-6 fw-bold">Nombre</th>
                            <th class="text-white fs-6 fw-bold">Nivel</th>
                            <th class="text-white fs-6 fw-bold">Programa</th>
                            <th class="text-white fs-6 fw-bold">Curriculum</th>
                            <th class="text-white fs-6 fw-bold">Tipo Materia</th>
                            <th class="text-white fs-6 fw-bold">Grupo</th>
                            <th class="text-white fs-6 fw-bold">Aula</th>
                            <th class="text-white fs-6 fw-bold">Materia</th>
                            <th class="text-white fs-6 fw-bold">Horario</th>
                            <th class="text-white fs-6 fw-bold">Entrada</th>
                            <th class="text-white fs-6 fw-bold">Salida</th>
                            <th class="text-white fs-6 fw-bold">Estatus</th>
                        </thead>
                        <tbody>
                            <?php
                            while ($rowTeachersReport = $resultTeacherReport->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($rowTeachersReport['CALENDAR_DATE'])) ?></td>
                                    <td><?php echo $rowTeachersReport['ACADEMIC_TERM'] ?></td>
                                    <td><?php echo $rowTeachersReport['ACADEMIC_SESSION'] ?></td>
                                    <td><?php echo $rowTeachersReport['NAME_DAY'] ?></td>
                                    <td><?php echo $rowTeachersReport['PERSON_CODE_ID'] ?></td>
                                    <td><?php echo $rowTeachersReport['ACA_NAME'] ?></td>
                                    <td><?php echo $rowTeachersReport['DEGREE'] ?></td>
                                    <td><?php echo $rowTeachersReport['PROGRAM'] ?></td>
                                    <td><?php echo $rowTeachersReport['CURRICULUM'] ?></td>
                                    <td><?php echo $rowTeachersReport['GENERAL_ED'] ?></td>
                                    <td><?php echo $rowTeachersReport['SERIAL_ID'] ?></td>
                                    <td><?php echo $rowTeachersReport['ROOM'] ?></td>
                                    <td><?php echo $rowTeachersReport['EVENT'] ?></td>
                                    <td><?php echo substr($rowTeachersReport['START_CLASS'], 0, 8) . ' - ' . substr($rowTeachersReport['END_CLASS'], 0, 8) ?></td>
                                    <td><?php echo $rowTeachersReport['CLASS_IN'] ?></td>
                                    <td><?php echo $rowTeachersReport['CLASS_OUT'] ?></td>
                                    <td><?php echo $rowTeachersReport['CLASS_INCIDENCE'] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    echo '
<div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>
<h4>No cuenta con docentes con horario registrados en sistema esa quincena</h4>
</div>
</div>';
                }
            } else {
                if ($payrollCode = '') {
                ?>
                    <div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                        <div>
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <h4>&nbsp; &nbsp; Favor de seleccionar otro Periodo</h4>
                        </div>
                    </div>
            <?php
                }
            }

            ?>
        </div>
    </div>

</body>

</html>