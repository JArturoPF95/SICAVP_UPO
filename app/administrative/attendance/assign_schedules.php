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



$week = '';

$sDate = '';

$eDate = '';

$empID = '';

$empNm = '';

$year = date('Y');

$month = date('m');

$emp = '';

$index = 0;



// Datos del formulario para seleccionar el mes

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['month'])) {

        $month = $_POST['month'];

    } else {

        $month = date('m');

    }



    if (isset($_POST['emp'])) {

        $emp = $_POST['emp'];

    } else {

        $emp = '';

    }

}
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['m'])) {

        $month = $_GET['m'];

    } else {

        $month = date('m');

    }



    if (isset($_GET['e'])) {

        $emp = $_GET['e'];

    } else {

        $emp = '';

    }

}



?>



<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Asignar Horarios</title>

    <script src="../../../static/js/popper.min.js"></script>

    <script src="../../../static/js/bootstrap.min.js"></script>

    <script src="../../../sweetalert2.min.js"></script>

    <link rel="stylesheet" href="../../../sweetalert2.min.css">

    <link rel="stylesheet" href="../../../static/css/bootstrap.css">

    <link rel="stylesheet" href="../../../static/css/styles/tables.css">

</head>



<body>



    <h4 class="my-3">Asignación de Horarios</h4>



    <div class="container-fluid"> <!-- Div principal -->

        <div class="row my-3"> <!-- Row de selección mes -->



            <!-- Formulario para elegir el mes a asignar horarios -->

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

                <div class="row g-3 align-items-center">

                    <div class="col-auto">

                        <label for="inputPassword6" class="col-form-label">Mes:</label>

                    </div>

                    <div class="col-auto">

                        <select class="form-select" aria-label="Default select example" name="month">

                            <option selected disabled>Mes</option>

                            <?php



                            //Obtener los meses

                            $sqlGetMonth = "SELECT * FROM code_month";

                            $resultGetMonth = $mysqli->query($sqlGetMonth);

                            if ($resultGetMonth->num_rows > 0) {

                                while ($rowMonth = $resultGetMonth->fetch_assoc()) {

                            ?>

                                    <option value="<?php echo $rowMonth['CODE_MONTH'] ?>"><?php echo $rowMonth['DESCRIPTION'] ?></option>;

                            <?php

                                }

                            }

                            ?>

                        </select>

                    </div>

                    <div class="col-auto">

                        <label for="inputPassword6" class="col-form-label">Empleado:</label>

                    </div>

                    <div class="col-auto">

                        <select class="form-select" aria-label="Default select example" name="emp">

                            <option selected disabled>Empleado</option>

                            <?php



                            //Obtener los meses

                            $sqlGetEmp = "SELECT * FROM employed INNER JOIN users ON USER = ID_NOM WHERE SUPERVISOR_ID = '$user_active' AND SEPARATION_FLAG = '0'";

                            $resultGetEmp = $mysqli->query($sqlGetEmp);

                            if ($resultGetEmp->num_rows > 0) {

                                while ($rowEmp = $resultGetEmp->fetch_assoc()) {

                            ?>

                                    <option value="<?php echo $rowEmp['ID_NOM'] ?>"><?php echo $rowEmp['NAME'].' '.$rowEmp['LAST_NAME'].' '.$rowEmp['LAST_NAME_PREFIX'] ?></option>;

                            <?php

                                }

                            }

                            ?>

                        </select>

                    </div>

                    <div class="col-auto">

                        <button type="submit" class="btn btn-primary">Seleccionar</button>

                    </div>

                </div>

            </form>



        </div> <!-- Cierra Row de selección mes -->



        <div class="row my-3"> <!-- row para tabla de asignación -->



            <table class="table">

                <thead>

                    <tr class="table-primary">

                        <th scope="col">ID Emp. </th>

                        <th scope="col">Nombre</th>

                        <th scope="col">Fecha Inicio</th>

                        <th scope="col">Fecha Fin</th>

                        <th scope="col">Horario</th>

                    </tr>

                </thead>

                <form action="process/assign_schedules.php" method="post" class="needs-validation" novalidate>

                    <tbody>



                        <?php



                        //Consulta para obtener nombre y ID del colaborador

                        $sqlGetEmployed = "SELECT DISTINCT ID_NOM, CONCAT(LAST_NAME,' ',LAST_NAME_PREFIX,' ',NAME) NAME FROM employed 

    INNER JOIN users ON USER = ID_NOM WHERE ID_NOM = '$emp'";

                        $resultGetEmployed = $mysqli->query($sqlGetEmployed);

                        if ($resultGetEmployed->num_rows > 0) {

                            while ($rowGetEmp = $resultGetEmployed->fetch_assoc()) {



                                $empID = $rowGetEmp['ID_NOM'];

                                $empNm = $rowGetEmp['NAME'];



                                // Consulta para obtener el calendario

                                $sqlGetWeekDates = "SELECT DISTINCT CAL.YEAR, CAL.WEEK

    , (SELECT CAL2.CALENDAR_DATE FROM calendar CAL2 WHERE CAL2.YEAR = CAL.YEAR AND CAL2.WEEK = CAL.WEEK ORDER BY CAL2.CALENDAR_DATE ASC LIMIT 1) START_DATE

    , (SELECT CAL2.CALENDAR_DATE FROM calendar CAL2 WHERE CAL2.YEAR = CAL.YEAR AND CAL2.WEEK = CAL.WEEK ORDER BY CAL2.CALENDAR_DATE DESC LIMIT 1) END_DATE

    FROM calendar CAL WHERE YEAR = '$year' AND MONTH(CALENDAR_DATE) = '$month';";

                                $resultGetWeekDates = $mysqli->query($sqlGetWeekDates);

                                if ($resultGetWeekDates->num_rows > 0) {

                                    while ($rowDates = $resultGetWeekDates->fetch_assoc()) {



                                        $year = $rowDates['YEAR'];

                                        $week = $rowDates['WEEK'];

                                        $sDate = $rowDates['START_DATE'];

                                        $eDate = $rowDates['END_DATE'];

                                        $index++;



                        ?>

                                        <tr>

                                            <input hidden type="text" name="assign[<?php echo $index ?>][syear]" value="<?php echo $year ?>">

                                            <input hidden type="text" name="assign[<?php echo $index ?>][sweek]" value="<?php echo $week ?>">

                                            <input hidden type="text" name="assign[<?php echo $index ?>][empID]" value="<?php echo $empID ?>">

                                            <input hidden type="text" name="assign[<?php echo $index ?>][empNm]" value="<?php echo $empNm ?>">

                                            <input hidden type="text" name="assign[<?php echo $index ?>][sDate]" value="<?php echo $sDate ?>">

                                            <input hidden type="text" name="assign[<?php echo $index ?>][eDate]" value="<?php echo $eDate ?>">

                                            <td scope="col"><?php echo $empID ?></td>

                                            <td scope="col"><?php echo $empNm ?></td>

                                            <td scope="col"><?php echo DATE('d/m/Y', strtotime($sDate)) ?></td>

                                            <td scope="col"><?php echo DATE('d/m/Y', strtotime($eDate)) ?></td>



                                            <?php



                                            //Validamos si ya cuenta con horario asignado, si sí generamos aopción de actualizar

                                            $sqlVal = "SELECT DISTINCT ASCH.ASSIGNMENT_ID, ASCH.SCHEDULE CODE, CSH.DAYTRIP DAYTRIP, CSH.SCHEDULE DESCRIP FROM assigned_schedule ASCH 

                                LEFT OUTER JOIN code_schedule CSH ON CSH.CODE_NOM = ASCH.SCHEDULE

                                WHERE ASCH.YEAR = '$year' AND ASCH.WEEK = '$week' AND ASCH.ID_NOM = '$empID';";

                                            $resultVal = $mysqli->query($sqlVal);

                                            if ($resultVal->num_rows > 0) {

                                                while ($rowVal = $resultVal->fetch_assoc()) {

                                                    $idAssign = $rowVal['ASSIGNMENT_ID'];

                                                    $codeSchedule = $rowVal['CODE'];

                                                    $scheduleDesc = $rowVal['DESCRIP'];
                                                    $scheduleDaytrip = $rowVal['DAYTRIP'];



                                            ?>

                                                    <td hidden><input type="text" name="assign[<?php echo $index ?>][assign]" value="<?php echo $idAssign ?>"></td>
                                                    <td hidden><input type="text" name="assign[<?php echo $index ?>][smonth]" value="<?php echo $month ?>"></td>
                                                    <td hidden><input type="text" name="assign[<?php echo $index ?>][semp]" value="<?php echo $emp ?>"></td>

                                                    <td scope="col">

                                                        <select class="form-select" id="validationCustom04" aria-label="Default select example" name="assign[<?php echo $index ?>][nsche]" required>

                                                            <option selected value="<?php echo $codeSchedule ?>"><?php echo $scheduleDaytrip . ' - ' . $scheduleDesc ?></option>

                                                            <?php

                                                            // Consulta para obtener horarios

                                                            $sqlGetSchedules = "SELECT * FROM code_schedule WHERE OFICIAL_SCHEDULE != '' AND CODE_NOM != '$codeSchedule'";

                                                            $resultGetSchedules = $mysqli->query($sqlGetSchedules);

                                                            if ($resultGetSchedules->num_rows > 0) {

                                                                while ($rowSchedules = $resultGetSchedules->fetch_assoc()) {



                                                                    echo '<option value="' . $rowSchedules['CODE_NOM'] . '">' . $rowSchedules['DAYTRIP'] . ' - ' . $rowSchedules['SCHEDULE'] . '</option>';

                                                                }

                                                            }

                                                            ?>

                                                        </select>

                                                    </td>



                                                <?php



                                                }

                                            } else { //El registro es nuevo



                                                ?>



                                                <td scope="col">

                                                    <select class="form-select" id="validationCustom04" aria-label="Default select example" name="assign[<?php echo $index ?>][nsche]" required>

                                                        <option selected disabled value=""> Seleccionar Horario </option>

                                                        <?php

                                                        // Consulta para obtener horarios

                                                        $sqlGetSchedules = "SELECT * FROM code_schedule WHERE OFICIAL_SCHEDULE != ''";

                                                        $resultGetSchedules = $mysqli->query($sqlGetSchedules);

                                                        if ($resultGetSchedules->num_rows > 0) {

                                                            while ($rowSchedules = $resultGetSchedules->fetch_assoc()) {



                                                                echo '<option value="' . $rowSchedules['CODE_NOM'] . '">'. $rowSchedules['DAYTRIP'] . ' - ' . $rowSchedules['SCHEDULE'] . '</option>';

                                                            }

                                                        }

                                                        ?>

                                                    </select>

                                                </td>



                                            <?php

                                            }

                                            ?>





                                        </tr>

                        <?php



                                    }

                                }

                            }

                        } // Termina Consulta para obtener el calendario

                        ?>



                        <tr class="text-end">

                            <td colspan="4"></td>

                            <td scope="col">

                                <div class="btn-group" role="group" aria-label="Default button group">

                                    <button class="btn btn-primary" type="submit" name="action" value="insert">Asignar Nuevo</button> &nbsp; &nbsp;

                                    <button class="btn btn-warning" type="submit" name="action" value="update">Actualizar Existentes</button>

                                </div>

                            </td>

                            </td>

                        </tr>

                    </tbody>



                </form>

            </table>



        </div> <!-- Cierra row para tabla de asignación -->



    </div> <!-- Cierra div principal -->



    <script>

        // Example starter JavaScript for disabling form submissions if there are invalid fields

        (() => {

            'use strict'



            // Fetch all the forms we want to apply custom Bootstrap validation styles to

            const forms = document.querySelectorAll('.needs-validation')



            // Loop over them and prevent submission

            Array.from(forms).forEach(form => {

                form.addEventListener('submit', event => {

                    if (!form.checkValidity()) {

                        event.preventDefault()

                        event.stopPropagation()

                    }



                    form.classList.add('was-validated')

                }, false)

            })

        })()

    </script>



</body>



</html>