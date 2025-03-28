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

use PhpOffice\PhpSpreadsheet\IOFactory;

$sqlDelete = "DELETE FROM code_schedule_groups WHERE OFICIAL_SCHEDULE = 'Y'";
if ($mysqli->query($sqlDelete)) {

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
            $encabezados[0] = 'CODE';
            $encabezados[1] = 'DESCRIPTION';

            // Construir la parte de la consulta SQL para los nombres de las columnas
            $columnas = implode(", ", $encabezados);
            try{
                // Iterar sobre las filas del archivo CSV
                while (($fila = fgetcsv($csvFile)) !== false) {
                    // Escapar los valores para prevenir inyección de SQL
                    $valoresEscapados = array_map(array($mysqli, 'real_escape_string'), $fila);
                    // Construir la parte de la consulta SQL para los valores
                    $valores = implode("', '", $valoresEscapados);
                    // Construir y ejecutar la consulta SQL
                    $insert = "INSERT IGNORE INTO code_schedule_groups ($columnas) VALUES ('$valores');";
                    if ($mysqli->query($insert)) {
                        $message = 'Layout de Grupos -Horarios Convertido y Cargado correctamente.';
                        $icon = 'success';
                    } else {
                        $message = 'Error cargando Grupos -Horarios';
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
            $message = 'Error cargano el Archivo';
            $icon = 'error';
        }
    }
} else {
    $message = 'Error Limpiando base';
    $icon = 'error';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envío de Grupos -Horarios</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Carga de Grupos -Horarios",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../schedules.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>