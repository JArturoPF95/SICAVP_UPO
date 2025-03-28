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
$alert = '<div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>&nbsp;&nbsp; No cuenta con vacaciones anticipadas registradas.</div>
                </div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idNom = $_POST['id'];
    $days = $_POST['days'];
    $comments = $_POST['comments'];
    $flag = $_POST['flag'];
    $send = $_POST['send'];

    if ($flag == '0') {
        $sqlVoid = "UPDATE vacation_anticipated SET COMMENTS = '$comments', ACTIVE_FLAG = '0', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE ID_NOM = '$idNom'";
        if ($mysqli->query($sqlVoid) === true) {
            $message = 'Vacaciones Anticipadas Anuladas';
            $icon = 'success';
        } else {
            $message = 'Error Anulando Vacaciones Anticipadas';
            $icon = 'error';
        }
    } elseif ($flag == '3') {
        $sqlActivate = "UPDATE vacation_anticipated SET DAYS = '$days', COMMENTS = '$comments', ACTIVE_FLAG = '1', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE ID_NOM = '$idNom'";
        if ($mysqli->query($sqlActivate) === true) {
            $message = 'Vacaciones Anticipadas Activadas';
            $icon = 'success';
        } else {
            $message = 'Error Activando Vacaciones Anticipadas';
            $icon = 'error';
        }
    } else {
        $sqlUpdate = "UPDATE vacation_anticipated SET DAYS = '$days', COMMENTS = '$comments', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE ID_NOM = '$idNom'";
        if ($mysqli->query($sqlUpdate) === true) {
            $message = 'Vacaciones Anticipadas Actualizadas';
            $icon = 'success';
        } else {
            $message = 'Error Actualizando Vacaciones Anticipadas';
            $icon = 'error';
        }
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
        <!-- Vacaciones Anticipadas Registrada -->
                <h4 class="mb-3">Vacaciones Anticipadas Registradas</h4>
                <hr class="my-2">
        <div class="row my-3">
            <?php
            $sqlGetAnticipated = "SELECT EMP.ID_NOM ID, CONCAT(EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX,' ',EMP.NAME) NAME, EMP.ANTIQUITY, EMP.SUPERVISOR_ID, VAA.ACTIVE_FLAG, VAA.DAYS, VAA.COMMENTS
    FROM employed EMP
    INNER JOIN vacation_anticipated VAA ON VAA.ID_NOM = EMP.ID_NOM";
            $resultAnticipated = $mysqli->query($sqlGetAnticipated);
            if ($resultAnticipated->num_rows > 0) {
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
                    while ($rowAnticipated = $resultAnticipated->fetch_assoc()) {
                        $idNomEmp = $rowAnticipated['ID'];
                        $empName = $rowAnticipated['NAME'];
                        $antiquity = $rowAnticipated['ANTIQUITY'];
                        $idNomSup = $rowAnticipated['SUPERVISOR_ID'];
                        $days = $rowAnticipated['DAYS'];
                        $comments = $rowAnticipated['COMMENTS'];
                        $activeFlag = $rowAnticipated['ACTIVE_FLAG'];
                    ?>
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                            <tbody class="table-light">
                                <tr class="text-center">
                                    <td hidden><input type="text" name="id" value="<?php echo $idNomEmp ?>"></td>
                                    <td hidden><input type="text" name="send" value="1"></td>
                                    <td style="width: 10%"><?php echo $term ?></td>
                                    <td style="width: 30%"><?php echo $empName ?></td>
                                    <td style="width: 10%"><?php echo date('d/m/Y', strtotime($antiquity)) ?></td>
                                    <td style="width: 2.5%"><input type="text" name="days" value="<?php echo $days ?>" required></td>
                                    <td style="width: 27.5%"><textarea name="comments" maxlength="250" rows="1" cols="50" required><?php $comments = ($activeFlag == '0') ? 'Anulado ' . $comments : $comments;
                                                                                                                                    echo $comments; ?></textarea></td>
                                    <td style="width: 20%"> <?php
                                                            if ($activeFlag == '1') {
                                                                echo '<button class="btn btn-secondary btn-sm" style="width: 80px;" value="2" name="flag" type="submit">Actualizar</button>&nbsp;<button class="btn btn-warning btn-sm" style="width: 80px;" value="0" name="flag" type="submit">Anular</button>';
                                                            } else {
                                                                echo '<button class="btn btn-primary btn-sm" style="width: 80px;" value="3" name="flag" type="submit">Activar</button>';
                                                            } ?>
                                    </td>
                                </tr>
                            </tbody>
                        </form>
                    <?php
                    }
                    ?>
                </table>
            <?php
            } else {
                echo $alert;
            }
            ?>
            <!-- Termina Vacaciones Anticipadas Registrada -->
        </div>

        <?php
    if ($send == '1') {
    ?>
        <script type="text/javascript">
            swal({
                title: "Actualización Vacaciones Anticipadas",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "anticipated_vacations.php?id=<?php echo $user_active ?>";
            });
        </script>
    <?php
    }
    ?>
</body>
</html>