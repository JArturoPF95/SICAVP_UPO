<?php

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

require_once '../logic/conn.php';

$term = date('Y');
$send = '0';
$alert = '<div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>&nbsp;&nbsp; Sin colaboradores con menos de un año de antiguedad.</div>
                </div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idNom = $_POST['id'];
    $days = $_POST['days'];
    $comments = $_POST['comments'];
    $send = $_POST['send'];

        $sqlInsert = "INSERT INTO vacation_anticipated (ID_NOM, VACATION_TERM, DAYS, COMMENTS, ACTIVE_FLAG, CREATED_BY, CREATED_DATE) 
            VALUES ('$idNom','$term','$days','$comments','1','$user_active',NOW())";
        if ($mysqli->query($sqlInsert) === true) {
            $message = 'Vacaciones Anticipadas Registradas';
            $icon = 'success';
        } else {
            $message = 'Error Registrando Vacaciones Anticipadas';
            $icon = 'error';
        }
    
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacaciones Anticipadas</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <script src="../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <div class="container">
        <!-- Empleados con antiguedad menor a un año sin solicitudes -->
                <h4 class="mb-3">Registrar Vacaciones Anticipadas</h4>
                <hr class="my-2">
        <div class="row my-3">
            <?php
            $sqlGetEmployed = "SELECT EMP.ID_NOM ID, CONCAT(EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX,' ',EMP.NAME) NAME, EMP.ANTIQUITY, EMP.SUPERVISOR_ID
            FROM employed EMP 
            LEFT JOIN vacation_anticipated VAA ON VAA.ID_NOM = EMP.ID_NOM
            WHERE (YEAR(DATE(NOW())) - YEAR(DATE(ANTIQUITY))) < 1 AND VAA.ID_NOM IS NULL";
            $resultEmployed = $mysqli->query($sqlGetEmployed);
            if ($resultEmployed->num_rows > 0) {
            ?>
                <table class="table table-primary table-hover table-bordered table-sm">
                    <thead class="text-white text-center">
                        <tr class="text-center">
                            <th scope="col" style="width: 10%;">Periodo <br> Anticipado</th>
                            <th scope="col" style="width: 30%;">Nombre</th>
                            <th scope="col" style="width: 10%;">Antigüedad</th>
                            <th scope="col" style="width: 2.55%;">Días <br> Autorizados</th>
                            <th scope="col" style="width: 27.5%;">Comentarios</th>
                            <th scope="col" style="width: 20%;"></th>
                        </tr>
                    </thead>
                    <?php
                    while ($rowEmployed = $resultEmployed->fetch_assoc()) {
                        $idNomEmp = $rowEmployed['ID'];
                        $empName = $rowEmployed['NAME'];
                        $antiquity = $rowEmployed['ANTIQUITY'];
                        $idNomSup = $rowEmployed['SUPERVISOR_ID'];
                    ?>
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                            <tbody class="table-light">
                                <tr class="text-center">
                                    <td hidden><input type="text" name="id" value="<?php echo $idNomEmp ?>"></td>
                                    <td hidden><input type="text" name="send" value="1"></td>
                                    <td style="width: 10%"><?php echo $term ?></td>
                                    <td style="width: 30%"><?php echo $empName ?></td>
                                    <td style="width: 10%"><?php echo date('d/m/Y', strtotime($antiquity)) ?></td>
                                    <td style="width: 2.5%"><input type="text" name="days" required></td>
                                    <td style="width: 27.5%"><textarea name="comments" maxlength="250" rows="1" cols="50" required></textarea></td>
                                    <td style="width: 20%"><button class="btn btn-primary btn-sm" style="width: 80px;" value="1" name="flag" type="submit">Autorizar</button></td>
                                </tr>
                            </tbody>
                        </form>
                <?php
                    }
                ?>
                </table>
            <?php
            }
            ?>
        </div>
        <!-- Termina Empleados con antiguedad menor a un año sin solicitudes -->
       
    </div>
    <?php
    if ($send == '1') {
    ?>
        <script type="text/javascript">
            swal({
                title: "Registrando Vacaciones Anticipadas",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "anticipated_record.php?id=<?php echo $user_active ?>";
            });
        </script>
    <?php
    }
    ?>
</body>

</html>