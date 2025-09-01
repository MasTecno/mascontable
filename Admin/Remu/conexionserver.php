<?php

session_cache_limiter('nocache,private');

date_default_timezone_set('America/Santiago');

function conectarServer(){

	$mysqli = new mysqli('localhost', 'mastecno_UMR', '3CghVoMxJ6Xm','mastecno_unionMasRem');

    if ($mysqli->connect_errno) {

        echo "Lo sentimos, este sitio web está experimentando problemas.";

        echo "Error: Fallo al conectarse a MySQL debido a: \n";
        echo "Errno: " . $mysqli->connect_errno . "\n";
        echo "Error: " . $mysqli->connect_error . "\n";
        
        exit;
    }

    return $mysqli;
}

// function desconectarServer(){
//     // $resultado->free();
//     $mysqli->close();
//     return $mysqli;
// }




?>