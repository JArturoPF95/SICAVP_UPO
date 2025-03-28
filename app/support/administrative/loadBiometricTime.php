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
$message_2 = '';
$icon = '';
$flag_carga = '';
$endDate = date('Y-m-d H:i:s');
$startDate = '';

//echo $endDate;



use PhpOffice\PhpSpreadsheet\IOFactory;



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
            $encabezados[0] = 'ID_NOM';
            $encabezados[1] = 'STATUS';            
            $encabezados[2] = 'RECORD_DATE';
            $encabezados[3] = 'RECORD_TIME';
            $encabezados[4] = 'CREATED_BY';
            $encabezados[5] = 'CREATED_DATE';

            // Limitar a los primeros 4 encabezados
            $encabezados_limitados = array_slice($encabezados, 0, 6);

            // Usar implode para convertirlos en una cadena
            $columnas = implode(", ", $encabezados_limitados);

            //echo $columnas;

            try{
                // Iterar sobre las filas del archivo CSV
                while (($fila = fgetcsv($csvFile)) !== false) {

                    $idNom = $fila[0];
                    $date = $fila[9];
                    $time = $fila[10];
                    $status = $fila[11];

                    // Escapar los valores para prevenir inyección de SQL
                    $valoresEscapados = array_map(array($mysqli, 'real_escape_string'), $fila);

                    $valoresEscapados[0] = $idNom;
                    $valoresEscapados[1] = $status;
                    $valoresEscapados[2] = $date;
                    $valoresEscapados[3] = $time;
                    $valoresEscapados[4] = $user_active;
                    $valoresEscapados[5] = $endDate;

                    // Limitar los valores a los primeros 4 elementos
                    $valoresNecesarios = array_slice($valoresEscapados, 0, 6);

                     // Construir la parte de la consulta SQL para los valores
                     $valores = implode("', '", $valoresNecesarios);

                    // Construir y ejecutar la consulta SQL
                    $insert = "INSERT IGNORE INTO biometrictimeclock ($columnas) VALUES ('$valores');";

                    //echo '<br>'.$insert;

                    if ($mysqli->query($insert)) {

                        $message = 'Carga de registros Checador Biométrico exitosa';
                        $flag_carga = '1';                                            

                    } else {
                        $message = 'Error cargando registros Checador Biométrico';
                        $flag_carga = '0';
                    }
                    
                }                

                //Si carga orrectamente el archivo del checador procede a correr el SP

                if ($flag_carga == '1') {
                    
                $sqlGetStartDate = "SELECT DISTINCT RECORD_DATE FROM biometrictimeclock WHERE CREATED_DATE = '$endDate' ORDER BY RECORD_DATE ASC LIMIT 1";
                $resultStartDate = $mysqli -> query($sqlGetStartDate);
                if ($resultStartDate -> num_rows > 0) {
                    while ($rowSD = $resultStartDate -> fetch_assoc()) {
                        $startDate = $rowSD['RECORD_DATE'];
                    }
                }

                $sqlGetendDate = "SELECT DISTINCT CREATED_DATE FROM biometrictimeclock ORDER BY RECORD_DATE DESC LIMIT 1";
                $resultendDate = $mysqli -> query($sqlGetendDate);
                if ($resultendDate -> num_rows > 0) {
                    while ($rowED = $resultendDate -> fetch_assoc()) {
                        $endDate = $rowED['CREATED_DATE'];
                    }
                }
                
                                    
                    if (!($sentencia = $mysqli->prepare("CALL InsertarSiNoExisteAsistencia('$startDate', '$endDate', '$user_active')"))) {
                        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
                    } else {
                        
                        if (!$sentencia->execute()) {
                            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
                            $message_2 = ', Error Generando Inasistencias';
                            $flag_carga = '1'; 
                            $url = 'updateAttendances.php?id='.$user_active;
                        } else { 
                            $message_2 = ' e Inasistencias ';
                            $flag_carga = '2'; 
                            $url = 'updateAttendances.php?id='.$user_active;
                        }
                    
                    }
                }


                //Valida el estatus de la carga y el SP para generar el ícono correspondiente
                if ($flag_carga == '2') {
                    $icon = 'success';
                } elseif ($flag_carga == '1') {
                    $icon = 'warning';
                } else {
                    $icon = 'error';
                }

                //echo $insert;
            } catch (mysqli_sql_exception $ex) {
                $message = 'Se ha producido un error al insertar los datos. Favor de cargar el archivo correcto. ' . $ex -> getMessage();
                $icon = 'warning';
                $url = '../index.php?id='.$user_active;
            }

            // Cerrar el csvFile de archivos y la conexión a la base de datos
            fclose($csvFile);
        } else {
            $message = 'Error cargano el Archivo';
            $icon = 'error';
            $url = '../index.php?id='.$user_active;
            //echo $insert;
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
            title: "Carga registros Checador",
            text: "<?php echo $message . $message_2; ?>",
            icon: "<?php echo $icon ?>",
            button: "Genera Asistencias",
        }).then(function() {
            window.location = "<?php echo $url; ?>";
        });
    </script>

</body>

</html>