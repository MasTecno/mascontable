<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
	include '../conexion/secciones.php';

    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $resultado="";

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTHonorarios WHERE periodo like '%-".$_POST['ano']."' AND estado='A' AND rutempresa='". $RutEmpresa."'";

    $resultado = $mysqli->query($SQL);

    $row_cnt = $resultado->num_rows;
    if ($row_cnt>0) {
        echo '<div class="alert alert-success"><strong>Disponible!</strong> Documentos disponible '.$numero.'</div>';
    }else{
        echo '<div class="alert alert-danger"><strong>Advertencia!</strong> No hay documentos disponibles.</div>';
    }

    $mysqli->close();
?>