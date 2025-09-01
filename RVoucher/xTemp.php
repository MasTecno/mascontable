<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$Periodo=$_SESSION['PERIODOPC'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$mysqli = xconectar("root", "", "mastecno_server08");

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

	if ($_POST['Codigo']!="") {
		$swDoc="N";
		if ($_POST['TDocumento']!="" && $_POST['NDocumento']!="" && $_POST['RutUno']!="") {
			$SQL="SELECT * FROM `CTTipoDocumento` WHERE tiposii='".$_POST['TDocumento']."' AND estado='A'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$idsii=$registro["id"];
			}

			$SQL="SELECT * FROM CTControRegDocPago WHERE ndoc='".$_POST['NDocumento']."' AND id_tipodocumento='$idsii' AND rutempresa='$RutEmpresa' AND rut='".$_POST['RutUno']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt>0) {
				$swDoc="S";
				echo "DocExiste*";
			}
		}

		if ($swDoc=="N") {
			$Sql="INSERT INTO CTVoucherT (Id, Codigo, Debe, Haber, CCosto, Keyas, RutEmpresa, Periodo, Usuario,Rut,Tipo,TipoDocumento,Documento) VALUES('','".$_POST['Codigo']."','".str_replace(".","",$_POST['Debe'])."','".str_replace(".","",$_POST['Haber'])."','".$_POST['SelCCosto']."','','$RutEmpresa','$Periodo','".$_SESSION['NOMBRE']."','".$_POST['RutUno']."','".$_POST['SelCliPro']."','".$_POST['TDocumento']."','".$_POST['NDocumento']."')";
		}

		$mysqli->query($Sql);
	}

	if ($_POST['SwElimTemp']!="") {
		$Sql="DELETE FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Usuario='".$_SESSION['NOMBRE']."' AND id='".$_POST['SwElimTemp']."'";
		$mysqli->query($Sql);
	}

	$Sql="DELETE FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Usuario='".$_SESSION['NOMBRE']."' AND Debe='0' AND Haber='0'";
	$mysqli->query($Sql);

	$StrSql="SELECT * FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo' AND Usuario='".$_SESSION['NOMBRE']."' ORDER BY id ASC";
	$Resultado = $mysqli->query($StrSql);
	while ($Registro = $Resultado->fetch_assoc()) {

		$NCodigo="";
		if ($_SESSION["PLAN"]=="S"){
			$Sql="SELECT * FROM CTCuentasEmpresa WHERE numero='".$Registro["Codigo"]."' AND rut_empresa='$RutEmpresa'";
		}else{
			$Sql="SELECT * FROM CTCuentas WHERE numero='".$Registro["Codigo"]."'";
		}
		$Resul = $mysqli->query($Sql);
		while ($Reg = $Resul->fetch_assoc()) {
			$NCodigo=$Reg['detalle'];
		}
		$Msj1=" (";

		if ($Registro['Rut']!=""){
			$Msj1=$Msj1."Rut: ".$Registro['Rut'];
		}

		if($Registro['Documento']>0) {
			$Msj1=$Msj1.=" - N: ".$Registro['Documento'];
		}

		$Msj1=$Msj1.")";

		if ($Msj1==" ()") {
			$Msj1="";
		}


		$opcCC="";
		$SqlCC="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='".$Registro['CCosto']."'";
		$SqlCCr = $mysqli->query($SqlCC);
		while ($RegCC = $SqlCCr->fetch_assoc()) {
			// $opcCC= strtoupper($RegCC['nombre']);
			$opcCC= strtoupper($RegCC['codigo']);
		}

		echo '
			<tr>
				<td>'.$Registro['Codigo'].'</td>
				<td>'.$NCodigo.$Msj1.'</td>
				<td style="text-align: right;">'.number_format($Registro['Debe'], $NDECI, $DDECI, $DMILE).'</td>
				<td style="text-align: right;">'.number_format($Registro['Haber'], $NDECI, $DDECI, $DMILE).'</td>
				<td style="text-align: center;">'.$opcCC.'</td>
				<td>
					<button type="button" class="btn btn-xs btn-danger" onclick="BorreTemp('.$Registro["Id"].')">
						<span class="glyphicon glyphicon-remove"></span> 
					</button>
				</td>
			</tr>
		';
		$SumDebe=$SumDebe+$Registro['Debe'];
		$SumHaber=$SumHaber+$Registro['Haber'];
	}
		echo '
			<tr>
				<td style="text-align: right;"></td>
				<td>Totales </td>
				<td style="text-align: right;"><strong>'.number_format($SumDebe, $NDECI, $DDECI, $DMILE).'</strong> <input type="hidden" name="ColDebe" id="ColDebe" value="'.$SumDebe.'"></td>
				<td style="text-align: right;"><strong>'.number_format($SumHaber, $NDECI, $DDECI, $DMILE).'</strong> <input type="hidden" name="ColHaber" id="ColHaber" value="'.$SumHaber.'"></td>
				<td style="text-align: right; color: #fd0303;"><strong>Dif: '.number_format(($SumDebe-$SumHaber), $NDECI, $DDECI, $DMILE).'</strong></td>
			</tr>
		';
	$mysqli->close();
?>