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

$alert = '
<div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>Aún no tiene Registros en esta base</div>
</div>';

$sqlPosition = "SELECT * FROM code_position POS
INNER JOIN code_area ARE ON ARE.CODE_AREA = POS.CODE_AREA
INNER JOIN code_jobs JOB ON JOB.CODE_JOB = POS.CODE_JOB
INNER JOIN code_department DEP ON DEP.CODE_DEPRTMENT = POS.CODE_DEPARTMENT
INNER JOIN code_sesion SES ON SES.CODE_SESION_NOM = POS.CODE_NOM_SESSION
WHERE CODE_POSITION != '00000'";
$resultPosition = $mysqli->query($sqlPosition);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Áreas</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <!--Header con buscador de usuario y opciónes para alta-->
    <header>
        <div class="px-3 py-2 border-bottom">
            <div class="px-3 mb-3">
                <div class="container d-flex flex-wrap justify-content-end">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Carga Archivo &nbsp; <i class="bi bi-upload"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <h4 class="my-3">Posiciones Administrativas</h4>
    <?php
    if ($resultPosition->num_rows > 0) {
    ?>
        <table id="myTable" class="table table-hover table-bordered">
            <thead class="text-center">
                <tr>
                    <th scope="col" class="text-white table-primary">Clave</th>
                    <th scope="col" class="text-white table-primary">Posición</th>
                    <th scope="col" class="text-white table-primary">Puesto</th>
                    <th scope="col" class="text-white table-primary">Sesión</th>
                    <th scope="col" class="text-white table-primary">Departamento</th>
                    <th scope="col" class="text-white table-primary">Área</th>
                    <th scope="col" class="text-white table-primary">Supervisor</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                while ($rowPosition = $resultPosition->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $rowPosition['CODE_POSITION'] ?></td>
                        <td><?php echo $rowPosition['POSITION_DESCRIPTION'] ?></td>
                        <td><?php echo $rowPosition['JOB_NAME'] ?></td>
                        <td><?php echo $rowPosition['NOM_SESION'] ?></td>
                        <td><?php echo $rowPosition['DEPARTMENT'] ?></td>
                        <td><?php echo $rowPosition['NAME_AREA'] ?></td>
                        <td><?php echo $rowPosition['BOSS_POSITION'] ?></td>
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

    <!-- Modal Carga Layouts Área-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Posiciones Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadPositions.php" method="post" enctype="multipart/form-data">
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

</body>

</html>