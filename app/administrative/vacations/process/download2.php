<?php
require '../../../../lib/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar opciones de Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

// Crear una instancia de Dompdf
$dompdf = new Dompdf($options);

// Generar la URL absoluta de la imagen
$host = $_SERVER['HTTP_HOST'];

if ($host == '200.52.75.189:8080') {
    $image_url = "../../../../static/img/1.png";
} elseif ($host == '192.168.1.252:8080') {
    $image_url = "http://$host/sicavp/static/img/1.png";
}


// Verificar si la imagen es accesible desde el servidor
$image_contents = file_get_contents($image_url);
if ($image_contents === false) {
    die("No se puede acceder a la imagen desde el servidor.");
}

// Contenido HTML del PDF
$html = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>PDF con Imágenes</title>
</head>
<body>
    <h1>Este es un PDF con una imagen</h1>
    <img src='$image_url' style='width: 100px; height: 25px;' alt='Logo'>
</body>
</html>
";

// Cargar contenido HTML en Dompdf
$dompdf->loadHtml($html);

// (Opcional) Configurar tamaño de papel y orientación
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Enviar el PDF al navegador o guardarlo en el servidor
$dompdf->stream("archivo.pdf", array("Attachment" => false));
?>
