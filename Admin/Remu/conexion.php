<?php

session_cache_limiter('nocache,private');

date_default_timezone_set('America/Santiago');

// function conectar(){
// 	xconectar($_SESSION['BaseSV'],$_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']));
// }

function xconectar($usu,$cla,$bas){

	$mysqli = new mysqli('localhost', $usu, $cla, $bas);

    if ($mysqli->connect_errno) {

        echo "Lo sentimos, este sitio web está experimentando problemas.";

        echo "Error: Fallo al conectarse a MySQL debido a: \n";
        echo "Errno: " . $mysqli->connect_errno . "\n";
        echo "Error: " . $mysqli->connect_error . "\n";
        
        exit;
    }

    return $mysqli;
}
// function desconectar(){
// 	mysql_close();
// }
?>