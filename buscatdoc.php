<?php 
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

	$resultado="";

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTTipoDocumento WHERE tiposii='".$_POST['dat1']."' AND estado='A'";
    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {
       $resultado=$registro["nombre"];           
    }
    $mysqli->close();

    if($resultado==""){
    	echo "";
    }else{
    	echo $resultado;
    }
?>