<?php 

$term = date('Y');

//Días solicitados ya depurados
$sql_getCalendarDays = "SELECT DAYS_VAC.START_DATE, DAYS_VAC.END_DATE, IF(DAYS_VAC.DAYS_REQ IS NULL OR DAYS_VAC.DAYS_REQ = 0, 6, DAYS_VAC.DAYS_REQ) DAYS_REQ
    FROM (SELECT DISTINCT
    (SELECT CDT.CALENDAR_DATE 
     FROM calendar CDT 
     WHERE CDT.DAY_OF_REST != '1' 
       AND CDT.CALENDAR_DATE BETWEEN '$start_date' AND '$end_date' 
     ORDER BY CDT.CALENDAR_DATE ASC
     LIMIT 1) AS START_DATE,
     
    (SELECT CDT.CALENDAR_DATE 
     FROM calendar CDT 
     WHERE CDT.DAY_OF_REST != '1' 
       AND CDT.CALENDAR_DATE BETWEEN '$start_date' AND '$end_date' 
     ORDER BY CDT.CALENDAR_DATE DESC
     LIMIT 1) AS END_DATE,
     
    (SELECT COUNT(DISTINCT CLD.CALENDAR_DATE)
		FROM employed EMP
		LEFT OUTER JOIN assigned_schedule ASSCH ON ASSCH.ID_NOM = EMP.ID_NOM AND (('$start_date' BETWEEN ASSCH.START_DATE AND ASSCH.END_DATE) OR ('$end_date' BETWEEN ASSCH.START_DATE AND ASSCH.END_DATE))
		LEFT OUTER JOIN admin_schedules ASH ON ASH.CODE_SCHEDULE = ASSCH.SCHEDULE
		LEFT OUTER JOIN admin_schedules ASH2 ON ASH2.CODE_SCHEDULE = EMP.SCHEDULE_GROUP
		INNER JOIN calendar CLD ON CLD.CODE_DAY = COALESCE(ASH.CODE_DAY, ASH2.CODE_DAY)
		WHERE EMP.ID_NOM = '$user_active' AND CLD.DAY_OF_REST = '0' AND CLD.CALENDAR_DATE BETWEEN '$start_date' AND '$end_date'
		ORDER BY CLD.CALENDAR_DATE ASC) DAYS_REQ
     
) DAYS_VAC";

//Validamos que no se soliciten entre fechas ya solicitadas
$sql_valRequestedDays = "SELECT * FROM vacation_request
    WHERE ID_NOM = '$user_active' AND ('$first_day' BETWEEN START_DATE AND END_DATE
        OR '$last_day' BETWEEN START_DATE AND END_DATE
        OR START_DATE BETWEEN '$first_day' AND '$last_day' 
        OR END_DATE BETWEEN '$first_day' AND '$last_day')";

//Información para el PDF
$sql_infoPDF = "SELECT 
        VRE.*, POS.POSITION_DESCRIPTION, ARE.NAME_AREA, JBS.JOB_NAME, DEP.DEPARTMENT
    FROM vacation_request VRE
    LEFT OUTER JOIN employed EMP ON EMP.ID_NOM = VRE.ID_NOM
    LEFT OUTER JOIN code_area ARE ON ARE.CODE_AREA = EMP.AREA
    LEFT OUTER JOIN code_jobs JBS ON JBS.CODE_JOB = EMP.JOB
    LEFT OUTER JOIN code_position POS ON POS.CODE_POSITION = EMP.POSITION
    LEFT OUTER JOIN code_department DEP ON DEP.CODE_DEPRTMENT = EMP.DEPARTMENT
    WHERE VRE.requestId = '$idVac'";

//Solicitudes
$sql_myRequest = "SELECT 
        VRE.ID_NOM, VRE.REQUEST_DATE, VRE.START_DATE, VRE.REQUEST_TERM,
        VRE.END_DATE, VRE.DAYS_REQUESTED, VRE.AUTHORIZATION_FLAG, VRE.requestId
    FROM vacation_request VRE 
    WHERE DAYS_REQUESTED != 0 AND VRE.ID_NOM = '$user_active' AND VRE.REQUEST_TERM = '$term'";

//Obtenemos solicitudes de vacaciones de los colaboradores
$sql_getRequest = "SELECT 
        EMP.ID_NOM, CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) NAME,
        VRE.*
    FROM vacation_request VRE
    INNER JOIN employed EMP ON EMP.ID_NOM = VRE.ID_NOM
    WHERE VRE.IMMEDIATE_BOSS = '$user_active' AND VRE.DAYS_REQUESTED != 0 
        AND VRE.REQUEST_TERM = '$term'";


?>