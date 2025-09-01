<?php 

    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
	include '../conexion/secciones.php';
    
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $resultado="";

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTHonoGene WHERE periodo='".$_POST['ano']."' AND estado='A'";

    $resultado = $mysqli->query($SQL);

    $row_cnt = $resultado->num_rows;
    if ($row_cnt>0) {
        echo '<div class="alert alert-success"><strong>Disponible!</strong> Proceso realizado.</div>';
    }else{
        echo '<div class="alert alert-danger"><strong>Advertencia!</strong> Procedo no Generado.</div>';
    }

    $mysqli->close();

?>