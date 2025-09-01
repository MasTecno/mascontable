<?php
	// echo "rrrr";
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

	// if (isset($_POST['KeyMov']) && $_POST['KeyMov']!="" && $_POST['SwMov']=="A") {
	// 	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	// 	$St=$_POST['Exento']+$_POST['Neto']+$_POST['IVA']+$_POST['Reten'];

	// 	$mysqli->query("UPDATE CTRegDocumentos SET exento='".$_POST['Exento']."', neto='".$_POST['Neto']."', iva='".$_POST['IVA']."', retencion='".$_POST['Reten']."', total='".$St."' WHERE id='".descriptSV($_POST['KeyMov'])."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'");

	// 	$mysqli->close();
	// }

	if (isset($_POST['KeyMov']) && $_POST['KeyMov']!="" && $_POST['SwMov']=="B") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$xfrm=$_POST['frm'];

		$xrut=$_POST['CtaRut'];
		$xcuenta=$_POST['SelCta'];
		$xccosto=$_POST['SelCC'];

		if (isset($_POST['SwCta'])) {
			$Mov="H";

			$SQL="SELECT * FROM CTCliProCuenta WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'";
			$resultado = $mysqli->query($SQL);
			$row_cnt = $resultado->num_rows;
			if ($row_cnt>0) {
				$mysqli->query("UPDATE CTCliProCuenta SET cuenta='$xcuenta' WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'");
			}else{
				$mysqli->query("INSERT INTO CTCliProCuenta VALUE('','$RutEmpresa','$xrut','$xcuenta','$Mov','A')");
			}
			$mysqli->query("UPDATE CTHonorarios SET cuenta='$xcuenta' WHERE rut='$xrut' AND rutempresa='$RutEmpresa' AND movimiento=''");
		}

		$mysqli->query("UPDATE CTHonorarios SET cuenta='$xcuenta' WHERE id='".descriptSV($_POST['KeyMov'])."'");

		if (isset($_POST['SwCC'])) {
			$Mov="H";

			$SQL="SELECT * FROM CTCliProCuenta WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'";
			$resultado = $mysqli->query($SQL);
			$row_cnt = $resultado->num_rows;
			if ($row_cnt>0) {
				$mysqli->query("UPDATE CTCliProCuenta SET ccosto='$xccosto' WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'");
			}else{
				$mysqli->query("INSERT INTO CTCliProCuenta VALUE('','$RutEmpresa','$xrut','','$xccosto','$Mov','A')");
			}

			$mysqli->query("UPDATE CTHonorarios SET ccosto='$xccosto' WHERE rut='$xrut' AND rutempresa='$RutEmpresa' AND movimiento=''");
		}

		$mysqli->query("UPDATE CTHonorarios SET ccosto='$xccosto' WHERE id='".descriptSV($_POST['KeyMov'])."'");
		$mysqli->close();
	}

	if ($_POST['SwMov']=="C") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		foreach($_POST['check_list'] as $selected) {
			if ($_POST['SelCtaMas']==0) {
				$mysqli->query("UPDATE CTHonorarios SET ccosto='".$_POST['SelCCMas']."' WHERE rutempresa='$RutEmpresa' AND movimiento='' AND id='".descriptSV($selected)."'");
			}else{
				$mysqli->query("UPDATE CTHonorarios SET ccosto='".$_POST['SelCCMas']."', cuenta='".$_POST['SelCtaMas']."' WHERE rutempresa='$RutEmpresa' AND movimiento='' AND id='".descriptSV($selected)."'");
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
	$Str="";

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
		<button type="button" class="btn btn-block btn-modificar" onclick="Historial()" title="Historial de documento de Procesados" >
			<span class="glyphicon glyphicon-folder-open"></span> Procesados
		</button>
		<input type="hidden" name="filtro" id="filtro" value="H">
	</div>

	<div class="col-md-2">
		<button type="button" class="btn btn-block btn-cancelar" onclick="EliDocu()" title="Eliminar&aacute; todos los documentos que no est&aacute;n procesados">
			<span class="glyphicon glyphicon-remove"></span> Eliminar
		</button>
	</div>
	<div class="clearfix"></div>

	<br>

	<table class="table table-hover table-condensed" width="100%" style="font-size: 12px;">
		<thead>
			<tr style="background-color: #d9d9d9;">
				<th width="1%"></th>
				<th>Rut</th>
				<th>Razon Social</th>
				<th>Cuenta</th>
				<th>C. Costo</th>
				<th>Documento</th>
				<th style="text-align: right;">N&deg; Doc</th>
				<th>Fecha</th>
				<th style="text-align: right;">Bruto</th>
				<th style="text-align: right;">Retención</th>
				<th style="text-align: right;">3% Prestamo</th>
				<th style="text-align: right;">Liquido</th>
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
	// $resultados = $mysqli->query("SELECT * FROM CTRegDocumentosDiv WHERE 1=1");
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$resultados1 = $mysqli->query("SELECT * FROM CTRegDocumentos WHERE id='".$registro["Id_Doc"]."'");
	// 	$row_cnt = $resultados1->num_rows;
	// 	if ($row_cnt==0) {
	// 		$mysqli->query("DELETE FROM CTRegDocumentosDiv WHERE Id='".$registro["Id"]."'");
	// 	}
	// }

    $dano = substr($PeriodoX,3,4);


    $SQL="SELECT * FROM CTParametros";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
		if ($registro['tipo']=='RETE_HONO') {
			$Val_Ret=$registro['valor'];
		}
    }
  
    if ($dano=="2020") {
		$Val_Ret=10.75;
    }
  
    if ($dano=="2021") {
		$Val_Ret=11.5;
    }
  
    if ($dano=="2022") {
		$Val_Ret=12.25;
    }
  
    if ($dano=="2023") {
		$Val_Ret=13;
    }
  
    if ($dano=="2024") {
		$Val_Ret=13.75;
    }
  
    if ($dano=="2025") {
		$Val_Ret=14.5;
    }
  
    if ($dano=="2026") {
		$Val_Ret=15.25;
    }
  
    if ($dano=="2027") {
		$Val_Ret=16;
    }
  
    if ($dano=="2028") {
		$Val_Ret=17;
    }



	$rsocial="";
	$SQL="SELECT * FROM CTHonorarios WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo like '%$PeriodoX' AND movimiento='' ORDER BY fecha";
	$cont=1;
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

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
		if ($registro["bruto"]<>($registro["retencion"]+$registro["liquido"])) {
			$ColLin="#ff9393;";
		}
		if ($operador==-1) {
			$ColLin="#94f1f9;";
			// $DRef='
			// <button type="button" class="btn btn-xs btn-default" onclick="NCRefe(\''.$Pref.$registro["id"].$Suf.'\')" onclick="" data-toggle="modal" data-target="#ModCta">
			// 	 Ref:'.$registro["FolioDocRef"].'
			// </button>
			// ';
		}

		$Nccosto="";
		if ($registro["ccosto"]>0){
			$SqlCta1="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='".$registro["ccosto"]."'";
			$resultados1 = $mysqli->query($SqlCta1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$Nccosto=$registro1["nombre"];
			}
		}

		$bot='random';
		$col='default';
		$SQL1="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".$registro["id"]."'";
		$resultados1 = $mysqli->query($SQL1);
		$row_cnt = $resultados1->num_rows;
		if ($row_cnt>0) {			
			$bot='link';
			$col='warning';
			
			// 	<button type="button" class="btn btn-xs btn-default" style="background-color: #d9d9d9;" onclick="ProceDivInfo(\''.$Pref.$registro["id"].$Suf.'\')" data-toggle="modal" data-target="#ModDiv">
			// 		<span class="glyphicon glyphicon-zoom-in"></span>
			// 	</button>
			// 	';
		}


		$Str=$Str.'
			<tr style="background-color:'.$ColLin.'">
				<td>
					<input type="checkbox" name="check_list[]" value="'.$Pref.$registro["id"].$Suf.'">
				</td>
				<td>'.$cont." - ".$registro["rut"].'</td>
				<td>'.substr(strtoupper($rsocial),0,30).'</td>
				<td>
					<button type="button" class="btn btn-xs btn-default" onclick="AsigCta(\''.$Pref.$registro["id"].$Suf.'\')" onclick="" data-toggle="modal" data-target="#ModCta">
						<span class="glyphicon glyphicon-th-list"></span>
					</button> '.$registro["cuenta"]." - ".(strtoupper($nomcuenta)).'
				</td>
				<td>'.$Nccosto.'</td>
				<td>HONORARIO</td>
				<td align="right">'.$registro["numero"].'</td>
				<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
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
			<td align="right"><strong>Totales</strong></td>
			<td align="right"><strong>$'.number_format($tbruto, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>$'.number_format($tretencion3, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>$'.number_format($tliquido, $NDECI, $DDECI, $DMILE).'</strong></td>
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