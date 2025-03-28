<?php

require '../../../logic/conn.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../../index.php');
    exit();
} else {    
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
    $user_sesion = $_SESSION['session'];
}

$payrollPeriodID = $_GET['id'];
$selectedDate = '';
$status = '';
$content = '';
$id_nom_employed = '';

$sql_payrollPeriod = "SELECT PRP.DESCRIPTION, PRP.START_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID'";
$result_payollPeriod = $mysqli -> query($sql_payrollPeriod);
while ($row_payrollPeriod = $result_payollPeriod -> fetch_assoc()) {
    $code = $row_payrollPeriod['START_DATE'];
    $description = $row_payrollPeriod['DESCRIPTION'];
}

require 'query_reports.php';

// Ruta donde deseas guardar el archivo
$ruta_carpeta = "../files/";

// Nombre del archivo
$file_name = "Periodo ".$code.' '.$description." ".$user_sesion.".txt";

// Abre o crea el archivo en modo escritura
$file = fopen($ruta_carpeta . $file_name, "w") or die("No se pudo abrir el archivo.");

$sqlAbsences = "SELECT EMP.ID_NOM, EMP.INSTITUTION, EMP.PAYROLL, BTC.RECORD_DATE
    FROM biometrictimeclock BTC 
    INNER JOIN employed EMP ON BTC.ID_NOM = EMP.ID_NOM
    WHERE BTC.STATUS = 'Falta Injustificada' AND EMP.SUPERVISOR_ID = '$user_active' AND
        BTC.RECORD_DATE BETWEEN (SELECT START_DATE FROM payroll_period WHERE ID = '$payrollPeriodID') AND (SELECT END_DATE FROM payroll_period WHERE ID = '$payrollPeriodID')";
$resultAbsences = $mysqli->query($sqlAbsences);

if ($resultAbsences->num_rows > 0) {
    while ($rowAbsences = $resultAbsences->fetch_assoc()) {
        $organization = $rowAbsences['INSTITUTION'];
        $employed = $rowAbsences['ID_NOM'];
        $date = $rowAbsences['RECORD_DATE'];
        $payroll = $rowAbsences['PAYROLL'];
        $status = '01';
            
        $content = $organization . ',' . $employed . ',' . date('d-m-Y', strtotime($date)) . ',' . $payroll . ',' . $status . ','; 
    
        // Escribe el contenido en el archivo
        // // Añadir nueva línea al final de cada entrada en caso de que no esté en blanco
        if ($content !== '') {
            fwrite($file, $content . "\n");
        }
    }

    // Cierra el archivo
    fclose($file);

    header("Content-disposition: attachment; filename=".$ruta_carpeta.$file_name);
    header("Content-type: MIME");
    readfile($ruta_carpeta.$file_name);
} else {
    //echo 'No se encontraron resultados';
    fwrite($file, 'No se encontraron resultados en el periodo seleccionado');

        // Cierra el archivo
        fclose($file);

        header("Content-disposition: attachment; filename=".$ruta_carpeta.$file_name);
        header("Content-type: MIME");
        readfile($ruta_carpeta.$file_name);
}

?>