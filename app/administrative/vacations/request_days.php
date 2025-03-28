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
//$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$idVac = '';
$start_date = '';
$end_date = '';
$first_day = '';
$last_day = '';
$today = date('Y-m-d');
$term = date('Y');
$termSel = '';
$admission_Date = '';
$headerName = '';

$myRequest_Authorization = 0;

$days = '';
$days_law = 0;
$antFlag = '0'; //Para validar si son días anticipados o no
$days_left = 0;
$days_used = 0;
$days_request = 0;

$supervisor_nom = '';
$antiquity_years = '';
$prevVal = '';

$val_days = '';
$flag_send = 0;

require '../../logic/conn.php';

//Recibe el formulario de la elección de fechas
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $flag_send = $_POST['flag_send'];
    $termSel = $_POST['termSel'];

    require 'process/query_vacations.php';

    //Validamos los días que solicita, eliminamos días festivos y días que no corresponden a su horario
    $sql_getCalendarDays;
    $restult_getCalendarDays = $mysqli->query($sql_getCalendarDays);
    if ($restult_getCalendarDays->num_rows > 0) {
        //$days_request = $restult_getCalendarDays -> num_rows; //Contamos los días que se solicitaron
        while ($row_getDays = $restult_getCalendarDays->fetch_assoc()) {
            $days_request = $row_getDays['DAYS_REQ'];
            $first_day = $row_getDays['START_DATE'];
            $last_day = $row_getDays['END_DATE'];

            //Obtenemos días previos para solicitar días
            $sqlGetPrev = "SELECT DISTINCT PREV_DAYS FROM code_vacation";
            $resultGetPrev = $mysqli -> query($sqlGetPrev);
            if ($resultGetPrev -> num_rows > 0) {
                while ($rowPrevDays = $resultGetPrev -> fetch_assoc()) {
                    $prev = $rowPrevDays['PREV_DAYS'];
            $prevVal = ( strtotime($start_date) - strtotime($today) ) / ( 60 * 60 * 24);
            
            if ($prevVal >= $prev) {

            require_once 'process/request_days.php';
                $sql_valRequestedDays = "SELECT * FROM vacation_request
                WHERE ID_NOM = '$user_active' AND AUTHORIZATION_FLAG != '2' AND ('$first_day' BETWEEN START_DATE AND END_DATE
                OR '$last_day' BETWEEN START_DATE AND END_DATE
                OR START_DATE BETWEEN '$first_day' AND '$last_day' 
                OR END_DATE BETWEEN '$first_day' AND '$last_day')";
                $result_valRequestDays = $mysqli->query($sql_valRequestedDays);
                if ($result_valRequestDays->num_rows > 0) {
                    $vacations_title = 'Días Inválidos';
                    $vacations_message = 'Seleccionó días ya antes solicitados. \n Favor de seleccionar los correspondientes';
                    $vacationes_icon = 'warning';
                } else {

                    if ($days_request <= ($days_law - $days_used) || $termSel != $term) {
                        //echo 'YA ENTRÓ AL IF DE INSERCIÓN';
                        $sql_insert_request = "INSERT INTO vacation_request (ID_NOM, REQUEST_TERM, REQUEST_DATE, START_DATE, END_DATE, DAYS_REQUESTED, AUTHORIZATION_FLAG, IMMEDIATE_BOSS) 
                VALUES ('$user_active','$termSel','$today','$first_day','$last_day','$days_request',0,'$supervisor_nom')";
                        //echo $sql_insert_request;
                        if ($mysqli->query($sql_insert_request) === true) {
                            $vacations_title = 'Solicitud Exitosa';
                            $vacations_message = 'Vacaciones solicitadas con éxito. \n Favor de esperar autorización';
                            $vacationes_icon = 'success';
                        } else {
                            $vacations_title = 'Error en Solicitud';
                            $vacations_message = 'Error solicitando sus vacaciones. \n Favor de intentar nuevamente';
                            $vacationes_icon = 'error';
                        }
                    } else {
                        $vacations_title = 'Días Excedidos';
                        $vacations_message = 'Seleccionó más días de los que corresponden. \n Favor de seleccionar los correspondientes';
                        $vacationes_icon = 'warning';
                    }
                }
            } else {
                $vacations_title = 'Falta Anticipación';
                $vacations_message = 'Las vacaciones deben ser solicitadas con ' . $prev . ' días de Anticipación';
                $vacationes_icon = 'warning';
            }

            
        }
    }

        }
    }

    //echo $sql_getCalendarDays;
}
require 'process/query_vacations.php';
$sql_myRequest;
$result_myRequest = $mysqli->query($sql_myRequest);

require_once 'process/request_days.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solcitar Vacaciones</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <div class="container"> <!--Div Inicial-->
        <!--Formulario de Solicitud-->
        <h4 class="mb-3">Solicitar Vacaciones</h4>
        <div class="row py-1">
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                <div class="row g-2">
                    <div class="col-3">
                        <div class="form-floating">
                            <select class="form-select form-select-sm" aria-label="Small select example" id="termSel" name="termSel">
                            <?php
                            $sqlGetYears = "SELECT DISTINCT YEAR FROM calendar WHERE YEAR >= '$term';";
                            $resultGetYears = $mysqli -> query($sqlGetYears);
                            if ($resultGetYears -> num_rows > 0) {
                                while ($rowYear = $resultGetYears -> fetch_assoc()) {
                           ?>                           
                           <option value="<?php echo $rowYear['YEAR'] ?>"><?php echo $rowYear['YEAR'] ?></option>
                           <?php
                                }
                            }
                            ?>
                            </select>
                            <label for="termSel">Periodo:</label>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-floating">
                            <input type="date" name="start_date" class="form-control" id="floatingInputGrid" placeholder="<?php echo $today ?>" value="<?php echo $today ?>">
                            <label for="floatingInputGrid">Fecha Inicio</label>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-floating">
                            <input type="date" name="end_date" class="form-control" id="floatingInputGrid" placeholder="<?php echo $today ?>" value="<?php echo $today ?>">
                            <label for="floatingInputGrid">Fecha Fin</label>
                        </div>
                    </div>
                    <input type="hidden" name="flag_send" value='1'>
                    <div class="col-3">
                        <?php echo $buttomRequest; ?>
                    </div>
                </div>
            </form>
        </div>
        <!--Mis días-->
        <div class="row py-2">
            <table class="table table-primary table-hover table-bordered table-sm">
                <thead class="text-white text-center">
                    <tr>
                        <th scope="col">Periodo</th>
                        <th scope="col">Fecha Ingreso</th>
                        <th scope="col">Antigüedad</th>
                        <th scope="col"><?php $headerName = ($antFlag == '0') ? 'Días por Ley' : 'Días Autorizados <br> con Anticipo' ; echo $headerName; ?></th>
                        <th scope="col">Días Solicitados</th>
                        <th scope="col">Días Restantes</th>
                    </tr>
                </thead>
                <tbody class="table-light">
                    <tr class="text-center">
                        <td><?php echo $term ?></td>
                        <td><?php echo date('d/m/Y', strtotime($antiquity)) ?></td>
                        <td><?php echo $anti_yrs ?></td>
                        <td><?php echo $days_law ?></td>
                        <td><?php echo $days_used ?></td>
                        <td><?php echo $days_left ?></td>
                    </tr>
                </tbody>
            </table>
        </div> <!--Cierra div solicitar días-->
        <!--Mis Solicitudes-->
        <hr class="my-4">
        <h4 class="mb-3">Mis Solicitudes</h4>
        <div class="row my-2">
            <?php

            if ($result_myRequest->num_rows > 0) {
            ?>

                <table class="table table-hover table-bordered table-sm">
                    <thead class="text-white text-center table-primary">
                        <tr>
                            <th scope="col">Periodo</th>
                            <th scope="col">Solicitadas el</th>
                            <th scope="col">Fecha de Inicio</th>
                            <th scope="col">Fecha de Fin</th>
                            <th scope="col">Días</th>
                            <th scope="col">Autorización</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>

                    <?php

                    while ($row_MyReq = $result_myRequest->fetch_assoc()) {
                        $myRequest_Term = $row_MyReq['REQUEST_TERM'];
                        $myRequest_Date = $row_MyReq['REQUEST_DATE'];
                        $myRequest_Start = $row_MyReq['START_DATE'];
                        $myRequest_End = $row_MyReq['END_DATE'];
                        $myRequest_Days = $row_MyReq['DAYS_REQUESTED'];
                        $myRequest_Authorization = $row_MyReq['AUTHORIZATION_FLAG'];
                        $myRequest_Id = $row_MyReq['requestId'];

                        if ($myRequest_Authorization == 0) {
                            $authorization = 'Pendiente';
                            $background = 'table-warning';
                        } elseif ($myRequest_Authorization == 1) {
                            $authorization = 'Autorizado';
                            $background = 'table-success';
                        } else {
                            $authorization = 'Rechazado';
                            $background = 'table-danger';
                        }
                    ?>

                        <tbody class="text-center <?php echo $background ?>">
                            <tr>
                                <td hidden scope="col"><?php echo $myRequest_Id ?></td>
                                <td scope="col"><?php echo $myRequest_Term ?></td>
                                <td scope="col"><?php echo date('d/m/Y', strtotime($myRequest_Date)) ?></td>
                                <td scope="col"><?php echo date('d/m/Y', strtotime($myRequest_Start)) ?></td>
                                <td scope="col"><?php echo date('d/m/Y', strtotime($myRequest_End)) ?></td>
                                <td scope="col"><?php echo $myRequest_Days ?></td>
                                <td scope="col"><?php echo $authorization ?></td>
                                <?php
                                if ($myRequest_Authorization == 1 || $myRequest_Authorization == 2) {
                                ?>
                                    <td scope="col">
                                        <a href="process/download.php?id=<?php echo $myRequest_Id ?>" type="button" class="btn btn-sm">
                                            <i class="bi bi-file-earmark-arrow-down-fill" style="color: #000"></i>
                                        </a>
                                    </td>
                                <?php
                                } else {
                                ?>
                                    <td scope="col">
                                        <a href="process/removeVac.php?id=<?php echo $myRequest_Id ?>" type="button" class="btn btn-sm btn-danger">
                                            Eliminar
                                        </a>
                                    </td>
                                <?php
                                }
                                ?>
                            </tr>
                        </tbody>

                    <?php
                    }
                    ?>
                </table>
            <?php
                //En caso de que aún no haya realizado solicitudes
            } else {
            ?>
                <div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>Aún no ha realizado solicitudes de Vacaciones</div>
                </div>
            <?php
            }

            ?>
        </div> <!--Cierra Div mis solicitudes-->
    </div>

    <!--Sweet Alert Envío-->
    <?php
    if ($flag_send == 1) {
    ?>
        <div class="container">
            <script type="text/javascript">
                swal({
                    title: "<?php echo $vacations_title . ' ' . $term; ?>",
                    text: "<?php echo $vacations_message; ?>",
                    icon: "<?php echo $vacationes_icon; ?>",
                    button: "Volver",
                }).then(function() {
                    window.location = "request_days.php?id=<?php echo $user_active ?>";
                });
            </script>
        </div>
    <?php
    }
    ?>

</body>

</html>