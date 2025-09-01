<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../?Msj=95");
		exit;
	}
	
	$xDato1="";
	$xDato2="";
	$xDato3="";
	$xDato4="";
	$xDato5="";
	$xDato6="";
	$xDato7=$_POST['IdCart'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SqlStr="SELECT * FROM CTConciliacionDet WHERE Id='".$_POST['IdCart']."'";
    $Resultado = $mysqli->query($SqlStr);
    while ($Registro = $Resultado->fetch_assoc()) {
		
		$xDato1=date('d-m-Y',strtotime($Registro['Fecha']));
        $xDato2=$Registro['Glosa'];	
        $xDato3=$Registro['Cargos'];	
        $xDato4=$Registro['Abonos'];	
        $xDato5=$Registro['Rut'];	
        $xDato6=$Registro['Numero'];	
	}

	$mysqli->close();

	echo json_encode(
      array("dato1" => "$xDato1", 
      "dato2" => "$xDato2",
      "dato3" => "$xDato3", 
      "dato4" => "$xDato4", 
      "dato5" => "$xDato5", 
      "dato6" => "$xDato6", 
      "dato7" => "$xDato7")
      );
