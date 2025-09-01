<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

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

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}
	$mysqli->close();

	if (isset($_POST['messelect']) || isset($_POST['anoselect'])) {
		if ($_POST['messelect']<=9) {
			$_SESSION['PERIODOPC']="0".$_POST['messelect']."-".$_POST['anoselect'];
		}else{
			$_SESSION['PERIODOPC']=$_POST['messelect']."-".$_POST['anoselect'];     
		}
	}else{
		$_SESSION['PERIODOPC']=$_SESSION['PERIODO'];
	}

	$PeriodoX=$_SESSION['PERIODOPC'];


	if (isset($_POST['anual']) && $_POST['anual']==1) {
		$PeriodoX=substr($_SESSION['PERIODOPC'],3,4);
	}


	if (substr($_SESSION['PERIODOPC'],3,4)=="2020") {
		$Val_Ret=10.75;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2021") {
		$Val_Ret=11.5;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2022") {
		$Val_Ret=12.25;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2023") {
		$Val_Ret=13;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2024") {
		$Val_Ret=13.75;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2025") {
		$Val_Ret=14.5;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2026") {
		$Val_Ret=15.25;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2027") {
		$Val_Ret=16;
	}

	if (substr($_SESSION['PERIODOPC'],3,4)=="2028") {
		$Val_Ret=17;
	}


	$Str="";

	$Str=$Str.'
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>LIBRO DE HONORARIOS '.$PeriodoX.'</strong></td>
			</tr>
		</table>
		<br>
	';

	$Str=$Str.'
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="1">
			<tr style="background-color: #d9d9d9;">
				<th>Fecha</th>
				<th>Rut</th>
				<th>Razon Social</th>
				<th>Cuenta</th> 
				<th>Procesa</th> 
				<th>Periodo</th> 
				<th align="center">N&deg; Doc</th>
				<th align="center">Tipo Documento</th>
				<th>Bruto</th>
				<th>Retenci&oacute;n</th>
				<th>3%</th>
				<th>Liquido</th>
			</tr>

	';
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if (isset($_POST['ACompleto']) && $_POST['ACompleto']!="") {
		$AnoCor="01-".$PeriodoX;
		$AComp=date('Y',strtotime($AnoCor));
		$SQL="SELECT * FROM CTHonorarios WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo like '%".$AComp."' ORDER BY fecha";
	}else{
		$SQL="SELECT * FROM CTHonorarios WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo like '%".$PeriodoX."' ORDER BY fecha";
	}
	// echo $SQL;

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$rsocial="";
		$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$rsocial=$registro1["razonsocial"];
		}

		$nomcuenta="";
		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$nomcuenta=$registro1["detalle"];
		}


		if ($registro["tdocumento"]=="R") {
			$tdocum="Recibido";
		}else{
			$tdocum="Terceros";
		}

		$simbol="";
		if ($registro["movimiento"]!="") {
			$simbol="X";
		}


		$calbruto=$registro["bruto"];
		$calrete=$registro["retencion"];
		$calliqui=$registro["liquido"];
		$calreteCal=round(($registro["bruto"]*$Val_Ret)/100);
		$calreteCal3=round(($registro["bruto"]*3)/100);
  
		$calrete3=$calrete-$calreteCal;
		$calrete=$calreteCal;
  
		$color='';
		if($calrete3<0){
		  $calrete3=0;
		}
  
		if($registro["retencion"]==0){
		  $calrete=0;
		}
  






		$Str=$Str.'
		
	
			<tr>
				<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
				<td>'.$registro["rut"].'</td>
				<td>'.utf8_encode($rsocial).'</td>
				<td>'.$registro["cuenta"]." - ".strtoupper($nomcuenta).'</td>
				<td align="center">'.$simbol.'</td>
				<td>'.$registro["periodo"].'</td>
				<td align="center">'.$registro["numero"].'</td>
				<td align="center">'.$tdocum.'</td>
				<td align="right">$'.number_format(($calbruto), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">$'.number_format(($calrete), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">$'.number_format(($calrete3), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">$'.number_format(($calliqui), $NDECI, $DDECI, $DMILE).'</td>	
			</tr>
		';

		$tbruto=$tbruto+($calbruto);
		$tretencion=$tretencion+($calrete);
		$tretencion3=$tretencion3+($calrete3);
		$tliquido=$tliquido+($calliqui);

	}

	$mysqli->close();

	$Str=$Str.'
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td align="right"><strong>Totales</strong></td>
			<td align="right"><strong>$'.number_format($tbruto, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>$'.number_format($tretencion3, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>$'.number_format($tliquido, $NDECI, $DDECI, $DMILE).'</strong></td>
		</tr>
	';

	$Str=$Str.'
		</table>
		<br><br>
	';

	if ($_SERVER["REQUEST_URI"]=="/HonorariosReport/ReportPDF.php") {
		$HTML=$Str;
	}else{
		if ($_SERVER["REQUEST_URI"]=="/RLDiario/ReportXLS.php") {
			echo utf8_decode($Str);
		}else{		
			echo $Str;
		}
	}
?>