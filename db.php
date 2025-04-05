<?php

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "fotballmatch"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Gabim lidhjeje: " . $conn->connect_error);
}
?>