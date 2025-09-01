<?php 
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

	$resultado="";
	
    if($_POST['dat2']=="C"){
        $data2="P";
    }

    if($_POST['dat2']=="V"){
        $data2="C";
    }

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTCliPro WHERE rut='".$_POST['dat1']."' AND tipo='$data2' AND estado='A'";

    if($data2==""){
        $SQL="SELECT * FROM CTCliPro WHERE rut='".$_POST['dat1']."' AND estado='A'";
    }

    // if($_POST['dat2']=="X"){
    //     $SQL="SELECT rut, razonsocial FROM CTCliPro WHERE rut='".$_POST['dat1']."' AND estado='A' GROUP BY rut, razonsocial ORDER BY razonsocial";
    // }


    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {
         $resultado=$registro["razonsocial"];
    }

    $mysqli->close();

    if($resultado==""){
    	echo "";
    }else{
    	echo utf8_encode($resultado);
    }

?>