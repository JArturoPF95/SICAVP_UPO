<?php

require_once 'query_vacations.php';

//$termSel = date('Y');

//Obtener la fecha de ingreso y el supervisor
$sql_get_antiquity = "SELECT ANTIQUITY, SUPERVISOR_ID FROM employed EMP WHERE ID_NOM = '$user_active'";
$result_getAntiquity  = $mysqli -> query($sql_get_antiquity);
if ($result_getAntiquity -> num_rows > 0) {
    while ($row_getAntiquity = $result_getAntiquity -> fetch_assoc()) {
        $antiquity = $row_getAntiquity['ANTIQUITY'];
        $supervisor_nom = $row_getAntiquity['SUPERVISOR_ID'];
    }
}

//Obtenemos la antiguedad para cálculos

    $antiquityY = ( strtotime($today) - strtotime($antiquity) ) / ( 60 * 60 * 24 * 365);




//Obtenemos la antiguedad para imprsión (formato)
$startDate = new DateTime($antiquity);
$endDate = new DateTime($today);

$antiquity_years = $startDate -> diff($endDate);
//$anti_yrs = $antiquity_years -> y . " años " . $antiquity_years -> m . ' meses ';
if ($antiquity_years -> y < 1) {
    $yrs = '';
} else {
    $yrs = $antiquity_years -> y . " años ";
}

if ($antiquity_years -> m < 1) {
    $mth = '';
} else {
    $mth = $antiquity_years -> m . " meses ";
}

$anti_yrs = $yrs . $mth;

//Obtenemos los días que le corresponden por ley dependiendo de su antiguedad
$sql_getDaysLaw = "SELECT DAYS_BY_LAW FROM code_vacation WHERE MIN_YEARS <= '$antiquityY' AND MAX_YEARS >= '$antiquityY'";
$result_daysLaw  = $mysqli -> query($sql_getDaysLaw);
if ($result_daysLaw -> num_rows > 0) {
    while ($row_daysLaw = $result_daysLaw -> fetch_assoc()) {
        $days_law = $row_daysLaw['DAYS_BY_LAW'];
        $antFlag = '0';
    }
} else { //Sino tiene antiguedad validamos si cuenta con un registro de autorización previa autorizado.
    $antTerm = date('Y');
    $sqlAntVac = "SELECT DISTINCT ID_NOM, DAYS FROM vacation_anticipated WHERE ID_NOM = '$user_active' AND ACTIVE_FLAG = '1' AND VACATION_TERM = '$antTerm'";
    $resultAntVac = $mysqli -> query($sqlAntVac);
    if ($resultAntVac -> num_rows > 0) {
        while ($rowAntVac = $resultAntVac -> fetch_assoc()) {
            $days_law = $rowAntVac['DAYS'];
            $antFlag = '1';
        }
    }
}

//

//Validamos los días que tiene disponibles con respecto a solicitudes previamente realizadas
$sql_getUsedDays = "SELECT REQUEST_TERM, SUM(DAYS_REQUESTED) DAYS_REQUESTED FROM vacation_request 
    WHERE REQUEST_TERM = '$term' AND ID_NOM = '$user_active' AND AUTHORIZATION_FLAG != 2 GROUP BY REQUEST_TERM";
$result_usedDays = $mysqli -> query($sql_getUsedDays);
if ($result_usedDays -> num_rows > 0) {
    while ($row_usedDays = $result_usedDays -> fetch_assoc()) {
        $days_used = $row_usedDays['DAYS_REQUESTED'];
    }
    $days_left = $days_law - $days_used;
} else {
    $days_left = $days_law;
}

//Activamos el botón de envío si corresponde

if ( ($days_law == 0 || $days_left == 0) && $termSel == date('Y')) {  //Se valida que sus días pendientes o por ley sean mayores a 0
    $buttomRequest = '<button type="submit" class="btn btn btn-outline-secondary disabled">Solicitar Días</button>';
} else {
    $buttomRequest = '<button type="submit" class="btn btn-primary">Solicitar Días</button>';
    //Validamos que los periodos vacacionales institucionales ya se hayan cargado
    /*$sqlValVacationTerm = "SELECT COUNT(DISTINCT COMMENTS) TERMS FROM vacation_request WHERE COMMENTS IN ('Semana Santa','Navidad - Fin de Año');";
    $result_valVacationTerm = $mysqli -> query($sqlValVacationTerm);
    if ($result_valVacationTerm -> num_rows > 0) {
        while ($rowValTerms = $result_valVacationTerm -> fetch_assoc()) {
            $terms = $rowValTerms['TERMS'];
            if ($terms === '2') {
                $buttomRequest = '<button type="submit" class="btn btn-primary">Solicitar Días</button>';
            } else {
                $buttomRequest = '<button type="submit" class="btn btn btn-outline-secondary disabled">Solicitar Días</button>';
            }
        }
    }*/
    //$buttomRequest = '<button type="submit" class="btn btn-primary">Solicitar Días</button>';
}




?>
