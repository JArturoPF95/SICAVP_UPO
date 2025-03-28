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
if (isset($_GET['id'])) {
    $id_nom = $_GET['id'];
} else {
    $id_nom = '';
}
$send = 0;
$access = '';

$sqlActualAccess = "SELECT * FROM users 
INNER JOIN code_accesslevels cal ON ACCESS_LEVEL = CODE_LEVEL
WHERE USER = '$id_nom'";
$resultActualAccess = $mysqli->query($sqlActualAccess);
if ($resultActualAccess->num_rows > 0) {
    while ($rowActual = $resultActualAccess->fetch_assoc()) {
        $level = $rowActual['LEVEL_DESCRIPTION'];
        $code = $rowActual['CODE_LEVEL'];
        $payroll = $rowActual['PAYROLL'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $send = $_POST['send'];
    $id_nom = $_POST['id'];
    if (isset($_POST['access'])) {
        $access = $_POST['access'];
    } else {
        $access = '1';
    }

    $sqlUpdate = "UPDATE users SET ACCESS_LEVEL = '$access' WHERE USER = '$id_nom'";
    if ($mysqli->query($sqlUpdate) === true) {
        $message = 'Acceso otorgado con éxito';
        $icon = 'success';
    } else {
        $message = 'Error otorgando acceso';
        $icon = 'error';
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencias del día</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" class="mx-4">
        <fieldset>
            <legend>Otorgar acceso al Usuario</legend>
            <input hidden type="text" name="send" value="1">
            <div class="mb-3 col-4">
                <label for="disabledTextInput" class="form-label">ID Nómina</label>
                <input type="text" id="disabledTextInput" name="id" class="form-control" value="<?php echo $id_nom ?>">
            </div>
            <div class="mb-3 col-4">
                <label for="disabledSelect" class="form-label">Nivel de Acceso</label>
                <select id="disabledSelect" class="form-select" name="access">
                    <option disabled selected><?php echo $level ?></option>
                    <?php
                    $sqlAccessLevels = "SELECT * FROM code_accesslevels WHERE PAYROLL = '$payroll' AND CODE_LEVEL != '$code'";
                    $resultAccessLevels = $mysqli->query($sqlAccessLevels);
                    if ($resultAccessLevels->num_rows > 0) {
                        while ($rowAccess = $resultAccessLevels->fetch_assoc()) {
                    ?>
                            <option value="<?php echo $rowAccess['CODE_LEVEL'] ?>"><?php echo $rowAccess['LEVEL_DESCRIPTION'] ?></option>
                        <?php
                        }
                    } else {
                        ?>
                        <option disabled selected>No hay opciones para mostrar</option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="../admin_users.php?id=<?php echo $user_active ?>" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill"></i> &nbsp; Volver</a>
        </fieldset>
    </form>

    <?php
    if ($send == 1) {
    ?>
        <script type="text/javascript">
            swal({
                title: "Actualización de Acceso",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "../admin_users.php?id=<?php echo $user_active ?>";
            });
        </script>
    <?php
    }
    ?>

</body>

</html>