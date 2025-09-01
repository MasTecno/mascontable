<?php

	// include 'conexion/conexionmysqli.php';
	// include 'js/funciones.php';
	// session_start();
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
        header("location:index.php?Msj=95");
        exit;
    }

	$DMes=$_POST['messelect'];
	$DAno=$_POST['anoselect'];

	if ($DMes<10) {
		$DMes="0".$DMes;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$xperiodo=$DMes."-".$DAno;

	$mysqli->query("UPDATE CTEmpresas SET periodo='$xperiodo' WHERE rut='".$_SESSION['RUTEMPRESA']."'");
	$mysqli->close();

	$_SESSION['PERIODO']=$xperiodo;

	echo $_SESSION['PERIODO'];
?>