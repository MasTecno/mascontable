<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$frm=$_POST['tdocumentos'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	echo '
		<br>
		<div class="col-md-12 centrar-item">
			<button type="button" onclick="seleccionar_todo()" class="btn btn-xs btn-Verde">Marcar Todos</button>
			<button type="button" onclick="deseleccionar_todo()" class="btn btn-Naranjo btn-xs">DesMarcar</button><br>
			<samp style="font-size: 11px;">* Grupo maximo de pago 500 Documentos</samp>
		</div>
		<br>

		<table class="table table-hover">

			<thead>
				<tr>
					<th width="1%"></th>
					<th width="1%"></th>
					<th width="10%">Fecha</th>
					<th width="" style="text-align: center;">Periodo</th>
					<th width="5%">N&deg; Doc</th>
					<th width="10%">Rut</th>
					<th>Razon Social</th>
					<th>T. Documento</th>
					<th width="5%" style="text-align: right;"">A/C</th>
					<th width="5%" style="text-align: right;"">Total</th>
					<th width="5%" style="text-align: right;"">Dif</th>
				</tr>
			</thead>
		<tbody id="myTable">  	
	';

	if ($frm=="C" || $frm=="V") {

		if ($frm=="V") {
			$CodCliPro="C";
		}else{
			$CodCliPro="P";
		}

		$SQL="SELECT CTRegDocumentos.id, CTRegDocumentos.periodo, CTRegDocumentos.rutempresa, CTRegDocumentos.rut, CTCliPro.razonsocial, CTRegDocumentos.id_tipodocumento, CTRegDocumentos.numero, CTRegDocumentos.fecha, CTRegDocumentos.total, CTRegDocumentos.tipo, CTRegDocumentos.estado, CTRegDocumentos.lote, CTRegDocumentos.keyas FROM CTRegDocumentos LEFT JOIN CTCliPro ON CTRegDocumentos.rut = CTCliPro.rut WHERE 1=1";

		if ($_POST['cadena']!="") {
			$SQL= $SQL." AND (CTRegDocumentos.numero like '%".$_POST['cadena']."%' OR CTRegDocumentos.rut like '%".$_POST['cadena']."%' OR CTCliPro.razonsocial like '%".$_POST['cadena']."%')";
		}

		$SQL= $SQL." AND CTRegDocumentos.tipo='$frm' AND CTRegDocumentos.estado='A' AND CTRegDocumentos.rutempresa='$RutEmpresa' AND  CTRegDocumentos.keyas<>'' AND CTCliPro.tipo='$CodCliPro'";

		if (isset($_POST['LSelPeriodoDoc']) && $_POST['LSelPeriodoDoc']!="" && $_POST['LSelPeriodoDoc']!="T") {
			$SQL= $SQL." AND CTRegDocumentos.periodo='".$_POST['LSelPeriodoDoc']."'";
		}

		$SQL= $SQL." GROUP BY id,periodo,rutempresa,rut,razonsocial,id_tipodocumento,numero,fecha,total,tipo,estado,lote,keyas ORDER BY CTRegDocumentos.fecha";
	}

	if ($frm=="H") {
		$CodCliPro="P";
		$SQL ="SELECT CTHonorarios.id, CTHonorarios.fecha, CTHonorarios.rutempresa, CTHonorarios.numero, CTHonorarios.periodo, CTHonorarios.rut, CTCliPro.razonsocial, CTHonorarios.liquido, CTHonorarios.estado, CTHonorarios.movimiento FROM CTHonorarios INNER JOIN CTCliPro ON CTHonorarios.rut = CTCliPro.rut WHERE 1=1";

		if ($_POST['cadena']!="") {
			$SQL= $SQL." AND (CTHonorarios.numero like '%".$_POST['cadena']."%' OR CTHonorarios.rut like '%".$_POST['cadena']."%' OR CTCliPro.razonsocial like '%".$_POST['cadena']."%')";
		}

		$SQL= $SQL." AND CTHonorarios.estado='A' AND CTHonorarios.rutempresa='$RutEmpresa' AND  CTHonorarios.movimiento<>'' AND CTCliPro.tipo='$CodCliPro'";

		if (isset($_POST['LSelPeriodoDoc']) && $_POST['LSelPeriodoDoc']!="" && $_POST['LSelPeriodoDoc']!="T") {
			$SQL= $SQL." AND CTHonorarios.periodo='".$_POST['LSelPeriodoDoc']."'";
		}

		$SQL= $SQL." GROUP BY CTHonorarios.id, CTHonorarios.fecha, CTHonorarios.rutempresa, CTHonorarios.numero, CTHonorarios.rut, CTCliPro.razonsocial, CTHonorarios.liquido, CTHonorarios.estado, CTHonorarios.movimiento, CTHonorarios.periodo, CTHonorarios.tdocumento ORDER BY CTHonorarios.fecha";

	}

	$con=1;
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		// echo $registro["keyas"];
		$NC=substr($registro["keyas"],0,2);

		$rsocial="";
		$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$rsocial=$registro1["razonsocial"];
		}

		if ($frm=="H") {
			$nomdoc="HONORARIOS";
			$operador=1;
			$Totalreg=$registro["liquido"];
		}else{
			$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$nomdoc=$registro1["nombre"];
				$operador=$registro1["operador"];
			}
			if($operador=="R"){
				$operador=-1;
			}else{
				$operador=1;
			}
			$Totalreg=$registro["total"];
		}

		$xsuma=0;
 
		$SQL1="SELECT sum(monto) as xsuma FROM CTControRegDocPago WHERE rutempresa='$RutEmpresa' AND id_tipodocumento='".$registro["id_tipodocumento"]."' AND rut='".$registro["rut"]."' AND ndoc='".$registro["numero"]."'";
		$SQL1=$SQL1." AND tipo='".$frm."'";

		$xsuma=0;
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$xsuma=$registro1["xsuma"];
		}

		if(is_null($xsuma)){
			$xsuma=0;
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

		if ($xsuma<$Totalreg && $NC!="NC") {
			echo '
				<tr>
				<td>'.$con++.'</td>
				<td><input type="checkbox" name="check_list[]" value="'.$registro["id"].'" onclick="Calculo()"></td>
				<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
				<td align="center">'.$registro["periodo"].'</td>
				<td align="right">'.$registro["numero"].'</td>
				<td>'.$registro["rut"].'</td>
				<td>'.($rsocial).'</td>
				<td>'.($nomdoc).'</td>
				<td align="right">$'.number_format(($xsuma*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">$'.number_format(($Totalreg*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">$'.number_format((($Totalreg*$operador)-($xsuma*$operador)), $NDECI, $DDECI, $DMILE).'</td>
				<td align="center" ></td>
				</tr>
			';
		}
	}
	$mysqli->close();