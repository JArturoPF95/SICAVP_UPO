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



$send = '0';

$day = array();



$message = '';

$icon = '';



if ($_SERVER["REQUEST_METHOD"] == "POST") {



    $send = $_POST['send'];

    if ($send == '2') {



        $scheduleC = $_POST['schedule2'];



        foreach ($_POST['schedule'] as $index => $updSchedule) {

            $updDay = $updSchedule['day'] ?? '';

            $updStart = $updSchedule['start_time'] ?? '';

            $updEnd = $updSchedule['end_time'] ?? '';

            $updBreak = $updSchedule['break_time'] ?? '';            



            $updEnd = $updSchedule['end_time'] ?? '';

            $sqlUpdateBreakTime2 = "UPDATE admin_schedules SET BREAK_TIME = $updBreak, TIME_START = '$updStart', OUT_TIME = '$updEnd', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE CODE_SCHEDULE = '$scheduleC' AND CODE_DAY = '$updDay'";

            if ($mysqli->query($sqlUpdateBreakTime2)) {

                $message = 'Horario actualizado con éxito';

                $icon = 'success';

            } else {

                echo $sqlUpdateBreakTime2;

                $message = 'Error actualizando horario';

                $icon = 'error';

            }

        }

    }

}



$alert = '

<div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">

<i class="bi bi-exclamation-circle-fill"></i>

<div>Aún no tiene Registros en esta base</div>

</div>';





$sqlSchedule = "SELECT * FROM code_schedule WHERE OFICIAL_SCHEDULE != ''";

$resultSchedule = $mysqli->query($sqlSchedule);

?>





<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Jornadas</title>

    <script src="../../../static/js/popper.min.js"></script>

    <script src="../../../static/js/bootstrap.min.js"></script>

    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>

    <link rel="stylesheet" href="../../../static/css/bootstrap.css">

    <link rel="stylesheet" href="../../../static/css/styles/tables.css">

    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">

</head>



<body>



    <header>

        <div class="px-3 py-2 border-bottom">

            <div class="px-3 mb-3">

                <div class="container d-flex flex-wrap justify-content-end">

                    <div class="text-end">

                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNewSchedule">

                            Nuevo Horario &nbsp; <i class="bi bi-clock-fill"></i>

                        </button>

                        <!--Carga Masiva de Layout-->

                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalSchedules">

                            Carga Archivo Jornada &nbsp; <i class="bi bi-upload"></i>

                        </button>

                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalDaytrip">

                            Carga Archivo Grupo Jornda &nbsp; <i class="bi bi-upload"></i>

                        </button>

                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalGroups">

                            Carga Archivo Grupos &nbsp; <i class="bi bi-upload"></i>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </header>



    <h4 class="my-3">Horarios</h4>



    <div class="container"> <!--Div Principal-->

        <?php

        if ($resultSchedule->num_rows > 0) {

        ?>

            <table id="myTable" class="table table-hover table-bordered table-sm table-responsive" style="font-size: 13px;">

                <thead class="text-center">

                    <tr>

                        <th scope="col" class="text-white table-primary fs-6">Clave</th>

                        <th scope="col" class="text-white table-primary fs-6">Horario</th>
                        
                        <th scope="col" class="text-white table-primary fs-6">Jornada</th>

                        <th scope="col" class="text-white table-primary fs-6" colspan="2"></th>

                    </tr>

                </thead>

                <tbody class="text-center">

                    <?php

                    while ($rowSchedule = $resultSchedule->fetch_assoc()) {

                        $scheduleCode = $rowSchedule['CODE_NOM'];

                        $scheduleName = $rowSchedule['SCHEDULE'];

                        $scheduleDaytrip = $rowSchedule['DAYTRIP'];

                    ?>



                        <tr>

                                <td><?php echo $scheduleCode ?></td>

                                <td><?php echo $scheduleDaytrip ?></td>

                                <td><?php echo $scheduleName ?></td>

                            <td>

                                <div class="dropstart">

                                    <button class="btn btn-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">

                                        Modificar

                                    </button>

                                    <a href="process/deleteSchedule.php?id=<?php echo $scheduleCode ?>" class="btn btn-danger btn-sm" type="button">

                                        Eliminar

                                    </a>

                                    <ul class="dropdown-menu" style="width: 40rem;">

                                        <li class="dropdown-item text-center">

                                            <h6>Modificar Horario</h6>

                                        </li>

                                        <li class="dropdown-item text-center">

                                            

                                        <h6> <?php echo $scheduleName ?></h6>

                                        </li>

                                        <li class="px-3 text-center">

                                            <table class="table table-striped">

                                                <tbody>

                                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

                                                <input hidden type="text" name="send" value="2">

                                                <input hidden type="text" name="schedule2" value="<?php echo $scheduleCode ?>">

                                                <?php

                                                for ($dayCode = 0; $dayCode <= 6; $dayCode ++) { 



                                                    $sqlDays = "SELECT * FROM code_days WHERE CODE_DAY = '$dayCode'";

                                                    $resultDays = $mysqli -> query($sqlDays);

                                                    if ($resultDays -> num_rows > 0) {

                                                        while ($rowDays = $resultDays -> fetch_assoc()) {

                                                            $nameDay = $rowDays['NAME_DAY'];



                                                            $sqlScheduleDetail = "SELECT DISTINCT TIME_START, OUT_TIME, BREAK_TIME FROM admin_schedules WHERE CODE_SCHEDULE = '$scheduleCode' AND CODE_DAY = '$dayCode'";

                                                            $resultScheduleDetail = $mysqli -> query($sqlScheduleDetail);

                                                            if ($resultScheduleDetail -> num_rows > 0) {

                                                                while ($rowScheduleD = $resultScheduleDetail -> fetch_assoc()) {

                                                                    $timeStart = $rowScheduleD['TIME_START'];

                                                                    $timeEnd = $rowScheduleD['OUT_TIME'];

                                                                    $timeBreak = $rowScheduleD['BREAK_TIME'];

                                                                    ?>

                                                                    <tr>

                                                                        <td>

                                                                            <label for="disabledTextInput" class="form-label"><?php echo $nameDay ?></label>

                                                                            <input hidden type="text" name="schedule[<?php echo $dayCode ?>][day]" id="disabledTextInput" class="form-control form-control-sm" value="<?php echo $dayCode ?>">

                                                                        </td>

                                                                        <td>

                                                                            <label for="inputEmail4" class="form-label">Hora Entrada</label>

                                                                            <input type="time" name="schedule[<?php echo $dayCode ?>][start_time]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeStart)) ?>">

                                                                        </td>

                                                                        <td>

                                                                            <label for="inputEmail4" class="form-label">Hora Salida</label>

                                                                            <input type="time" name="schedule[<?php echo $dayCode ?>][end_time]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeEnd)) ?>">

                                                                        </td>

                                                                        <td>

                                                                            <label for="inputEmail4" class="form-label">Comida (min)</label>

                                                                            <input type="text" name="schedule[<?php echo $dayCode ?>][break_time]" class="form-control form-control-sm" value="<?php echo $timeBreak ?>">

                                                                        </td>

                                                                    </tr>

                                                                    <?php

                                                                }

                                                            }

                                                        }

                                                    }

                                                }

                                                ?>

                                                

                                                

                                            <tr class="flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0 my-3">

                                                <td><button type="submit" class="btn btn-lg btn-warning">Actualizar</button></td>

                                            </tr>

                                            </form>

                                            </tbody>

                                            </table>

                                        </li>

                                    </ul>

                                </div>

                            </td>

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

    </div> <!--Cierre Div principal-->



    <!-- Modal Carga Layouts Jornada-->

    <div class="modal fade" id="exampleModalSchedules" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Jornadas Nom2001</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <form action="csvLoads/loadSchedules.php" method="post" enctype="multipart/form-data">

                    <div class="modal-body">

                        <div class="input-group mb-3">

                            <input type="file" name="archivo_xlsx" accept=".csv" class="form-control" id="inputGroupFile01">

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" name="submit" class="btn btn-primary">Enviar</button>

                    </div>

                </form> 

            </div>

        </div>

    </div>



    <!-- Modal Carga Layouts Grupo Jornada-->

    <div class="modal fade" id="exampleModalDaytrip" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Grupo Jornada Nom2001</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <form action="csvLoads/loadDaytrip.php" method="post" enctype="multipart/form-data">

                    <div class="modal-body">

                        <div class="input-group mb-3">

                            <input type="file" name="archivo_xlsx" accept=".csv" class="form-control" id="inputGroupFile01">

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" name="submit" class="btn btn-primary">Enviar</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <!-- Modal Carga Layouts Grupos-->

    <div class="modal fade" id="exampleModalGroups" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Grupos Horarios Nom2001</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <form action="csvLoads/loadGroups.php" method="post" enctype="multipart/form-data">

                    <div class="modal-body">

                        <div class="input-group mb-3">

                            <input type="file" name="archivo_xlsx" accept=".csv" class="form-control" id="inputGroupFile01">

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" name="submit" class="btn btn-primary">Enviar</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <!-- Formulario para Nuevos Horarios -->

    <div class="modal modal-sheet fade modal-lg bg-body-secondary" id="modalNewSchedule" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content rounded-4 shadow">

                <div class="modal-header border-bottom-0">

                    <h1 class="modal-title fs-5">Nuevo Horario</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <table class="table table-striped">

                    <tbody>

                <form action="process/insertSchedule.php" method="post" enctype="multipart/form-data">

                    <div class="modal-body">

                        <tr>

                            <td colspan="2">

                                <label for="inputEmail4" class="form-label">Horario</label>

                                <input type="text" name="schedule_name" class="form-control form-control-md" placeholder="Ventas Vespertino (Informativo)" maxlength="40" required>

                            </td>

                            <td colspan="2">

                                <label for="inputEmail4" class="form-label">Jornada</label>

                                <input type="text" name="schedule_daytrip" class="form-control form-control-md" placeholder="Lun-Vie 10am-7pm, Sab 8am-12pm (Informativo)" maxlength="150" required>

                            </td>

                        </tr>

                        <?php

                    for ($dayCodeIns = 0; $dayCodeIns <= 6; $dayCodeIns ++) { 



                        $sqlDays2 = "SELECT * FROM code_days WHERE CODE_DAY = '$dayCodeIns'";

                        $resultDays2 = $mysqli -> query($sqlDays2);

                        if ($resultDays2 -> num_rows > 0) {

                            while ($rowDays2 = $resultDays2 -> fetch_assoc()) {

                                $nameDay2 = $rowDays2['NAME_DAY'];

                                ?>

                        <tr>

                            <td>

                                <label for="disabledTextInput" class="form-label"><?php echo $nameDay2 ?></label>

                                <input hidden type="text" name="schedule[<?php echo $dayCodeIns ?>][day]" id="disabledTextInput" class="form-control form-control-sm" value="<?php echo $dayCodeIns ?>">

                            </td>

                            <td>

                                <label for="inputEmail4" class="form-label">Hora Entrada</label>

                                <input type="time" name="schedule[<?php echo $dayCodeIns ?>][start_time]" class="form-control form-control-sm">

                            </td>

                            <td>

                                <label for="inputEmail4" class="form-label">Hora Salida</label>

                                <input type="time" name="schedule[<?php echo $dayCodeIns ?>][end_time]" class="form-control form-control-sm">

                            </td>

                            <td>

                                <label for="inputEmail4" class="form-label">Comida (min)</label>

                                <input type="text" name="schedule[<?php echo $dayCodeIns ?>][break_time]" class="form-control form-control-sm">

                            </td>

                        </tr>

                        <?php

                            }

                        }

                    }

                        ?>

                    </div>

                    <tr class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0">

                        <td><button type="submit" class="btn btn-lg btn-primary">Agregar</button></td>

                </tr>

                </form>

                </tbody>

                </table>

            </div>

        </div>

    </div>



    <?php

    if ($send == '1') {

        //echo $sqlUpdateTolerances;

    ?>

        <script type="text/javascript">

            swal({

                title: "Generar Tiempos de Comida",

                text: "<?php echo $message; ?>",

                icon: "<?php echo $icon ?>",

                button: "Volver",

            }).then(function() {

                window.location = "schedules.php?id=<?php echo $user_active ?>";

            });

        </script>

    <?php

    } elseif ($send == '2') {

    ?>

        <script type="text/javascript">

            swal({

                title: "Horario Actualizado con éxito",

                text: "<?php echo $message; ?>",

                icon: "<?php echo $icon ?>",

                button: "Volver",

            }).then(function() {

                window.location = "schedules.php?id=<?php echo $user_active ?>";

            });

        </script>

    <?php

    }

    ?>



    <script>

        const dropdownElementList = document.querySelectorAll('.dropdown-toggle')

        const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl))

    </script>



</body>



</html>