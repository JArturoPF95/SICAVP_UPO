<?php
require '../../../logic/conn.php';
require '../../../../lib/vendor/autoload.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$message = '';
$icon = '';
$year = date('Y');

use PhpOffice\PhpSpreadsheet\IOFactory;

$sqlDelete = "DELETE FROM calendar WHERE YEAR = '$year'";
if ($mysqli->query($sqlDelete)) {

    // Iterar sobre cada día del año - Creamos el calendario del año
    for ($month = 1; $month <= 12; $month++) {
        $monthDays = cal_days_in_month(CAL_GREGORIAN, $month, $year); // Obtener el número de días del mes
        for ($days = 1; $days <= $monthDays; $days++) {

            $fecha = "$year-$month-$days";

            $sqlInsertCalendar = "INSERT IGNORE INTO calendar (YEAR, CALENDAR_DATE) VALUES ('$year','$fecha')";
            if ($mysqli->query($sqlInsertCalendar)) {

                $updateDay = "UPDATE calendar SET CODE_DAY = (DAYOFWEEK(CALENDAR_DATE) - 1), WEEK = WEEK(CALENDAR_DATE, 1);";
                if ($mysqli->query($updateDay) === true) {

                    if (isset($_FILES['archivo_xlsx'])) {
                        $archivo_xlsx = $_FILES['archivo_xlsx'];

                        // Verificar si no hay errores al subir el archivo
                        if ($archivo_xlsx['error'] === UPLOAD_ERR_OK) {

                            // Ruta temporal donde se guardó el archivo subido
                            $archivoTemporal = $archivo_xlsx['tmp_name'];

                            // Cargar el archivo Excel desde la ruta temporal
                            $spreadsheet = IOFactory::load($archivoTemporal);

                            // Obtener el escritor para CSV
                            $writer = IOFactory::createWriter($spreadsheet, 'Csv');
                            $writer->setDelimiter(',');
                            $writer->setEnclosure('"');
                            $writer->setSheetIndex(0); // Puedes ajustar el índice de la hoja según sea necesario

                            // Guardar el archivo CSV
                            $writer->save($archivo_xlsx['tmp_name']);

                            //echo "El archivo Excel se ha convertido correctamente a formato CSV.";

                            // Cerrar el archivo CSV
                            $writer = null;

                            // Abrir el archivo CSV en modo lectura
                            $csvFile = fopen($archivo_xlsx['tmp_name'], 'r');

                            // Leer la primera fila para obtener los nombres de las columnas
                            $encabezados = fgetcsv($csvFile);

                            // Eliminar caracteres no deseados del principio del primer encabezado de columna
                            $encabezados[0] = trim($encabezados[0]);

                            //Asignamos nombre al encabezado
                            $encabezados[0] = 'CALENDAR_DATE';
                            $encabezados[1] = 'DESCRIPTION';

                            // Construir la parte de la consulta SQL para los nombres de las columnas
                            $columnas = implode(", ", $encabezados);
                            // Iterar sobre las filas del archivo CSV
                            try{
                                while (($fila = fgetcsv($csvFile)) !== false) {

                                    //Convertir datos a formato fecha
                                    $calendarDate = date('Y-m-d', strtotime($fila[0]));
                                    $description = $fila[1];

                                    $updateDays = "UPDATE calendar SET DAY_OF_REST = '1', DESCRIPTION = '$description' WHERE CALENDAR_DATE = '$calendarDate';";
                                    if ($mysqli->query($updateDays)) {
                                            $message = 'Calendario Cargado correctamente';
                                            $icon = 'success';
                                    } else {
                                        $message = 'Error Cargando Calendario';
                                        $icon = 'error';
                                    }
                                }
                            } catch (mysqli_sql_exception $ex) {
                                $message = 'Se ha producido un error al insertar los datos. Favor de cargar el archivo correcto.'; // . $ex -> getMessage();
                                $icon = 'warning';
                            }

                            // Cerrar el csvFile de archivos y la conexión a la base de datos
                            fclose($csvFile);
                        } else {
                            $message = 'Error cargando el Archivo';
                            $icon = 'error';
                        }
                    }
                }
            }
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
    <title>Envío de Días Festivos</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Carga de Días Festivos",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../vacations.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>