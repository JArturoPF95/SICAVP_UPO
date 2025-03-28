<?php
require '../lib/vendor/autoload.php'; // Reemplaza con la ruta correcta
require 'logic/conn.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', '¡Hola Mundo!');

// Guardar el archivo como XLSX
$writer = new Xlsx($spreadsheet);
$writer->save('hola_mundo.xlsx');

echo "El archivo Excel se ha creado correctamente.\n";
