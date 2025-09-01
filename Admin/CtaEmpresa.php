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
	$row_cnt = $resultado->num_rows;

    $Act=0;
    $ina=0;    
	$StrSql="SELECT estado, COUNT(estado) AS CON FROM CTContadores";
	// $StrSql="SELECT estado, COUNT(estado) AS CON FROM CTEmpresas";


	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		$xcla=$registro["Clave"];
		$xbas=$registro["Base"];

		$mysqliX=xconectar($xusu,$xcla,$xbas);

        $resultadoIN = $mysqliX->query($StrSql);

        while ($registroIN = $resultadoIN->fetch_assoc()) {
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

    echo "<br>";
    echo "Lista Server: ".$row_cnt;

?>