<?php
ini_set('max_execution_time', -1); // 10 minutos para ejecutar el script

require '../../logic/conn.php';
require '../../../lib/vendor/autoload.php';

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

$loadFlag = '0';
$checks = 0;
$dateCreated = date('Y-m-d');

/** Validamos cantidad de capturas que tiene en el día */
$sqlCountChecks = "SELECT DISTINCT BTC.CREATED_DATE, BTC.ID_NOM, BTC.RECORD_DATE, COUNT(DISTINCT BTC.RECORD_TIME) CHECKS
    FROM biometrictimeclock BTC
    WHERE ID_NOM <> '0000000'
    GROUP BY BTC.ID_NOM, BTC.RECORD_DATE
    ORDER BY BTC.ID_NOM, BTC.CREATED_DATE ASC, BTC.RECORD_DATE ASC;";
$resultCountChecks = $mysqli->query($sqlCountChecks);
if ($resultCountChecks->num_rows > 0) {
    while ($rowCountChecks = $resultCountChecks->fetch_assoc()) {

        $idNom1 = $rowCountChecks['ID_NOM'];
        $recordDate1 = $rowCountChecks['RECORD_DATE'];
        $codeDay = date('w', strtotime($recordDate1));
        $checks = $rowCountChecks['CHECKS'];

        //echo $rowCountChecks['ID_NOM'] . ' - ' . $rowCountChecks['RECORD_DATE'] . ' - ' . $rowCountChecks['CHECKS'] . '<br>';

        switch ($checks) {
            /** Cuenta con 1 registro */
            case '1':
                $sqlGetIn = "SELECT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME, BTC.STATUS
                    FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                    ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1";
                $resultGetIn = $mysqli->query($sqlGetIn);
                if ($resultGetIn = $mysqli->query($sqlGetIn)) {
                    while ($rowGetIn = $resultGetIn->fetch_assoc()) {
                        $status = $rowGetIn['STATUS'];
                        if ($status == 'Falta Injustificada') {
                            $incidence = '01';
                        } else {
                            $recordTime1 = $rowGetIn['RECORD_TIME'];
                            $incidence = '07';

                            //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime4 . ' - ' . $incidenceIn . '<br>';

                            $sqlValReg = "SELECT DISTINCT AttendanceId, ATTENDANCE_TIME FROM admin_attendance WHERE NOM_ID = '$idNom1' AND ATTENDANCE_DATE = '$recordDate1' AND IN_OUT = 1";
                            $resultValReg = $mysqli->query($sqlValReg);
                            if ($resultValReg->num_rows > 0) {
                                while ($rowValReg = $resultValReg->fetch_assoc()) {
                                    $idCheck = $rowValReg['AttendanceId'];
                                    $timeCheck = $rowValReg['ATTENDANCE_TIME'];
                                    $updateAttendance = "UPDATE admin_attendance SET ATTENDANCE_TIME = '$recordTime1', TINC = '$incidence' WHERE AttendanceId = '$idCheck'";
                                    if ($mysqli->query($updateAttendance)) {
                                    }
                                }
                            } else {

                                $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime1','$incidence','1','B')";
                                if ($mysqli->query($sqlInsertAttendance)) {
                                    $loadFlag = '1';
                                } else {
                                    $loadFlag = '0';
                                }
                            }
                        }
                    }
                }

                break;

                /** Cuenta con 2 registros */
            case '2':
                $sqlGetIn = "SELECT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                    FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                    ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1";
                $resultGetIn = $mysqli->query($sqlGetIn);
                if ($resultGetIn = $mysqli->query($sqlGetIn)) {
                    while ($rowGetIn = $resultGetIn->fetch_assoc()) {
                        $recordTime1 = $rowGetIn['RECORD_TIME'];
                        $incidenceIn = '07';

                        //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime1 . ' - ' . $incidenceIn . '<br>';

                        $sqlValReg = "SELECT DISTINCT AttendanceId, ATTENDANCE_TIME FROM admin_attendance WHERE NOM_ID = '$idNom1' AND ATTENDANCE_DATE = '$recordDate1' AND IN_OUT = 1";
                        $resultValReg = $mysqli->query($sqlValReg);
                        if ($resultValReg->num_rows > 0) {
                            while ($rowValReg = $resultValReg->fetch_assoc()) {
                                $idCheck = $rowValReg['AttendanceId'];
                                $timeCheck = $rowValReg['ATTENDANCE_TIME'];
                                $updateAttendance = "UPDATE admin_attendance SET ATTENDANCE_TIME = '$recordTime1', TINC = '$incidence' WHERE AttendanceId = '$idCheck'";
                                if ($mysqli->query($updateAttendance)) {
                                }
                            }
                        } else {

                            $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                            VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime1','$incidenceIn','1','B')";
                            if ($mysqli->query($sqlInsertAttendance)) {
                                $loadFlag = '1';
                            } else {
                                $loadFlag = '0';
                            }
                        }

                        $sqlGetOut = "SELECT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                            FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                            ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME DESC LIMIT 1";
                        $resultGetOut = $mysqli->query($sqlGetOut);
                        if ($resultGetOut = $mysqli->query($sqlGetOut)) {
                            while ($rowGetOut = $resultGetOut->fetch_assoc()) {
                                $recordTime2 = $rowGetOut['RECORD_TIME'];
                                $incidenceOut = '00';

                                //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime2 . ' - ' . $incidenceIn . '<br>';

                                $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime2','00','2','B')";
                                if ($mysqli->query($sqlInsertAttendance)) {
                                    $loadFlag = '1';
                                } else {
                                    $loadFlag = '0';
                                }
                            }
                        }
                    }
                }

                break;

                /** Cuenta con 3 registros */
            case '3':
                $sqlGetIn = "SELECT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                    FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                    ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1";
                $resultGetIn = $mysqli->query($sqlGetIn);
                if ($resultGetIn = $mysqli->query($sqlGetIn)) {
                    while ($rowGetIn = $resultGetIn->fetch_assoc()) {
                        $recordTime1 = $rowGetIn['RECORD_TIME'];
                        $incidenceIn = '07';

                        //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime1 . ' - ' . $incidenceIn . '<br>';

                        $sqlValReg = "SELECT DISTINCT AttendanceId, ATTENDANCE_TIME FROM admin_attendance WHERE NOM_ID = '$idNom1' AND ATTENDANCE_DATE = '$recordDate1' AND IN_OUT = 1";
                        $resultValReg = $mysqli->query($sqlValReg);
                        if ($resultValReg->num_rows > 0) {
                            while ($rowValReg = $resultValReg->fetch_assoc()) {
                                $idCheck = $rowValReg['AttendanceId'];
                                $timeCheck = $rowValReg['ATTENDANCE_TIME'];
                                $updateAttendance = "UPDATE admin_attendance SET ATTENDANCE_TIME = '$recordTime1', TINC = '$incidenceIn' WHERE AttendanceId = '$idCheck'";
                                if ($mysqli->query($updateAttendance)) {
                                }
                            }
                        } else {


                            $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                            VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime1','$incidenceIn','1','B')";
                            if ($mysqli->query($sqlInsertAttendance)) {
                                $loadFlag = '1';
                            } else {
                                $loadFlag = '0';
                            }
                        }

                        $sqlGetOut = "SELECT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                            FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                            ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME DESC LIMIT 1";
                        $resultGetOut = $mysqli->query($sqlGetOut);
                        if ($resultGetOut = $mysqli->query($sqlGetOut)) {
                            while ($rowGetOut = $resultGetOut->fetch_assoc()) {
                                $recordTime2 = $rowGetOut['RECORD_TIME'];
                                $incidenceOut = '00';
                                $flagOut = '2';

                                //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime2 . ' - ' . $incidenceIn . '<br>';


                                    $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime2',00,'2','B')";
                                    if ($mysqli->query($sqlInsertAttendance)) {
                                        $loadFlag = '1';
                                    } else {
                                        $loadFlag = '0';
                                    }
                            }
                        }
                    }
                }

                break;

                /** Cuenta con 4 registros */
            case 4:
                $sqlGetIn = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                    FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                    ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1";
                $resultGetIn = $mysqli->query($sqlGetIn);
                if ($resultGetIn = $mysqli->query($sqlGetIn)) {
                    while ($rowGetIn = $resultGetIn->fetch_assoc()) {
                        $recordTime1 = $rowGetIn['RECORD_TIME'];
                        $incidenceIn = '07';

                        //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime1 . ' - ' . $incidenceIn . '<br>';


                                $sqlValReg = "SELECT DISTINCT AttendanceId, ATTENDANCE_TIME FROM admin_attendance WHERE NOM_ID = '$idNom1' AND ATTENDANCE_DATE = '$recordDate1' AND IN_OUT = 1";
                                $resultValReg = $mysqli->query($sqlValReg);
                                if ($resultValReg->num_rows > 0) {
                                    while ($rowValReg = $resultValReg->fetch_assoc()) {
                                        $idCheck = $rowValReg['AttendanceId'];
                                        $timeCheck = $rowValReg['ATTENDANCE_TIME'];
                                        $updateAttendance = "UPDATE admin_attendance SET ATTENDANCE_TIME = '$recordTime1', TINC = '$incidenceIn' WHERE AttendanceId = '$idCheck'";
                                        if ($mysqli->query($updateAttendance)) {
                                        }
                                    }
                                } else {

                        $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                        VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime1','$incidenceIn','1','B')";
                        if ($mysqli->query($sqlInsertAttendance)) {
                            $loadFlag = '1';
                        } else {
                            $loadFlag = '0';
                        }
                    }

                        $sqlGetOut = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                            FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                            ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME DESC LIMIT 1";
                        $resultGetOut = $mysqli->query($sqlGetOut);
                        if ($resultGetOut = $mysqli->query($sqlGetOut)) {
                            while ($rowGetOut = $resultGetOut->fetch_assoc()) {
                                $recordTime2 = $rowGetOut['RECORD_TIME'];
                                $incidenceOut = '00';
                                $flagOut = '2';

                                //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime2 . ' - ' . $incidenceIn . '<br>';

                                $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime2',00,'2','B')";
                                if ($mysqli->query($sqlInsertAttendance)) {
                                    $loadFlag = '1';
                                } else {
                                    $loadFlag = '0';
                                }

                                $sqlGetBreakStart = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                                    FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                                    ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1 OFFSET 1";
                                $resultGetBreakStart = $mysqli->query($sqlGetBreakStart);
                                if ($resultGetBreakStart = $mysqli->query($sqlGetBreakStart)) {
                                    while ($rowGetBreakStart = $resultGetBreakStart->fetch_assoc()) {
                                        $recordTime3 = $rowGetBreakStart['RECORD_TIME'];
                                        $incidenceIn = '00';

                                        //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime3 . ' - ' . $incidenceIn . '<br>';

                                        $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                        VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime3',00,'3','B')";
                                        if ($mysqli->query($sqlInsertAttendance)) {
                                            $loadFlag = '1';
                                        } else {
                                            $loadFlag = '0';
                                        }


                                        $sqlGetBreakEnd = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                                            FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                                            ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1 OFFSET 2";
                                        $resultGetBreakEnd = $mysqli->query($sqlGetBreakEnd);
                                        if ($resultGetBreakEnd = $mysqli->query($sqlGetBreakEnd)) {
                                            while ($rowGetBreakEnd = $resultGetBreakEnd->fetch_assoc()) {
                                                $recordTime4 = $rowGetBreakEnd['RECORD_TIME'];
                                                $incidenceIn = '00';

                                                //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime4 . ' - ' . $incidenceIn . '<br>';

                                                $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                                VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime4',00,'4','B')";
                                                if ($mysqli->query($sqlInsertAttendance)) {
                                                    $loadFlag = '1';
                                                } else {
                                                    $loadFlag = '0';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;

            default:
                $sqlGetIn = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                    FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                    ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1";
                $resultGetIn = $mysqli->query($sqlGetIn);
                if ($resultGetIn = $mysqli->query($sqlGetIn)) {
                    while ($rowGetIn = $resultGetIn->fetch_assoc()) {
                        $recordTime1 = $rowGetIn['RECORD_TIME'];
                        $incidenceIn = '07';

                        //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime1 . ' - ' . $incidenceIn . '<br>';

                        $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                        VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime1','$incidenceIn','1','B')";
                        if ($mysqli->query($sqlInsertAttendance)) {
                            $loadFlag = '1';
                        } else {
                            $loadFlag = '0';
                        }

                        $sqlGetOut = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                            FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                            ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME DESC LIMIT 1";
                        $resultGetOut = $mysqli->query($sqlGetOut);
                        if ($resultGetOut = $mysqli->query($sqlGetOut)) {
                            while ($rowGetOut = $resultGetOut->fetch_assoc()) {
                                $recordTime2 = $rowGetOut['RECORD_TIME'];
                                $incidenceOut = '00';
                                $flagOut = '2';

                                //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime2 . ' - ' . $incidenceIn . '<br>';

                                $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime2',00,'2','B')";
                                if ($mysqli->query($sqlInsertAttendance)) {
                                    $loadFlag = '1';
                                } else {
                                    $loadFlag = '0';
                                }

                                $sqlGetBreakStart = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                                    FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                                    ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME ASC LIMIT 1 OFFSET 1";
                                $resultGetBreakStart = $mysqli->query($sqlGetBreakStart);
                                if ($resultGetBreakStart = $mysqli->query($sqlGetBreakStart)) {
                                    while ($rowGetBreakStart = $resultGetBreakStart->fetch_assoc()) {
                                        $recordTime3 = $rowGetBreakStart['RECORD_TIME'];
                                        $incidenceIn = '00';

                                        //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime3 . ' - ' . $incidenceIn . '<br>';

                                        $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                        VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime3',00,'3','B')";
                                        if ($mysqli->query($sqlInsertAttendance)) {
                                            $loadFlag = '1';
                                        } else {
                                            $loadFlag = '0';
                                        }

                                        $sqlGetBreakEnd = "SELECT DISTINCT BTC.ID_NOM, BTC.RECORD_DATE, BTC.RECORD_TIME
                                            FROM biometrictimeclock BTC WHERE BTC.ID_NOM = '$idNom1' AND RECORD_DATE = '$recordDate1'
                                            ORDER BY BTC.ID_NOM, BTC.RECORD_DATE ASC, BTC.RECORD_TIME DESC LIMIT 1 OFFSET 1";
                                        $resultGetBreakEnd = $mysqli->query($sqlGetBreakEnd);
                                        if ($resultGetBreakEnd = $mysqli->query($sqlGetBreakEnd)) {
                                            while ($rowGetBreakEnd = $resultGetBreakEnd->fetch_assoc()) {
                                                $recordTime4 = $rowGetBreakEnd['RECORD_TIME'];
                                                $incidenceIn = '00';

                                                //echo $idNom1 . ' - ' . $codeDay . ' - ' . $recordDate1 . ' - ' . $recordTime4 . ' - ' . $incidenceIn . '<br>';

                                                $sqlInsertAttendance = "INSERT IGNORE INTO admin_attendance(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) 
                                                VALUES ('$idNom1','$codeDay','$recordDate1','$recordTime4',00,'4','B')";
                                                if ($mysqli->query($sqlInsertAttendance)) {
                                                    $loadFlag = '1';
                                                } else {
                                                    $loadFlag = '0';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envío de Empleados</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Asistencias",
            text: "Asistencias generadas correctamente",
            icon: "success",
            button: "Volver",
        }).then(function() {
            window.location = "../index.php?id=<?php echo $user_active; ?>";
        });
    </script>

</body>

</html>