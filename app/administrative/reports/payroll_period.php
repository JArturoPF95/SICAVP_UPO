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

$today = date('Y-m-d');
$payrollPEndDate = '';
$payrollPStartDate = '';
$year = date('Y');
$status = '';
$flag_send = 0;
$payrollID = 0;
$selectedDate = '';
$id_nom_employed = '';

$alert = '
<div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center my-3" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>Sin registros en el periodo de nómina seleccionado</div>
</div>';

function sumarDiff($hora1, $hora2, $hora3, $hora4) {
    // Convertir a DateTime
    $inicio1 = new DateTime($hora1);
    $fin1 = new DateTime($hora2);
    $inicio2 = new DateTime($hora3);
    $fin2 = new DateTime($hora4);

    // Obtener diferencias en segundos
    $diff1 = $fin1->getTimestamp() - $inicio1->getTimestamp();
    $diff2 = $fin2->getTimestamp() - $inicio2->getTimestamp();

    // Sumar diferencias
    $totalSegundos = $diff1 + $diff2;

    // Formatear como HH:MM:SS
    $horas = floor($totalSegundos / 3600);
    $minutos = floor(($totalSegundos % 3600) / 60);
    $segundos = $totalSegundos % 60;

    return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supervisor_id = $_POST['supervisor_id'];
    if (isset($_POST['codePP'])) {
        $payrollPeriodID = $_POST['codePP'];
    } else {
        $payrollPeriodID = '0';
    }
    $flag_send = $_POST['flag'];

    $sqlPayrollP = "SELECT * FROM payroll_period WHERE ID = $payrollPeriodID";
    $resultPayrollP = $mysqli->query($sqlPayrollP);
    if ($resultPayrollP->num_rows > 0) {
        while ($rowPayrollP = $resultPayrollP->fetch_assoc()) {
            $payrollPEndDate = $rowPayrollP['END_DATE'];
            $payrollPStartDate = $rowPayrollP['START_DATE'];
            $payrollPID = $rowPayrollP['CODE'];
            $payrollPDesc = $rowPayrollP['DESCRIPTION'];
            $payrollPYear = $rowPayrollP['YEAR'];
        }
    }
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
</head>

<body>
    <div class="container my-4" style="width: 100%;">
        <h4 class="mb-3">Reporte Periodo de Nómina</h4>
        <div class="row my-2">
            <div class="col">
                <form class="row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <input hidden type="text" name="flag" id="" value="1">
                    <div class="col-sm-3">
                        <select class="form-select" id="specificSizeSelect" name="codePP">
                            <option selected disabled>Periodo de Nómina</option>
                            <?php
                            $sql_payrollTForm = "SELECT * FROM payroll_period WHERE START_DATE <= '$today' AND YEAR = '$year' ORDER BY START_DATE ASC";
                            $result_payrollTForm = $mysqli->query($sql_payrollTForm);
                            if ($result_payrollTForm->num_rows > 0) {
                                while ($row_payrollTForm = $result_payrollTForm->fetch_assoc()) {
                                    $id = $row_payrollTForm['ID'];
                                    $year = $row_payrollTForm['YEAR'];
                                    $code = $row_payrollTForm['CODE'];
                                    $description = $row_payrollTForm['DESCRIPTION'];
                                    $start_date = $row_payrollTForm['START_DATE'];
                                    $end_date = $row_payrollTForm['END_DATE'];
                            ?>
                                    <option value="<?php echo $id ?>"><?php echo $code . ' ' . $description ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input hidden type="text" class="form-control" id="specificSizeInputName" name="supervisor_id" value="<?php echo $supervisor_id ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Seleccionar</button>
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-secondary" href="process/downloadPayrollPeriod.php?id=<?php echo $payrollPeriodID ?>" role="button">Descargar Excel</a>
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-secondary" href="process/downloadAbsences.php?id=<?php echo $payrollPeriodID ?>" role="button">Descargar Faltas</a>
                    </div>
                </form>
            </div>
        </div>
        <?php
        if ($flag_send == 1) {
        ?>
            <div class="row my-2">
                <?php

                require 'process/query_reports.php';
                $sql_paryrollPeriod_report ;
                $result_payrollPReport = $mysqli->query($sql_paryrollPeriod_report);

                //echo $sql_paryrollPeriod_report;

                if ($result_payrollPReport->num_rows > 0) {
                ?>

                    <table class="table table-hover table-bordered table-sm my-3" style="font-size: 13px;">
                        <thead class="text-center table-primary">
                            <th class="text-white fw-bold">Fecha</th>
                            <th class="text-white fw-bold">Día</th>
                            <th class="text-white fw-bold">ID Empleado</th>
                            <th class="text-white fw-bold">Colaborador</th>
                            <th class="text-white fw-bold">Hora de Entrada</th>
                            <th class="text-white fw-bold">Salida a Comer</th>
                            <th class="text-white fw-bold">Regreso de Comer</th>
                            <th class="text-white fw-bold">Hora de Salida</th>
                            <th class="text-white fw-bold">Estatus</th>
                            <th class="text-white fw-bold">Tiempo <br> Laborado</th>
                        </thead>
                        <?php
                        while ($row_payrollPRepor = $result_payrollPReport->fetch_assoc()) {
                            $calendarDate = $row_payrollPRepor['CALENDAR_DATE'];
                            $nameDay = $row_payrollPRepor['NAME_DAY'];
                            $employID = $row_payrollPRepor['ID_NOM'];
                            $employed = $row_payrollPRepor['NOMBRE'];
                            $checkin = $row_payrollPRepor['CHECK_IN'];
                            $checkout = $row_payrollPRepor['CHECK_OUT'];
                            $breakin = $row_payrollPRepor['BREAK_END'];
                            $breakout = $row_payrollPRepor['BREAK_START'];
                            $status = $row_payrollPRepor['STATUS'];

                            $campusIn = new DateTime($checkin);
                            $campusOut = new DateTime($checkout);
                            $breakStart = new DateTime($breakout);
                            $breakEnd = new DateTime($breakin);

                            if ($checkout == '') {
                                $workTime = 'Sin registro de salida';
                            } elseif ($breakin == '') {
                                $wTime = $campusOut->diff($campusIn);
                                $workTime = $wTime->format('%H:%I:%S') . ' <br> Sin registros de comida';
                            } else {
                                $workTime = sumarDiff($checkin, $breakout, $breakin, $checkout);
                            }

                        ?>
                            <tbody class="text-center">
                                <tr>
                                    <td>
                                        <p><?php echo date('d/m/Y', strtotime($calendarDate)) ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $nameDay ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $employID ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $employed ?></p>
                                    </td>
                                    <td>
                                        <p><?php if ($checkin != '') {
                                         echo date('H:i:s', strtotime($checkin)); } else { echo '';} ?></p>
                                    </td>
                                    <td>
                                        <p><?php if ($breakout != '') {
                                         echo date('H:i:s', strtotime($breakout)); } else { echo '';} ?></p>
                                    </td>
                                    <td>
                                        <p><?php if ($breakin != '') {
                                         echo date('H:i:s', strtotime($breakin)); } else { echo '';} ?></p>
                                    </td>
                                    <td>
                                        <p><?php if ($checkout != '') {
                                         echo date('H:i:s', strtotime($checkout)); } else { echo '';} ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $status ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $workTime ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        <?php
                        }
                        ?>
                    </table>
            <?php
                } else {
                    echo $alert;
                }
            }
            ?>
            </div>
    </div>
</body>

</html>