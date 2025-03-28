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

$send = 1;
$title = '';
$alert = '
<div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>Aún no tiene Registros en esta base</div>
</div>';

$sqlAccessLevels = "SELECT * FROM code_accesslevels INNER JOIN code_payroll ON PAYROLL = CODE_PAYROLL";
$resultAccess = $mysqli->query($sqlAccessLevels);

$sqlPayroll = "SELECT * FROM code_payroll WHERE CODE_PAYROLL != '00000'";
$resultPayroll = $mysqli->query($sqlPayroll);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $send = $_POST['send'];
    if ($send == 1 or $send == 2) {
        $title = 'Típo de Nómina';
    } else {
        $title = 'Niveles de Acceso';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puestos</title>
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
                            <!--Lista Accesos-->
                            <button name="send" type="submit" value="2" class="btn btn-primary btn-sm">
                                Nuevo Accesso &nbsp; <i class="bi bi-database-fill-up"></i>
                            </button>
                            <!--Formulario Accesos-->
                            <button name="send" type="submit" value="3" class="btn btn-primary btn-sm">
                                Niveles Acceso &nbsp; <i class="bi bi-person-badge"></i>
                            </button>
                            <button name="send" type="submit" value="1" class="btn btn-primary btn-sm">
                                Tipos de Nómina &nbsp; <i class="bi bi-person-vcard-fill"></i>
                            </button>
                            <!--Layout Tipos de Nómina-->
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Carga Archivo &nbsp; <i class="bi bi-upload"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <h4 class="my-3"><?php echo $title; ?></h4>
    <div class="container"> <!--Inicia dv principal-->

        <?php

        switch ($send) {
            case '1':
                if ($resultPayroll->num_rows > 0) {
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
                            while ($rowPayroll = $resultPayroll->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $rowPayroll['CODE_PAYROLL'] ?></td>
                                    <td><?php echo $rowPayroll['DESCRIPTION'] ?></td>
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
                break;
            case '2':
                ?>
                <form class="row g-3" method="post" action="process/insertAccess.php">
                    <div class="col-md-5">
                        <label for="inputCity" class="form-label">Descripción</label>
                        <input type="text" name="name" class="form-control" id="inputCity">
                    </div>
                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Tipo de Nómina</label>
                        <select name="payroll" id="inputState" class="form-select">
                            <?php
                            if ($resultPayroll->num_rows > 0) {
                                while ($rowPayroll = $resultPayroll->fetch_assoc()) {
                            ?>
                                    <option value="<?php echo $rowPayroll['CODE_PAYROLL'] ?>"><?php echo $rowPayroll['DESCRIPTION'] ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
                <?php
                break;
            case '3':
                if ($resultAccess->num_rows > 0) {
                ?>
                    <table id="myTable" class="table table-hover table-bordered table-sm">
                        <thead class="text-center">
                            <tr>
                                <th scope="col" class="text-white table-primary">Clave</th>
                                <th scope="col" class="text-white table-primary">Tipo de Nómina</th>
                                <th scope="col" class="text-white table-primary">Descripción</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            while ($rowAccess = $resultAccess->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $rowAccess['CODE_LEVEL'] ?></td>
                                    <td><?php echo $rowAccess['DESCRIPTION'] ?></td>
                                    <td><?php echo $rowAccess['LEVEL_DESCRIPTION'] ?></td>
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
                break;
            default:
                # code...
                break;
        }

        ?>

    </div> <!--Cierra Div principal-->

    <!-- Modal Carga Layouts Tipo de Nómina-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Tipos Nómina Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadPayrolls.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <input type="file" name="archivo_xlsx" accept=".xlsx" class="form-control" id="inputGroupFile01">
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