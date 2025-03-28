<?php
session_start();
$_SESSION = array(); 
session_destroy();
unset($_SESSION);

//Redirecciona a la página de login
header('Location: ../../../');
?>