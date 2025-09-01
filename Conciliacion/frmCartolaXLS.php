<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../?Msj=95");
		exit;
	}
	$IdCab=$_POST['EdiCon'];

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

        if($registro['tipo']=="NUME_DECI"){
            $NDECI=$registro['valor'];  
        } 
    }

    $SqlStr="SELECT * FROM CTConciliacionDet WHERE IdCab='$IdCab' ORDER BY Fecha ASC, Id ASC";
    $Resultado = $mysqli->query($SqlStr);
    while ($Registro = $Resultado->fetch_assoc()) {

        $IdDiario="";
        $SStr="SELECT * FROM CTConciliacionLog WHERE IdCab='$IdCab' AND IdDet='".$Registro['Id']."'";
        $Res = $mysqli->query($SStr);
        while ($Reg = $Res->fetch_assoc()) {
            $IdDiario=$Reg['IdDiario'];											
        }										

        $DComp="";
        $DGlosa="";
        $DTMovio="";
        $SStr="SELECT * FROM CTRegLibroDiario WHERE id='".$IdDiario."' AND rutempresa='$RutEmpresa' AND glosa<>''";
        $Res = $mysqli->query($SStr);
        while ($Reg = $Res->fetch_assoc()) {
            $DComp=$Reg['ncomprobante'];
            $DGlosa=$Reg['glosa'];		

            if ($Reg["tipo"]=="E") {
                $DTMovio="Egreso";
            }
            if ($Reg["tipo"]=="I") {
                $DTMovio="Ingreso";	
            }
            if ($Reg["tipo"]=="T") {
                $DTMovio="Traspaso";
            }
        }
        if($Registro['Numero']>0){
            $DNumDoc=$Registro['Numero'];
        }else{
            $DNumDoc="";
        }
        $Cuerpo=$Cuerpo. '
            <tr>
                <td>'.date('d-m-Y',strtotime($Registro['Fecha'])).'</td>
                <td>'.$Registro['Glosa'].'</td>
                <td>'.$Registro['Cargos'].'</td>
                <td>'.$Registro['Abonos'].'</td>
                <td>'.$Registro['Rut'].'</td>
                <td>'.$DNumDoc.'</td>
            </tr>
        ';
        $Cont++;
    }
    $mysqli->close();   


    $NomArch="CartolaRespaldo.xls";
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$NomArch.""); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

    echo '
    
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Glosa</th>
                    <th>Monto Abono</th>
                    <th>Monto Cargo</th>
                    <th>Rut</th>
                    <th>N&uacute;mero</th>
                </tr>
            </thead>
            <tbody>
    ';

    echo $Cuerpo;

    echo '
            </tbody>
        </table>
    ';