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


$bg = 'table-light';
$send = '1';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $send = $_POST['send'];
}


require 'academic/query.php';
$sqlUsers;
$result_users = $mysqli->query($sqlUsers);

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
    <link rel="stylesheet" href="../../static/css/styles/tables.css">
</head>

<body>
    <!--Header con buscador de usuario y opciónes para alta-->
    <header>
        <div class="px-3 py-2 border-bottom">
            <div class="px-3 mb-3">
                <div class="container d-flex flex-wrap justify-content-end">

                    <div class="text-end">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                            <!--Lista de Usuarios-->
                            <button type="submit" class="btn btn-primary" name="send" value="1">
                                Usuarios &nbsp; <i class="bi bi-person-lines-fill"></i>
                            </button>
                            <!--Carga Tolerancias-->
                            <button type="submit" class="btn btn-primary" name="send" value="2">
                                Tolerancia Horarios &nbsp; <i class="bi bi-clock-fill"></i>
                            </button>
                            <!--Lista de Campus PwC-->
                            <button type="submit" class="btn btn-primary" name="send" value="3">
                                Campus PwC &nbsp; <i class="bi bi-mortarboard-fill"></i>
                            </button>
                            <!--Usuarios Masivo-->
                            <a href="academic/userGenerate.php" class="btn btn-primary d-inline-flex align-items-center" type="button">
                                Genera Usuarios &nbsp; <i class="bi bi-person-fill-add"></i>
                            </a>
                            <!--Carga Masiva de Layout-->
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Horarios Docentes PwC &nbsp; <i class="bi bi-box-arrow-up"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <?php
    if ($send == '1') {
    ?>
        <!--Tabla de usuarios-->
        <h4 class="my-3 col-4">Docentes</h4>
        <form class="col-4 mx-2 my-2" role="search">
            <input type="search" id="searchInput" class="form-control" onkeyup="searchTable()" placeholder="Buscar" aria-label="Search">
        </form>
        <div class="d-flex gap-2 py-2">
            <?php
            if ($result_users->num_rows > 0) {
            ?>
                <table id="myTable" class="table table-primary table-hover table-bordered table-sm">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" class="text-white">Usuario</th>
                            <th scope="col" class="text-white">Nombre</th>
                            <th scope="col" class="text-white"></th>
                        </tr>
                    </thead>
                    <?php
                    while ($rowUsers = $result_users->fetch_assoc()) {
                        if ($rowUsers['SEPARATION_FLAG'] == '0') {
                            $bg = 'table-light';
                        } elseif ($rowUsers['SEPARATION_FLAG'] == '1') {
                            $bg = 'table-warning';
                        }
                    ?>
                        <tbody class="text-center">
                            <tr class="<?php echo $bg ?>">
                                <td><?php echo $rowUsers['USER'] ?></td>
                                <td><?php echo $rowUsers['NAME_DOC'] ?></td>
                                <td>
                                    <div class="btn-group dropend">
                                        <!--Droopdown de opciones-->
                                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Opciones
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-item"><a class="dropdown-item" href="academic/user_reset.php?id=<?php echo $rowUsers['USER'] ?>"><i class="bi bi-person-fill-gear"></i> &nbsp; Reestablecer Contraseña</a></li>
                                            <?php
                                            if ($rowUsers['SEPARATION_FLAG'] == '0') {
                                            ?>
                                                <li class="dropdown-item"><a class="dropdown-item" href="academic/user_lock.php?id=<?php echo $rowUsers['USER'] ?>"><i class="bi bi-person-fill-lock"></i> &nbsp; Bloquear Docente</a></li>
                                            <?php
                                            } elseif ($rowUsers['SEPARATION_FLAG'] == '1') {
                                            ?>

                                                <li class="dropdown-item"><a class="dropdown-item" href="academic/user_unlock.php?id=<?php echo $rowUsers['USER'] ?>"><i class="bi bi-person-fill-up"></i> &nbsp; Desbloquear Docente</a></li>
                                            <?php
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    <?php
                    }
                    ?>
                </table>
        </div>

    <?php
            } else {
    ?>
    <?php
            }
    ?>
    </div>
<?php
    } elseif ($send == '2') {
?>

    <h4 class="my-3 col-4">Tolerancias</h4>
    <div class="container text-center mb-2">
        <div class="row">
            <div class="col">
            </div>
            <div class="col">
                <!--Carga Masiva de Layout-->
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModalDPPWC">
                    Programas PwC &nbsp; <i class="bi bi-box-arrow-up"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-11 col-lg-11 d-flex justify-content-center py-2 px-5">
        <?php
        $sqlTolerance = "SELECT * FROM academic_tolerance";
        $resultTolerance = $mysqli->query($sqlTolerance);
        if ($resultTolerance->num_rows > 0) {
        ?>
            <table id="myTable" class="table table-primary table-hover table-bordered table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col" class="text-white">Nivel Programa</th>
                        <th scope="col" class="text-white">Minutos Inicio Anticipado</th>
                        <th scope="col" class="text-white">Minutos Tolerancia</th>
                        <th scope="col" class="text-white">Minutos Retardo</th>
                        <th scope="col" class="text-white">Minutos Conclusión Anticipada</th>
                        <td scope="col"></td>
                    </tr>
                </thead>
                <tbody class="table-light">
                    <?php
                    while ($rowTolerance = $resultTolerance->fetch_assoc()) {
                    ?>
                        <form action="academic/tolerance.php" method="post">
                            <tr>
                                <td hidden><input type="text" name="level" value="<?php echo $rowTolerance['CODE_DEGPRO'] ?>"></td>
                                <td><?php echo $rowTolerance['DESCRIPTION'] ?></td>
                                <td><input type="text" name="t_minIn" value="<?php echo $rowTolerance['MIN_TIME'] ?>"></td>
                                <td><input type="text" name="t_tolerance" value="<?php echo $rowTolerance['DELAY_CLASS'] ?>"></td>
                                <td><input type="text" name="t_delay" value="<?php echo $rowTolerance['MAX_CLASS'] ?>"></td>
                                <td><input type="text" name="t_minOut" value="<?php echo $rowTolerance['MIN_END'] ?>"></td>
                                <td><button type="submit" class="btn btn-secondary btn-sm" value="<?php echo $rowTolerance['CODE_DEGPRO'] ?>">Actualizar</button></td>
                            </tr>
                        </form>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
        ?>
    </div>
<?php
    } elseif ($send == '3') {
?>
    <!--Tabla de usuarios-->
    <h4 class="my-3 col-4">Campus PwC</h4>
    <div class="container text-center">
        <div class="row">
            <div class="col">
                <!--Buscador-->
                <form class="col-4 mx-2 my-2" role="search">
                    <input type="search" id="searchInput" class="form-control" onkeyup="searchTable()" placeholder="Buscar" aria-label="Search">
                </form>
            </div>
            <div class="col">
                <!--Carga Masiva de Layout-->
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModalCPWC">
                    Campus PwC &nbsp; <i class="bi bi-box-arrow-up"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 py-2">
        <?php
        $sqlSesionPwC = "SELECT CSE.NOM_SESION, CSA.* FROM code_sesion_academic CSA LEFT OUTER JOIN code_sesion CSE ON CSE.CODE_SESION_NOM = CSA.CODE_NOM WHERE CODE_VALUE_KEY != 'SELVAL'";
        $resultSesionPwC = $mysqli->query($sqlSesionPwC);
        if ($resultSesionPwC->num_rows > 0) {
        ?>
            <table id="myTable" class="table table-primary table-hover table-bordered table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col" class="text-white">Campus PwC</th>
                        <th scope="col" class="text-white">Clave Nom2001</th>
                        <th scope="col" class="text-white">Campus Nom2001</th>
                        <th scope="col" class="text-white"></th>
                    </tr>
                </thead>
                <?php
                while ($rowSesionPwC = $resultSesionPwC->fetch_assoc()) {
                    if ($rowSesionPwC['CODE_NOM'] == '0' or $rowSesionPwC['CODE_NOM'] == '') {
                        $bg = 'table-warning';
                    } else {
                        $bg = 'table-light';
                    }
                ?>
                    <tbody class="text-center">
                        <form action="academic/updateNomSesion.php" method="post">
                            <tr class="<?php echo $bg ?>">
                                <td hidden><input type="text" name="codePwC" value="<?php echo $rowSesionPwC['CODE_VALUE_KEY'] ?>"></td>
                                <td><?php echo $rowSesionPwC['LONG_DESC'] ?></td>
                                <td><input type="text" name="idNom2001" value="<?php echo $rowSesionPwC['CODE_NOM'] ?>"> </td>
                                <td><?php echo $rowSesionPwC['NOM_SESION'] ?></td>
                                <td>
                                    <div class="btn-group dropend">
                                        <!--Droopdown de opciones-->
                                        <button type="submit" class="btn btn-secondary btn-sm" value="<?php echo $rowSesionPwC['CODE_VALUE_KEY'] ?>"><i class="bi bi-arrow-counterclockwise"></i> &nbsp; Actualizar</a></li>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </form>
                    </tbody>
                <?php
                }
                ?>
            </table>
    </div>
<?php
        }
    }
?>

<!-- Modal Archivo Horarios Docente -->
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Horario Docentes de PwC</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="academic/loadSchedules.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <input type="file" name="archivo_xlsx" accept=".xlsx" class="form-control" id="inputGroupFile01">
                    </div>
                </div>
                <div class="modal-footer">
                    <p style="font-size: 0.90rem; color: gray; float: left;">Este proceso puede tardar unos minutos.</p>
                    <button type="submit" name="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Archivo Campus PwC -->
<!-- Modal -->
<div class="modal fade" id="exampleModalCPWC" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Campus PwC</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="academic/loadSesions.php" method="post" enctype="multipart/form-data">
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

<!-- Modal Archivo Niveles Programas PwC -->
<!-- Modal -->
<div class="modal fade" id="exampleModalDPPWC" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Programas PwC</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="academic/loadPrograms.php" method="post" enctype="multipart/form-data">
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

<script>
    function searchTable() {
        // Obtiene el valor del input de búsqueda
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");

        // Itera sobre todas las filas y oculta las que no coincidan con el término de búsqueda
        for (i = 1; i < tr.length; i++) {
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