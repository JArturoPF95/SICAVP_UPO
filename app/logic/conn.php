<?php

$servername = "127.0.0.1";
$username = "univ4663_sicavp";
$password = "T+zaKK;]?4.K";
$nameDB = "univ4663_sicavp_upo";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $nameDB);
$mysqli->set_charset("utf8");

// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  exit();
}

//echo "Connected successfully";
