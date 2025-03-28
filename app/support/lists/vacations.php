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

$year = date('Y');
$today = date('Y-m-d');
$title = '';
$alert = '
<div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>Aún no tiene Registros en esta base</div>
</div>';

if (isset($option)) {
    $option = $_GET['option'];
} else {
    $option = '1';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $option = $_POST['option'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Días de Descanso</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <!--Header con menú de opciones-->
    <header>
        <div class="px-3 py-2 border-bottom">
            <div class="px-3 mb-3">
                <div class="container d-flex flex-wrap justify-content-end">
                    <div class="text-end">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                            <!--Lista días Feriados-->
                            <button name="option" type="submit" value="1" class="btn btn-primary btn-sm">
                                Días Feriados &nbsp; <i class="bi bi-calendar-week"></i>
                            </button>
                            <!--Formulario Vacaciones-->
                            <button name="option" type="submit" value="2" class="btn btn-primary btn-sm">
                                Vacaciones &nbsp; <i class="bi bi-calendar4-range"></i>
                            </button>
                            <!--Lista de Vacaciones-->
                            <a href="process/downloadVacations.php" type="submit" class="btn btn-secondary btn-sm">
                                Detalle de Vacaciones &nbsp; <i class="bi bi-download"></i>
                            </a>
                            <!--Layout Fechas Feriados-->
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Carga Archivo Días Feriados &nbsp; <i class="bi bi-upload"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container">
        <?php
        if ($option == '1') {
        ?>

            <h4 class="my-3">Días Feriados</h4>
            <?php
            $sqlRestDays = "SELECT * FROM calendar WHERE DAY_OF_REST = '1' AND YEAR = '$year'";
            $resultRestDays = $mysqli->query($sqlRestDays);
            if ($resultRestDays->num_rows > 0) {
            ?>
                <table id="myTable" class="table table-hover table-bordered table-sm">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" class="text-white table-primary">Fecha</th>
                            <th scope="col" class="text-white table-primary">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php
                        while ($rowDays = $resultRestDays->fetch_assoc()) {
                        ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($rowDays['CALENDAR_DATE'])) ?></td>
                                <td><?php echo $rowDays['DESCRIPTION'] ?></td>
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
        } elseif ($option == '2') {
            ?>
            <div class="container"> <!--Div Principal Vacaciones -->
                <div class="row">
                    <div class="col"> <!-- Div cards con vacaciones -->
                        <h4 class="my-3">Vacaciones</h4>
                        <div class="row my-3">
                            <div class="col-sm-6 mb-3 mb-sm-0"> <!-- Div Semana Santa -->
                                <div class="card text-bg-info">
                                    <div class="card-body text-center text-white">
                                        <h5 class="card-title">Semana Santa</h5>
                                        <?php
                                        $sqlValSS = "SELECT DISTINCT ADDDATE(REQUEST_DATE, INTERVAL 15 DAY) REQUEST_DATE, START_DATE, END_DATE FROM vacation_request WHERE COMMENTS = 'Semana Santa' AND REQUEST_TERM = '$year'";
                                        $resultSS = $mysqli->query($sqlValSS);
                                        if ($resultSS->num_rows > 0) {
                                            while ($rowSS = $resultSS->fetch_assoc()) {
                                        ?>

                                                <p class="card-text"><?php echo date('d/m/Y', strtotime($rowSS['START_DATE'])) . ' al ' . date('d/m/Y', strtotime($rowSS['END_DATE'])) ?></p>
                                                <?php if ($rowSS['START_DATE'] >= $today or $rowSS['REQUEST_DATE'] <= $today) { ?>
                                                    <a href="process/deleteVacations.php?id=SS" class="btn btn-danger btn-sm">Eliminar Días</a>
                                                <?php } ?>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <p class="card-text">Aún no hay Fecha de Vacaciones</p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div> <!-- Cierra div Semana Santa -->
                            <div class="col-sm-6"> <!--Div Navidad -->
                                <div class="card text-bg-info">
                                    <div class="card-body text-center text-white">
                                        <h5 class="card-title">Navidad / Fin de Año</h5>
                                        <?php
                                        $sqlValFA = "SELECT DISTINCT ADDDATE(REQUEST_DATE, INTERVAL 15 DAY) REQUEST_DATE, START_DATE, END_DATE FROM vacation_request WHERE COMMENTS = 'Navidad - Fin de Año' AND REQUEST_TERM = '$year'";
                                        $resultFA = $mysqli->query($sqlValFA);
                                        if ($resultFA->num_rows > 0) {
                                            while ($rowFA = $resultFA->fetch_assoc()) {
                                        ?>

                                                <p class="card-text"><?php echo date('d/m/Y', strtotime($rowFA['START_DATE'])) . ' al ' . date('d/m/Y', strtotime($rowFA['END_DATE'])) ?></p>
                                                <?php if ($rowFA['START_DATE'] >= $today or $rowFA['REQUEST_DATE'] <= $today) { ?>
                                                    <a href="process/deleteVacations.php?id=FN" class="btn btn-danger btn-sm">Eliminar Días</a>
                                                <?php } ?>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <p class="card-text">Aún no hay Fecha de Vacaciones</p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div> <!-- Cierra div Navidad -->
                        </div>
                    </div> <!-- Cierra div cards vacaciones -->
                </div>
                <div class="row">
                    <hr class="my-4">
                    <div class="col"> <!--Div Cargar Días de vacaciones -->
                        <h4 class="mb-3">Carga Días Vacaciones Institucionales</h4>
                        <form method="POST" action="process/generateVacations.php">
                            <div class="row mb-3">
                                <label for="inputEmail3" class="col-sm-4 col-form-label">Fecha Inicio</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" name="startDate" id="inputEmail3" value="<?php echo date('d/m/Y', strtotime($today)) ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputPassword3" class="col-sm-4 col-form-label">Fecha Final</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" name="endDate" id="inputPassword3" value="<?php echo date('d/m/Y', strtotime($today)) ?>">
                                </div>
                            </div>
                            <fieldset class="row mb-3">
                                <legend class="col-form-label col-sm-5 pt-0">Periodo Vacacional</legend>
                                <div class="col-sm-7">
                                    <div class="form-check">
                                        <?php
                                        $sqlVSS = "SELECT * FROM vacation_request WHERE REQUEST_TERM = '$year' AND COMMENTS = 'Semana Santa'";
                                        $resultVSS = $mysqli->query($sqlVSS);
                                        if ($resultVSS->num_rows > 0) {
                                        ?>
                                            <input id="semanaSanta" name="comment" value="Semana Santa" type="radio" class="form-check-input" disabled required>
                                        <?php
                                        } else {
                                        ?>
                                            <input id="semanaSanta" name="comment" value="Semana Santa" type="radio" class="form-check-input" required>
                                        <?php
                                        }
                                        ?>
                                        <label class="form-check-label" for="semanaSanta">Semana Santa</label>
                                    </div>
                                    <div class="form-check">
                                        <?php
                                        $sqlVSS = "SELECT * FROM vacation_request WHERE REQUEST_TERM = '$year' AND COMMENTS = 'Navidad - Fin de Año'";
                                        $resultVSS = $mysqli->query($sqlVSS);
                                        if ($resultVSS->num_rows > 0) {
                                        ?>
                                            <input id="navidad" name="comment" type="radio" value="Navidad - Fin de Año" class="form-check-input" disabled required>
                                        <?php
                                        } else {
                                        ?>
                                            <input id="navidad" name="comment" type="radio" value="Navidad - Fin de Año" class="form-check-input" required>
                                        <?php
                                        }
                                        ?>
                                        <label class="form-check-label" for="navidad">Fin de Año</label>
                                    </div>
                                </div>
                            </fieldset>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div> <!-- Cierra div cargar vacaciones -->
                    <div class="col"> <!--Div Parametrizar días de Anticipación -->
                        <h4 class="mb-3">Días de Anticipación para Solicitar Vacaciones</h4>
                        <form class="row g-3" method="POST" action="process/prevDays.php">
                            <div class="col-auto">
                                <label for="inputPassword2" class="visually-hidden">Días</label>
                                <?php
                                $sqlGetPrevDays = "SELECT DISTINCT PREV_DAYS FROM code_vacation";
                                $resultGetPrevDays = $mysqli->query($sqlGetPrevDays);
                                if ($resultGetPrevDays->num_rows > 0) {
                                    while ($rowPrevDays = $resultGetPrevDays->fetch_assoc()) {
                                ?>
                                        <input type="number" class="form-control" id="inputPassword2" name="prevDays" value="<?php echo $rowPrevDays['PREV_DAYS'] ?>" required>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <input type="number" class="form-control" id="inputPassword2" name="prevDays" value="0" required>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary mb-3">Enviar</button>
                            </div>
                        </form>
                    </div> <!--Cierra div parametro solicitar -->
                </div>
            </div> <!-- Cierra div principal Vacaciones -->

        <?php
        }
        ?>
    </div>

    <!-- Modal Carga Layouts Fechas de Descanso-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Días Festivos Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadDays.php" method="post" enctype="multipart/form-data" name="formulario">
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <input type="file" name="archivo_xlsx" accept=".xlsx" class="form-control" id="inputGroupFile01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary btn-formulario">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>