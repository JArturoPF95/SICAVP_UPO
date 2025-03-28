<?php



date_default_timezone_set('America/Mexico_City');

session_start();

if (!isset($_SESSION['usuario'])) {

    header('Location: ../../index.php');

    exit();

} else {

    $user_name = $_SESSION['user_name'];

    $user_active = $_SESSION['usuario'];

    $user_payroll = $_SESSION['payroll'];

    $user_access = $_SESSION['access_lev'];

}



require_once '../logic/conn.php';

$payrollPeriodID = '';

$unassigned = 0;

$i = 0;

$unassignedArray = array();



$sqlYesterday = "SELECT DATE_ADD(NOW(), INTERVAL -1 DAY) YEST";

$resultYesterday = $mysqli->query($sqlYesterday);

if ($resultYesterday->num_rows > 0) {

    while ($rowY = $resultYesterday->fetch_assoc()) {

        $yesterday = date('d/m/Y', strtotime($rowY['YEST']));

    }

}

$sqltoday = "SELECT DATE(NOW()) TODAY;";

$resulttoday = $mysqli->query($sqltoday);

if ($resulttoday->num_rows > 0) {

    while ($rowT = $resulttoday->fetch_assoc()) {

        $today = date('d/m/Y', strtotime($rowT['TODAY']));

    }

}



?>

<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registro</title>

    <script src="../../static/js/popper.min.js"></script>

    <script src="../../static/js/bootstrap.min.js"></script>

    <script src="../../sweetalert2.min.js"></script>

    <link rel="stylesheet" href="../../sweetalert2.min.css">

    <link rel="stylesheet" href="../../static/css/bootstrap.css">

</head>



<body>



    <div class="container-fluid card border-light mb-3">

        <div class="card-header border-light mb-3 text-center h4">

            Bienvenido...

        </div>



        <?php

        //Validamos las solicitudes que tiene el supervisor



        if ($user_access == '2') {

        ?>

            <div class="btn-group" role="group" aria-label="Basic example" style="width: 35rem;">



                <button type="submit" style="width: 10rem;" class="btn btn-sm btn-warning position-relative" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $user_active . '">Justificar</button>

                &nbsp; &nbsp;

                <?php

                $sqlJustify = "SELECT COUNT(EMP.ID_NOM) REQUESTS FROM employed EMP

                                INNER JOIN biometrictimeclock ADA ON EMP.ID_NOM = ADA.ID_NOM

                                WHERE EMP.SUPERVISOR_ID = '$user_active' AND ADA.JUSTIFY = 'P'";

                $resultJustify = $mysqli->query($sqlJustify);

                if ($resultJustify->num_rows > 0) {

                    while ($rowJustify = $resultJustify->fetch_assoc()) {

                        $requestJustify = $rowJustify['REQUESTS'];

                ?>

                        <a href="reports/justify_delays.php" type="button" style="width: 10rem;" class="btn btn-sm btn-primary position-relative">

                            Justificaciones

                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                                <?php echo $requestJustify ?>

                                <span class="visually-hidden">unread messages</span>

                            </span>

                        </a>

                        &nbsp; &nbsp;

                    <?php

                    }

                }

                $sqlVacations = "SELECT COUNT(requestId) VACATIONS FROM vacation_request WHERE IMMEDIATE_BOSS = '$user_active' AND AUTHORIZATION_FLAG = '0' AND DAYS_REQUESTED != '0'";

                $resultVacations = $mysqli->query($sqlVacations);

                if ($resultVacations->num_rows > 0) {

                    while ($rowVacations = $resultVacations->fetch_assoc()) {

                        $requestVacations = $rowVacations['VACATIONS'];

                    ?>

                        <a href="vacations/authorizations.php" type="button" style="width: 10rem;" class="btn btn-sm btn-primary position-relative">

                            Vacaciones

                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                                <?php echo $requestVacations ?>

                                <span class="visually-hidden">unread messages</span>

                            </span>

                        </a>

                <?php

                    }

                }



                ?>



            </div>

            <?php



            $year = date('Y');

            $cDay = date('w');



            if ($cDay == '1') {

                $week = date('W');

            } else {

                $week = date('W') + 1;

            }



            if ($cDay == '5' or $cDay == '6' or $cDay == '1') {

                $sqlWeeks = "SELECT DISTINCT CAL.YEAR, CAL.WEEK FROM calendar CAL WHERE CAL.YEAR = '$year' AND CAL.WEEK = '$week'";

                $resultWeeks = $mysqli->query($sqlWeeks);

                if ($resultWeeks->num_rows > 0) {

                    while ($rowWeeks = $resultWeeks->fetch_assoc()) {

                        $wYear = $rowWeeks['YEAR'];

                        $wWeek = $rowWeeks['WEEK'];



                        $sqlEmp = "SELECT DISTINCT EMP.ID_NOM, CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) NOMB , ASCH.ASSIGNMENT_ID, ASCH.SCHEDULE

                            FROM employed EMP 

                            LEFT OUTER JOIN assigned_schedule ASCH ON ASCH.ID_NOM = EMP.ID_NOM AND ASCH.WEEK = '$week'

                            WHERE EMP.SUPERVISOR_ID = '$user_active'";

                        $resultEmp = $mysqli->query($sqlEmp);

                        if ($resultEmp->num_rows > 0) {

                            while ($rowEmp = $resultEmp->fetch_assoc()) {

                                $eIdNm = $rowEmp['ID_NOM'];

                                $eName = $rowEmp['NOMB'];

                                $unassignedId = $rowEmp['ASSIGNMENT_ID'];

                                $unassignedSchedule = $rowEmp['SCHEDULE'];



                                if ($unassignedSchedule == '0' OR $unassignedId == '' OR $unassignedId == NULL) {

                                    $unassignedArray[$unassigned] = $eIdNm . ' - ' . $eName;

                                    $unassigned++;

                                }



                            }

                        }

                    }

                }

            }



            //echo $unassigned;

            if ($unassigned > 0) {

            ?>

            <div class="alert alert-danger alert-dismissible fade show my-3" role="alert">

                <h4 class="alert-heading"><strong>¡Horarios no asignados!</strong></h4>

                Los siguientes colaboradores no tienen un horario asignado para la próxima semana o se realizó incorrectamente. <br> Favor de realizar la asignación correcta.

                <hr>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                <?php

                for ($i = 0; $i < $unassigned; $i++) {

                ?>



                    <p class="mb-0"><?php echo $unassignedArray[$i] ?></p>

                <?php

                }

                ?>

            </div>



        <?php

            }

        } else {



        ?>

            <div class="btn-group" role="group" aria-label="Basic example" style="width: 12rem;">



                <button type="submit" style="width: 10rem;" class="btn btn-sm btn-warning position-relative" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $user_active . '">Justificar</button>

                &nbsp; &nbsp;



            </div>

        <?php

        }



        require_once 'attendance/process/query_attendance.php';



        $sql_get_incidence;

        $result_incidenceButton = $mysqli->query($sql_get_incidence);

        if ($result_incidenceButton->num_rows > 0) {

            while ($row_IB = $result_incidenceButton->fetch_assoc()) {

                $time_in = $row_IB['TIME_START'];

                $time_out = $row_IB['OUT_TIME'];

                $time_break = $row_IB['BREAK_TIME'];

            }

        } else {

            $time_in = '';

            $time_out = '';

            $time_break = '';

        }



        $sql_attendance_today;

        $result_attendance_today = $mysqli->query($sql_attendance_today);

        if ($result_attendance_today->num_rows > 0) {

            while ($row = $result_attendance_today->fetch_assoc()) {

                $today_date = $row['RECORD_DATE'];

                $today_checkin = $row['CHECK_IN'];

                $today_breakStart = $row['BREAK_START'];

                $today_breakEnd = $row['BREAK_END'];

                $today_checkOut = $row['CHECK_OUT'];

                $today_break = $row['BREAK_TIME'];

            }

        } else {

            $today_date = $getDate;



            $today_checkin = '';

            $today_breakStart = '';

            $today_breakEnd = '';

            $today_checkOut = '';

            $today_break = '';

        }



        ?>



        <div class="row my-3"> <!--Row Hoy-->



            <div class="border-light text-center h4">

                Asistencia <?php echo $today ?>

            </div>

            <div class="card-group">

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Hora de Entrada</h5>

                            <p class="card-text h4"><?php if ($today_checkin != null or $today_checkin != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($today_checkin, 0, 8); ?></p>

                    </div>

                </div>

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Salida a Comer</h5>

                            <p class="card-text h4"><?php if ($today_breakStart != NULL or $today_breakStart != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($today_breakStart, 0, 8); ?></p>

                    </div>

                </div>

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Regreso de Comer</h5>

                            <p class="card-text h4"><?php if ($today_breakEnd != null or $today_breakEnd != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($today_breakEnd, 0, 8); ?></p>

                    </div>

                </div>

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Hora de Salida</h5>

                            <p class="card-text h4"><?php if ($today_checkOut != NULL or $today_checkOut != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($today_checkOut, 0, 8); ?></p>

                    </div>

                </div>

            </div>

        </div> <!-- Cierra Row Hoy-->



        <?php



        $sql_attendance_yesterday;

        $result_attendance_yesterday = $mysqli->query($sql_attendance_yesterday);

        if ($result_attendance_yesterday->num_rows > 0) {

            while ($row = $result_attendance_yesterday->fetch_assoc()) {

                $yesterday_date = $row['RECORD_DATE'];

                $yesterday_checkin = $row['CHECK_IN'];

                $yesterday_breakStart = $row['BREAK_START'];

                $yesterday_breakEnd = $row['BREAK_END'];

                $yesterday_checkOut = $row['CHECK_OUT'];

                $yesterday_break = $row['BREAK_TIME'];

            }

        } else {

            $yesterday_date = $getDate;



            $yesterday_checkin = '';

            $yesterday_breakStart = '';

            $yesterday_breakEnd = '';

            $yesterday_checkOut = '';

            $yesterday_break = '';

        }

        ?>

        <div class="row my-3"> <!--Row Ayer-->



            <div class="border-light text-center h4">

                Asistencia <?php echo $yesterday ?>

            </div>

            <div class="card-group">

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Hora de Entrada</h5>

                            <p class="card-text h4"><?php if ($yesterday_checkin != null or $yesterday_checkin != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($yesterday_checkin, 0, 8); ?></p>

                    </div>

                </div>

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Salida a Comer</h5>

                            <p class="card-text h4"><?php if ($yesterday_breakStart != NULL or $yesterday_breakStart != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($yesterday_breakStart, 0, 8); ?></p>

                    </div>

                </div>

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Regreso de Comer</h5>

                            <p class="card-text h4"><?php if ($yesterday_breakEnd != null or $yesterday_breakEnd != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($yesterday_breakEnd, 0, 8); ?></p>

                    </div>

                </div>

                <div class="card text-center">

                    <div class="card-body">

                        <h4 class="card-title">Hora de Salida</h5>

                            <p class="card-text h4"><?php if ($yesterday_checkOut != NULL or $yesterday_checkOut != '') {

                                                        echo "Registro: ";

                                                    } else {

                                                        echo '<br>';

                                                    }

                                                    echo substr($yesterday_checkOut, 0, 8); ?></p>

                    </div>

                </div>

            </div>

        </div> <!-- Cierra Row Ayer-->





    </div>



    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <form action="attendance/process/justify.php" method="post">

                    <div class="modal-header">

                        <h1 class="modal-title fs-5" id="exampleModalLabel">Solicitar Justificación</h1>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>

                    <div class="modal-body">

                        <div hidden class="mb-3">

                            <label for="recipient-name" class="col-form-label">Empleado:</label>

                            <input type="text" disabled name="user" class="form-control" id="recipient-name" value="<?php $user_active ?>">

                        </div>



                        <div class="mb-3">

                            <label for="message-text" class="col-form-label">Fecha:</label>

                            <input type="date" class="form-control" name="justDate" id="message-text">

                        </div>

                        <div class="mb-3">

                            <label for="message-text" class="col-form-label">Justificación:</label>

                            <textarea class="form-control" name="comment" id="message-text"></textarea>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-primary">Solicitar</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <script type="text/javascript">

        const exampleModal = document.getElementById('exampleModal')

        if (exampleModal) {

            exampleModal.addEventListener('show.bs.modal', event => {

                // Button that triggered the modal

                const button = event.relatedTarget

                // Extract info from data-bs-* attributes

                const recipient = button.getAttribute('data-bs-whatever')

                // If necessary, you could initiate an Ajax request here

                // and then do the updating in a callback.



                // Update the modal's content.

                const modalTitle = exampleModal.querySelector('.modal-title')

                const modalBodyInput = exampleModal.querySelector('.modal-body input')



                //modalTitle.textContent = `New message to ${recipient}`

                modalBodyInput.value = recipient

            })

        }

    </script>



</body>



</html>