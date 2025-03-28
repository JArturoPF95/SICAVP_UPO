<?php

require '../logic/conn.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
    $user_sesion = $_SESSION['session'];
}

$codeDay = date('w');
$selectedDay = '';
$today = date('Y-m-d');
$send_flag = 0;
$payrollCode = '';
$time = date('H:i:s');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['program'])) {
        $program = $_POST['program'];
    } else {
        $program = '-';
    }
    $send_flag = $_POST['send'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo1.jpg" type="image/x-icon">
    <title>Clases del Día</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <div class="container-fluid">

        <form class="row g-3 my-1" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <input hidden type="text" name="send" value="1">
            <div class="col-auto">
                <select class="form-select" aria-label="Default select example" name="program">
                    <option selected disabled>Seleccionar Programa</option>
                    <?php
                    $sqlProgram = "SELECT DISTINCT PROGRAM FROM academic_schedules";
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
            <div class="col-auto">
                <?php
                $sqlJustify = "SELECT COUNT(AAT.AttendanceId) REQUESTS FROM academic_attendance AAT WHERE AAT.JUSTIFY = 'P'";
                $resultJustify = $mysqli->query($sqlJustify);
                if ($resultJustify->num_rows > 0) {
                    while ($rowJustify = $resultJustify->fetch_assoc()) {
                        $requestJustify = $rowJustify['REQUESTS'];
                ?>
                        <a href="reports/events.php" type="button" class="btn btn-primary position-relative">
                            Justificaciones
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $requestJustify ?>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </a>
                <?php
                    }
                }
                ?>
            </div>
            <div class="col-auto">        
            <form class="col-4 mx-2 my-2" role="search">
                <input type="search" id="searchInput" class="form-control" onkeyup="searchTable()" placeholder="Buscar" aria-label="Search">
            </form>

            </div>
        </form>
        <div class="row">

            <?php
            if ($send_flag == 1) {

                require_once 'reports/process/query_reports.php';

                $sql_get_teachers;
                $result_teachers = $mysqli->query($sql_get_teachers);
                if ($result_teachers->num_rows > 0) {
            ?>
                    <table class="table table-primary table-hover table-bordered table-sm" id="myTable">
                        <thead class="text-white text-center">
                            <tr>
                                <th scope="col">Campus</th>
                                <th scope="col">Docente</th>
                                <th scope="col">Grado</th>
                                <th scope="col">Modalidad</th>
                                <th scope="col">Carrera</th>
                                <th scope="col">Aula</th>
                                <th scope="col">Materia</th>
                                <th scope="col">Horario</th>
                                <th scope="col">Asistencia</th>
                                <th scope="col">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="table-light">
                            <?php
                            while ($rowTeachers = $result_teachers->fetch_assoc()) {
                                $teacher_id = $rowTeachers['PK'];
                                $teacher_csesion = $rowTeachers['CODE_SESSION'];
                                $teacher_sesion = $rowTeachers['ACADEMIC_SESSION'];
                                $teacher_idPwC = $rowTeachers['PERSON_CODE_ID'];
                                $teacher_name = $rowTeachers['DOC_NAME'];
                                $teacher_degree = $rowTeachers['DEGREE'];
                                $teacher_program = $rowTeachers['PROGRAM'];
                                $teacher_curriculum = $rowTeachers['CURRICULUM'];
                                $teacher_room = $rowTeachers['ROOM'];
                                $teacher_event = $rowTeachers['EVENT'];
                                $teacher_startClass = $rowTeachers['START_CLASS'];
                                $teacher_endClass = $rowTeachers['END_CLASS'];
                                $teacher_delay = $rowTeachers['DELAY_CLASS'];
                                $teacher_maxTime = $rowTeachers['MAX_DELAY_CLASS'];
                                $teacher_attendance = $rowTeachers['ATTENDANCE_START_CLASS'];
                                $teacher_attendanceStatus = $rowTeachers['ATTENDANCE_STATUS'];
                                $teacher_attendanceCodeStatus = $rowTeachers['ATTENDANCE_TINC'];
                                $teacher_attendanceClass = $rowTeachers['TEACHER_CLASS'];
                            ?>
                                <tr class="text-center">
                                    <input hidden type="text" class="form-control" id="validationCustom01" name="id" value="<?php echo $teacher_id ?>">
                                    <td><?php echo $teacher_sesion ?></td>
                                    <td><?php echo $teacher_idPwC . ' - ' . $teacher_name ?></td>
                                    <td><?php echo $teacher_degree ?></td>
                                    <td><?php echo $teacher_program ?></td>
                                    <td><?php echo $teacher_curriculum ?></td>
                                    <td><?php echo $teacher_room ?></td>
                                    <td><?php echo $teacher_event ?></td>
                                    <td><?php echo date('H:i:s', strtotime($teacher_startClass)) ?> - <?php echo date('H:i:s', strtotime($teacher_endClass)) ?></td>
                                    <?php
                                    if ($teacher_attendanceStatus == '') {
                                    ?>
                                        <td>--</td>
                                    <?php
                                    } else {
                                    ?>
                                        <td><?php echo date('H:i:s', strtotime($teacher_attendance)) ?></td>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if ($teacher_attendanceStatus == '' AND ($time >= $teacher_delay AND $time <= $teacher_maxTime) ) {
                                    ?>
                                        <td><button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="<?php echo $teacher_id ?>">Suplente</button></td>
                                    <?php
                                    } elseif ($teacher_attendanceCodeStatus == '11') {
                                    ?>
                                        <td>Suplente - <?php echo $teacher_attendanceClass ?></td>
                                    <?php
                                    } else {
                                    ?>
                                        <td><?php echo $teacher_attendanceStatus ?></td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                } else {
                ?>
                    <div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i> &nbsp; &nbsp;
                        <div>
                            <h4>Sin Registros. Favor de seleccionar otra opción</h4>
                        </div>
                    </div>
            <?php
                }
            }
            ?>

        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="attendance/process/substitute_teacher.php">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Colocar Suplente</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div hidden class="mb-3">
                            <label for="recipient-name" class="col-form-label">Recipient:</label>
                            <input type="text" name="class_id" class="form-control" id="recipient-name">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">ID PwC Docente:</label>
                            <input type="text" name='id_teacher' class="form-control" id="recipient-name">
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Tema:</label>
                            <textarea class="form-control" name="summary" id="message-text"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Enviar</button>
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

    function searchTable() {
        // Obtiene el valor del input de búsqueda
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");

        // Itera sobre todas las filas y oculta las que no coincidan con el término de búsqueda
        for (i = 0; i < tr.length; i++) {
            // Itera sobre todas las celdas de la fila
            var found = false;
            for (td of tr[i].getElementsByTagName("td")) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            if (found) {
                tr[i].style.display = ""; // Muestra la fila si coincide con el término de búsqueda
            } else {
                tr[i].style.display = "none"; // Oculta la fila si no coincide
            }
        }

    }
    </script>

</body>

</html>