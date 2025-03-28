<?php

require '../conn.php';

$message = '';
$icon = '';
$url = '';
$send = 0;

if (isset($_GET['u_12345'])) {
    $user = $_GET['u_12345'];
} else {
    $user = '';
}

$sql_getUser = "SELECT 
DISTINCT
USR.*, IF(USR.ACCESS_LEVEL = 1 OR USR.ACCESS_LEVEL = 2, CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX), 
          IF(USR.ACCESS_LEVEL = '4', CONCAT(ASH.NAME,' ',ASH.LAST_NAME,' ',ASH.LAST_NAME_PREFIX), 
             IF(USR.ACCESS_LEVEL = '5', 'SUPERVISOR DOCENTE', 'ADMINISTRADOR'))) USER_NAME
FROM users USR 
LEFT OUTER JOIN employed EMP ON EMP.ID_NOM = USR.USER
LEFT OUTER JOIN academic_schedules ASH ON ASH.PERSON_CODE_ID = USR.USER
WHERE USR.USER = '$user'";

$result_user = $mysqli->query($sql_getUser);
if ($result_user->num_rows > 0) {
    while ($row = $result_user->fetch_assoc()) {
        $name = $row['USER_NAME'];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usr = $_POST['usuarioS'];
    $password_1 = $_POST['pass1'];
    $password_2 = $_POST['pass2'];
    $send = $_POST['send'];

    if ($password_1 != $password_2) {
        $message = 'Las contraseñas no coinciden';
        $icon = "warning";
        $url = "update_password.php?u_12345=$usr";
    } else {
        //Validamos tamaño de la contraseña
        if (strlen($password_1) < 8 or (!preg_match('/[A-Za-z]/', $password_1) || !preg_match('/[0-9]/', $password_1))) {
            $message = 'Contraseña demasiado Débil';
            $icon = "warning";
            $url = "update_password.php?u_12345=$usr";
        } else {
            $nvo_pass = mysqli_real_escape_string($mysqli, $_POST['pass1']);
            $pass_cifrado = password_hash($password_1, PASSWORD_DEFAULT);

            $sql_update_pass = "UPDATE users SET PASS_TEMP = '', PASSWORD = '$pass_cifrado' WHERE USER = '$usr'";
            if ($mysqli->query($sql_update_pass) === TRUE) {
                $message = 'Contraseña Actualizada Correctamente';
                $icon = "success";
                $url = "../../../index.php";
            } else {
                $message = 'Error Actualizando Contraseña';
                $icon = "error";
                $url = "../../../index.php";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../../static/img/fav_sicavp.png" type="image/x-icon">
    <title>Cambia Contaseña</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/upd_pass.css">
    <style>
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body style="background-color: #FFF;">
    <?php
    if ($send == '0') {
    ?>
    <div class="container">

        <div class="row" style="margin-top: 15%;">
            <div class="col-lg-1 col-sm-12"></div>
            <div class="col-lg-5 col-sm-12">
                <div class="row text-center">
                    <div class="col">
                        <img src="../../../static/img/universidad-potosina_logo.png" class="img-fluid" style="width: 100px; height: 100px;">
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col text-center">
                        <img src="../../../static/img/1.png" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="col-1 d-flex d-none d-lg-flex d-xl-flex">
                <div class="vr" style="width: 3px; color:#FFF;"></div>
            </div>
            <div class="col-lg-3 col-sm-12">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <h3 class=" text-center text-warning">Actualiza Contraseña</h3>
                    <input hidden type="text" name="send" value="1">
                    <input hidden type="text" name="usuarioS" id="inputUser" class="form-control" aria-describedby="passwordHelpBlock" value="<?php echo $user; ?>">
                    <div class="mb-3 float-start" style="width: 100%">
                        <label for="inputPassword5" class="form-label  text-center"><?php echo $user . ' - ' . $name ?></label>
                    </div>
                    <div class="mb-3 float-start" style="width: 100%">
                        <label for="inputPassword5" class="form-label ">Contraseña Nueva</label>
                        <input type="password" class="form-control" id="inputPassword5" name="pass1" class="form-control my-1" aria-describedby="passwordHelpBlock" required>
                    </div>
                    <div class="mb-3 float-start" style="width: 100%">
                        <label for="inputPassword5" class="form-label ">Repita Contraseña</label>
                        <input type="password" class="form-control" id="inputPassword5" name="pass2" class="form-control my-1" aria-describedby="passwordHelpBlock" required>
                    </div>
                    <button type="submit" class="btn btn-dark my-3 fs-6" style="width: 100%">Actualizar</button>
                </form>
                <hr style="color:#FFF;">
                <p class="text-danger fs-6 text-center"><b>Su contraseña debe contener al menos 8 caracteres. Números y letras</b></p>
            </div>
            <div class="col-lg-3 col-sm-12"></div>
        </div>
    </div>
    <footer class="py-3 my-4">
        <p class="text-center " style="font-size: 11px;">
            <img src="../../../static/img/new_nacerlogo.png" alt="" srcset="" style="width: auto; height: 30px;">
            <br>
            &copy; 2024 Dirección de Tecnologías de la Información
        </p>
    </footer>
    <?php
    } elseif ($send == '1') {
    ?>
        <script type="text/javascript">
            swal({
                title: "Actualizar Contraseña",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "<?php echo $url ?>";
            });
        </script>
    <?php
    }
    ?>

</body>

</html>