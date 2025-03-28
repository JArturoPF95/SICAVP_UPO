<?php

$sqlUsers = "SELECT 
        DISTINCT USR.USER
        , CONCAT(ASH.NAME,' ',ASH.LAST_NAME,' ',ASH.Last_Name_Prefix) NAME_DOC
        , CAL.LEVEL_DESCRIPTION
        , USR.SEPARATION_FLAG
    FROM users USR 
    INNER JOIN academic_schedules ASH ON ASH.PERSON_CODE_ID = USR.USER
    INNER JOIN code_accesslevels CAL ON CAL.CODE_LEVEL = USR.ACCESS_LEVEL
    WHERE ACCESS_LEVEL = '4';";

?>