<?php

require '../../../../lib/vendor/autoload.php';

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

$contCells = 1;

$contCells2 = 0;

$selectedDate = date('d/m/Y');

$status = '';

$id_nom_employed  = '';

$reportePeriodoNomina = '';

function sumarDiff($hora1, $hora2, $hora3, $hora4) {
    // Convertir a DateTime
    $inicio1 = new DateTime($hora1);
    $fin1 = new DateTime($hora2);
    $inicio2 = new DateTime($hora3);
    $fin2 = new DateTime($hora4);

    // Obtener diferencias en segundos
    $diff1 = $fin1->getTimestamp() - $inicio1->getTimestamp();
    $diff2 = $fin2->getTimestamp() - $inicio2->getTimestamp();

    // Sumar diferencias
    $totalSegundos = $diff1 + $diff2;

    // Formatear como HH:MM:SS
    $horas = floor($totalSegundos / 3600);
    $minutos = floor(($totalSegundos % 3600) / 60);
    $segundos = $totalSegundos % 60;

    return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
}


$sql_payrollPeriod = "SELECT PRP.DESCRIPTION, PRP.START_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID'";

$result_pPeriod = $mysqli->query($sql_payrollPeriod);

while ($rowcodePP = $result_pPeriod->fetch_assoc()) {

    $code = $rowcodePP['START_DATE'];

    $description = $rowcodePP['DESCRIPTION'];

}



require 'query_reports.php';



$sql_payrollPeriod_calendar;

$result_cellsDate = $mysqli->query($sql_payrollPeriod_calendar);



if ($result_cellsDate = $mysqli->query($sql_payrollPeriod_calendar)) {

    while ($rowCells = $result_cellsDate->fetch_assoc()) {

        $contCells++;

    }

}



// Librerías Excel

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill};



// EncabezadoPrincipal

$head = [

    'font' => [

        'color' => ['rgb' => 'FFFFFF'],

        'bold' => true,

        'size' => 14

    ],

    'fill' => [

        'fillType' => Fill::FILL_SOLID,

        'startColor' => ['rgb' => '010440']

    ],

];



// Encabezado Tabla

$tableHead = [

    'font' => [

        'color' => ['rgb' => 'FFFFFF'],

        'bold' => true,

        'size' => 11

    ],

    'fill' => [

        'fillType' => Fill::FILL_SOLID,

        'startColor' => ['rgb' => '5176A6']

    ],

];



$nombredeSpreadsheet = new Spreadsheet();

$nombredeSpreadsheet->getProperties()->setCreator("TIC NACER")->setTitle("Periodo " . $description);



$hojaActiva = $nombredeSpreadsheet->getActiveSheet();

$hojaActiva->setTitle("Periodo " . $description);



$columnLetter = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($contCells);



//Combinamos y Centramos

$hojaActiva->mergeCells('A1:' . $columnLetter . '1');

$hojaActiva->getStyle('A1:' . $columnLetter . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$hojaActiva->getStyle('A1:' . $columnLetter . '1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

$hojaActiva->setCellValue('A1', $code . ' ' . $description . ' ' . $user_sesion);



//Encabezados de tabla

$hojaActiva->getColumnDimension('A')->setAutoSize(true);

$hojaActiva->setCellValue('A2', 'Colaborador');



// Definir la letra de columna inicial

$columnLetter2 = 'B';



if ($result_cellsDate = $mysqli->query($sql_payrollPeriod_calendar)) {

    while ($rowCells = $result_cellsDate->fetch_assoc()) {



        $cellDate = $rowCells['CALENDAR_DATE'];

        $format_cellDate = date('d/m/Y', strtotime($cellDate));



        // Establecer el tamaño de la columna automáticamente

        $hojaActiva->getColumnDimension($columnLetter2)->setAutoSize(true);



        // Escribir la fecha en la celda correspondiente

        $hojaActiva->setCellValue($columnLetter2 . '2', $format_cellDate);



        // Avanzar a la siguiente letra de columna

        $columnLetter2++;

    }

}





$hojaActiva->getStyle('A1:' . $columnLetter . '1')->applyFromArray($head);

$hojaActiva->getStyle('A2:' . $columnLetter . '2')->applyFromArray($tableHead);



$row = 3;

$rowHead = 2;



$sql_employed_report = "SELECT 

DISTINCT

EMP.ID_NOM,

CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) NAME_EMP 

FROM employed EMP

WHERE EMP.SUPERVISOR_ID = '$user_active'

AND (EMP.STATUS = 'A' OR EMP.SEPARATION_DATE BETWEEN (SELECT PRP.START_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID') AND (SELECT PRP.END_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID'))

AND (EMP.STATUS = 'A' OR EMP.ADMISSION_DATE > (SELECT PRP.START_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID'))

ORDER BY EMP.ID_NOM ASC;";

$result_employedReport = $mysqli->query($sql_employed_report);

while ($rowEmpRep = $result_employedReport->fetch_assoc()) {



    $id_nom_employed = $rowEmpRep['ID_NOM'];



    //require_once 'query_reports.php';



    $sql_paryrollPeriod_report_xlsx = "SELECT DISTINCT EMP.ID_NOM
        , CONCAT(EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX,' ',EMP.NAME) NOMBRE
        , DYS.NAME_DAY
        , CAL.CALENDAR_DATE
        , IFNULL( (SELECT CIN.DESCRIP_TINC FROM admin_attendance ATE INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ATE.TINC WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 1 ORDER BY ATE.JUSTIFY DESC LIMIT 1), '') STATUS
        , IFNULL( (SELECT ATE.JUSTIFY FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 1 ORDER BY ATE.JUSTIFY DESC LIMIT 1), '') JUSTIFY
        , IFNULL( (SELECT ATE.ATTENDANCE_TIME FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 1 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), '') CHECK_IN
        , IFNULL( (SELECT ATE.ATTENDANCE_TIME FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 2 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), '') CHECK_OUT
        , IFNULL( (SELECT ATE.ATTENDANCE_TIME FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 3 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), '') BREAK_START
        , IFNULL( (SELECT ATE.ATTENDANCE_TIME FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 4 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), '') BREAK_END
        , IFNULL( TIMEDIFF(
                IFNULL( (SELECT ATE.ATTENDANCE_TIME FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 4 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), ''),
                IFNULL( (SELECT ATE.ATTENDANCE_TIME FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 3 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), '')), '') BREAK_TIME_TAKEN
        FROM admin_attendance ATT
                    INNER JOIN employed EMP ON EMP.ID_NOM = ATT.NOM_ID
                    INNER JOIN code_days DYS ON DYS.CODE_DAY = (DAYOFWEEK(ATTENDANCE_DATE) - 1)
                    INNER JOIN calendar CAL ON CAL.CALENDAR_DATE = ATT.ATTENDANCE_DATE
                    WHERE EMP.SUPERVISOR_ID = '$user_active' AND EMP.ID_NOM = '$id_nom_employed'
                        AND ATT.ATTENDANCE_DATE BETWEEN (SELECT START_DATE FROM payroll_period WHERE ID = '$payrollPeriodID') AND (SELECT END_DATE FROM payroll_period WHERE ID = '$payrollPeriodID')
        ORDER BY EMP.ID_NOM ASC, ATT.ATTENDANCE_DATE ASC;";

    $result_incidenceAttendance = $mysqli->query($sql_paryrollPeriod_report_xlsx);



    $hojaActiva->setCellValue('A' . $row, $id_nom_employed . ' - ' . $rowEmpRep['NAME_EMP']);



    if ($result_incidenceAttendance -> num_rows > 0) {

        while ($rowIncAtt = $result_incidenceAttendance->fetch_assoc()) {

            // Recorremos las fechas en la fila 2 del Excel

            $columnLetter2 = 'B'; // Empezamos en la columna B

            while ($columnLetter2 <= $columnLetter) {

                $headerDate = $hojaActiva->getCell($columnLetter2 . $rowHead)->getValue(); // Obtener valor de la celda

    

    

                // Comparar la fecha de asistencia con la fecha en la celda del Excel

                if ($headerDate == date('d/m/Y', strtotime($rowIncAtt['CALENDAR_DATE']))) {

                    $campusIn = new DateTime($rowIncAtt['CHECK_IN']);
                    $campusOut = new DateTime($rowIncAtt['CHECK_OUT']);
                    $breakStart = new DateTime($rowIncAtt['BREAK_START']);
                    $breakEnd = new DateTime($rowIncAtt['BREAK_END']);

                    if ($rowIncAtt['CHECK_OUT'] == '') {
                        $workTime = 'Sin registro de salida';

                        $timeBreakOut = '';
                    } elseif ($rowIncAtt['BREAK_START'] == '') {
                        $wTime = $campusOut->diff($campusIn);
                        $workTime = $wTime->format('%H:%I:%S') . ' - Sin registros de comida';

                        $timeBreakOut = '';
                    } else {
                        $workTime = sumarDiff($rowIncAtt['CHECK_IN'],$rowIncAtt['BREAK_START'],$rowIncAtt['BREAK_END'],$rowIncAtt['CHECK_OUT']);   

                        $timeBreakOut = "\n".'Salida a Comer: '. substr($rowIncAtt['BREAK_START'],0,8).' a '. substr($rowIncAtt['BREAK_END'],0,8);
                    }



                    //if ($rowIncAtt['STATUS'] !== '') {  

                        $status = $rowIncAtt['STATUS'];

                        //$break = "\n".'Tiempo Comida: '. substr($rowIncAtt['BREAK_TIME_TAKEN'],0,8);

                        $timeInOut = "\n".'Horario: '. substr($rowIncAtt['CHECK_IN'],0,8).' a '. substr($rowIncAtt['CHECK_OUT'],0,8);
                        $workTimePrint = "\n".'Tiempo Laborado: '.$workTime;

                    /**} else {

                        $status = $rowIncAtt['STATUS'];

                        $break = '';

                        $timeInOut = '';

                    }*/

    

                    $hojaActiva->setCellValue($columnLetter2 . $row, $status.$timeInOut.$timeBreakOut.$workTimePrint);

    

                }

                $columnLetter2++;

            }

        }

    } 



    $row++;

}



$firstRow = 2;

$lastRow = $row - 1;

$hojaActiva->setAutoFilter("A" . $firstRow . ":" . $columnLetter . $lastRow);

$hojaActiva->getStyle("A" . $firstRow . ":" . $columnLetter . $lastRow)->getAlignment()->setWrapText(true); // Ajuste de texto

$hojaActiva->getStyle("A" . $firstRow . ":" . $columnLetter . $lastRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Alineación vertical

$hojaActiva->getStyle("A" . $firstRow . ":" . $columnLetter . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT); // Alineación horizontal





// Configura los encabezados antes de enviar la salida

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="' . $description . ' ' . $user_active . '.xlsx"');

header('Cache-Control: max-age=0');

header('Expires: 0');

header('Pragma: public');



// Envía el archivo Excel directamente al navegador

$writer = new Xlsx($nombredeSpreadsheet);

$writer->save('php://output');

exit;



?>