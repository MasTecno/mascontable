<?php 
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';


    if($_POST['idmod']!=""){
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTTipoDocumento SET tiposii='".$_POST['csii']."', nombre='".$_POST['nombre']."', operador='".$_POST['operador']."' WHERE id='".$_POST['idmod']."'");
        $mysqli->close();    	
    }else{
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("INSERT INTO CTTipoDocumento VALUE('','".$_POST['csii']."','','".$_POST['nombre']."','".$_POST['operador']."','A')");
        $mysqli->close();    	
    }

	header("location:frmTipoDocumento.php?Exito");
 ?>