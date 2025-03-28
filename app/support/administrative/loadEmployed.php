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

$sqlDelete = "DELETE FROM employed WHERE LAST_NAME != 'TEST'";
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
            $encabezados[0] = 'INSTITUTION';
            $encabezados[1] = 'ID_NOM';
            $encabezados[2] = 'PAYROLL';
            $encabezados[3] = 'LAST_NAME';
            $encabezados[4] = 'LAST_NAME_PREFIX';
            $encabezados[5] = 'NAME';
            $encabezados[6] = 'TAXPAYER_ID';
            $encabezados[7] = 'GOVERNMENT_ID';
            $encabezados[8] = 'IMSS';
            $encabezados[9] = 'STATUS';
            $encabezados[10] = 'AREA';
            $encabezados[11] = 'DEPARTMENT';
            $encabezados[12] = 'COUNTRY';
            $encabezados[13] = 'CITY';
            $encabezados[14] = 'NOM_SESSION';
            $encabezados[15] = 'JOB';
            $encabezados[16] = 'POSITION';
            $encabezados[17] = 'SCHEDULE_GROUP';
            $encabezados[18] = 'ADMISSION_DATE';
            $encabezados[19] = 'PERMANENT_EMP_DATE';
            $encabezados[20] = 'ANTIQUITY';
            $encabezados[21] = 'CLASIF';
            $encabezados[22] = 'SEPARATION_DATE';
            $encabezados[23] = 'SEPARATION_COMMENTS';
            $encabezados[24] = 'CONTRACT';
            $encabezados[25] = 'CONTRACT_START';
            $encabezados[26] = 'CONTRACT_END';
            $encabezados[27] = 'GENRE';
            $encabezados[28] = 'POSITION_SUEPRVISOR';
            $encabezados[29] = 'SUPERVISOR_ID';
            $encabezados[30] = 'SUPERVISOR_NAME';

            // Construir la parte de la consulta SQL para los nombres de las columnas
            $columnas = implode(", ", $encabezados);
            try{
                // Iterar sobre las filas del archivo CSV
                while (($fila = fgetcsv($csvFile)) !== false) {

                    //Convertir datos a formato fecha
                    $addmisionDate = date('Y-m-d', strtotime($fila[18]));
                    $permanentEmpDate = date('Y-m-d', strtotime($fila[19]));
                    $antiquity = date('Y-m-d', strtotime($fila[20]));
                    $separationDate = date('Y-m-d', strtotime($fila[22]));
                    $contractStart = date('Y-m-d', strtotime($fila[25]));
                    $contractEnd = date('Y-m-d', strtotime($fila[26]));

                    // Escapar los valores para prevenir inyección de SQL
                    $valoresEscapados = array_map(array($mysqli, 'real_escape_string'), $fila);

                    $valoresEscapados[18] = $addmisionDate;
                    $valoresEscapados[19] = $permanentEmpDate;
                    $valoresEscapados[20] = $antiquity;
                    $valoresEscapados[22] = $separationDate;
                    $valoresEscapados[25] = $contractStart;
                    $valoresEscapados[26] = $contractEnd;

                    // Construir la parte de la consulta SQL para los valores
                    $valores = implode("', '", $valoresEscapados);
                    // Construir y ejecutar la consulta SQL
                    $insert = "INSERT IGNORE INTO employed ($columnas) VALUES ('$valores');";
                    if ($mysqli->query($insert)) {
                        $message = 'Archivo Empleado Cargando Correctamente';
                        $icon = 'success';
                    } else {
                        $message = 'Error cargando Empleados';
                        $icon = 'error';

                        //echo $insert;
                    }
                }

                //echo $insert;
            } catch (mysqli_sql_exception $ex) {
                $message = 'Se ha producido un error al insertar los datos. Favor de cargar el archivo correcto.'; // . $ex -> getMessage();
                $icon = 'warning';
            }

            // Cerrar el csvFile de archivos y la conexión a la base de datos
            fclose($csvFile);
        } else {
            $message = 'Error cargano el Archivo';
            $icon = 'error';

            //echo $insert;
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
            title: "Carga de Empleados",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../admin_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>