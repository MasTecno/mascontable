<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		echo ('<script> window.location.href="../?Msj=95";</script>');
		exit;
	}

    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $PeriodoX=$_POST['anoselect'];

    $xtccosto=$_POST['SelCCosto'];

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

    $Str='';

    if (isset($_POST['check3'])) {
        $Str=$Str.'<br><br><br><br><br><br>';
    }

    if (isset($_POST['check1'])) {

        if (strlen($xRUT)==9) {
            $RutPunto1=substr($xRUT,-10,1);
        }else{
            $RutPunto1=substr($xRUT,-10,2);
        }
        
        $RutPunto2=substr($xRUT,-5);
        $RutPunto3=substr($xRUT,-8,3);
        $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

        $Str=$Str.'
            <table width="100%">
                <tr>
                    <td width="10%">Contribuyente:</td>
                    <td width="">'.$xNOM.'&nbsp;</td>
                </tr>
                <tr>
                    <td>Rut:</td>
                    <td>'.$srtRut.'&nbsp;</td>
                </tr>
                <tr>
                    <td>Domicilio:</td>
                    <td>'.$xDIR.'&nbsp;</td>
                </tr>
                <tr>
                    <td>Ciudad:</td>
                    <td>'.$xCUI.'&nbsp;</td>
                </tr>
                <tr>
                    <td>Giro:</td>
                    <td>'.$xGIR.'&nbsp;</td>
                </tr>
            </table>
        ';

            if (isset($_POST['check7'])) {
                if (strlen($xRrep)==9) {
                    $RutPunto1=substr($xRrep,-10,1);
                }else{
                    $RutPunto1=substr($xRrep,-10,2);
                }
                
                $RutPunto2=substr($xRrep,-5);
                $RutPunto3=substr($xRrep,-8,3);
                $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

                $Str=$Str.'
                    <table width="100%">
                        <tr>
                            <td width="10%">Rep. Legal:</td>
                            <td width="">'.$xRep.'&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Rep. Rut:</td>
                            <td>'.$srtRut.'&nbsp;</td>
                        </tr>
                    </table>
                ';
            }

        $Str=$Str.'
            
            <br>
        ';
    }

	if($_POST['SelCCosto']>0){

		$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' AND id='".$_POST['SelCCosto']."' ORDER BY nombre";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$NCosot="Centro de Costos: ".$registro['nombre'];
		}
	}

	$Str=$Str.'
		<table width="100%">
			<tr style="text-align:center; font-size: 18px;">
				<td><strong>ESTADO DE RESULTADO</strong></td>	
			</tr>';

    if ($_POST['fdesde']!="" && $_POST['fhasta']!="" && $_POST['SwFecha']=="R") {
        $Str=$Str.'<tr style="text-align:center; font-size: 18px;">
            <td>Periodo comprendido entre '.$_POST['fdesde'].' AL '.$_POST['fhasta'].'</td>
        </tr>  ';           
    }else{
        $Str=$Str.'<tr style="text-align:center; font-size: 18px;">
            <td>Periodo comprendido entre Enero a Diciembre del '.$PeriodoX.'</td>
        </tr>  ';           
    }

    $Str=$Str.'
			<tr style="text-align:center; font-size: 18px;">
				<td>'.$NCosot.'</td>	
			</tr>
		</table>
		<br>
	';	

    ////////////////INGRESOS
    $Str=$Str.'
            <br><br>
            <table class="table-condensed table-bordered table-hover" style="width: 100%;">
                <tr>
                    <th colspan="2" style=" background-color: #ccc;">INGRESOS</th>
                </tr>';

                $totoI=0;

                if ($_SESSION["PLAN"]=="S"){
                    $SQLint2="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa'";
                }else{
                    $SQLint2="SELECT * FROM CTCuentas WHERE 1=1";
                }

                $SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='I' ORDER BY Id";
                $resultados = $mysqli->query($SQL);
                $cont=1;
                while ($registro = $resultados->fetch_assoc()) {

                    $Titulo='
                        <tr>
                            <th colspan="2" style="text-align: left;">'.$registro['Nombre'].'</th>
                        </tr>
                    ';

                    $SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='$RutEmpresa'";
                    $resultados1 = $mysqli->query($SQLint);
                    $row_cnt = $resultados1->num_rows;
                    if ($row_cnt==0) {
                        $SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa=''";
                    }

                    $linea = '';
                    $resultadosint = $mysqli->query($SQLint." AND IdCab='".$registro['Id']."'");
                    while ($registroint = $resultadosint->fetch_assoc()) {

                        $resultados2 = $mysqli->query($SQLint2." AND numero='".$registroint['Cuenta']."'");
                        while ($registroint2 = $resultados2->fetch_assoc()) {
                            $Xoper=$registroint2['detalle'];
                        }

                        // $Str=$Str.$linea;
                        $valorlinea=0;

                        if ($_POST['fdesde']!="" && $_POST['fhasta']!="" && $_POST['SwFecha']=="R") {
                            
                            $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fdesde']); // Crear objeto DateTime
                            $FDesde = $dateTime->format('Y-m-d');
                            $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fhasta']); // Crear objeto DateTime
                            $FHasta = $dateTime->format('Y-m-d');
                            $SQL3="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND estado='A' AND cuenta='".$registroint['Cuenta']."' AND fecha BETWEEN '$FDesde' AND '$FHasta'";
                        }else{
                            $SQL3="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%".$PeriodoX."%' AND estado='A' AND cuenta='".$registroint['Cuenta']."'";
                        }

                        if ($_POST['SelCCosto']>0) {
                            $SQL3=$SQL3." AND ccosto='".$_POST['SelCCosto']."'";
                        }

                        $resultados3 = $mysqli->query($SQL3);
                        while ($registro3 = $resultados3->fetch_assoc()) {
                            $totoI=$totoI+$registro3['shaber']-$registro3['sdebe'];
                            $valorlinea=$registro3['shaber']-$registro3['sdebe'];
                        }

                        if($valorlinea>0){
                            $linea=$linea.'
                            <tr>
                                <td style="text-align: right;" width="40%">'.$registroint['Cuenta'].' - '.$Xoper.'</td>
                                
                                <td style="text-align: right;">'.number_format($valorlinea, $NDECI, $DDECI, $DMILE).'</td>
                            </tr>';
                        }

                    }

                    if($linea!=""){
                        $Str=$Str.$Titulo.$linea;
                    }

                }

            $Str=$Str.'
                <tr>
                    <td style="text-align: right;" width="40%"><strong>TOTAL INGRESOS</strong></td>
                    <td style="text-align: right;"><strong>'.number_format($totoI, $NDECI, $DDECI, $DMILE).'</strong></td>
                </tr>
            </table>
        ';

        ///////////////EGRESOS
        $Str=$Str.'
            <br><br>
            <table class="table-condensed table-bordered table-hover" style="width: 100%;">
                <tr>
                    <th colspan="2" style="background-color: #ccc;">EGRESOS</th>
                </tr>';

                $totoE=0;

                if ($_SESSION["PLAN"]=="S"){
                    $SQLint2="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa'";
                }else{
                    $SQLint2="SELECT * FROM CTCuentas WHERE 1=1";
                }

                $SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='E' ORDER BY Id";
                $resultados = $mysqli->query($SQL);
                $cont=1;
                while ($registro = $resultados->fetch_assoc()) {

                    $Titulo='
                        <tr>
                            <th colspan="2" style="text-align: left;">'.$registro['Nombre'].'</th>
                        </tr>
                    ';

                    $SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='$RutEmpresa'";
                    $resultados1 = $mysqli->query($SQLint);
                    $row_cnt = $resultados1->num_rows;
                    if ($row_cnt==0) {
                        $SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa=''";
                    }

                    $linea = '';
                    $resultadosint = $mysqli->query($SQLint." AND IdCab='".$registro['Id']."'");
                    while ($registroint = $resultadosint->fetch_assoc()) {

                        $resultados2 = $mysqli->query($SQLint2." AND numero='".$registroint['Cuenta']."'");
                        while ($registroint2 = $resultados2->fetch_assoc()) {
                            $Xoper=$registroint2['detalle'];
                        }



                        // $Str=$Str.$linea;
                        $valorlinea=0;

                        if ($_POST['fdesde']!="" && $_POST['fhasta']!="" && $_POST['SwFecha']=="R") {
                            
                            $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fdesde']); // Crear objeto DateTime
                            $FDesde = $dateTime->format('Y-m-d');
                            $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fhasta']); // Crear objeto DateTime
                            $FHasta = $dateTime->format('Y-m-d');
                            $SQL3="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND estado='A' AND cuenta='".$registroint['Cuenta']."' AND fecha BETWEEN '$FDesde' AND '$FHasta'";
                        }else{
                            $SQL3="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%".$PeriodoX."%' AND estado='A' AND cuenta='".$registroint['Cuenta']."'";
                        }

                        if ($_POST['SelCCosto']>0) {
                            $SQL3=$SQL3." AND ccosto='".$_POST['SelCCosto']."'";
                        }

                        $resultados3 = $mysqli->query($SQL3);
                        while ($registro3 = $resultados3->fetch_assoc()) {
                            $totoE=$totoE+$registro3['sdebe']-$registro3['shaber'];
                            $valorlinea=$registro3['sdebe']-$registro3['shaber'];
                        }

                        if($valorlinea>0){
                            $linea=$linea.'
                            <tr>
                                <td style="text-align: right;" width="40%">'.$registroint['Cuenta'].' - '.$Xoper.'</td>
                                
                                <td style="text-align: right;">'.number_format($valorlinea, $NDECI, $DDECI, $DMILE).'</td>
                            </tr>';
                        }

                    }

                    if($linea!=""){
                        $Str=$Str.$Titulo.$linea;
                    }

                }

                $Str=$Str.'
                <tr>
                    <td style="text-align: right;" width="40%"><strong>TOTAL EGRESOS</strong></td>
                    <td style="text-align: right;"><strong>'.number_format($totoE, $NDECI, $DDECI, $DMILE).'</strong></td>
                </tr>
            </table>';

        $Str=$Str.'
            <br>
            <table class="table-condensed table-bordered table-hover" style="width: 100%;">
                <tr>
                    <td style="text-align: center; background-color: #ccc;" width="33%"><strong>TOTAL INGRESOS</strong></td>
                    <td style="text-align: center; background-color: #ccc;" width="33%"><strong>TOTAL EGRESOS</strong></td>
                    <td style="text-align: center; background-color: #ccc;" width="33%"><strong>RESULTADO</strong></td>
                </tr>
                <tr>
                    <td style="text-align: center;"><strong>'.number_format($totoI, $NDECI, $DDECI, $DMILE).'</strong></td>
                    <td style="text-align: center;"><strong>'.number_format($totoE, $NDECI, $DDECI, $DMILE).'</strong></td>
                    <td style="text-align: center;"><strong>'.number_format(($totoI-$totoE), $NDECI, $DDECI, $DMILE).'</strong></td>
                </tr>
            </table>
        ';

        if ($_SERVER["REQUEST_URI"]=="/EResultado/ReportPDF.php") {
            if (count($_POST['check_list'])>0) { 
                $Str=$Str.'
                    <br><br>
                    <table width="100%" align="center">
                        <tr>
                            <td>Certificamos que el presente balance ha sido confeccionado con datos proporcionados por la empresa, conjuntamente con la documentaci&oacute;n que se</td>
                        </tr>
                        <tr>
                            <td>encuentra en los libros de contabilidad (Art. 100 del C. Tributario)</td>
                        </tr>
                    </table>
                ';
    
                foreach($_POST['check_list'] as $selected) {
                    $SQL="SELECT * FROM CTContadoresFirma WHERE Id='".$selected."'";
                    $resultados = $mysqli->query($SQL);
                    $row_cnt = $resultados->num_rows;
    
                    while ($registro = $resultados->fetch_assoc()) {
                        $NomContador=$NomContador.'<td align="center">'.$registro['Nombre'].'</td>';
                        $RutContador=$RutContador.'<td align="center">'.$registro['Rut'].'<br>'.$registro['Cargo'].'</td>';
                    }
                }
    
                $SQL="SELECT * FROM CTEmpresas WHERE rut='".$RutEmpresa."'";
                $resultados = $mysqli->query($SQL);
                while ($registro = $resultados->fetch_assoc()) {
                    $representante=$registro['representante'];
                    $xRrep=$registro['rut_representante'];    
                }
    
                $i=1;
                while ($i<=count($_POST['check_list'])){
                    $TutContador=$TutContador.'<td align="center">Firma Contador(a)</td>';
                    $i++;
                }
    
                $Str=$Str.'
                    <br><br><br>
                    <table width="100%" align="center">
                        <tr>
                            <td align="center">'.$representante.'</td>
                            '.$NomContador.'
                        </tr>
                        <tr>
                            <td align="center">'.$xRrep.'<br>'.$RazonSocial.'</td>
                            '.$RutContador.'
                        </tr>
                        <tr>
                            <td align="center">Firma Representante Legal</td>
                            '.$TutContador.'
                        </tr>
                    </table>
                ';
            }    

            $HTML=$Str;
        }else{
            if ($_SERVER["REQUEST_URI"]=="/EResultado/ReportXLS.php") {
                echo utf8_decode($Str);
            }else{
                echo $Str;
            }
        }
?>