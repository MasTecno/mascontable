<?php
include '../conexion/conexionmysqli.php';
include '../js/funciones.php';
session_start();    

$EstId=$_POST['idTP'];

$target_path = "../Facturas/Archivos/";

// $target_path = $target_path ."".$_SESSION['IDEMPRESA']."".basename( $_FILES['uploadedfile']['name']); 
$target_path = $target_path ."".$_SESSION['RUTEMPRESA']."".basename( $_FILES['uploadedfile']['name']); 

$archivo = $_FILES["uploadedfile"]["tmp_name"]; 
$tamanio = $_FILES["uploadedfile"]["size"];
$tipo    = $_FILES["uploadedfile"]["type"];
$nombre  = $_SESSION['RUTEMPRESA'].$_FILES["uploadedfile"]["name"];

if($tipo=="image/jpeg"){
	//echo "es image/jpeg";
}elseif($tipo=="image/gif"){
	//echo "es image/gif";
}elseif($tipo=="image/png"){
	//echo "es image/png";
}else{
	header("location:frmMain.php?Msj=7"); 	
	exit;
}

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) { 

	$fecha=Date("Y/m/d");	
	
	if ( $archivo != "none" ){

		$mysqliX=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$ArcExi="";
		$sqlin = "SELECT * FROM RMLogo WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultadoin = $mysqliX->query($sqlin);

		while ($registroin = $resultadoin->fetch_assoc()) {
			$ArcExi=$registroin['nombre'];
		}

		if ($ArcExi=="") {
			$mysqliX->query("INSERT INTO RMLogo VALUES('','$fecha','".$_SESSION['RUTEMPRESA']."','$nombre','A')");
		}else{
			$rutanueva="../Facturas/Archivos/".$ArcExi;
			if(file_exists($rutanueva)){ 
				if(unlink($rutanueva)){ 
				} 
			}
			$mysqliX->query("UPDATE RMLogo SET nombre='$nombre' WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}
		$mysqliX->close();

		header("location:frmMain.php");
		
	}else{
	    print "No se ha podido subir el archivo al servidor";
	}

	/////FIN GRABA IMAGEN

} else{
	echo "Ha ocurrido un error, trate de nuevo!";
}
exit;
?>