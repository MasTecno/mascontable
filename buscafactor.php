<?php 
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';
    
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $resultado="";

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTFactores WHERE periodo='".$_POST['ano']."' AND estado='A' AND mes1>0 AND mes2>0 AND mes3>0 AND mes4>0 AND mes5>0 AND mes6>0 AND mes7>0 AND mes8>0 AND mes9>0 AND mes10>0 AND mes11>0 AND mes12>0";

    $resultado = $mysqli->query($SQL);

    $row_cnt = $resultado->num_rows;
    if ($row_cnt>0) {
        echo '<div class="alert alert-success"><strong>Disponible!</strong> Factores cargados.</div>';
    }else{
        echo '<div class="alert alert-danger"><strong>Advertencia!</strong> Factores no disponibles.</div>';
    }

    $mysqli->close();
?>