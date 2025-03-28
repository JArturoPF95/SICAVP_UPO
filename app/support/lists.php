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
}


//Catálogos soporte
$urlList = [
    "area" => "lists/area.php?id=$user_active",
    "departamento" => "lists/department.php?id=$user_active",
    "puestos" => "lists/jobs.php?id=$user_active",
    "posiciones" => "lists/positions.php?id=$user_active",
    "perNomina" => "lists/payrollPeriod.php?id=$user_active",
    "incidencias" => "lists/incidences.php?id=$user_active",
    "campus" => "lists/sesions.php?id=$user_active",
    "horarios" => "lists/schedules.php?id=$user_active",
    "layouts" => "lists/layouts.zip",
    "vacaciones" => "lists/vacations.php?id=$user_active",
    "ips" => "lists/ip.php?id=$user_active",
    "nomina" => "lists/payroll.php?id=$user_active"
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <div class="d-flex gap-2 justify-content-center py-2">
        <table>
            <tbody>
                <tr class="mb-2">
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=area" style="width: 10rem;">Área</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=departamento" style="width: 10rem;">Departamento</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=puestos" style="width: 10rem;">Puestos</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=posiciones" style="width: 10rem;">Posiciones</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=perNomina" style="width: 10rem;">Periodo de Nómina</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=incidencias" style="width: 10rem;">Incidencias</a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex gap-2 justify-content-center py-2">
        <table>
            <tbody>
                <tr>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=campus" style="width: 10rem;">Campus</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=horarios" style="width: 10rem;">Horarios</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=nomina" style="width: 10rem;">Tipo de Nómina</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=ips" style="width: 10rem;">IPs</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=vacaciones" style="width: 10rem;">Días de Descanso</a></td>
                    <td class="px-2"><a class="btn btn-primary rounded-pill px-3 btn-sm" type="button" href="?catalogo=layouts" style="width: 10rem;">Layouts &nbsp; <i class="bi bi-download"></i> </a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4">
        <div class="row">
            <?php
            if (empty($_GET) || !isset($_GET['catalogo'])) {
                $list = "";
            ?>
                <iframe class="my-4 w-100" src=" <?php echo $list; ?>" id="myChart" style="width: 10rem; height: 70vh"></iframe>
            <?php
            } else {
                $opcion = $_GET['catalogo'];
                $list = $urlList[$opcion];
            ?>
                <iframe class="my-4 w-100" src=" <?php echo $list; ?>" id="myChart" style="width: 10rem; height: 70vh"></iframe>
            <?php
            }
            ?>
        </div>
    </main>
</body>

</html>