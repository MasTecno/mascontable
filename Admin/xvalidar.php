<?php


	if(isset($_GET['destroy'])=="S"){
		session_start();
		session_destroy();
		header("location:../Admin");
		exit;
	}


session_cache_limiter('private');
$cache_limiter = session_cache_limiter();

/* establecer la caducidad de la caché a 30 minutos */
session_cache_expire((30*6));
$cache_expire = session_cache_expire();

/* iniciar la sesión */

	include 'conexionserver.php';
	session_start();

	$mysqli=conectarServer();

	$sql = "SELECT * FROM Acceso WHERE Usuario='".$_POST['Username']."' AND Clave='".$_POST['Password']."'";

	$resultado = $mysqli->query($sql);
	$row_cnt = $resultado->num_rows;

	if ($row_cnt==0) {
		header("location:index.php?DenegaAcceso");
		$mysqli->close();
		exit;
	}

	$_SESSION['ROL']="Samito";

	$mysqli->close();
	header("location:frmMain.php");


?>