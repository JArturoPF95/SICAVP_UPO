<?php
require '../../logic/conn.php';
require '../../../lib/vendor/autoload.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location:../../../index.php');
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

$sqlDelete = "DELETE FROM academic_schedules";
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
            $encabezados[0] = 'PK';
            $encabezados[1] = 'ACADEMIC_YEAR';
            $encabezados[2] = 'ACADEMIC_TERM';
            $encabezados[3] = 'ACADEMIC_SESSION';
            $encabezados[4] = 'PERSON_CODE_ID';
            $encabezados[5] = 'NAME';
            $encabezados[6] = 'LAST_NAME';
            $encabezados[7] = 'Last_Name_Prefix';
            $encabezados[8] = 'ID_NOM';
            $encabezados[9] = 'GOVERNMENT_ID';
            $encabezados[10] = 'SECTION';
            $encabezados[11] = 'CODE_DEGPRO';
            $encabezados[12] = 'DEGREE';
            $encabezados[13] = 'PROGRAM';
            $encabezados[14] = 'CURRICULUM';
            $encabezados[15] = 'EVENT';
            $encabezados[16] = 'GENERAL_ED';
            $encabezados[17] = 'SERIAL_ID';
            $encabezados[18] = 'BUILDING';
            $encabezados[19] = 'ROOM';
            $encabezados[20] = 'CODE_DAY';
            $encabezados[21] = 'MAX_BEFORE_CLASS';
            $encabezados[22] = 'START_CLASS';
            $encabezados[23] = 'DELAY_CLASS';
            $encabezados[24] = 'MAX_DELAY_CLASS';
            $encabezados[25] = 'MIN_END_CLASS';
            $encabezados[26] = 'END_CLASS';
            $encabezados[27] = 'SessionPeriodId';

            // Construir la parte de la consulta SQL para los nombres de las columnas
            $columnas = implode(", ", $encabezados);
            try{
                // Iterar sobre las filas del archivo CSV
                while (($fila = fgetcsv($csvFile)) !== false) {

                    //Convertir datos a formato fecha
                    $startClass = date('H:i:s', strtotime($fila[21]));
                    $endClass = date('H:i:s', strtotime($fila[25]));

                    //quitamos NULL
                    if ($fila[8] == 'NULL') {
                        $id_nom = '';
                    } else {
                        $id_nom = $fila[8];
                    }

                    if ($fila[9] == 'NULL') {
                        $government_id = '';
                    } else {
                        $government_id = $fila[9];
                    }

                    if ($fila[10] == 'NULL') {
                        $section = '';
                    } else {
                        $section = $fila[10];
                    }

                    if ($fila[12] == 'NULL') {
                        $degree = '';
                    } else {
                        $degree = $fila[12];
                    }

                    if ($fila[13] == 'NULL') {
                        $program = '';
                    } else {
                        $program = $fila[13];
                    }

                    if ($fila[14] == 'NULL') {
                        $curriculum = '';
                    } else {
                        $curriculum = $fila[14];
                    }

                    if ($fila[15] == 'NULL') {
                        $event = '';
                    } else {
                        $event = $fila[15];
                    }

                    if ($fila[16] == 'NULL') {
                        $general_ed = '';
                    } else {
                        $general_ed = $fila[16];
                    }

                    if ($fila[17] == 'NULL') {
                        $serial_id = '';
                    } else {
                        $serial_id = $fila[17];
                    }

                    if ($fila[18] == 'NULL') {
                        $building = '';
                    } else {
                        $building = $fila[18];
                    }

                    if ($fila[19] == 'NULL') {
                        $room = '';
                    } else {
                        $room = $fila[19];
                    }

                    // Escapar los valores para prevenir inyección de SQL
                    $valoresEscapados = array_map(array($mysqli, 'real_escape_string'), $fila);

                    $valoresEscapados[8] = $id_nom;
                    $valoresEscapados[9] = $government_id;
                    $valoresEscapados[10] = $section;
                    $valoresEscapados[12] = $degree;
                    $valoresEscapados[13] = $program;
                    $valoresEscapados[14] = $curriculum;
                    $valoresEscapados[15] = $event;
                    $valoresEscapados[16] = $general_ed;
                    $valoresEscapados[17] = $serial_id;
                    $valoresEscapados[18] = $building;
                    $valoresEscapados[19] = $room;
                    $valoresEscapados[21] = $startClass;
                    $valoresEscapados[25] = $endClass;

                    // Construir la parte de la consulta SQL para los valores
                    $valores = implode("', '", $valoresEscapados);
                    // Construir y ejecutar la consulta SQL
                    $insert = "INSERT IGNORE INTO academic_schedules ($columnas) VALUES ('$valores');";
                    if ($mysqli->query($insert)) {
                        $message = 'Layout de Horarios Docentes Convertido y Cargado correctamente.';
                        $icon = 'success';
                    } else {
                        $message = 'Error cargando Horarios Docentes';
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
    <title>Envío de Horarios Docentes</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Carga de Horarios Docentes",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>