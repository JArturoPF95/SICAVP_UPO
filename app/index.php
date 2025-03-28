<?php
require 'logic/conn.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$ip = $_SERVER["REMOTE_ADDR"];

$sqlUpdate = "UPDATE users_last_access SET LAST_ACCESS = NOW(), UBICATION = '$ip' WHERE USER = '$user_active'";
if ($mysqli->query($sqlUpdate) === true) {
}

require 'menu.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../static/img/fav_sicavp.png" type="image/x-icon">
    <title>SICAVP</title>
    <script src="../static/js/popper.min.js"></script>
    <script src="../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../static/css/bootstrap.css">
    <link rel="stylesheet" href="../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../static/css/styles/main.css">
</head>

<body>
    <header class="navbar sticky-top bg-primary flex-md-nowrap p-0 shadow" data-bs-theme="primary" style="height: auto;">
        <img class="img-fluid" src="../static/img/4.png" alt="" srcset="" style="width: 200px; height: 50px;">
        <ul class="navbar-nav flex-row">
            <li class="nav-item text-nowrap d-md-none">
                <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>
            </li>
            <li class="nav-item text-nowrap">
                <a class="me-3 py-2 link-body-emphasis text-decoration-none text-white fw-bolder fs-4" href="index.php">
                    <i class="bi bi-house-door-fill"></i> &nbsp; Inicio </a>
            </li>
            <li class="nav-item text-nowrap">
                <a class="me-3 py-2 link-body-emphasis text-decoration-none text-white fw-bolder fs-4" href="logic/session/logout.php">
                    <i class="bi bi-door-open-fill"></i> &nbsp; Salir</a>
            </li>
        </ul>

        <div id="navbarSearch" class="navbar-search w-100 collapse">
            <input class="form-control w-100 rounded-0 border-0" type="text" placeholder="Search" aria-label="Search">
        </div>
    </header>
    <div class="container-fluid">
        <div class="row">
        <div class="sidebar col-md-3 col-lg-2 p-0 bg-body-tertiary">
                <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                    <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
                        <?php echo $sidebar ?>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2">
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2">
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2">
                            </a>
                        </li>
                    </ul>
                    <hr class="my-5 text-light">
                    <footer class="py-3 my-4">
                        <p class="text-center text-light">&copy; 2024 Dirección de Tecnologías de la Información</p>
                        <ul class="nav justify-content-center border-bottom">
                            <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">
                                    <img src="../static/img/new_nacerlogo.png" alt="" srcset="" style="width: auto; height: 30px;">
                                </a></li>
                        </ul>
                    </footer>
                </div>
            </div>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom">
                    <div class="container">
                        <div class="col">
                            <p class="h2" width="50%"><?php echo $user_active . ' - ' . $user_name ?></p>
                        </div>
                        <div class="col">
                            <?php 
                            $sqlAccess = "SELECT LEVEL_DESCRIPTION FROM code_accesslevels WHERE CODE_LEVEL = '$user_access'";
                            $resultAccess = $mysqli -> query($sqlAccess);
                            if ($resultAccess -> num_rows > 0) {
                                while ($rowAccess = $resultAccess -> fetch_assoc()) {
                                    if ($user_access != '1' OR $user_access != '4') {
                                    ?>
                            <p width="50%" style="font-size: 0.90rem; color: gray;"><?php echo $rowAccess['LEVEL_DESCRIPTION'] ?></p>
                                    <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <iframe class="pt-4" src="../static/clock.html" width="50%" frameborder="0"></iframe>
                </div>
                <div class="row">
                    <?php
                    if (empty($_GET)) {
                        if ($user_access == 1 OR $user_access == 2) {
                            $url = "administrative/last_today.php?id=$user_active";
                        } elseif ($user_access == 4) {
                            $url = "academic/last_today.php?id=$user_active";
                        } elseif ($user_access == 5) {
                            $url = "academic/teachers.php?id=$user_active";
                        } elseif ($user_access == 3) {
                            $url = "support/index.php?id=$user_active";
                        }
                    ?>
                        <iframe class="my-4 w-100" src=" <?php echo $url; ?>" id="myChart" style="width: 90%; height: 70vh"></iframe>
                    <?php
                    } else {
                    $opcion = $_GET['opcion'];
                    $url = $urlMenu[$opcion];

                    ?>
                        <iframe class="my-4 w-100" src=" <?php echo $url; ?>" id="myChart" style="width: 90%; height: 70vh"></iframe>
                    <?php
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>
</body>

</html>