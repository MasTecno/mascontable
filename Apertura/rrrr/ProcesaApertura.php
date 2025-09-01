<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$_SESSION['KEYASIENTO']=date("YmdHis");


	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}


					$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

					$_SESSION['KEYASIENTO']=date("YmdHis");
					$KeyAs=$_SESSION['KEYASIENTO'];
					$xttmovimiento="T";
					$xglosa=$_POST['glosa'];
					$FECHA=date("Y/m/d");

					// $dmes = substr($Periodo,0,2);
					//$dano = substr($Periodo,3,4);



					// $Periodo="01-".$dano;

					$CuentaDif=$_POST['cuenta'];
					if ($_SESSION["PLAN"]=="S"){
						$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero ='".$CuentaDif."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
					}else{
						$SQL="SELECT * FROM CTCuentas WHERE numero ='".$CuentaDif."' ";
					}

					$consulta = $mysqli->query($SQL);
					while ($registro = $consulta->fetch_assoc()) {
						$MCuentaDif=$registro["detalle"];
					}	

					// $TanoD = $dano;
					// $dano=$dano-1;

					// $dano= substr($_POST['anoselect'],3,4);
					$dano= $_POST['anoselect'];
					$TanoD = substr($_POST['PApertura'],3,4);
					$Periodo=$_POST['PApertura'];


					$xfecha=$TanoD."-01-01";

					if ($_SESSION['CCOSTO']=="S"){
						$FolioComp=0;
						$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='$xttmovimiento' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
						$resultados = $mysqli->query($SQL1);
						while ($registro = $resultados->fetch_assoc()) {
							$FolioComp=$registro['valor'];
						}
						if ($FolioComp==0) {
							$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','$xttmovimiento','2','A');");
							$FolioComp=1;
						}else{
							$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$xttmovimiento' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
						}
					}else{
						$FolioComp=0;
					}
					if ($_SESSION["PLAN"]=="S"){
						$SQLx="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' order by numero";
					}else{
						$SQLx="SELECT * FROM CTCuentas WHERE estado='A' order by numero";
					}
					$consultax = $mysqli->query($SQLx);
					while ($registrox = $consultax->fetch_assoc()) {
						$sw=0;
						$SQL="SELECT periodo, cuenta, sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE periodo like '%$dano' AND cuenta='".$registrox["numero"]."' AND glosa='' AND rutempresa='$RutEmpresa' group by cuenta";

						$consulta = $mysqli->query($SQL);
						while ($registro = $consulta->fetch_assoc()) {
							$sw=1;
							if ($_SESSION["PLAN"]=="S"){
								$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero ='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
							}else{
								$SQL1="SELECT * FROM CTCuentas WHERE numero ='".$registro["cuenta"]."' ";
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
								$Tactivo=$Tactivo+$sdeudor;
							}
							if($registro["sdebe"]<$registro["shaber"]){
								$sacreedor=$registro["shaber"]-$registro["sdebe"];
								$Tpasivo=$Tpasivo+$sacreedor;
							}

							$activo=0;
							$pasivo=0;
							$perdida=0;
							$ganancia=0;

							if($tcuenta=="ACTIVO"){
								if ($sdeudor>0 || $sacreedor>0) {
									// echo '
									// 	<tr>
									// 	<td>'.$registro["cuenta"].'</td>
									// 	<td>'.strtoupper($ncuenta).'</td>
									// 	<td><div align="right">'.number_format($sdeudor, $NDECI, $DDECI, $DMILE).'</div></td>
									// 	<td><div align="right">'.number_format($sacreedor, $NDECI, $DDECI, $DMILE).'</div></td>
									// 	</tr>
									// ';
	        						$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$registro["cuenta"]."','$sdeudor','$sacreedor','$FECHA','A','$KeyAs','0','','0')");
								}
							}

							if($tcuenta=="PASIVO"){
								if ($sdeudor>0 || $sacreedor>0) {
									// echo '
									// 	<tr>
									// 	<td>'.$registro["cuenta"].'</td>
									// 	<td>'.strtoupper($ncuenta).'</td>
									// 	<td><div align="right">'.number_format($sdeudor, $NDECI, $DDECI, $DMILE).'</div></td>
									// 	<td><div align="right">'.number_format($sacreedor, $NDECI, $DDECI, $DMILE).'</div></td>
									// 	</tr>
									// ';
	        						$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$registro["cuenta"]."','$sdeudor','$sacreedor','$FECHA','A','$KeyAs','0','','0')");
								}
							}

							if($tcuenta=="RESULTADO"){
								if($registro["sdebe"]<$registro["shaber"]){
									$ganancia=$registro["shaber"]-$registro["sdebe"];
									$Tganancia=$Tganancia+$ganancia;
								}
								if($registro["sdebe"]>$registro["shaber"]){
									$perdida=$registro["sdebe"]-$registro["shaber"];
									$Tganancia=$Tganancia-$perdida;
								}
							}

						}
					}
					// exit;
					if ($Tganancia<0) {
						$Tganancia=$Tganancia*-1;

						$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','$CuentaDif','$Tganancia','0','$FECHA','A','$KeyAs','0','','0')");
	        			//$Tpasivo=$Tpasivo+$Tganancia;
						// echo '
						// 	<tr>
						// 	<td>'.$CuentaDif.'</td>
						// 	<td>'.$MCuentaDif.'</td>
						// 	<td><div align="right">'.number_format($Tganancia, $NDECI, $DDECI, $DMILE).'</div></td>
						// 	<td><div align="right">0</div></td>
						// 	</tr>
						// ';
					}else{
	        			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','$CuentaDif','0','$Tganancia','$FECHA','A','$KeyAs','0','','0')");
						//$Tactivo=$Tactivo+$Tganancia;
						// echo '
						// 	<tr>
						// 	<td>'.$CuentaDif.'</td>
						// 	<td>'.$MCuentaDif.'</td>
						// 	<td><div align="right">0</div></td>
						// 	<td><div align="right">'.number_format($Tganancia, $NDECI, $DDECI, $DMILE).'</div></td>
						// 	</tr>
						// ';
					}

        			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','$xglosa','$xcuenta','0','0','$FECHA','A','$KeyAs','$FolioComp','$xttmovimiento','0')");

					// echo '
					// 	<tr>
					// 	<td></td>
					// 	<td>Totales</td>
					// 	<td><div align="right">'.number_format($Tactivo, $NDECI, $DDECI, $DMILE).'</div></td>
					// 	<td><div align="right">'.number_format($Tpasivo, $NDECI, $DDECI, $DMILE).'</div></td>
					// 	</tr>
					// ';

					$mysqli->close();
					$_SESSION['KEYASIENTO']=date("YmdHis");
					$_SESSION['KEYASIENTO']=$_SESSION['KEYASIENTO']+5;
					$_SESSION['PERIODOPC']=$Periodo;
					header("location:../RVoucher/");
					exit;
				?>