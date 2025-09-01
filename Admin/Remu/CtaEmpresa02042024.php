<?php
	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../index.php?Msj=95");
		exit;
	}

	include 'conexionserver.php';
	include 'conexion.php';
	$mysqli=conectarServer();

	$sql = "SELECT * FROM UnionServer WHERE Estado='A'";//AND Numero<=10
	$resultado = $mysqli->query($sql);

    $Act=0;
    $ina=0;    
	$StrSql="SELECT estado, COUNT(estado) AS CON FROM RMAcceso";

	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		$xcla=$registro["Clave"];
		$xbas=$registro["Base"];

		$mysqliX=xconectar($xusu,$xcla,$xbas);

        $resultadoIN = $mysqliX->query($StrSql);
        // echo $StrSql."<br>".$xusu."<br>";

        while ($registroIN = $resultadoIN->fetch_assoc()) {
            // echo $registroIN["estado"];
            if($registroIN["estado"]=="A"){
                $Act=$Act+$registroIN["CON"];
            }else{
                $ina=$ina+$registroIN["CON"];
            }
        }
	}
	
	$mysqliX->close();
	$mysqli->close();

    echo "Activos: ".$Act;
    echo "<br>";
    echo "Inactivos: ".$ina;
?>