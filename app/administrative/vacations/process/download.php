<?php
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

require '../../../../lib/dompdf/autoload.inc.php';
require '../../../logic/conn.php';

//Librerías PDF

use Dompdf\Dompdf;
use Dompdf\Css\Stylesheet;
use Dompdf\FrameDecorator\TableCell;
use Dompdf\Options;
Use Dompdf\Adapter\CPDF;
Use Dompdf\Exception;

ob_start();
$host = $_SERVER['HTTP_HOST'];



$idVac = $_GET['id'];
$first_day = '';
$last_day = '';
$start_date = '';
$end_date = '';
$term = date('Y');
$authorization = '';
require 'query_vacations.php';
$sql_infoPDF;
$result_infoPDF = $mysqli->query($sql_infoPDF);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Vacaciones</title>

</head>

<body>
    <div>
        <div class="container" style="height: 10%;">
            <table class="table" style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 10%"><img src="https://sicavp.upotosina.com.mx/sicavp/static/img/2.png" style="width: 100px; height: 25px;" alt=" " srcset=" "><img></td>
                        <td style="width: 10%"></td>
                        <td style="width: 60%"><span style="color:silver">FORMATO PARA SOLICITAR VACACIONES</span></td>
                        <td style="width: 10%"></td>
                        <td style="width: 10%"><img src="https://sicavp.upotosina.com.mx/sicavp/app/administrative/vacations/process/universidad-potosina_logo.png" style="width: 50px; height: 50px;" alt=" " srcset=" "></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="container" style="height: 10%;">

        </div>
        <div class="container" style="height: 50%;">
            <?php
            if ($result_infoPDF->num_rows > 0) {
                while ($row_PDF = $result_infoPDF->fetch_assoc()) {
                    $infoPDF_area = $row_PDF['NAME_AREA'];
                    $infoPDF_department = $row_PDF['DEPARTMENT'];
                    $infoPDF_job = $row_PDF['JOB_NAME'];
                    $infoPDF_days = $row_PDF['DAYS_REQUESTED'];
                    $infoPDF_position = $row_PDF['POSITION_DESCRIPTION'];
                    $infoPDF_startDate = $row_PDF['START_DATE'];
                    $infoPDF_endDate = $row_PDF['END_DATE'];
                    $infoPDF_comment = $row_PDF['COMMENTS'];
                    $infoPDF_authorization = $row_PDF['AUTHORIZATION_FLAG'];

                    if ($infoPDF_authorization == 2) {
                        $authorization = 'Rechazado';
                    } elseif ($infoPDF_authorization == 1) {
                        $authorization = 'Autorizado';
                    }
            ?>
                    <div class="container" style="height: 10%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="width: 10%;">Nombre: </td>
                                    <td style="width: 60%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $user_name ?></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 15%;">No. Empleado: </td>
                                    <td style="width: 10%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $user_active ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="container" style="height: 10%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="width: 5%;">Área: </td>
                                    <td style="width: 45%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $infoPDF_area ?></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;">Departamento: </td>
                                    <td style="width: 40%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $infoPDF_department ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="container" style="height: 10%;">
                    </div>
                    <div class="container" style="height: 10%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="font-size: 11pt; width: 8%;">Puesto: </td>
                                    <td style="font-size: 11pt; width: 92%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $infoPDF_job ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="container" style="height: 10%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="font-size: 11pt; width: 8%;">Posición: </td>
                                    <td style="font-size: 11pt; width: 92%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $infoPDF_position ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="container" style="height: 10%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="font-size: 11pt; width: 7%;">Fecha: </td>
                                    <td style="font-size: 11pt; width: 30%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo date('d/m/Y', strtotime($infoPDF_startDate)) ?> al <?php echo date('d/m/Y', strtotime($infoPDF_endDate)) ?></td>
                                    <td style="width: 5%;"></td>
                                    <td style="font-size: 11pt; width: 18%;">Días Solicitados: </td>
                                    <td style="font-size: 11pt; width: 17%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $infoPDF_days ?></td>
                                    <td style="width: 5%;"></td>
                                    <td style="font-size: 11pt; width: 8%;">Estatus: </td>
                                    <td style="font-size: 11pt; width: 30%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $authorization ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="container" style="height: 10%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="font-size: 11pt; width: 10%;">Comentarios: </td>
                                    <td style="font-size: 11pt; width: 90%; border-bottom: solid; border-color: #000; border-width: 0.5px; text-align: center;"><?php echo $infoPDF_comment ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <div class="container" style="height: 20%;">

        </div>
        <!--div class="container" style="height: 10%;">
            <table class="table" style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 10%"></td>
                        <td style="width: 35%; text-align: center;"><span style="color:silver;">&copy; 2024 Dirección de Tecnologías de la Información</span></td>
                        <td style="width: 10%"></td>
                        <td style="width: 35%"><img src="http://200.52.75.189:8080/sicavp/static/img/new_nacerlogo.png" style="width: auto; height: 50px;" alt="Logo NG" srcset="Logo NG"></td>
                        <td style="width: 10%"></td>
                    </tr>
                </tbody>
            </table>
        </div-->
    </div>
</body>

</html>
<?php
$html = ob_get_clean();
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
$dompdf = new DOMPDF();
$options = $dompdf->getOptions();
$options->set(array('isRemoteEnabled' => True));
$dompdf->setOptions($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Solicitud_Vacaciones.pdf", array("Attachment" => True)); //Para descargar*/

?>