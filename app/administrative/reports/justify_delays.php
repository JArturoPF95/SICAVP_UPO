<?php



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



require_once '../../logic/conn.php';

date_default_timezone_set('America/Mexico_City');



$payrollPeriodID = 0;

$flag_send = 0;

$id_nom_employed = '';

$today = date('Y-m-d');

$year = date('Y');

$alert = '

<div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center my-3" role="alert">

<i class="bi bi-exclamation-circle-fill"></i>

<div>Sin solicitudes en el periodo de nómina seleccionado</div>

</div>';



    // Guardar el valor del select en la sesión cuando se envía el formulario

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['codePP'])) {

        $payrollPeriodID = $_POST['codePP'];

        $flag_send = '1';

    } else {

            $flag_send = '0';

            $payrollPeriodID = '0';

        }



        

    require_once 'process/query_reports.php';



    $sqlValJustifies = "SELECT BTC.AttendanceId, EMP.ID_NOM, EMP.NAME, EMP.LAST_NAME, EMP.LAST_NAME_PREFIX, BTC.ATTENDANCE_DATE, BTC.ATTENDANCE_TIME, BTC.JUSTIFY, BTC.COMMENTS, BTC.TINC

        FROM admin_attendance BTC INNER JOIN employed EMP ON EMP.ID_NOM = BTC.NOM_ID WHERE JUSTIFY != '' AND EMP.SUPERVISOR_ID = '$user_active' AND ATTENDANCE_DATE BETWEEN (SELECT START_DATE FROM payroll_period WHERE ID = '$payrollPeriodID') AND (SELECT END_DATE FROM payroll_period WHERE ID = '$payrollPeriodID')";

    $resultValJustifies = $mysqli -> query($sqlValJustifies);

}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['pp'])) {

        $payrollPeriodID = $_GET['pp'];

        $flag_send = '1';

    } else {

            $flag_send = '0';

            $payrollPeriodID = '0';

        }



        

    require_once 'process/query_reports.php';



    $sqlValJustifies = "SELECT BTC.AttendanceId, EMP.ID_NOM, EMP.NAME, EMP.LAST_NAME, EMP.LAST_NAME_PREFIX, BTC.ATTENDANCE_DATE, BTC.ATTENDANCE_TIME, BTC.JUSTIFY, BTC.COMMENTS, BTC.TINC

        FROM admin_attendance BTC INNER JOIN employed EMP ON EMP.ID_NOM = BTC.NOM_ID WHERE JUSTIFY != '' AND EMP.SUPERVISOR_ID = '$user_active' AND ATTENDANCE_DATE BETWEEN (SELECT START_DATE FROM payroll_period WHERE ID = '$payrollPeriodID') AND (SELECT END_DATE FROM payroll_period WHERE ID = '$payrollPeriodID')";

    $resultValJustifies = $mysqli -> query($sqlValJustifies);

}











?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="../../../static/js/popper.min.js" ></script>

    <script src="../../../static/js/bootstrap.min.js" ></script>

    <link rel="stylesheet" href="../../../static/css/bootstrap.css">

</head>

<body>

    <div class="container-fluid" style="height: 100%; width: 85%;">

        <h4 class="mb-3">Justificación Retardos, Faltas</h4>

        <div class="row">

            

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

                        <button type="submit" class="btn btn-primary">Consultar</button>

                    </div>

                </form>

        </div>

        <div class="row">

            <?php

            if ($flag_send == 1) {              



                if( $resultValJustifies -> num_rows > 0 ){

            ?>

            <table class="table table-primary table-striped table-hover table-bordered my-4 table-sm" style="font-size: 13px;">

                <thead>

                    <tr class="text-white text-center">

                        <th scope="col" hidden>Asistencia</th>

                        <th scope="col">Fecha</th>

                        <th scope="col">Día</th>

                        <th scope="col">Empleado</th>

                        <th scope="col">Nombre</th>

                        <th scope="col">Entrada</th>

                        <th scope="col">Comentario</th>

                        <th scope="col">Estatus</th>

                        <th scope="col">Justificar</th>

                    </tr>

                </thead>

                <form action="process/update_attendance.php" method="post">

                <tbody class="table-light">

                <?php

                    while($rowJustifies = $resultValJustifies -> fetch_assoc()) {

                        $justifyattendance = $rowJustifies['AttendanceId'];

                        $justifydate = $rowJustifies['ATTENDANCE_DATE'];

                        $justifycheckIn = substr( $rowJustifies['ATTENDANCE_TIME'], 0, 8 );

                        $justifynomID = $rowJustifies['ID_NOM'];

                        $justifyname = $rowJustifies['NAME'];

                        $justifylastName = $rowJustifies['LAST_NAME'];

                        $justifylastNameP = $rowJustifies['LAST_NAME_PREFIX'];

                        $justifycomment = $rowJustifies['COMMENTS'];                        

                        $justifyjustify = $rowJustifies['JUSTIFY'];

                        $justifyTinc = $rowJustifies['TINC'];



                        $code_day = date('w', strtotime($justifydate));



                        switch ($code_day) {

                            case '1':

                                $day = 'Lunes';

                                break;

                            case '2':

                                $day = 'Martes';

                                break;

                            case '3':

                                $day = 'Miércoles';

                                break;

                            case '4':

                                $day = 'Jueves';

                                break;

                            case '5':

                                $day = 'Viernes';

                                break;

                            case '6':

                                $day = 'Sábado';

                                break;

                            default:

                                $day = 'Domingo';

                                break;

                        }

                        

                ?>

                    <tr class="text-center">

                            <td hidden><input type="text" name="attendance[<?php echo $justifyattendance ?>][pk]" id="attendance" value="<?php echo $justifyattendance ?>"></td>
                            <td hidden><input type="text" name="attendance[<?php echo $justifyattendance ?>][ppd]" id="attendance" value="<?php echo $payrollPeriodID ?>"></td>
                            <td hidden><input type="text" name="attendance[<?php echo $justifyattendance ?>][tinc]" id="attendance" value="<?php echo $justifyTinc ?>"></td>

                            <td><?php echo date("d/m/Y", strtotime($justifydate)) ?></td>

                            <td><?php echo $day ?></td>

                            <td><?php echo $justifynomID ?></td>

                            <td><?php echo $justifylastName . ' ' . $justifylastNameP . ' ' . $justifyname ?></td>

                            <td><?php echo $justifycheckIn ?></td>

                            <td><?php echo $justifycomment ?></td>

                            <td><?php

                            switch($justifyjustify){

                                case "P":

                                    echo "Pendiente";

                                    break;

                                case "Y":

                                    echo "Aceptada";

                                    break;

                                case "N":

                                    echo "Rechazada";

                                    break;

                            }

                            

                            ?></td>

                            <td>

                                <?php

                                if ($justifyjustify == 'P') {

                                ?>

                                <div class="form-check  form-check-inline">

                                    <input class="form-check-input" type="radio" name="attendance[<?php echo $justifyattendance ?>][justify]" value="Y" id="flexRadioDefault1">

                                    <label class="form-check-label" for="flexRadioDefault1">

                                        Sí

                                    </label>

                                </div>

                                <div class="form-check  form-check-inline">

                                    <input class="form-check-input" type="radio" name="attendance[<?php echo $justifyattendance ?>][justify]" value="N" id="flexRadioDefault2">

                                    <label class="form-check-label" for="flexRadioDefault2">

                                        No

                                    </label>

                                </div>

                                <?php

                                }elseif($justifyjustify == 'Y'){

                                    ?>

                                    <div class="form-check  form-check-inline">

                                    <input class="form-check-input" type="radio" name="attendance[<?php echo $justifyattendance ?>][justify]" value="Y" id="flexRadioDefault1" checked>

                                    <label class="form-check-label" for="flexRadioDefault1">

                                        Sí

                                    </label>

                                </div>

                                <div class="form-check  form-check-inline">

                                    <input class="form-check-input" type="radio" name="attendance[<?php echo $justifyattendance ?>][justify]" value="N" id="flexRadioDefault2">

                                    <label class="form-check-label" for="flexRadioDefault2">

                                        No

                                    </label>

                                </div>

                                <?php }elseif($justifyjustify == 'N'){

                                    ?>

                                    <div class="form-check  form-check-inline">

                                    <input class="form-check-input" type="radio" name="attendance[<?php echo $justifyattendance ?>][justify]" value="Y" id="flexRadioDefault1">

                                    <label class="form-check-label" for="flexRadioDefault1">

                                        Sí

                                    </label>

                                </div>

                                <div class="form-check  form-check-inline">

                                    <input class="form-check-input" type="radio" name="attendance[<?php echo $justifyattendance ?>][justify]" value="N" id="flexRadioDefault2" checked>

                                    <label class="form-check-label" for="flexRadioDefault2">

                                        No

                                    </label>

                                </div>

                                <?php 

                                }

                                ?>

                            </td>

                    </tr>

                <?php

                    }

                ?>

                <tr class="text-end">

                        <td colspan="8"><button type="submit" class="btn btn-sm btn-primary">Justificar</button></td>

                        </tr>

                </tbody>

                

                </form>

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