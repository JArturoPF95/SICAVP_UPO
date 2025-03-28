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



$year2 = date('Y');

$today = date('Y-m-d');

$payrollPeriodID = '';

$flag_send = 0;

$breakTime = 0;

$font = '';

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

    $payrollPeriodID = $_POST['payrollPeriodID'];

    $flag_send = $_POST['flag'];



    require 'process/query_attendance.php';



    $sqlBiometricTimeClock;

    $resultBiometricTimeClock = $mysqli->query($sqlBiometricTimeClock);

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

    <div class="container-fluid" style="height: 100%; width: 100%;">

        <h4 class="mb-3">Historial de Asistencias</h4>

        <!--Formulario-->

        <div class="row my-2">

            <div class="col">

                <form class="row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

                    <input hidden type="text" name="flag" id="" value="1">

                    <div class="col-sm-4">

                        <label class="visually-hidden" for="specificSizeSelect">Preference</label>

                        <select class="form-select" id="specificSizeSelect" name="payrollPeriodID">

                            <option selected>Periodo de Nómina</option>

                            <?php

                            $sql_payroll_period_form = "SELECT * FROM payroll_period WHERE START_DATE <= '$today' AND YEAR = '$year2'

AND END_DATE >= (SELECT ADMISSION_DATE FROM employed WHERE ID_NOM = '$user_active')";

                            $result_payroll_period_form = $mysqli->query($sql_payroll_period_form);

                            if ($result_payroll_period_form->num_rows > 0) {

                                while ($rowPayrollPeriod = $result_payroll_period_form->fetch_assoc()) {

                                    $id = $rowPayrollPeriod['ID'];

                                    $year = $rowPayrollPeriod['YEAR'];

                                    $code = $rowPayrollPeriod['CODE'];

                                    $description = $rowPayrollPeriod['DESCRIPTION'];

                                    $start_date = $rowPayrollPeriod['START_DATE'];

                                    $end_date = $rowPayrollPeriod['END_DATE'];

                            ?>

                                    <option value="<?php echo $id ?>"><?php echo $code . ' ' . $description ?></option>

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

        <!--Tabla de Historial-->

        <hr class="my-4">

        <?php

        if ($flag_send == 1) {

        ?>

            <table class="table table-primary table-hover table-bordered table-sm" style="font-size: 13px;">

                <thead class="text-white text-center">

                    <tr>

                        <th scope="col">Día</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Hora de Entrada</th>
                        <th scope="col">Salida a Comer</th>
                        <th scope="col">Regreso de Comer</th>
                        <th scope="col">Hora de Salida</th>
                        <th scope="col">Estatus</th>
                        <th scope="col">Tiempo <br> Laborado</th>
                        <th scope="col">Justificado</th>

                    </tr>

                </thead>

                <tbody class="table-light">

                    <?php

                    if ($resultBiometricTimeClock -> num_rows > 0) {

                        while ($rowBiometricTimeClock = $resultBiometricTimeClock -> fetch_assoc()) {

                            $biometricRecordDate = $rowBiometricTimeClock['RECORD_DATE'];
                            $biometricRecordDay = $rowBiometricTimeClock['NAME_DAY'];
                            $biometricRecordCheckIn = substr($rowBiometricTimeClock['CHECK_IN'], 0, 8);
                            $biometricRecordCheckOut = substr($rowBiometricTimeClock['CHECK_OUT'], 0, 8);
                            $biometricRecordBreakIn = substr($rowBiometricTimeClock['BREAK_END'], 0, 8);
                            $biometricRecordBreakOut = substr($rowBiometricTimeClock['BREAK_START'], 0, 8);
                            $biometricRecordBreak = $rowBiometricTimeClock['BREAK_TIME'];
                            $biometricRecordJustify = $rowBiometricTimeClock['JUSTIFY'];
                            $biometricRecordStatus = $rowBiometricTimeClock['STATUS'];

                            $campusIn = new DateTime($biometricRecordCheckIn);
                            $campusOut = new DateTime($biometricRecordCheckOut);
                            $breakStart = new DateTime($biometricRecordBreakOut);
                            $breakEnd = new DateTime($biometricRecordBreakIn);

                            if ($biometricRecordCheckOut == '') {
                                $workTime = 'Sin registro de salida';
                            } elseif ($biometricRecordBreakIn == '') {
                                $wTime = $campusOut->diff($campusIn);
                                $workTime = $wTime->format('%H:%I:%S') . ' <br> Sin registros de comida';
                            } else {
                                $workTime = sumarDiff($biometricRecordCheckIn, $biometricRecordBreakOut, $biometricRecordBreakIn, $biometricRecordCheckOut);
                            }



                    ?>

                            <tr class="text-center">
                                <td><?php echo $biometricRecordDay ?></td>
                                <td><?php echo date("d/m/Y", strtotime($biometricRecordDate)) ?></td>
                                <td><?php echo $biometricRecordCheckIn ?></td>
                                <td><?php echo $biometricRecordBreakOut ?></td>
                                <td><?php echo $biometricRecordBreakIn ?></td>
                                <td><?php echo $biometricRecordCheckOut ?></td>
                                <td><?php echo $biometricRecordStatus ?></td>
                                <td><?php echo $workTime ?></td>
                                <td>
                                    <?php
                                    //Enviamos un ícono dependiendo si fue justificado
                                    if ($biometricRecordJustify == 'Y') {
                                        echo '<i class="bi-check-circle-fill" style="color: green;"></i>';
                                    } elseif ($biometricRecordJustify == 'N') {
                                        echo '<i class="bi-x-circle-fill" style="color: red;"></i>';
                                    } elseif ($biometricRecordJustify == 'P') {
                                        echo '<i class="bi bi-exclamation-circle-fill" style="color: yellow;"></i>';
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



    <!-- Incluye la biblioteca de iconos de Bootstrap Icons -->

    <script src="../../../static/css/bootstrap-icons/font/bootstrap-icons.css"></script>



</body>



</html>