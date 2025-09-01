<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	
	if(isset($_POST['ModUpdEmp']) && $_POST['ModUpdEmp']=="Empr"){
		// echo "UPDATE CTEmpresas SET razonsocial='".$_POST['rsocial']."', rut_representante='".$_POST['rutrep']."', representante='".$_POST['representante']."', direccion='".$_POST['direccion']."', giro='".$_POST['giro']."', ciudad='".$_POST['ciudad']."', correo='".$_POST['correo']."' WHERE rut='".$_SESSION['RUTEMPRESA']."'";
	
		// exit;
		$mysqli->query("UPDATE CTEmpresas SET razonsocial='".$_POST['rsocial']."', rut_representante='".$_POST['rutrep']."', representante='".$_POST['representante']."', direccion='".$_POST['direccion']."', giro='".$_POST['giro']."', ciudad='".$_POST['ciudad']."', correo='".$_POST['correo']."' WHERE rut='".$_SESSION['RUTEMPRESA']."'");

		$_SESSION['RAZONSOCIAL']=$_POST['rsocial'];
		
	}

	$mysqli->close();

	header("location:frmMain.php");
?>