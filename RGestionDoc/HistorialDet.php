<?php

	// header('Content-Type: text/html; charset=iso-8859-1');
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$frm=$_POST['frm'];

	if(isset($_POST['PorcEli']) && $_POST['PorcEli']=="S"){
		foreach($_POST['check_list'] as $selected) {

			$IdElimina = descript($selected);

			$ERut="";
			$ENumero="";
			$EIdTipo="";
			$EKeyas="";

			if ($frm=="H") {

				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
				$SQL="SELECT * FROM CTHonorarios WHERE id='$IdElimina' AND rutempresa='$RutEmpresa' AND movimiento<>''";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$ERut=$registro['rut'];
					$ENumero=$registro['numero'];
					$EIdTipo=0;
					$EKeyas=$registro['movimiento'];

					$mysqli->query("UPDATE CTHonorarios SET movimiento='', origen='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND movimiento='$EKeyas'");
					$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='$EKeyas' AND rutempresa='$RutEmpresa'");

					$SQL="SELECT * FROM CTControRegDocPago WHERE rutempresa='$RutEmpresa' AND rut='$ERut' AND ndoc='$ENumero' AND id_tipodocumento='$EIdTipo'";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$EKeyasPago=$registro['keyas'];
					}

					$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='$EKeyasPago' AND rutempresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='$EKeyasPago' AND rutempresa='$RutEmpresa'");
					$d=date("Y-m-d");
					$EKeyasH=$EKeyas."H";
					$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','$EKeyasH','".date("H:i:s")."','$NomCont');");
				}
			}

			if ($frm=="C" || $frm=="V") {
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
				$SQL="SELECT * FROM CTRegDocumentos WHERE id='$IdElimina' AND rutempresa='$RutEmpresa' AND lote<>'' AND keyas<>''";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$ERut=$registro['rut'];
					$ENumero=$registro['numero'];
					$EIdTipo=$registro['id_tipodocumento'];
					$EKeyas=$registro['keyas'];

					$d=date("Y-m-d");
					$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','$EKeyas','".date("H:i:s")."','$NomCont');");

					$mysqli->query("UPDATE CTRegDocumentos SET lote='', keyas='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND keyas='$EKeyas'");
					$mysqli->query("DELETE FROM CTFondo WHERE keyas='$EKeyas' AND rutempresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='$EKeyas' AND rutempresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='$EKeyas' AND rutempresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTAnticipos WHERE KeyAs='$EKeyas' AND RutEmpresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTAnticipos WHERE KeyasDestino='$EKeyas' AND RutEmpresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTBoletasDTE WHERE keyas='$EKeyas' AND RutEmpresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTAsientoApertura WHERE KeyAs='$EKeyas' AND RutEmpresa='$RutEmpresa'");


					$SQL="SELECT * FROM CTControRegDocPago WHERE rutempresa='$RutEmpresa' AND rut='$ERut' AND ndoc='$ENumero' AND id_tipodocumento='$EIdTipo'";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$EKeyasPago=$registro['keyas'];
					}

					$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='$EKeyasPago' AND rutempresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='$EKeyasPago' AND rutempresa='$RutEmpresa'");
					$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='$EKeyasPago' AND rutempresa='$RutEmpresa'");

				}
			}
		}
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

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}
	$mysqli->close();

	if (isset($_POST['messelect']) && isset($_POST['anoselect'])) {
		if ($_POST['messelect']<=9) {
			$PeriodoX="0".$_POST['messelect']."-".$_POST['anoselect'];
		}else{
			$PeriodoX=$_POST['messelect']."-".$_POST['anoselect'];     
		}
	}else{
		$PeriodoX="";
	}

	$frm=$_POST['frm'];
	$Str="";

	if ($frm=="C") {
		$Tut='
		<table width="100%">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>REGISTROS DE COMPRAS '.$PeriodoX.'</strong></td>
			</tr>
		</table>
		';
	}
	if ($frm=="V") {
		$Tut='
		<table width="100%">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>REGISTROS DE VENTAS '.$PeriodoX.'</strong></td>
			</tr>
		</table>
		';
	}

	if ($frm=="H") {
		$Tut='
		<table width="100%">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>REGISTROS DE HONORARIOS '.$PeriodoX.'</strong></td>
			</tr>
		</table>
		';
	}



	$Str=$Str.$Tut;

$Str=$Str.'
	<br>

	<div class="col-md-6">
		<input class="form-control" id="myInput" type="text" placeholder="Buscar...">
	</div>

	<div class="col-md-2">
		<button type="button" class="btn btn-block btn-cancelar" onclick="Liberar()">Liberar</button>
	</div>

	<div class="col-md-2">
		<button type="button" class="btn btn-block btn-mastecno" onclick="Volver()">Volver</button>
		<input type="hidden" name="filtro" id="filtro" value="T">
		<input type="hidden" name="PorcEli" id="PorcEli">
	</div>
	<div class="clearfix">
	</div>
	<br>

	<table class="table table-hover table-condensed" width="100%" style="font-size: 12px;">
		<thead>
			<tr style="background-color: #d9d9d9;">
				<th width="1%"></th>
				<th width="1%"></th>
				<th>Rut</th>
				<th>Razon Social</th>
				<th style="text-align: center;">Documento</th>
				<th style="text-align: center;">N&deg; Doc</th>
				<th style="text-align: center;">Fecha</th>
				<th style="text-align: right;">Total</th>
                <th width="1%"></th>
			</tr>
		</thead>
		<tbody id="myTable">
';

	
	// if ($_SESSION["PLAN"]=="S"){
	// 	$ConPlaCta="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND ";
	// }else{
	// 	$ConPlaCta="SELECT * FROM CTCuentas WHERE ";
	// }

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


	/// por si elimine el documentos, sque se elimine la distribucion anterior
	$resultados = $mysqli->query("SELECT * FROM CTRegDocumentosDiv WHERE 1=1");
	while ($registro = $resultados->fetch_assoc()) {
		$resultados1 = $mysqli->query("SELECT * FROM CTRegDocumentos WHERE id='".$registro["Id_Doc"]."'");
		$row_cnt = $resultados1->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("DELETE FROM CTRegDocumentosDiv WHERE Id='".$registro["Id"]."'");
		}
	}

	if($frm=="H"){
		$rsocial="";
		$SQL="SELECT * FROM CTHonorarios WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo like '%$PeriodoX' AND movimiento<>'' ORDER BY fecha";
		$cont=1;
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {

			$cl=1;
			
			$Pref=randomText(35);
			$Suf=randomText(8);

			if ($rrut!=$registro["rut"]) {
				$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
				$resultados1 = $mysqli->query($SQL1);
				$row_cnt = $resultados1->num_rows;
				if ($row_cnt==0) {
					$rsocial="";
				}else{
					$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
					$resultados1 = $mysqli->query($SQL1);
					while ($registro1 = $resultados1->fetch_assoc()) {
						$rsocial=$registro1["razonsocial"];
					}				
				}
				$rrut=$registro["rut"];
			}

			$operador=1;
			$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$nomdoc=$registro1["sigla"];
				if($registro1["operador"]=="R"){
					$operador=-1;
				}
			}

			$ColLin="";
			if ($operador==-1) {
				$ColLin="#94f1f9;";
			}

			$MensMult="";
			$resultados1 = $mysqli->query("SELECT COUNT(id) as CanReg FROM CTHonorarios WHERE rutempresa='$RutEmpresa' and movimiento='".$registro["movimiento"]."';");
			while ($registro1 = $resultados1->fetch_assoc()) {
				if($registro1['CanReg']>1){
					$MensMult='<l style="font-size: 8px;"> (<strong> MULTIPLES DOCUMENTOS</strong>)</l>';
				}
			}

			$Str=$Str.'
				<tr style="background-color:'.$ColLin.'">
					<td>'.$cont.'</td>
					<td>
						<input type="checkbox" name="check_list[]" id="Z'.$registro["id"].'" value="'.$Pref.$registro["id"].$Suf.'">
					</td>
					<td>'.$registro["rut"].'</td>
					<td>'.substr(strtoupper($rsocial),0,30).$MensMult.'</td>
					<td align="center">'.strtoupper($nomdoc).$DRef.'</td>
					<td align="center">'.$registro["numero"].'</td>
					<td align="center">'.date('d-m-Y',strtotime($registro["fecha"])).'</td>	
					<td align="right">'.number_format(($registro["bruto"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
					<td>
						<button type="button" class="btn btn-xs btn-cancelar" onclick="LiberaDoc(\'Z'.$registro["id"].'\')">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</td>
					</tr>
			';            

			$ttotal=$ttotal+($registro["bruto"]*$operador);
			$cont++;
		}
	}else{
		$rsocial="";
		$SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo like '%$PeriodoX' AND lote<>'' AND keyas<>'' ORDER BY fecha";
		$cont=1;
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {

			$cl=1;
			
			$Pref=randomText(35);
			$Suf=randomText(8);

			if ($rrut!=$registro["rut"]) {
				$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
				$resultados1 = $mysqli->query($SQL1);
				$row_cnt = $resultados1->num_rows;
				if ($row_cnt==0) {
					$rsocial="";
				}else{
					$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
					$resultados1 = $mysqli->query($SQL1);
					while ($registro1 = $resultados1->fetch_assoc()) {
						$rsocial=$registro1["razonsocial"];
					}				
				}
				$rrut=$registro["rut"];
			}

			$operador=1;
			$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$nomdoc=$registro1["sigla"];
				if($registro1["operador"]=="R"){
					$operador=-1;
				}
			}

			$ColLin="";
			if ($operador==-1) {
				$ColLin="#94f1f9;";
			}

			$MensMult="";
			$resultados1 = $mysqli->query("SELECT COUNT(id) as CanReg FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' and keyas='".$registro["keyas"]."';");
			while ($registro1 = $resultados1->fetch_assoc()) {
				if($registro1['CanReg']>1){
					$MensMult='<l style="font-size: 8px;"> (<strong> MULTIPLES DOCUMENTOS</strong>)</l>';
				}
			}

			$Str=$Str.'
				<tr style="background-color:'.$ColLin.'">
					<td>'.$cont.'</td>
					<td>
						<input type="checkbox" name="check_list[]" id="Z'.$registro["id"].'" value="'.$Pref.$registro["id"].$Suf.'">
					</td>
					<td>'.$registro["rut"].'</td>
					<td>'.substr(strtoupper($rsocial),0,30).$MensMult.'</td>
					<td align="center">'.strtoupper($nomdoc).$DRef.'</td>
					<td align="center">'.$registro["numero"].'</td>
					<td align="center">'.date('d-m-Y',strtotime($registro["fecha"])).'</td>	
					<td align="right">'.number_format(($registro["total"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
					<td>
						<button type="button" class="btn btn-xs btn-cancelar" onclick="LiberaDoc(\'Z'.$registro["id"].'\')">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</td>
					</tr>
			';            

			$ttotal=$ttotal+($registro["total"]*$operador);
			$cont++;
		}
	}




	if ($cl==0) {
		$Str=$Str.'
			<tr>
				<td align="center" colspan="13"><strong>No hay documentos para Visualizar</strong></td>
			</tr>
		';
	}

	$mysqli->close();
	if ($cl!=0) {
		$Str=$Str.'
			<tr style="background-color: #d9d9d9;">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align="right"><strong>Totales</strong></td>
				<td align="right"><strong>'.number_format($ttotal, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td></td>
			</tr>
		';
	}
$Str=$Str.'
		</tbody>
	</table>

		<script>
			$(document).ready(function(){
			$("#myInput").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#myTable tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
			});
			});
		</script>

';
// echo $Str;
// exit;

	// if ($_SERVER["REQUEST_URI"]=="/Nexus/RLComprasVentas/frmLibComVenPDF.php") {
	// 	$HTML=$Str;
	// }else{
		echo $Str;
	// }
?>