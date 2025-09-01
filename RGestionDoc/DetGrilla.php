<?php
	// echo "xxx";

	// header('Content-Type: text/html; charset=iso-8859-1');
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	// $PDFStr=0;
	// if ($_SERVER["REQUEST_URI"]=="/Nexus/RLComprasVentas/frmLibComVenPDF.php") {
	// 	$PDFStr=1;
	// }

	$NomCont=$_SESSION['NOMBRE'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if (isset($_POST['KeyMov']) && $_POST['KeyMov']!="" && $_POST['SwMov']=="A") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$St=$_POST['Exento']+$_POST['Neto']+$_POST['IVA']+$_POST['Reten'];

		$mysqli->query("UPDATE CTRegDocumentos SET exento='".$_POST['Exento']."', neto='".$_POST['Neto']."', iva='".$_POST['IVA']."', retencion='".$_POST['Reten']."', total='".$St."' WHERE id='".descriptSV($_POST['KeyMov'])."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'");

		$mysqli->close();
	}

	if (isset($_POST['KeyMov']) && $_POST['KeyMov']!="" && $_POST['SwMov']=="B") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$xfrm=$_POST['frm'];

		$xrut=$_POST['CtaRut'];
		$xcuenta=$_POST['SelCta'];
		$xccosto=$_POST['SelCC'];

		if (isset($_POST['SwCta'])) {
			if($xfrm=="V"){
				$Mov="C";
			}else{
				$Mov="P";
			}

			$SQL="SELECT * FROM CTCliProCuenta WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'";
			$resultado = $mysqli->query($SQL);
			$row_cnt = $resultado->num_rows;
			if ($row_cnt>0) {
				$mysqli->query("UPDATE CTCliProCuenta SET cuenta='$xcuenta' WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'");
			}else{
				$mysqli->query("INSERT INTO CTCliProCuenta VALUE('','$RutEmpresa','$xrut','$xcuenta','','$Mov','A')");
			}



			$mysqli->query("UPDATE CTRegDocumentos SET cuenta='$xcuenta' WHERE rut='$xrut' AND rutempresa='$RutEmpresa' AND lote='' AND tipo='".$xfrm."'");
		}

		$mysqli->query("UPDATE CTRegDocumentos SET cuenta='$xcuenta' WHERE id='".descriptSV($_POST['KeyMov'])."' AND tipo='".$xfrm."'");



		if (isset($_POST['SwCC'])) {
			if($xfrm=="V"){
				$Mov="C";
			}else{
				$Mov="P";
			}

			$SQL="SELECT * FROM CTCliProCuenta WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'";
			$resultado = $mysqli->query($SQL);
			$row_cnt = $resultado->num_rows;
			if ($row_cnt>0) {
				$mysqli->query("UPDATE CTCliProCuenta SET ccosto='$xccosto' WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'");
			}else{
				$mysqli->query("INSERT INTO CTCliProCuenta VALUE('','$RutEmpresa','$xrut','','$xccosto','$Mov','A')");
			}

			$mysqli->query("UPDATE CTRegDocumentos SET ccosto='$xccosto' WHERE rut='$xrut' AND rutempresa='$RutEmpresa' AND lote='' AND tipo='".$xfrm."'");
		}

		$mysqli->query("UPDATE CTRegDocumentos SET ccosto='$xccosto' WHERE id='".descriptSV($_POST['KeyMov'])."' AND tipo='".$xfrm."'");

		$mysqli->close();
	}

	if ($_POST['SwMov']=="C") {

		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		foreach($_POST['check_list'] as $selected) {
			if ($_POST['SelCtaMas']==0) {
				$mysqli->query("UPDATE CTRegDocumentos SET ccosto='".$_POST['SelCCMas']."' WHERE rutempresa='$RutEmpresa' AND lote='' AND id='".descriptSV($selected)."'");
			}else{
				$mysqli->query("UPDATE CTRegDocumentos SET ccosto='".$_POST['SelCCMas']."', cuenta='".$_POST['SelCtaMas']."' WHERE rutempresa='$RutEmpresa' AND lote='' AND id='".descriptSV($selected)."'");
			}
		}
		$mysqli->close();
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


	// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// $rsocial="";

	// echo $SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo like '%$PeriodoX' AND lote='' ORDER BY periodo, fecha";
	

	// if (isset($_POST['anual']) && $_POST['anual']==1) {
	// 	$PeriodoX=substr($_SESSION['PERIODOPC'],3,4);
	// }
	$frm=$_POST['frm'];
	
    if($_POST['EliRegi']!="" && $_POST['EliRegi']=="S"){
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("DELETE FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND lote='' AND keyas=''");
        $mysqli->close();
    }


    if($_POST['EliRegi']!="" && $_POST['EliRegi']=="I"){

		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		foreach($_POST['check_list'] as $selected) {
			// descript($selected)
			$IdElimina = descript($selected);
			// $mysqli->query("UPDATE CTRegDocumentos SET periodo='$PerMov' WHERE id='$IdElimina'");
			$mysqli->query("DELETE FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND id='$IdElimina' AND lote='' AND keyas=''");
		}

        
        // $mysqli->query("DELETE FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND lote='' AND keyas=''");
        $mysqli->close();
    }



	if (isset($_POST['IdMovDoc']) && $_POST['IdMovDoc']!="") {
		$messelect=$_POST['messelectM'];
		$anoselect=$_POST['anoselectM'];

		if ($messelect<=9) {
			$messelect="0".$messelect;
		}
		$PerMov=$messelect."-".$anoselect;

		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		foreach($_POST['check_list'] as $selected) {
			$IdElimina = descript($selected);
			$mysqli->query("UPDATE CTRegDocumentos SET periodo='$PerMov' WHERE id='$IdElimina'");
		}
		$mysqli->close();
	}




	$Str="";
	if ($frm=="C") {
		$Tut='
		<table width="100%">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>REGISTROS DE COMPRAS '.$PeriodoX.'</strong></td>
			</tr>
		</table>
		';
	}else{
		$Tut='
		<table width="100%">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>REGISTROS DE VENTAS '.$PeriodoX.'</strong></td>
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
		<button type="button" class="btn btn-block btn-modificar" onclick="Historial()" title="Historial de documento de Procesados" >
			<span class="glyphicon glyphicon-folder-open"></span> Procesados
		</button>
		<input type="hidden" name="filtro" id="filtro" value="T">
	</div>

	<div class="col-md-2">
		<button type="button" class="btn btn-block btn-modificar" onclick="Mover()" id="MovDoc" name="MovDoc" title="Mover documento de Periodo" data-toggle="modal" data-target="#MovDocumento">
			<span class="glyphicon glyphicon-time"></span> Mover
		</button>
	</div>

	<div class="col-md-2">
		<button type="button" class="btn btn-block btn-cancelar" onclick="EliDocu()" title="Eliminar&aacute; todos los documentos que no est&aacute;n procesados">
			<span class="glyphicon glyphicon-remove"></span> Eliminar
		</button>
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
				<th>Cuenta</th>
				<th>C. Costo</th>
				<th>Documento</th>
				<th style="text-align: right;">N&deg; Doc</th>
				<th>Fecha</th>
				<th style="text-align: right;">Exento</th>
				<th style="text-align: right;">Neto</th>
				<th width="1%"></th>
				<th style="text-align: right;">Iva</th>
				<th style="text-align: right;">Otro Imp</th>
				<th width="1%"></th>
				<th style="text-align: right;">Total</th>
				<th width="1%"></th>
			</tr>
		</thead>
		<tbody id="myTable">
';

	
	if ($_SESSION["PLAN"]=="S"){
		$ConPlaCta="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND ";
	}else{
		$ConPlaCta="SELECT * FROM CTCuentas WHERE ";
	}

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

	$resultados = $mysqli->query("SELECT * FROM CTRegDocumentosDivRete WHERE 1=1");
	while ($registro = $resultados->fetch_assoc()) {
		$resultados1 = $mysqli->query("SELECT * FROM CTRegDocumentos WHERE id='".$registro["Id_Doc"]."'");
		$row_cnt = $resultados1->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("DELETE FROM CTRegDocumentosDivRete WHERE Id='".$registro["Id"]."'");
		}
	}

	$rsocial="";
	$SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo like '%$PeriodoX' AND lote='' ORDER BY fecha";
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

		$nomcuenta="";
		$SqlCta=$ConPlaCta."numero='".$registro["cuenta"]."'";
		$resultados1 = $mysqli->query($SqlCta);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$nomcuenta=$registro1["detalle"];
		}

		$ColLin="";
		$DRef="";
		if ($registro["total"]<>($registro["exento"]+$registro["neto"]+$registro["iva"]+$registro["retencion"])) {
			$ColLin="#ff9393;";
		}
		if ($operador==-1) {
			$ColLin="#94f1f9;";
		}

		$Nccosto="";
		if ($registro["ccosto"]>0){
			$SqlCta1="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='".$registro["ccosto"]."'";
			$resultados1 = $mysqli->query($SqlCta1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$Nccosto=$registro1["nombre"];
			}
		}

		$Anticipo="NO";
		$SqlCta1="SELECT * FROM CTAnticipos WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Rut='".$registro["rut"]."'";
		$resultados1 = $mysqli->query($SqlCta1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$Anticipo="SI";
		}

		if ($Anticipo=="SI") {
			$ColLin="#f8f0cd";
		}

		$bot='random';
		$col='default';
		$SQL1="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".$registro["id"]."'";
		$resultados1 = $mysqli->query($SQL1);
		$row_cnt = $resultados1->num_rows;
		if ($row_cnt>0) {			
			$bot='link';
			$col='warning';
		}


		$botR='random';
		$colR='default';
		$SQL1="SELECT * FROM CTRegDocumentosDivRete WHERE Id_Doc='".$registro["id"]."'";
		$resultados1 = $mysqli->query($SQL1);
		$row_cnt = $resultados1->num_rows;
		if ($row_cnt>0) {			
			$botR='link';
			$colR='warning';
		}

		$VisibleRet="";
		if($registro["retencion"]<=0){
			$VisibleRet="visibility: hidden;";
		}

		$Str=$Str.'
			<tr style="background-color:'.$ColLin.'">
				<td>'.$cont.'</td>
				<td>
					<input type="checkbox" name="check_list[]" value="'.$Pref.$registro["id"].$Suf.'">
				</td>
				<td>'.$registro["rut"].'</td>
				<td>'.substr(strtoupper($rsocial),0,30).'</td>
				<td>
					<button type="button" class="btn btn-xs btn-default" onclick="AsigCta(\''.$Pref.$registro["id"].$Suf.'\')" onclick="" data-toggle="modal" data-target="#ModCta">
						<span class="glyphicon glyphicon-th-list"></span>
					</button> '.$registro["cuenta"]." - ".(strtoupper($nomcuenta)).'
				</td>
				<td>'.$Nccosto.'</td>
				<td>'.strtoupper($nomdoc).$DRef.'</td>
				<td align="right">'.$registro["numero"].'</td>
				<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
				<td align="right">'.number_format(($registro["exento"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format(($registro["neto"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td>
					<button type="button" class="btn btn-xs btn-'.$col.'" onclick="ProceDiv(\''.$Pref.$registro["id"].$Suf.'\')" data-toggle="modal" data-target="#ModDiv">
						<span class="glyphicon glyphicon-'.$bot.'"></span>
					</button>
				</td>
				<td align="right">'.number_format(($registro["iva"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format(($registro["retencion"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td>
					<button type="button" class="btn btn-xs btn-'.$colR.'" onclick="ProceDivRete(\''.$Pref.$registro["id"].$Suf.'\')" data-toggle="modal" data-target="#ModDivRete" style="'.$VisibleRet.'">
						<span class="glyphicon glyphicon-'.$botR.'"></span>
					</button>
				</td>	
				<td align="right">'.number_format(($registro["total"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td>
					<button type="button" class="btn btn-xs btn-default" onclick="PMonto(\''.$Pref.$registro["id"].$Suf.'\')" data-toggle="modal" data-target="#ModMon">
						<span class="glyphicon glyphicon-usd"></span>
					</button>
				</td>

			</tr>
		';

		$texento=$texento+($registro["exento"]*$operador);
		$tneto=$tneto+($registro["neto"]*$operador);
		$tiva=$tiva+($registro["iva"]*$operador);
		$trete=$trete+($registro["retencion"]*$operador);
		$ttotal=$ttotal+($registro["total"]*$operador);
		$cont++;
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
				<td></td>
				<td></td>
				<td align="right"><strong>Totales</strong></td>
				<td align="right"><strong>'.number_format($texento, $NDECI, $DDECI, $DMILE).'</strong></td>
				<td align="right"><strong>'.number_format($tneto, $NDECI, $DDECI, $DMILE).'</strong></td>
				<td></td>			
				<td align="right"><strong>'.number_format($tiva, $NDECI, $DDECI, $DMILE).'</strong></td>
				<td align="right"><strong>'.number_format($trete, $NDECI, $DDECI, $DMILE).'</strong></td>
				<td></td>
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