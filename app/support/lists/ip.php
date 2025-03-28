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

$send = 3;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $send = $_POST['send'];
}

//Obtenemos IP
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

//Query Campus
$sqlGetSesion = "SELECT DISTINCT CODE_SESION_NOM, NOM_SESION FROM code_sesion";
$resultGetSesion = $mysqli->query($sqlGetSesion);

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
                            <!--Agregamos una IP-->
                            <button name="send" type="submit" value="1" class="btn btn-primary btn-sm">
                                Nueva IP &nbsp; <i class="bi bi-database-fill-up"></i>
                            </button>
                            <!--Obtenemos la IP-->
                            <button name="send" type="submit" value="2" class="btn btn-primary btn-sm">
                                Obtener IP &nbsp; <i class="bi bi-router-fill"></i>
                            </button>
                            <!--Listado de IPs-->
                            <button name="send" type="submit" value="3" class="btn btn-primary btn-sm">
                                IPs &nbsp; <i class="bi bi-wifi"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <h4 class="my-3">Direcciones IP</h4>
    <div class="container align-items-center"> <!--Div Principal-->
        <?php
        if ($send == 3) {
            $sqlGetIP = "SELECT ipID, NOM_SESION, IP, LONG_DESC
FROM code_ip IP 
INNER JOIN code_sesion SES ON IP.CODE_SESION = SES.CODE_SESION_NOM
LEFT OUTER JOIN code_sesion_academic CSA ON IP.CODE_SESION = CSA.CODE_NOM";
            $resultGetIP = $mysqli->query($sqlGetIP);
            if ($resultGetIP->num_rows > 0) {
        ?>

                <table id="myTable" class="table table-hover table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" class="text-white table-primary">Ubicación NOM</th>
                            <th scope="col" class="text-white table-primary">Campus</th>
                            <th scope="col" class="text-white table-primary">IP</th>
                            <th scope="col" class="text-white table-primary">Actualizar</th>
                            <th scope="col" class="text-white table-primary">Eliminar</th>
                        </tr>
                    </thead>
                    <?php
                    while ($rowIP = $resultGetIP->fetch_assoc()) {
                    ?>
                        <tbody class="text-center">
                            <tr>
                                <td><?php echo $rowIP['NOM_SESION'] ?></td>
                                <td><?php echo $rowIP['LONG_DESC'] ?></td>
                                <td><?php echo $rowIP['IP'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalUpdate" data-bs-whatever="<?php echo $rowIP['ipID'] ?>">
                                        <!--Actualizar &nbsp; -->
                                        <i class="bi bi-database-fill-gear"></i>
                                    </button>
                                </td>
                                <td>
                                    <a type="button" class="btn btn-primary btn-sm" href="process/deleteIP.php?id=<?php echo $rowIP['ipID'] ?>">
                                        <!--Borrar &nbsp; -->
                                        <i class="bi bi-wifi-off"></i>
                                    </a>
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
                <div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div class="text-end fs-4">Aún no tiene IPs Registradas en sistema</div>
                </div>
            <?php
            } //Cierra IF Consulta Tabla

        } elseif ($send == 2) {
            ?>
            <div class="card text-bg-secondary mb-3 align-items-center" style="max-width: 18rem;">
                <div class="card-header"> Dirección IP </div>
                <div class="card-body">
                    <p class="card-text"><?php echo $ip ?> </p>
                </div>
            </div>
        <?php
        } elseif ($send == 1) {
        ?>
            <form class="row g-3" method="post" action="process/insertIP.php">
                <fieldset>
                    <div class="row g-3">
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Dirección IP</label>
                            <input type="text" name="ip" id="disabledTextInput" class="form-control" placeholder="000.000.000.000" required>
                        </div>
                        <div class="col">
                            <label for="disabledSelect" class="form-label">Campus</label>
                            <select id="disabledSelect" name="sesion" class="form-select">
                                <?php
                                if ($resultGetSesion->num_rows > 0) {
                                    while ($rowSesion = $resultGetSesion->fetch_assoc()) {
                                        $ipID = $rowIP['idIP'];
                                ?>
                                        <option value="<?php echo $rowSesion['CODE_SESION_NOM'] ?>"><?php echo $rowSesion['NOM_SESION'] ?></option>
                                <?php
                                    }
                                } else {
                                    echo '<option disabled>Sin Información</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col py-2">
                        <button type="submit" class="btn btn-primary px-3">Crear</button>
                    </div>
    </div>
    </fieldset>
    </form>
<?php
        }

?>
</div> <!--Cierra Div Principal-->

<!--Modal Update IP-->
<div class="modal fade" id="exampleModalUpdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Actualizar IP</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process/updateIP.php" method="post">
                    <div hidden class="mb-3">
                        <label for="recipient-name" class="col-form-label">IP:</label>
                        <input type="text" name="idIP" class="form-control" id="recipient-name">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">Nueva IP:</label>
                        <input type="text" class="form-control" name="ip" id="recipient-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">Nuevo Campus:</label>
                        <select id="disabledSelect" name="sesion" class="form-select">
                            <?php
                            if ($resultGetSesion->num_rows > 0) {
                                while ($rowSesion = $resultGetSesion->fetch_assoc()) {
                            ?>
                                    <option value="<?php echo $rowSesion['CODE_SESION_NOM'] ?>"><?php echo $rowSesion['NOM_SESION'] ?></option>
                            <?php
                                }
                            } else {
                                echo '<option disabled>Sin Información</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const exampleModal = document.getElementById('exampleModalUpdate')
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

            modalTitle.textContent = `Actualizar IP ${recipient}`
            modalBodyInput.value = recipient
        })
    }
</script>

</body>

</html>