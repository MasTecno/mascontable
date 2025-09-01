<?php 
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $RutEmpresa="";

    if ($_SESSION["PLAN"]=="S"){
        $RutEmpresa=$_SESSION['RUTEMPRESA'];
    }


    // echo $_POST['idmod']."sss";
    // exit;

    if ($_POST['idmod']!="") {
        $mysqli->query("UPDATE CTPlantillas SET rut='".$_POST['trut']."',rsocial='".$_POST['trsocial']."', tipodocumento='".$_POST['tdoc']."', numero='".$_POST['tnum']."', fecha='".$_POST['tfec']."', exento='".$_POST['texe']."', neto='".$_POST['tnet']."', iva='".$_POST['tiva']."', retencion='".$_POST['tret']."', total='".$_POST['ttot']."', tipo='".$_POST['stipo']."', cuenta='".$_POST['cuenta']."'  WHERE id='".$_POST['idmod']."'");
    }else{
        $mysqli->query("INSERT INTO CTPlantillas VALUE('','$RutEmpresa','".$_POST['nombre']."','".$_POST['trut']."','".$_POST['trsocial']."','".$_POST['cuenta']."','".$_POST['tdoc']."','".$_POST['tnum']."','".$_POST['tfec']."','".$_POST['texe']."','".$_POST['tnet']."','".$_POST['tiva']."','".$_POST['tret']."','".$_POST['ttot']."','".$_POST['stipo']."','A')");
    }



 	$mysqli->close();

	header("location:frmPLCompraVenta.php");