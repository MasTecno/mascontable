<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

    $Periodo=$_SESSION['PERIODO'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $FECHA=date("Y/m/d");

    $xfecha=$_POST['d1'];
    $xrut=$_POST['d2'];
    $xrs=$_POST['d3'];

    $sdoc=$_POST['d5'];
    $xndoc=$_POST['d6'];

    $xcuenta=$_POST['d7'];
    $xncuenta=$_POST['d8'];

    $xmonto=$_POST['d10'];

    $xbruto=$_POST['d11'];
    $xrete=$_POST['d12'];
    $xliqui=$_POST['d13'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    // if($xfecha=="" || $xrut=="" || $xrs=="" || $xndoc=="" || $xcuenta=="" || $xncuenta=="" || $xmonto==""){
    if($xfecha=="" || $xrut=="" || $xrs=="" || $xndoc==""){
        echo ' 
            <div class="alert alert-danger alert-dismissible fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Advertencia!</strong> El Formulario debe contener todos los datos.
            </div>';
        exit;
    }

    // $sdoc=$_POST['thono'];//"R";
    $sdoc="R";
    
    $SQL="SELECT * FROM CTAsientoHono";
    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {
        if ($registro['tipo']==$sdoc) { ///honorarios recividos
          $xcuenta=$registro['L1'];
        }
        if ($registro['tipo']==$sdoc) { ///Honorarios emitidos
          $xcuenta=$registro['L1'];
        }
    }


	$dia = substr($xfecha,0,2);
    $mes = substr($xfecha,3,2);
    $ano = substr($xfecha,6,4);

    $xfecha=$ano."/".$mes."/".$dia;

    $SQL="SELECT * FROM CTHonorarios WHERE rutempresa='$RutEmpresa' AND rut='$xrut' AND tdocumento='$sdoc' AND numero='$xndoc'";
    $resultado = $mysqli->query($SQL);
    $numero = $resultado->num_rows;
    if ($numero>0){
        echo ' 
            <div class="alert alert-danger alert-dismissible fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Advertencia!</strong> Documento ya ingresado.
            </div>';
        exit;
    }
    
    if ($sdoc=="R") {
        $tiregistro="P";
    }else{
        $tiregistro="C";
    }

    $SQL="SELECT * FROM CTCliPro WHERE rut='$xrut'";
    $resultado = $mysqli->query($SQL);
    $numero = $resultado->num_rows;
    if ($numero==0){
        $mysqli->query("INSERT INTO CTCliPro VALUES('','$xrut','$xrs','','','','','','$tiregistro','A')");
    }      

    $mysqli->query("INSERT INTO CTHonorarios VALUES('','$Periodo','$RutEmpresa','$xfecha','$xrut','$xndoc','$xcuenta','','$xbruto','$xrete','$xliqui','$sdoc','$FECHA','','A','')");
    $mysqli->close();

    echo "";    

 ?>