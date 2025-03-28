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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario Nuevo</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../static/css/styles/tables.css">
</head>

<body>
    <!--Header con buscador de usuario y opciónes para alta-->
    <header>
        <div class="px-3 py-1 border-bottom">
            <div class="px-3 mb-3">
                <div class="container d-flex flex-wrap justify-content-end">

                    <div class="text-end">
                        <!--Usuario Individual-->
                        <a href="admin_users.php" class="btn btn-primary d-inline-flex align-items-center" type="button">
                            Volver &nbsp;
                            <i class="bi bi-arrow-left-circle-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--Formulario de alta de usuarios-->

    <h4 class="my-3">Usuario</h4>
    <div class="col-md-11 col-lg-11 d-flex justify-content-center py-2 px-5">
        <form class="needs-validation" novalidate method="post" action="administrative/userCreate.php">
            <div class="row g-3">
                <!--Usuario-->
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                    <label for="idnom" class="form-label">Usuario</label>
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="idnom" id="idnom" placeholder="Usuario" required>
                        <div class="invalid-feedback">
                            Usuario es Obligatorio.
                        </div>
                    </div>
                </div>
                <div class="col-sm-4"></div>
                <!--Divisor-->
                <hr class="my-2">
                <!-- Campus-->
                <div class="col-md-6">
                    <label for="sesion" class="form-label">Campus</label>
                    <select class="form-select" id="sesion" name="sesion" required>
                        <?php
                        $sqlSesion = "SELECT * FROM code_sesion";
                        $resultSesion = $mysqli->query($sqlSesion);
                        if ($resultSesion->num_rows > 0) {
                            while ($rowSesion = $resultSesion->fetch_assoc()) {
                        ?>
                                <option value="<?php echo $rowSesion['CODE_SESION_NOM'] ?>"><?php echo $rowSesion['NOM_SESION'] ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">
                        Seleccione un Campus Válido.
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="access" class="form-label">Acceso</label>
                    <select class="form-select" id="access" name="access" required>
                        <?php
                        $sqlAccess = "SELECT * FROM code_accesslevels WHERE CODE_LEVEL IN ('3','5')";
                        $resultAccess = $mysqli->query($sqlAccess);
                        if ($resultAccess->num_rows > 0) {
                            while ($rowAccess = $resultAccess->fetch_assoc()) {
                        ?>
                                <option value="<?php echo $rowAccess['CODE_LEVEL'] ?>"><?php echo $rowAccess['LEVEL_DESCRIPTION'] ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">
                        Seleccione un Acceso Válido.
                    </div>
                </div>
                <hr class="my-2">

                <button class="w-100 btn btn-primary btn-lg" type="submit">Crear</button>
            </div>
        </form>
    </div>

    <!--Script de formulario-->
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>

</html>