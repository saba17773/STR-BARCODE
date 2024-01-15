<?php 

$serverName =  $_SERVER["SERVER_NAME"]; 
if (strpos($serverName, "lanister") !== false) {
    echo 'have';
} else {
    echo 'not';
}


?>