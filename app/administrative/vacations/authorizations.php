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

$start_date = '';
$end_date = '';
$first_day = '';
$last_day = '';
$idVac = '';

$bg = 'bg-info';
$authorization = '';

require 'process/query_vacations.php';

$sql_getRequest;
$result_getRequest = $mysqli->query($sql_getRequest);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Solicitudes</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
</head>

<body>
    <h4 class="my-3">Autorizar Vacaciones</h4>
    <div class="container">
        <div class="row my-2">
            <?php
            if ($result_getRequest->num_rows > 0) {
            ?>
                <table class="table table-hover table-bordered table-sm">
                    <thead class="text-white text-center table-primary">
                        <tr>
                            <th hidden scope="col">ID Solicitud</th>
                            <th scope="col">Periodo</th>
                            <th scope="col">Colaborador</th>
                            <th scope="col">Fecha de Inicio</th>
                            <th scope="col">Fecha de Término</th>
                            <th scope="col">Días</th>
                            <th scope="col">Estatus</th>
                            <th scope="col">Autorizar</th>
                        </tr>
                    </thead>

                    <?php
                    while ($row_teamRequest = $result_getRequest->fetch_assoc()) {
                        $teamRequest_id = $row_teamRequest['requestId'];
                        $teamRequest_employed = $row_teamRequest['ID_NOM'];
                        $teamRequest_name = $row_teamRequest['NAME'];
                        $teamRequest_term = $row_teamRequest['REQUEST_TERM'];
                        $teamRequest_date = $row_teamRequest['REQUEST_DATE'];
                        $teamRequest_start = $row_teamRequest['START_DATE'];
                        $teamRequest_end = $row_teamRequest['END_DATE'];
                        $teamRequest_days = $row_teamRequest['DAYS_REQUESTED'];
                        $teamRequest_authorization = $row_teamRequest['AUTHORIZATION_FLAG'];

                        if ($teamRequest_authorization == 0) {
                            $authorization = 'Pendiente';
                            $bg = 'table-warning';
                        } elseif ($teamRequest_authorization == 1) {
                            $authorization = 'Autorizado';
                            $bg = 'table-success';
                        } else {
                            $authorization = 'Rechazado';
                            $bg = 'table-danger';
                        }
                    ?>

                        <tbody class="text-center <?php echo $bg ?>">
                            <tr>
                                <td hidden scope="col"> <input type="text" name="idRequest" value="<?php echo $teamRequest_id ?>">
                            </tr>
                            <td scope="col" class="fs-6"><?php echo $teamRequest_term ?></td>
                            <td scope="col" class="fs-6"><?php echo $teamRequest_employed . ' - ' . $teamRequest_name ?></td>
                            <td scope="col" class="fs-6"><?php echo date("d/m/Y", strtotime($teamRequest_start)) ?></td>
                            <td scope="col" class="fs-6"><?php echo date("d/m/Y", strtotime($teamRequest_end)) ?></td>
                            <td scope="col" class="fs-6"><?php echo $teamRequest_days ?></td>
                            <td scope="col" class="fs-6"><?php echo $authorization ?></td>
                            <td>
                                <?php
                                if ($teamRequest_authorization == '0') {
                                ?>
                                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <button type="submit" name="authorization" value="1" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="<?php echo $teamRequest_id ?>">Sí</button>
                                        &nbsp;
                                        <button type="submit" name="authorization" value="2" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="<?php echo $teamRequest_id ?>">No</button>
                                    </div>
                                <?php
                                }
                                ?>
                            </td>
                            </tr>
                        </tbody>

                    <?php
                    }
                    ?>
                </table>
            <?php
            } else {
            ?>
                <div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                    </svg>
                    <div>Aún no han solicitado vacaciones</div>
                </div>
            <?php
            }

            ?>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="process/authorize.php" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Autorización</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div hidden class="mb-3" id="body1">
                            <label for="recipient-name" class="col-form-label">ID Solicitud:</label>
                            <input type="text" name="vacationId" class="form-control" id="recipient-name">
                        </div>
                        <div hidden class="mb-3" id="body2">
                            <label for="recipient-name" class="col-form-label">Autorización:</label>
                            <input type="text" name="authorization" class="form-control" id="recipient-name">
                        </div>
                        <div class="mb-3" id="body3">
                            <label for="message-text" class="col-form-label">Comentarios:</label>
                            <textarea class="form-control" name="comment" id="message-text" placeholder="Obligatorio si se rechazó"></textarea>
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
        const exampleModal = document.getElementById('exampleModal');
        if (exampleModal) {
            exampleModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const recipient = button.getAttribute('data-bs-whatever')
                const recipient2 = button.getAttribute('value')

                const modalTitle = exampleModal.querySelector('.modal-title')
                const modalBodyInput = exampleModal.querySelector('#body1 input')
                const modalBodyInput2 = exampleModal.querySelector('#body2 input')

                const modalTextarea = exampleModal.querySelector('#message-text')

                modalBodyInput.value = recipient
                modalBodyInput2.value = recipient2

                // Check if the value of #body2 is 2
                if (recipient2 === '2') {
                    modalTextarea.setAttribute('required', 'required');
                } else {
                    modalTextarea.removeAttribute('required');
                }
            });
        }
    </script>

</body>

</html>