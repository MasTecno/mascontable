<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
?>

	<table class="table table-condensed">
		<thead>
			<tr>
				<th>Tipo</th>
				<th style="text-align: right;">N&uacute;mero</th>
				<th style="text-align: center;">Fecha</th>
				<th>Rut</th>
				<th>R.Social</th>
				<th style="text-align: right;">Monto</th>
			</tr>
		</thead>
		<tbody>
			<?php
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

					if ($_POST['R2']=="C") {
						
						$SqlSimple="SELECT * FROM `CTRegDocumentos` WHERE keyas='".$_POST['R1']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
						$ResSimple = $mysqli->query($SqlSimple);
						while ($LinSimple = $ResSimple->fetch_assoc()) {
							$XRSocial="";
							$xSigla="";

							$SqlTipDoc="SELECT * FROM `CTCliPro` WHERE rut='".$LinSimple["rut"]."' AND estado='A'";
							$xResultado = $mysqli->query($SqlTipDoc);
							while ($Linea = $xResultado->fetch_assoc()) {
								$XRSocial=$Linea["razonsocial"];
							}

							$SqlTipDoc="SELECT * FROM `CTTipoDocumento` WHERE id='".$LinSimple["id_tipodocumento"]."' AND estado='A'";
							$xResultado = $mysqli->query($SqlTipDoc);
							while ($Linea = $xResultado->fetch_assoc()) {
								$xSigla=$Linea["sigla"];
							}

							echo '
									<tr>
										<td>'.$xSigla.'</td>
										<td style="text-align: right;">'.$LinSimple["numero"].'</td>
										<td style="text-align: center;">'.date('d-m-Y',strtotime($LinSimple["fecha"])).'</td>
										<td>'.$LinSimple["rut"].'</td>
										<td>'.$XRSocial.'</td>
										<td style="text-align: right;">'.number_format($LinSimple["total"], $NDECI, $DDECI, $DMILE).'</td>
									</tr>
							';
						}	
					}

					if ($_POST['R2']=="P") {
						
						$SqlSimple="SELECT * FROM `CTControRegDocPago` WHERE keyas='".$_POST['R1']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
						$ResSimple = $mysqli->query($SqlSimple);
						while ($LinSimple = $ResSimple->fetch_assoc()) {
							$XRSocial="";
							$xSigla="";

							$SqlTipDoc="SELECT * FROM `CTCliPro` WHERE rut='".$LinSimple["rut"]."' AND estado='A'";
							$xResultado = $mysqli->query($SqlTipDoc);
							while ($Linea = $xResultado->fetch_assoc()) {
								$XRSocial=$Linea["razonsocial"];
							}

							$SqlTipDoc="SELECT * FROM `CTTipoDocumento` WHERE id='".$LinSimple["id_tipodocumento"]."' AND estado='A'";
							$xResultado = $mysqli->query($SqlTipDoc);
							while ($Linea = $xResultado->fetch_assoc()) {
								$xSigla=$Linea["sigla"];
							}

							echo '
									<tr>
										<td>'.$xSigla.'</td>
										<td style="text-align: right;">'.$LinSimple["ndoc"].'</td>
										<td style="text-align: center;">'.date('d-m-Y',strtotime($LinSimple["fecha"])).'</td>
										<td>'.$LinSimple["rut"].'</td>
										<td>'.$XRSocial.'</td>
										<td style="text-align: right;">'.number_format($LinSimple["monto"], $NDECI, $DDECI, $DMILE).'</td>
									</tr>
							';
						}	
					}

					if ($_POST['R2']=="H") {
						
						$SqlSimple="SELECT * FROM `CTHonorarios` WHERE movimiento='".$_POST['R1']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
						$ResSimple = $mysqli->query($SqlSimple);
						while ($LinSimple = $ResSimple->fetch_assoc()) {
							$XRSocial="";
							$xSigla="";

							$SqlTipDoc="SELECT * FROM `CTCliPro` WHERE rut='".$LinSimple["rut"]."' AND estado='A'";
							$xResultado = $mysqli->query($SqlTipDoc);
							while ($Linea = $xResultado->fetch_assoc()) {
								$XRSocial=$Linea["razonsocial"];
							}
							$xSigla="Honorario";
							// $SqlTipDoc="SELECT * FROM `CTTipoDocumento` WHERE id='".$LinSimple["id_tipodocumento"]."' AND estado='A'";
							// $xResultado = $mysqli->query($SqlTipDoc);
							// while ($Linea = $xResultado->fetch_assoc()) {
							// 	$xSigla=$Linea["sigla"];
							// }

							echo '
									<tr>
										<td>'.$xSigla.'</td>
										<td style="text-align: right;">'.$LinSimple["numero"].'</td>
										<td style="text-align: center;">'.date('d-m-Y',strtotime($LinSimple["fecha"])).'</td>
										<td>'.$LinSimple["rut"].'</td>
										<td>'.$XRSocial.'</td>
										<td style="text-align: right;">'.number_format($LinSimple["liquido"], $NDECI, $DDECI, $DMILE).'</td>
									</tr>
							';
						}	
					}


				// $SQL="SELECT * FROM CTCliPro WHERE tipo='L' AND estado='A'";


				// $resultados = $mysqli->query($SQL);
				// while ($registro = $resultados->fetch_assoc()) {

				// 	echo '
				// 		<tr onclick="Buscar(\''.$registro["rut"].'\')">
				// 		<td>'.$registro["rut"].'</td>
				// 		<td>'.$registro["razonsocial"].'</td>
				// 		</tr>
				// 	';
				// }
				$mysqli->close();
			?>
		</tbody>
	</table>