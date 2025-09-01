<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);


	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];  
		}
	}

    $SQL="SELECT * FROM CTAnticipos WHERE RutEmpresa='$RutEmpresa'";
    $resultado = $mysqli->query($SQL);
    while ($registro = $resultado->fetch_assoc()) {
        $Id=$registro['Id'];
        $fecha=date('d/m/Y', strtotime($registro['Fecha']));
        $rut=$registro['Rut'];
        $glosa=$registro['Glosa'];
        $monto=$registro['Monto'];
        $estado=$registro['Estado'];
        $tipo=$registro['Tipo'];

        if($tipo=='C'){
            $tipo='ANTICIPO CLIENTES';
            $color='background-color:rgba(160, 251, 144, 0.27);';
        }else{
            $tipo='ANTICIPO PROVEEDORES';
            $color='background-color:rgba(255, 175, 175, 0.27);';
        }

        $Sql1="SELECT * FROM CTCliPro WHERE rut='$rut' LIMIT 1";
        $resultado1 = $mysqli->query($Sql1);
        while ($registro1 = $resultado1->fetch_assoc()) {
            $RazonSocialAnticipo=$registro1['razonsocial'];
        }

        echo '
            <tr style="'.$color.';">
                <td style="text-align: center;">'.$Id.'</td>
                <td style="text-align: center;">'.$fecha.'</td>
                <td style="text-align: right;">'.$rut.'</td>
                <td>'.$RazonSocialAnticipo.'</td>   
                <td>'.$glosa.'</td>
                <td style="text-align: right;">'.number_format($monto,0,$DDECI,$DMILE).'</td>
                <td style="text-align: center;">'.$tipo.'</td>
                <td style="text-align: right;">
                    <button type="button" class="btn btn-primary btn-xs" onclick="VerAnticipo('.$Id.')">
                        <i class="fa fa-eye"></i> Ver
                    </button>
                    <button type="button" class="btn btn-danger btn-xs" onclick="EliminarAnticipo('.$Id.')">
                        <i class="fa fa-trash"></i> Eliminar
                    </button>
                </td>
            </tr>
        ';
    }

    $mysqli->close();   

