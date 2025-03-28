<?php
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

$message = '';
$icon = '';

use PhpOffice\PhpSpreadsheet\IOFactory;

$sqlDelete = "DELETE FROM code_sesion_academic";
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
            $encabezados[0] = 'CODE_VALUE_KEY';
            $encabezados[1] = 'CODE_VALUE';
            $encabezados[2] = 'SHORT_DESC';
            $encabezados[3] = 'MEDIUM_DESC';
            $encabezados[4] = 'LONG_DESC';
            $encabezados[5] = 'STATUS';
            $encabezados[6] = 'CREATE_DATE';
            $encabezados[7] = 'CREATE_TIME';
            $encabezados[8] = 'CREATE_OPID';
            $encabezados[9] = 'CREATE_TERMINAL';
            $encabezados[10] = 'REVISION_DATE';
            $encabezados[11] = 'REVISION_TIME';
            $encabezados[12] = 'REVISION_OPID';
            $encabezados[13] = 'REVISION_TERMINAL';
            $encabezados[14] = 'CODE_XVAL';
            $encabezados[15] = 'CODE_XDESC';
            $encabezados[16] = 'ABT_JOIN';
            $encabezados[17] = 'SORT_ORDER';
            $encabezados[18] = 'SessionId';

            // Construir la parte de la consulta SQL para los nombres de las columnas
            $columnas = implode(", ", $encabezados);
            try{
                // Iterar sobre las filas del archivo CSV
                while (($fila = fgetcsv($csvFile)) !== false) {

                    //Convertir datos a formato fecha
                    $createDate = date('Y-m-d', strtotime($fila[6]));
                    $createTime = date('H:i:s', strtotime($fila[7]));
                    $revisionDate = date('Y-m-d', strtotime($fila[10]));
                    $revisionTime = date('H:i:s', strtotime($fila[11]));

                    // Escapar los valores para prevenir inyección de SQL
                    $valoresEscapados = array_map(array($mysqli, 'real_escape_string'), $fila);

                    $valoresEscapados[6] = $createDate;
                    $valoresEscapados[7] = $createTime;
                    $valoresEscapados[10] = $revisionDate;
                    $valoresEscapados[11] = $revisionTime;

                    // Construir la parte de la consulta SQL para los valores
                    $valores = implode("', '", $valoresEscapados);
                    // Construir y ejecutar la consulta SQL
                    $insert = "INSERT IGNORE INTO code_sesion_academic ($columnas) VALUES ('$valores');";
                    if ($mysqli->query($insert)) {
                        $message = 'Layout de Campus PwC Convertido y Cargado correctamente.';
                        $icon = 'success';
                    } else {
                        $message = 'Error cargando Campus PwC';
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
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envío de Campus PwC</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Carga de Campus PwC",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>