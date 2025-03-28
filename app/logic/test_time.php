<?php
date_default_timezone_set('America/Mexico_City');
// Verificar si el horario de verano está activado
echo $horarioVerano = date('I'); // Retorna 1 si el horario de verano está activado, 0 si no
echo '<br>';
// Restar una hora a la hora actual si el horario de verano está activado
if ($horarioVerano == 1) {
  echo $hoyYahora = date('Y-m-d H:i:s', strtotime('-2 hour'));
} else {
  echo $hoyYahora = date('Y-m-d H:i:s');
}
