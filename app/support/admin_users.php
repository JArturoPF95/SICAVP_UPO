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



$bg = '';



require 'administrative/query.php';

$sql_users;

$result_users = $mysqli->query($sql_users);



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

                <div class="container d-flex flex-wrap justify-content-center">

                    <form class="col-12 col-lg-auto mb-2 mb-lg-0 me-lg-auto" role="search">

                        <input type="search" id="searchInput" class="form-control" onkeyup="searchTable()" placeholder="Buscar" aria-label="Search">

                    </form>



                    <div class="text-end">

                        <!--Carga Masiva de Layout-->

                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">

                            Layout Empleados Nom2001 &nbsp; <i class="bi bi-box-arrow-up"></i>

                        </button>

                        <!--Usuarios Masivo-->

                        <a href="administrative/usersLoad.php" class="btn btn-primary d-inline-flex align-items-center" type="button">

                            Genera Usuarios &nbsp; <i class="bi bi-people-fill"></i>

                        </a>

                        <!--Usuario-->

                        <a href="user.php" class="btn btn-primary d-inline-flex align-items-center" type="button">

                            Nuevo Usuario &nbsp; <i class="bi bi-people-fill"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </header>

    <!--Tabla de usuarios-->

    <h4 class="my-3">Personal Administrativo</h4>

    <div class="d-flex gap-2 py-2">

        <?php

        if ($result_users->num_rows > 0) {

        ?>

            <table id="myTable" class="table table-primary table-hover table-bordered table-sm">

                <thead class="text-center">

                    <tr>

                        <?php echo $bg ?>

                        <th scope="col" class="text-white">Usuario</th>

                        <th scope="col" class="text-white">Nombre</th>

                        <th scope="col" class="text-white">Campus</th>

                        <th scope="col" class="text-white">Área</th>

                        <th scope="col" class="text-white">Departamento</th>

                        <th scope="col" class="text-white">Puesto</th>

                        <th scope="col" class="text-white">Nivel de Acceso</th>

                        <th scope="col" class="text-white"></th>

                    </tr>

                </thead>

                <?php

                while ($rowUsers = $result_users->fetch_assoc()) {

                    if ($rowUsers['SEPARATION_FLAG'] === '1') {

                        $bg = 'class="table-warning"';

                    } else {

                        if ($rowUsers['ACCESS_LEVEL'] === '3' OR $rowUsers['ACCESS_LEVEL'] === '5') {

                            $bg = 'class="table-success"';

                        } else {

                            $bg = 'class="table-light"';

                        }

                    }

                    

                ?>

                    <tbody>

                        <tr <?php echo $bg ?>>

                            <td><?php echo $rowUsers['USER'] ?></td>

                            <td><?php 

                            if ($rowUsers['ACCESS_LEVEL'] == '3') {

                                echo 'ADMINISTRADOR';

                            } elseif ($rowUsers['ACCESS_LEVEL'] == '5') {

                                echo 'SUPERVISOR DOCENTE';

                            } else {

                                echo $rowUsers['EMP_NAME'];

                            }  ?></td>

                            <td><?php echo $rowUsers['NOM_SESION'] ?></td>

                            <td><?php echo $rowUsers['NAME_AREA'] ?></td>

                            <td><?php echo $rowUsers['DEPARTMENT'] ?></td>

                            <td><?php echo $rowUsers['JOB_NAME'] ?></td>

                            <td><?php echo $rowUsers['LEVEL_DESCRIPTION'] ?></td>

                            <td>

                                <div class="btn-group dropstart">

                                    <!--Droopdown de opciones-->

                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                                        Opciones

                                    </button>

                                    <ul class="dropdown-menu">

                                        <?php 
                                        if ($rowUsers['SEPARATION_FLAG'] === '0') {
                                        ?>

                                        <li class="dropdown-item"><a class="dropdown-item" href="administrative/user_reset.php?id=<?php echo $rowUsers['USER'] ?>"><i class="bi bi-person-fill-gear"></i> &nbsp; Reestablecer Contraseña</a></li>
                                        <li class="dropdown-item"><a class="dropdown-item" href="administrative/user_update.php?id=<?php echo $rowUsers['USER'] ?>"><i class="bi bi-person-lines-fill"></i> &nbsp; Actualizar Acceso</a></li>
                                        <li class="dropdown-item"><a class="dropdown-item" href="administrative/user_lock.php?id=<?php echo $rowUsers['USER'] ?>"><i class="bi bi-person-fill-lock"></i> &nbsp; Bloquear Usuario</a></li>

                                        <?php 
                                        } elseif ($rowUsers['SEPARATION_FLAG'] === '1') {
                                        ?>
                                        
                                        <li class="dropdown-item"><a class="dropdown-item" href="administrative/user_unlock.php?id=<?php echo $rowUsers['USER'] ?>"><i class="bi bi-person-fill-up"></i> &nbsp; Desbloquear Usuario</a></li>

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

        <?php

        } else {

        ?>

        <?php

        }

        ?>

    </div>



    <!-- Modal Archivo Empleados -->

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="exampleModalLabel">Carga Empleados</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <form action="administrative/loadEmployed.php" method="post" enctype="multipart/form-data">

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