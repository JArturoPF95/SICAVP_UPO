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
    <title>Administración</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">

</head>

<body>

    <div class="container">
        <div class="row">

            <div class="card text-center mb-4">
                <div class="card-header">
                    <h3>Bienvenido...</h3>
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <h4>Administración del Sistema.</h4>
                    </blockquote>
                </div>
            </div>
        </div>

        <!--Carga archivo de Asistencias -->
        <div class="row">
            <div class="card text-bg-secondary col-sm-6 mb-3 mb-sm-0 text-center">
                <div class="card-header">
                    <blockquote class="blockquote mb-0">
                        <h4>Actualización de Entrada y Salida</h4>
                    </blockquote>
                </div>
                <div class="card-body">

                    <form action="administrative/loadBiometricTime.php" method="post" enctype="multipart/form-data" name="formulario">
                        <input hidden type="text" value="1" name="send">
                        <div class="modal-body">
                            <div class="input-group mb-3">
                                <input type="file" name="archivo_xlsx" accept=".xlsx" class="form-control" id="inputGroupFile01">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-formulario">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-3"></div>
            <div class="col-sm-3"></div>
        </div>
    </div>

<script type="text/javascript">
window.onload = function () {
    document.forms['formulario'].addEventListener('submit', avisarUsuario);
}

function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function avisarUsuario(evObject) {
    evObject.preventDefault();
    var botones = document.querySelectorAll('.btn-formulario');
    for (var i = 0; i < botones.length; i++) {
        botones[i].disabled = true;
    }
    var nuevoNodo = document.createElement('h2');
    nuevoNodo.innerHTML = '<div class="alert alert-info my-4" style="width: 300px; font-size: 16px; margin-left: 300px;" role="alert">Cargando Datos ...</div>';
    document.body.appendChild(nuevoNodo);

    // Espera 100 milisegundos para asegurarse de que el mensaje se muestre
    await delay(100);

    document.forms['formulario'].submit();
}
</script>

</body>

</html>