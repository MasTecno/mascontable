<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
	include '../conexion/secciones.php';
    
    if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
        header("location:../index.php?Msj=95");
        exit;
    }

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $contador=$NomCont;

    if($Periodo==""){
        header("location:../frmMain.php");
        exit;
    }

    if (isset($_POST['anoselect'])) {
        if ($_POST['anoselect']!=""){
            $danol=$_POST['anoselect'];
            $Xfdesde="01-01-".$danol;
            $Xfhasta="31-12-".$danol;
        }else{
            $dmes = substr($Periodo,0,2);
            $danol = substr($Periodo,3,4);
            $Xfdesde="01-01-".$danol;
            $Xfhasta="31-12-".$danol;
        } 
    }else{
        $dmes = substr($Periodo,0,2);
        $danol = substr($Periodo,3,4);
        $Xfdesde="01-01-".$danol;
        $Xfhasta="31-12-".$danol;
    }

    $CSS='';

    $Str=$CSS.'
        <br>
        <table width="100%" border="0" align="center">
            <tr>
                <td align="center"><h3><strong>BALANCE GENERAL</strong></h3></td>
            </tr>
    ';

    //$Str='BALANCE GENERAL';

    if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
        $dia = substr($_POST['fdesde'],0,2);
        $mes = substr($_POST['fdesde'],3,2);
        $ano = substr($_POST['fdesde'],6,4);
        $Pdesde=$dia."/".$mes."/".$ano;

        $dia = substr($_POST['fhasta'],0,2);
        $mes = substr($_POST['fhasta'],3,2);
        $ano = substr($_POST['fhasta'],6,4);
        $Phasta=$dia."/".$mes."/".$ano;

        $Str=$Str.'
            <tr>
                <td align="center"><h3>'.$Pdesde.' al '.$Phasta.'</h3></td>
            </tr>
        ';
    }else{
        if (isset($_POST['anoselect']) && $_POST['anoselect']!=""){
            $Str=$Str.'
            <tr>
                <td align="center"><h3>Per&iacute;odo '.$_POST['anoselect'].'</h3></td>
            </tr>
        ';
        }else{
            $Str=$Str.'
                <tr>
                    <td align="center"><h3>Per&iacute;odo '.$_POST['aproceso'].'</h3></td>
                </tr>
            ';
        }
    }

    if ($_SERVER["REQUEST_URI"]=="/Balance/frmBalancePDF.php") {
		if (isset($_POST['check5'])) {
            $Str=$Str.'
                <tr>
                    <td align="center"><h3><strong>PRE - BALANCE</strong></h3></td>
                </tr>
            ';
        }
    }
    $Str=$Str.'
        </table>
    ';

    if ($_SERVER["REQUEST_URI"]=="/Balance/frmBalancePDF.php") {
        $Str=$Str.'
            <br>
                
            <table class="table-condensed table-bordered table-hover" style="width: 100%;" border="0">

                <tr style="background-color: #d9d9d9;">
                    <td style="text-align: center;" width="52%"></td>
                    <td style="text-align: center;" width="16%"><strong>Saldos</strong></td>
                    <td style="text-align: center;" width="16%"><strong>Balance</strong></td>
                    <td style="text-align: center;" width="16%"><strong>Resultado</strong></td>
                </tr>
            
                <tr style="background-color: #d9d9d9;">
                    <td style="text-align: center;" width="8%"><strong>Codigo</strong></td>
                    <td width="28%"><strong>Cuenta</strong></td>
                    <td style="text-align: right;" width="8%"><strong>D&eacute;bito</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Cr&eacute;dito</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Deudor</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Acreedor</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Activo</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Pasivo</strong></td>
                    <td style="text-align: right;" width="8%"><strong>P&eacute;rdida</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Ganancia</strong></td>
                </tr>
        ';
    }else{
        $Str=$Str.'
            <br>
                
            <table class="table-condensed table-bordered table-hover" style="width: 100%;" border="1">

            <tr style="background-color: #d9d9d9;">
                <td style="text-align: center;" colspan="4"></td>
                <td style="text-align: center;" colspan="2"><strong>Saldos</strong></td>
                <td style="text-align: center;" colspan="2"><strong>Balance</strong></td>
                <td style="text-align: center;" colspan="2"><strong>Resultado</strong></td>
            </tr>
            
                <tr style="background-color: #d9d9d9;">
                    <td style="text-align: center;" width="8%"><strong>Codigo</strong></td>
                    <td width="28%"><strong>Cuenta</strong></td>
                    <td style="text-align: right;" width="8%"><strong>D&eacute;bito</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Cr&eacute;dito</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Deudor</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Acreedor</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Activo</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Pasivo</strong></td>
                    <td style="text-align: right;" width="8%"><strong>P&eacute;rdida</strong></td>
                    <td style="text-align: right;" width="8%"><strong>Ganancia</strong></td>
                </tr>
        ';

    }


        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

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

        if ($_SESSION["PLAN"]=="S"){
            $SQLx="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero";
        }else{
            $SQLx="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY numero";
        }
        $consultax = $mysqli->query($SQLx);
        while ($registrox = $consultax->fetch_assoc()) {
            $sw=0;
            $SQL="SELECT periodo, cuenta, sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE periodo like '%$danol' AND cuenta='".$registrox["numero"]."' AND glosa='' AND rutempresa='$RutEmpresa'";

            if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
                $dia = substr($_POST['fdesde'],0,2);
                $mes = substr($_POST['fdesde'],3,2);
                $ano = substr($_POST['fdesde'],6,4);

                $LFdesde=$ano."/".$mes."/".$dia;

                $dia = substr($_POST['fhasta'],0,2);
                $mes = substr($_POST['fhasta'],3,2);
                $ano = substr($_POST['fhasta'],6,4);

                $LFhasta=$ano."/".$mes."/".$dia;

                $SQL=$SQL." AND fecha BETWEEN '".$LFdesde."' AND '".$LFhasta."'";
            }

            $SQL=$SQL." group by cuenta";

            $consulta = $mysqli->query($SQL);
            while ($registro = $consulta->fetch_assoc()) {
                $sw=1;
                if ($_SESSION["PLAN"]=="S"){
                    $SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero ='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
                }else{
                    $SQL1="SELECT * FROM CTCuentas WHERE numero ='".$registro["cuenta"]."'";
                }
                $consulta1 = $mysqli->query($SQL1);
                while ($registro1 = $consulta1->fetch_assoc()) {
                    $ncuenta=$registro1["detalle"];
                    $idcat=$registro1["id_categoria"];
                }
        
                $SQL1="SELECT * FROM CTCategoria WHERE id ='$idcat'";
                $consulta1 = $mysqli->query($SQL1);
                while ($registro1 = $consulta1->fetch_assoc()) {
                    $tcuenta=$registro1["tipo"];
                }

                $sdeudor=0;
                $sacreedor=0;
                if($registro["sdebe"]>$registro["shaber"]){
                    $sdeudor=$registro["sdebe"]-$registro["shaber"];
                }
                if($registro["sdebe"]<$registro["shaber"]){
                    $sacreedor=$registro["shaber"]-$registro["sdebe"];
                }

                $activo=0;
                $pasivo=0;
                $perdida=0;
                $ganancia=0;

                if($tcuenta=="ACTIVO"){
                    if($registro["sdebe"]>$registro["shaber"]){
                    $activo=$registro["sdebe"]-$registro["shaber"];
                    }
                    if($registro["sdebe"]<$registro["shaber"]){
                    $pasivo=$registro["shaber"]-$registro["sdebe"];
                    }
                }

                if($tcuenta=="PASIVO"){
                    if($registro["sdebe"]<$registro["shaber"]){
                    $pasivo=$registro["shaber"]-$registro["sdebe"];
                    }
                    if($registro["sdebe"]>$registro["shaber"]){
                    $activo=$registro["sdebe"]-$registro["shaber"];
                    }
                }

                if($tcuenta=="RESULTADO"){
                    if($registro["sdebe"]<$registro["shaber"]){
                    $ganancia=$registro["shaber"]-$registro["sdebe"];
                    }
                    if($registro["sdebe"]>$registro["shaber"]){
                    $perdida=$registro["sdebe"]-$registro["shaber"];
                    }
                }

                if ($_SERVER["REQUEST_URI"]=="/Balance/frmBalancePDF.php" || $_SERVER["REQUEST_URI"]=="/Balance/frmBalanceXLS.php") {
                    $Str=$Str.
                    '
                        <tr>
                            <td align="center">'.$registro["cuenta"].'</td>
                            <td>'.strtoupper($ncuenta).'</td>
                            <td align="right">'.number_format($registro["sdebe"], $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($registro["shaber"], $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($sdeudor, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($sacreedor, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($activo, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($pasivo, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($perdida, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($ganancia, $NDECI, $DDECI, $DMILE).'</td>
                        </tr>
                    ';
                }else{
                    $Str=$Str.
                    '
                        <tr>
                            <td align="center"><a href="javascript:MayorCta('.$registro["cuenta"].');"><strong>'.$registro["cuenta"].'</strong></a></td>
                            <td><a href="javascript:MayorCta('.$registro["cuenta"].');"><strong>'.strtoupper($ncuenta).'</strong></a></td>
                            <td align="right">'.number_format($registro["sdebe"], $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($registro["shaber"], $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($sdeudor, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($sacreedor, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($activo, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($pasivo, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($perdida, $NDECI, $DDECI, $DMILE).'</td>
                            <td align="right">'.number_format($ganancia, $NDECI, $DDECI, $DMILE).'</td>
                        </tr>
                    ';
                }
        
                $XDSDEBE=$XDSDEBE+$registro["sdebe"];
                $XDSHABER=$XDSHABER+$registro["shaber"];

                $XDSDEUDOR=$XDSDEUDOR+$sdeudor;
                $XDSACREEDOR=$XDSACREEDOR+$sacreedor;

                $XDACTIVO=$XDACTIVO+$activo;
                $XDPASIVO=$XDPASIVO+$pasivo;

                $XDPEDIDA=$XDPEDIDA+$perdida;
                $XDGANANCIA=$XDGANANCIA+$ganancia;
            }

            if ($_SERVER["REQUEST_URI"]=="/Balance/frmBalanceXLS.php") {
                if (isset($_POST['check2'])) {

                }else{
                    if ($sw==0) {
                        $Str=$Str.
                        '
                            <tr>
                                <td align="center">'.$registrox["numero"].'</td>
                                <td>'.strtoupper ($registrox["detalle"]).'</td>
                                <td><div align="right">0</div></td>
                                <td><div align="right">0</div></td>
                                <td><div align="right">0</div></td>
                                <td><div align="right">0</div></td>
                                <td><div align="right">0</div></td>
                                <td><div align="right">0</div></td>
                                <td><div align="right">0</div></td>
                                <td><div align="right">0</div></td>
                            </tr>
                        '; 
                    }
                }            
            }

        }
        // $mysqli->close();    

        $mdebe="0";
        $mhaber="0";
        if($XDSDEBE>$XDSHABER){
            $mdebe=$XDSDEBE-$XDSHABER;
        }
        if($XDSHABER>$XDSDEBE){
            $mhaber=$XDSHABER-$XDSDEBE;
        }

        $mdeudor="0";
        $macreedor="0";
        if($XDSDEUDOR>$XDSACREEDOR){
            $mdeudor=$XDSDEUDOR-$XDSACREEDOR;
        }
        if($XDSACREEDOR>$XDSDEUDOR){
            $macreedor=$XDSACREEDOR-$XDSDEUDOR;
        }

        $mayact="0";
        $maypas="0";
        if($XDACTIVO>$XDPASIVO){
            $mayact=$XDACTIVO-$XDPASIVO;
        }
        if($XDPASIVO>$XDACTIVO){
            $maypas=$XDPASIVO-$XDACTIVO;
        }

        $mayper="0";
        $maygan="0";
        if($XDPEDIDA>$XDGANANCIA){
            $mayper=$XDPEDIDA-$XDGANANCIA;
            $MsjDif="PERDIDA DEL EJERCICIO";
        }
        if($XDGANANCIA>$XDPEDIDA){
            $maygan=$XDGANANCIA-$XDPEDIDA;
            $MsjDif="UTILIDAD DEL EJERCICIO";
        }

        if($maypas!=$mayper){
            $MsjDif="DESCUADRE";
        }

        if(round($mayact)!=round($maygan)){
            $MsjDif="DESCUADRE";
        }
        
    $Str=$Str.'
        <tr style="background-color: #d9d9d9;">
            <td colspan="2"><strong>SUB-TOTAL</strong></td>
            <td align="right">'.number_format($XDSDEBE, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($XDSHABER, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($XDSDEUDOR, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($XDSACREEDOR, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($XDACTIVO, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($XDPASIVO, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($XDPEDIDA, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($XDGANANCIA, $NDECI, $DDECI, $DMILE).'</td>
        </tr>
        <tr style="background-color: #f1f1f1;">
            <td colspan="2"><strong>'.$MsjDif.'</strong></td>
            <td align="right">'.number_format($mhaber, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($mdebe, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($macreedor, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($mdeudor, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($maypas, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($mayact, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($maygan, $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format($mayper, $NDECI, $DDECI, $DMILE).'</td>
        </tr>
        <tr style="background-color: #d9d9d9;">
            <td colspan="2"><strong>TOTAL FINAL</strong></td>
            <td align="right">'.number_format(($XDSDEBE+$mhaber), $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format(($XDSHABER+$mdebe), $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format(($XDSDEUDOR+$macreedor), $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format(($XDSACREEDOR+$mdeudor), $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format(($XDACTIVO+$maypas), $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format(($XDPASIVO+$mayact), $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format(($XDPEDIDA+$maygan), $NDECI, $DDECI, $DMILE).'</td>
            <td align="right">'.number_format(($XDGANANCIA+$mayper), $NDECI, $DDECI, $DMILE).'</td>
        </tr>
    </table> 
    ';

    if ($_SERVER["REQUEST_URI"]=="/Balance/frmBalancePDF.php") {
        if (isset($_POST['check2'])) {
            $Str=$Str.'
            <br><br>
            <table width="100%" border="0" align="center">
                <tr>
                    <td>Certificamos que el presente balance ha sido confeccionado con datos proporcionados por la empresa, conjuntamente con la documentaci&oacute;n que se</td>
                </tr>
                <tr>
                    <td>encuentra en los libros de contabilidad (Art. 100 del C. Tributario)</td>
                </tr>
            </table>
            ';        
        }

        if (isset($_POST['check3'])) {
            $SQL="SELECT * FROM CTEmpresas WHERE rut='".$RutEmpresa."'";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {
                $representante=$registro['representante'];
                $xRrep=$registro['rut_representante'];    
            }

            $r='
            <td>
                <table width="100%" border="0">
                    <tr>
                        <td>'.$representante.'</td>
                    </tr>
                    <tr>
                        <td>'.$xRrep.'</td>
                    </tr>
                    <tr>
                        <td>'.$RazonSocial.'</td>
                    </tr>
                    <tr>
                        <td>Firma Representante Legal</td>
                    </tr>
                </table>
            </td>
            ';
        }

        if (isset($_POST['check_list']) && is_array($_POST['check_list']) && count($_POST['check_list'])>0) { 
            foreach($_POST['check_list'] as $selected) {
                $SQL="SELECT * FROM CTContadoresFirma WHERE Id='".$selected."'";
                $resultados = $mysqli->query($SQL);
                $row_cnt = $resultados->num_rows;

                while ($registro = $resultados->fetch_assoc()) {
                    $NomContador=$NomContador.'<td align="center">'.$registro['Nombre'].'</td>';
                    $RutContador=$RutContador.'<td align="center">'.$registro['Rut'].'<br>'.$registro['Cargo'].'</td>';
                }
            }

            $t='<td><table width="100%" border="0">';

            $i=1;
            while ($i<=count($_POST['check_list'])){
                $TutContador=$TutContador.'<td align="center">Firma Contador(a)</td>';
                $i++;
            }

            $t=$t.'
                <tr>
                    '.$NomContador.'
                </tr>
                <tr>
                    '.$RutContador.'
                </tr>
                <tr>
                    '.$TutContador.'
                </tr>
            </table></td>';
        }

        $Str=$Str.'
            <br><br><br>
            <table width="100%" border="0" align="center">
                <tr>
                    '.$r.'
                    '.$t.'
                </tr>
            </table>
        ';

		if ($_POST['comment']!="") {
            $Str=$Str.'
                <br><br>
                <table width="80%" border="0" align="center">
                    <tr>
                        <td align="center">'.$_POST['comment'].'</td>
                    </tr>
                </table>
            ';
		}        
    }

    if ($_SERVER["REQUEST_URI"]=="/Balance/frmBalancePDF.php") {
		$HTML=$Str;
	}else{
		echo $Str;
	}
?>