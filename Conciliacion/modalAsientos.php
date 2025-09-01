<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$IdCab=$_POST['EdiCon'];
	$_SESSION['EdiCon']=$_POST['EdiCon'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $CCta=0;
    $SqlStr="SELECT * FROM CTConciliacionCab WHERE Id='$IdCab' AND RutEmpresa='$RutEmpresa'";
    $Resultado = $mysqli->query($SqlStr);
    while ($Registro = $Resultado->fetch_assoc()) {
        $CCta=$Registro['Cuenta'];
        $FDesde=$Registro['FDesde'];
        $FHasta=$Registro['FHasta'];
    }

    $Sql="SELECT * FROM CTRegLibroDiario WHERE cuenta='$CCta' AND rutempresa='$RutEmpresa'";
    if($_POST['monconciAbono']>0){
        $Sql=$Sql." AND haber = '".$_POST['monconciAbono']."'";
    }else{
        $Sql=$Sql." AND debe = '".$_POST['monconciCargo']."'";
    }

    $Sql=$Sql." AND fecha BETWEEN '$FDesde' AND '$FHasta'";
    $Res = $mysqli->query($Sql);

    $row_cnt = $Res->num_rows;
    if($row_cnt>0){
        while ($Registro = $Res->fetch_assoc()) {
            $keyas=$Registro['keyas'];
            $monto=$Registro['debe'];
            if($monto==0){
                $monto=$Registro['haber'];
            }

            $SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$keyas' AND cuenta='0' AND rutempresa='$RutEmpresa' AND glosa<>''";
            $Resbus = $mysqli->query($SqlBus);
            while ($RegBus = $Resbus->fetch_assoc()) {
                $Glosa=$RegBus['glosa'];
                $ncomprobante=$RegBus['ncomprobante'];
                $fecha=$RegBus['fecha'];
            }

            $SqlBus="SELECT * FROM CTConciliacionLog WHERE IdCab='$IdCab' AND RutEmpresa='$RutEmpresa' AND IdDiario='".$Registro['id']."'";
            $Resbus = $mysqli->query($SqlBus);
            $row_cn = $Resbus->num_rows;
            if($row_cn==0){
                echo '
                    <tr>
                        <td>'.$ncomprobante.'</td>
                        <td style="text-align: center;">'.date('d-m-Y',strtotime($fecha)).'</td>
                        <td>'.$Glosa.'</td>
                        <td style="text-align: right;">'.number_format($monto, $NDECI, $DDECI, $DMILE).'</td>
                        <td style="text-align: center;"><button type="button" title="Modificar Registro" class="btn btn-modificar btn-xs" onclick="AsociarAsiento(\''.$keyas.'\')"><span class="glyphicon glyphicon-ok"></span>  </button></td>
                    </tr>
                ';
            }
        }
    }else{
        echo '
            <tr>
                <td colspan="5" style="text-align: center;"><strong>No existen alguna coincidencia de monto y cuenta para asociar el movimiento</strong></td>
            </tr>
        ';
    }

?>