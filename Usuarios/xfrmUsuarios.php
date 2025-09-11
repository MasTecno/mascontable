<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    if ($_POST['idmod']!="") {
    	$mysqli->query("UPDATE CTContadores SET clave='".$_POST['claveX']."' WHERE id='".$_POST['idmod']."'");
    }else{

        $SQL="SELECT * FROM CTContadores WHERE correo='".strtolower($_POST['correo'])."'";

        $resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt>0) {
            $mysqli->close();
            header("location:frmUsuarios.php?Err=1");
            exit;
        }

    	$mysqli->query("INSERT INTO CTContadores VALUE('','".$_POST['tnombre']."','','".strtolower($_POST['correo'])."','".$_POST['clave']."','U','','A')");
    }

 	$mysqli->close();

	header("location:frmUsuarios.php");
 ?>