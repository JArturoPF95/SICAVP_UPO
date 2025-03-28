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

$year = date('Y');

$start_date = '';
$end_date = '';
$idnom = '';
require_once 'query.php';

//Librerías Excel

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill};
//use PhpOffice\PhpSpreadsheet\Worksheet\Row;

$title = [
    'font' => [
        'color' => [
            'rgb' => '000000'
        ],
        'bold' => true,
        'size' => 12
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'DAEEF3'
        ]
    ],
];

$head = [
    'font' => [
        'color' => [
            'rgb' => 'FFFFFF'
        ],
        'bold' => true,
        'size' => 12
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => '022859'
        ]
    ],
];

$reporteVacations = new SpreadSheet();
$reporteVacations->getProperties()->setCreator("TI NACER")->setTitle('Vacaciones ' . $year);

$reporteVacations->setActiveSheetIndex(0);

$hojaActiva = $reporteVacations->getActiveSheet();
$hojaActiva->setTitle('Vacaciones ' . $year);

//Títulos
$hojaActiva->mergeCells('A2:Q2');
$hojaActiva->getStyle('A2:Q2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$hojaActiva->setCellValue('A2', 'Vacaciones '.$year);
$hojaActiva->getStyle('A2:Q2')->applyFromArray($title);

//Encabezados tabla
$hojaActiva->getColumnDimension('A')->setWidth(14);
$hojaActiva->setCellValue('A3', 'ID Nómina');
$hojaActiva->getColumnDimension('B')->setWidth(66);
$hojaActiva->setCellValue('B3', 'Nombre');
$hojaActiva->getColumnDimension('C')->setWidth(16);
$hojaActiva->setCellValue('C3', 'Antiguedad');
$hojaActiva->getColumnDimension('D')->setWidth(16);
$hojaActiva->setCellValue('D3', 'Días por Ley');
$hojaActiva->getColumnDimension('E')->setWidth(16);
$hojaActiva->setCellValue('E3', 'Enero');
$hojaActiva->getColumnDimension('F')->setWidth(16);
$hojaActiva->setCellValue('F3', 'Febrero');
$hojaActiva->getColumnDimension('G')->setWidth(16);
$hojaActiva->setCellValue('G3', 'Marzo');
$hojaActiva->getColumnDimension('H')->setWidth(16);
$hojaActiva->setCellValue('H3', 'Abril');
$hojaActiva->getColumnDimension('I')->setWidth(16);
$hojaActiva->setCellValue('I3', 'Mayo');
$hojaActiva->getColumnDimension('J')->setWidth(16);
$hojaActiva->setCellValue('J3', 'Junio');
$hojaActiva->getColumnDimension('K')->setWidth(16);
$hojaActiva->setCellValue('K3', 'Julio');
$hojaActiva->getColumnDimension('L')->setWidth(16);
$hojaActiva->setCellValue('I3', 'Agosto');
$hojaActiva->getColumnDimension('M')->setWidth(16);
$hojaActiva->setCellValue('M3', 'Septiembre');
$hojaActiva->getColumnDimension('N')->setWidth(16);
$hojaActiva->setCellValue('N3', 'Octubre');
$hojaActiva->getColumnDimension('O')->setWidth(16);
$hojaActiva->setCellValue('O3', 'Noviembre');
$hojaActiva->getColumnDimension('P')->setWidth(16);
$hojaActiva->setCellValue('P3', 'Diciembre');
$hojaActiva->getColumnDimension('Q')->setWidth(16);
$hojaActiva->setCellValue('Q3', 'Días Restantes');


$hojaActiva->getStyle('A3:Q3')->applyFromArray($head);

// Variable para mantener el número de fila actual
$row = 4;
$rowHead = 3;

$sqlDownloadVacations;
$resultDownloadVac = $mysqli -> query($sqlDownloadVacations);
if ($resultDownloadVac -> num_rows > 0) {
    while ($rows = $resultDownloadVac->fetch_assoc()) {

        $daysLeft = $rows['DAYS_BY_LAW'] - $rows['DAYS'];

        $hojaActiva->setCellValue('A' . $row, $rows['ID_NOM']);
        $hojaActiva->setCellValue('B' . $row, $rows['EMP_NAME']);
        $hojaActiva->setCellValue('C' . $row, $rows['YEARS'] . ' años');
        $hojaActiva->setCellValue('D' . $row, $rows['DAYS_BY_LAW']);
        $hojaActiva->setCellValue('E' . $row, $rows['JANUARY']);
        $hojaActiva->setCellValue('F' . $row, $rows['FEBRUARY']);
        $hojaActiva->setCellValue('G' . $row, $rows['MARCH']);
        $hojaActiva->setCellValue('H' . $row, $rows['APRIL']);
        $hojaActiva->setCellValue('I' . $row, $rows['MAY']);
        $hojaActiva->setCellValue('J' . $row, $rows['JUNE']);
        $hojaActiva->setCellValue('K' . $row, $rows['JULY']);
        $hojaActiva->setCellValue('L' . $row, $rows['AUGUST']);
        $hojaActiva->setCellValue('M' . $row, $rows['SEPTEMBER']);
        $hojaActiva->setCellValue('N' . $row, $rows['OCTOBER']);
        $hojaActiva->setCellValue('O' . $row, $rows['NOVEMBER']);
        $hojaActiva->setCellValue('P' . $row, $rows['DECEMBER']);
        $hojaActiva->setCellValue('Q' . $row, $daysLeft);

        $row++;

    }
}

// Establecer el filtro automático
$firstRow = 3;
$lastRow = $row - 1;
$hojaActiva->setAutoFilter("A" . $firstRow . ":Q" . $lastRow);

$filename = 'Vacaciones Administrativos '.$year.'.xlsx';

$writer = new Xlsx($reporteVacations);
$writer->save($filename);

// Enviar encabezados HTTP para forzar la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Leer el archivo y enviarlo al flujo de salida
readfile($filename);

// Eliminar el archivo temporal después de enviarlo al cliente
unlink($filename);