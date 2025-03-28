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

$id = $_GET['id'];
$codeDay = '';
$program = '';
$selectedDay = '';
$today = date('Y-m-d');
$num = 1;
$level = '';

$sqlGetSesion = "SELECT * FROM code_sesion_academic WHERE CODE_NOM = '$user_sesion'";
$resultSesion = $mysqli->query($sqlGetSesion);
if ($resultSesion->num_rows > 0) {
    while ($rowSesion = $resultSesion->fetch_assoc()) {
        $sesionAcademic = $rowSesion['LONG_DESC'];
    }
}

//Obtenemos fechas de 
$sqlPayrollPeriod = "SELECT * FROM payroll_period WHERE ID = '$id'";
$resultPayrollPeriod = $mysqli->query($sqlPayrollPeriod);
if ($resultPayrollPeriod->num_rows > 0) {
    while ($rowPayrollPeriod = $resultPayrollPeriod->fetch_assoc()) {
        $payrollCode = $rowPayrollPeriod['ID'];
        $start_date = $rowPayrollPeriod['START_DATE'];
        $end_date = $rowPayrollPeriod['END_DATE'];
        $code = $rowPayrollPeriod['CODE'];
        $description = $rowPayrollPeriod['DESCRIPTION'];
    }
}

//Librerías Excel

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill};
//use PhpOffice\PhpSpreadsheet\Worksheet\Row;

//EncabezadoPrincipal
$titles = [
    'font' => [
        'color' => [
            'rgb' => '000000'
        ],
        'bold' => true,
        'size' => 11
    ],
];

$head = [
    'font' => [
        'color' => [
            'rgb' => '000000'
        ],
        'bold' => true,
        'size' => 9
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'DAEEF3'
        ]
    ],
];

$infoHead = [
    'font' => [
        'color' => [
            'rgb' => '000000'
        ],
        'bold' => true,
        'size' => 9
    ],
];

$content = [
    'font' => [
        'color' => [
            'rgb' => '000000'
        ],
        'bold' => false,
        'size' => 8
    ],
];

$reportePeriodoNomina = new SpreadSheet();
$reportePeriodoNomina->getProperties()->setCreator("TI NACER")->setTitle('Periodo Docente ' . $end_date);

$reportePeriodoNomina->setActiveSheetIndex(0);

$hojaActiva = $reportePeriodoNomina->getActiveSheet();
$hojaActiva->setTitle('Periodo Docente ' . $end_date);

//Títulos
$hojaActiva->mergeCells('A1:T1');
$hojaActiva->getStyle('A1:T1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$hojaActiva->setCellValue('A1', 'COORDINACIÓN DE NÓMINAS DOCENTES');
$hojaActiva->getStyle('A1:T1')->applyFromArray($titles);

$hojaActiva->mergeCells('A2:T2');
$hojaActiva->getStyle('A2:T2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$hojaActiva->setCellValue('A2', 'REPORTE DE INCIDENCIAS');
$hojaActiva->getStyle('A2:T2')->applyFromArray($titles);

//Encabezados
//Combinamos y Centramos
$hojaActiva->mergeCells('A4:B4');
$hojaActiva->getStyle('A4:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('A4', 'UNIVERSIDAD:');
$hojaActiva->getColumnDimension('C')->setAutoSize(true);
$hojaActiva->setCellValue('C4', 'EDUCACION UNIVERSITARIA SAN LUIS POTOSI');
$hojaActiva->getStyle('A4:D4')->applyFromArray($infoHead);

$hojaActiva->mergeCells('A5:B5');
$hojaActiva->getStyle('A5:B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('A5', 'CAMPUS:');
$hojaActiva->getColumnDimension('C')->setAutoSize(true);
$hojaActiva->setCellValue('C5', $sesionAcademic);
$hojaActiva->getStyle('A5:D5')->applyFromArray($infoHead);

$hojaActiva->mergeCells('A6:B6');
$hojaActiva->getStyle('A6:B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('A6', 'PERIODO DE PAGO:');
$hojaActiva->getColumnDimension('C')->setAutoSize(true);
$hojaActiva->setCellValue('C6', date('d/m/Y', strtotime($start_date)));
$hojaActiva->getColumnDimension('D')->setWidth(13);
$hojaActiva->setCellValue('D6', date('d/m/Y', strtotime($end_date)));
$hojaActiva->getStyle('A6:D6')->applyFromArray($infoHead);

$hojaActiva->mergeCells('A7:B7');
$hojaActiva->getStyle('A7:B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('A7', 'NÓMINA:');
$hojaActiva->getColumnDimension('C')->setAutoSize(true);
$hojaActiva->setCellValue('C7', 'DOCENTES');
$hojaActiva->getStyle('A7:D7')->applyFromArray($infoHead);

$hojaActiva->mergeCells('A8:B8');
$hojaActiva->getStyle('A8:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('A8', 'PERIODO DE INCIDENCIAS:');
$hojaActiva->getColumnDimension('C')->setAutoSize(true);
$hojaActiva->setCellValue('C8', date('d/m/Y', strtotime($start_date)));
$hojaActiva->getColumnDimension('D')->setWidth(13);
$hojaActiva->setCellValue('D8', date('d/m/Y', strtotime($end_date)));
$hojaActiva->getStyle('A8:D8')->applyFromArray($infoHead);

$hojaActiva->mergeCells('G4:H4');
$hojaActiva->getStyle('G4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('G4', 'FECHA:');
$hojaActiva->getColumnDimension('I')->setWidth(13);
$hojaActiva->setCellValue('I4', date('d/m/Y', strtotime($today)));
$hojaActiva->getStyle('G4:I4')->applyFromArray($infoHead);

$hojaActiva->mergeCells('G5:H5');
$hojaActiva->getStyle('G5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('G5', 'PERIODO CUATRIMESTRAL:');
$hojaActiva->getColumnDimension('I')->setWidth(13);
$hojaActiva->setCellValue('I5', '');
$hojaActiva->getStyle('G5:I5')->applyFromArray($infoHead);

$hojaActiva->mergeCells('G7:H7');
$hojaActiva->getStyle('G7:H7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$hojaActiva->setCellValue('G7', 'PERIODO NÓMINA:');
$hojaActiva->getColumnDimension('I')->setWidth(13);
$hojaActiva->setCellValue('I7', $code);
$hojaActiva->getStyle('G7:I7')->applyFromArray($infoHead);

//Encabezados tabla
$hojaActiva->getColumnDimension('A')->setWidth(6.43);
$hojaActiva->setCellValue('A22', 'No.');
$hojaActiva->getColumnDimension('B')->setWidth(15.29);
$hojaActiva->setCellValue('B22', 'No. DOCENTE');
$hojaActiva->getColumnDimension('C')->setWidth(36.57);
$hojaActiva->setCellValue('C22', 'NOMBRE DOCENTE');
$hojaActiva->getColumnDimension('D')->setWidth(13);
$hojaActiva->setCellValue('D22', 'NIVEL');
$hojaActiva->getColumnDimension('E')->setWidth(13);
$hojaActiva->setCellValue('E22', 'Tipo de TAB');
$hojaActiva->getColumnDimension('F')->setWidth(13);
$hojaActiva->setCellValue('F22', 'No. de Grupos / Materia');
$hojaActiva->getColumnDimension('G')->setWidth(13);
$hojaActiva->setCellValue('G22', 'Bachillerato Tecnológico');
$hojaActiva->getColumnDimension('H')->setWidth(13);
$hojaActiva->setCellValue('H22', 'Bachillerato General RC');
$hojaActiva->getColumnDimension('I')->setWidth(13);
$hojaActiva->setCellValue('I22', 'Materia Escolarizada / Presencial / Tradicional Liquidación');
$hojaActiva->getColumnDimension('J')->setWidth(13);
$hojaActiva->setCellValue('J22', 'Materia Mixta  / Ejecutiva Liquidación');
$hojaActiva->getColumnDimension('K')->setWidth(13);
$hojaActiva->setCellValue('K22', 'Materia Escolarizada / Presencial / Tradicional RC');
$hojaActiva->getColumnDimension('L')->setWidth(13);
$hojaActiva->setCellValue('L22', 'Materia Mixta / Blended Modalidad Escolarizada RC');
$hojaActiva->getColumnDimension('M')->setWidth(13);
$hojaActiva->setCellValue('M22', 'Materia OnLine Modalidad Escolarizada a 14 semanas');
$hojaActiva->getColumnDimension('N')->setWidth(13);
$hojaActiva->setCellValue('N22', 'Materia Presencial / Salud RC');
$hojaActiva->getColumnDimension('O')->setWidth(13);
$hojaActiva->setCellValue('O22', 'Materia Prácticas Clínicas RC');
$hojaActiva->getColumnDimension('P')->setWidth(13);
$hojaActiva->setCellValue('P22', 'Materias Especiales');
$hojaActiva->getColumnDimension('Q')->setWidth(13);
$hojaActiva->setCellValue('Q22', 'No. de horas de la materia a la semana');
$hojaActiva->getColumnDimension('R')->setWidth(13);
$hojaActiva->setCellValue('R22', 'Total de horas/semana  por No. Grupos');
$hojaActiva->getColumnDimension('S')->setWidth(13);
$hojaActiva->setCellValue('S22', 'Total de Horas por catorcena');
$hojaActiva->getColumnDimension('T')->setWidth(13);
$hojaActiva->setCellValue('T22', 'TOTAL INCIDENCIAS');
$hojaActiva->getColumnDimension('U')->setWidth(13);
$hojaActiva->setCellValue('U22', 'No. Horas incidencia +/-');
$hojaActiva->getColumnDimension('V')->setWidth(13);
$hojaActiva->setCellValue('V22', 'TOTAL INCIDENCIAS');
$hojaActiva->getColumnDimension('W')->setWidth(13);
$hojaActiva->setCellValue('W22', 'OBSERVACIONES');
$hojaActiva->getColumnDimension('X')->setWidth(13);
$hojaActiva->setCellValue('X22', '');

$hojaActiva->getStyle('A22:X22')->applyFromArray($head);
$hojaActiva->getRowDimension(22)->setRowHeight(60.75);
$hojaActiva->getStyle('A22:X22')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$hojaActiva->getStyle('A22:X22')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

$style = $hojaActiva->getStyle('A22:X22'); //Ajustar texto
$alignment = $style->getAlignment();
$alignment->setWrapText(true);

// Variable para mantener el número de fila actual
$row = 23;
$rowHead = 22;

//Cuerpo de la tabla

require_once 'query_reports.php';
$sqlIncidenceReport;
$resultIncidenceReport = $mysqli->query($sqlIncidenceReport);
if ($resultIncidenceReport->num_rows > 0) {
    while ($rows = $resultIncidenceReport->fetch_assoc()) {

        $hojaActiva->setCellValue('A' . $row, $num);
        $hojaActiva->setCellValue('B' . $row, $rows['ID_PWC']);
        $hojaActiva->setCellValue('C' . $row, $rows['DOC_NAME']);


        if (strpos($rows['GENERAL_ED'], 'Clínica') !== false) {
            $level = 'LCS';
        } elseif (strpos($rows['GENERAL_ED'], 'Salud') !== false) {
            $level = 'LCS';
        } elseif (strpos($rows['GENERAL_ED'], 'OnLine') !== false or strpos($rows['PROGRAM'], 'On') !== false) {
            $level = 'LOL';
        } elseif (strpos($rows['GENERAL_ED'], 'Blend') !== false and (strpos($rows['PROGRAM'], 'Mix') !== false or strpos($rows['PROGRAM'], 'Eje') !== false) and strpos($rows['CURRICULUM'], 'Ref Cu') !== false) {
            $level = 'LBD';
        } elseif ((strpos($rows['PROGRAM'], 'Mix') !== false or strpos($rows['PROGRAM'], 'Eje') !== false) and strpos($rows['CURRICULUM'], 'Ref') === false) {
            $level = 'LEJ';
        } elseif (strpos($rows['PROGRAM'], 'BG') !== false) {
            $level = 'B';
        } elseif (strpos($rows['PROGRAM'], 'BT') !== false) {
            $level = 'B';
        } elseif (strpos($rows['CURRICULUM'], 'Ref Cu') === false && (strpos($rows['PROGRAM'], 'Mix') === false or strpos($rows['PROGRAM'], 'Eje') === false)) {
            $level = 'L';
        } elseif (strpos($rows['CURRICULUM'], 'Ref Cu') !== false && (strpos($rows['PROGRAM'], 'Mix') === false or strpos($rows['PROGRAM'], 'Eje') === false)) {
            $level = 'L';
        } else {
            $level = 'LE';
        }

        $hojaActiva->setCellValue('D' . $row, $level);

        if (strpos($rows['GENERAL_ED'], 'Clínica') !== false) {
            $hojaActiva->setCellValue('O' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif (strpos($rows['GENERAL_ED'], 'Salud') !== false) {
            $hojaActiva->setCellValue('N' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif (strpos($rows['GENERAL_ED'], 'OnLine') !== false or strpos($rows['PROGRAM'], 'On') !== false) {
            $hojaActiva->setCellValue('M' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif (strpos($rows['GENERAL_ED'], 'Blend') !== false and (strpos($rows['PROGRAM'], 'Mix') !== false or strpos($rows['PROGRAM'], 'Eje') !== false) and strpos($rows['CURRICULUM'], 'Ref Cu') !== false) {
            $hojaActiva->setCellValue('L' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif ((strpos($rows['PROGRAM'], 'Mix') !== false or strpos($rows['PROGRAM'], 'Eje') !== false) and strpos($rows['CURRICULUM'], 'Ref') === false) {
            $hojaActiva->setCellValue('J' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif (strpos($rows['PROGRAM'], 'BG') !== false) {
            $hojaActiva->setCellValue('H' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif (strpos($rows['PROGRAM'], 'BT') !== false) {
            $hojaActiva->setCellValue('G' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif (strpos($rows['CURRICULUM'], 'Ref Cu') === false && (strpos($rows['PROGRAM'], 'Mix') === false or strpos($rows['PROGRAM'], 'Eje') === false)) {
            $hojaActiva->setCellValue('I' . $row, '-' . $rows['CONT_ATTEN']);
        } elseif (strpos($rows['CURRICULUM'], 'Ref Cu') !== false && (strpos($rows['PROGRAM'], 'Mix') === false or strpos($rows['PROGRAM'], 'Eje') === false)) {
            $hojaActiva->setCellValue('K' . $row, '-' . $rows['CONT_ATTEN']);
        } else {
            $hojaActiva->setCellValue('P' . $row, '-' . $rows['CONT_ATTEN']);
        }

        $hojaActiva->setCellValue('T' . $row, '-' . $rows['CONT_ATTEN']);
        $hojaActiva->setCellValue('U' . $row, '-' . $rows['CONT_ATTEN']);
        $hojaActiva->setCellValue('V' . $row, '-' . $rows['CONT_ATTEN']);

        $row++;
        $num++;
    }
}


// Establecer el filtro automático
$firstRow = 22;
$lastRow = $row - 1;
$hojaActiva->setAutoFilter("A" . $firstRow . ":X" . $lastRow);
$hojaActiva->getStyle("A23:X" . $lastRow)->applyFromArray($content);

// Ocultar las columnas
$hojaActiva->getColumnDimension('F')->setVisible(false);
$hojaActiva->getColumnDimension('Q')->setVisible(false);
$hojaActiva->getColumnDimension('R')->setVisible(false);
$hojaActiva->getColumnDimension('S')->setVisible(false);

// Ocultar las filas
for ($rowHeader = 10; $rowHeader <= 20; $rowHeader++) {
    $hojaActiva->getRowDimension($rowHeader)->setVisible(false);
}

$filename = 'Docente Nómina ' . $end_date . ' ' . $description . '.xlsx';

$writer = new Xlsx($reportePeriodoNomina);
$writer->save($filename);

// Enviar encabezados HTTP para forzar la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Leer el archivo y enviarlo al flujo de salida
readfile($filename);

// Eliminar el archivo temporal después de enviarlo al cliente
unlink($filename);
