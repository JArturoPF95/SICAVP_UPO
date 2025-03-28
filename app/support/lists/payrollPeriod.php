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
$alert = '
<div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>Aún no tiene Registros en esta base</div>
</div>';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periodos de Nómina</title>
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
    <h4 class="my-3">Periodos de Nómina</h4>
    <div class="container"> <!--Div Principal-->

        <?php
        $sqlpayrollPeriod = "SELECT * FROM payroll_period WHERE YEAR = '$year'";
        $resultpayrollPeriod = $mysqli->query($sqlpayrollPeriod);
        if ($resultpayrollPeriod->num_rows > 0) {
        ?>
            <table id="myTable" class="table table-hover table-bordered table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col" class="text-white table-primary">Código</th>
                        <th scope="col" class="text-white table-primary">Descripción</th>
                        <th scope="col" class="text-white table-primary">Fecha Inicio</th>
                        <th scope="col" class="text-white table-primary">Fecha Fin</th>
                        <th scope="col" class="text-white table-primary">Año</th>
                        <th scope="col" class="text-white table-primary">Mes</th>
                        <th scope="col" class="text-white table-primary">Bimestre</th>
                        <th scope="col" class="text-white table-primary">Estatus</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    while ($rowpayrollPeriod = $resultpayrollPeriod->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo $rowpayrollPeriod['CODE'] ?></td>
                            <td><?php echo $rowpayrollPeriod['DESCRIPTION'] ?></td>
                            <td><?php echo date('d/m/Y', strtotime($rowpayrollPeriod['START_DATE'])) ?></td>
                            <td><?php echo date('d/m/Y', strtotime($rowpayrollPeriod['END_DATE'])) ?></td>
                            <td><?php echo $rowpayrollPeriod['YEAR'] ?></td>
                            <td><?php echo $rowpayrollPeriod['MONTH'] ?></td>
                            <td><?php echo $rowpayrollPeriod['BIMESTER'] ?></td>
                            <td><?php echo $rowpayrollPeriod['STATUS'] ?></td>
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
    </div> <!-- Cierra Div Principal-->

    <!-- Modal Carga Layouts Área-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Periodos de Nómina Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadPayrollPeriod.php" method="post" enctype="multipart/form-data">
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