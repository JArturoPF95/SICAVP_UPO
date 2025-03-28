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

$sqlJIncidences = "SELECT * FROM code_incidence WHERE CODE_TINC != '00'";
$resultJIncidences = $mysqli->query($sqlJIncidences);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidencias</title>
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
                        <!--Carga Masiva de Layout-->
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Carga Archivo &nbsp; <i class="bi bi-upload"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <h4 class="my-3">Tipos de Faltas</h4>
    <div class="container"> <!--Inicia dv principal-->
        <?php
        if ($resultJIncidences->num_rows > 0) {
        ?>
            <table id="myTable" class="table table-hover table-bordered table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col" class="text-white table-primary">Clave</th>
                        <th scope="col" class="text-white table-primary">Descripción</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    while ($rowJIncidences = $resultJIncidences->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo $rowJIncidences['CODE_TINC'] ?></td>
                            <td><?php echo $rowJIncidences['DESCRIP_TINC'] ?></td>
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
    </div> <!--Cierra div principal-->

    <!-- Modal Carga Layouts Área-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Incidencias Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadIncidences.php" method="post" enctype="multipart/form-data">
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