<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	$dmes = substr($Periodo,0,2);
	$dano = substr($Periodo,3,4);

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if($registro['tipo']=="IVA"){
			$DIVA=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];	
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];	
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];	
		}	
	}


	if (isset($_POST['enviar'])){
		$filename=$_FILES["file"]["name"];
		$info = new SplFileInfo($filename);
		$extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);

		if($extension == 'csv'){
			$filename = $_FILES['file']['tmp_name'];
			$filename1 = $_FILES['file']['tmp_name'];

			////Cuento Linea del Archivo
			$LArchivo=0;
			$SwOK=1;
			$fp = fopen ($filename1,"r"); 
			while ($data = fgetcsv ($fp, 0, $DLIST)){
				$LArchivo=$LArchivo+1;

				if ($LArchivo>1) {
					$SQL="SELECT * FROM CTBoletasDTE WHERE Folio='".$data[0]."' AND DTE='".$data[4]."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
					$resultados = $mysqli->query($SQL);
					$row_cnt = $resultados->num_rows;
					if ($row_cnt>0){

						$ResX = $mysqli->query($SQL);
						while ($RegX = $ResX->fetch_assoc()) {
							$F1=$RegX['Periodo'];
							$F2=$RegX['keyas'];
						}

						$SwOK=0;
						$Msj='<div class="alert alert-danger"><strong>Advertencia!</strong> Existe un folio registrado previmente, favor revisar ('.$F1.'_'.$F2.')</div><br>';
						break;
						// exit;
					}
				}

			}

			fclose($fp);

			if ($SwOK==1) {

				if ($_POST['messelect']<=9) {
					$Xmesselect="0".$_POST['messelect'];
				}else{
					$Xmesselect=$_POST['messelect'];
				}

				$LPeriodo=$Xmesselect."-".$_POST['anoselect'];

				$dmes = substr($LPeriodo,0,2);
				$dano = substr($LPeriodo,3,4);

				$Linea=0;
				$SumaMontos=0;
				$handle = fopen($filename, "r");
				while( ($data = fgetcsv($handle, 0, $DLIST) ) !== FALSE ){

					if ($Linea>0) {
						$Dat1=$data[0];
						$Dat2=$data[1];
						$Dat3=$data[2];
						$Dat4=$data[3];
						$Dat5=$data[4];
						$Dat6=$data[5];
						$Dat7=$data[6];
						$Dat8=$data[7];

						$q = "INSERT INTO CTBoletasDTE VALUES ('','".$_SESSION['RUTEMPRESA']."','$LPeriodo','$Dat1','$Dat2','$Dat3','$Dat4','$Dat5','$Dat6','$Dat7','$Dat8','')";

						$mysqli->query($q);
						$SumaMontos=$SumaMontos+($Dat2+$Dat3+$Dat4);






					}else{
						$Linea=1;
					}
				}

				fclose($handle);







				if ($SumaMontos==0) {
					$q = "DELETE FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Periodo='$LPeriodo' AND keyas=''";
					$mysqli->query($q);
					$Msj='<div class="alert alert-danger"><strong>Advertencia!</strong> No existen datos para procesar, favor revisar</div><br>';
				}

				$KeyAs=date("YmdHis");

				//////Centralización
				//echo "hhh".$_POST['OptCentra']."fff";
				////Mensual
				if ($_POST['OptCentra']=='M' && $SumaMontos>0) {


					$SQL="SELECT * FROM CTAsientoBolEle WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
					$resultados = $mysqli->query($SQL);
					$row_cnt = $resultados->num_rows;
					if ($row_cnt==0){
						$SQL="SELECT * FROM CTAsientoBolEle WHERE rut_empresa=''";
					}


					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$L1=$registro['L1'];
						$L2=$registro['L2'];
						$L3=$registro['L3'];

						$CAJA=$registro['pago'];
					}

					$SQL="SELECT Sum(Neto) as XNeto, Sum(IVA) as XIVA, Sum(Total) as XTotal FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND keyas='' AND Periodo='".$LPeriodo."'";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$Net=$registro['XNeto'];
						$Iva=$registro['XIVA'];
						$Tot=$registro['XTotal'];
					}

					if ($Tot>($Net+$Iva)) {
						$dif=$Tot-($Net+$Iva);
						$Net=$Net+$dif;
					}

					function UltimoDiaMesD($LPer) { 
						$month = substr($LPer,0,2);
						$year = substr($LPer,3,4);
						$day = date("d", mktime(0,0,0, $month+1, 0, $year));

						return date('d', mktime(0,0,0, $month, $day, $year));
					};

					$XFecAsisnto=UltimoDiaMesD($LPeriodo);
					$FecAsisnto=$XFecAsisnto."-".$LPeriodo;
					$dia = substr($FecAsisnto,0,2);
					$mes = substr($FecAsisnto,3,2);
					$ano = substr($FecAsisnto,6,4);

					$FecAsisnto=$ano."-".$mes."-".$dia;

					//// Asigan Keyas a Boletas Electronicas
					$SQL ="UPDATE CTBoletasDTE SET keyas='$KeyAs' WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND keyas='' AND Periodo='$LPeriodo'";
					$mysqli->query($SQL);


					///Asisneto de centralización
					$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
					$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FecAsisnto."','','$L1','$Tot','0','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','',''),";
					$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FecAsisnto."','','$L2','0','$Net','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','',''),";
					$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FecAsisnto."','','$L3','0','$Iva','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','','');";

					$mysqli->query($SQL);

						$TanoD = substr($LPeriodo,3,4);
						$FolioComp=0;
						$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
						$resultados = $mysqli->query($SQL1);
						while ($registro = $resultados->fetch_assoc()) {
							$FolioComp=$registro['valor'];
						}

						if ($FolioComp==0) {
							$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','".$_SESSION['RUTEMPRESA']."','$TanoD','T','2','A');");
							$FolioComp=1;
						}else{
							$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND ano='$TanoD'");
						}

					$xglosa="CENTRALIZACION BOLETAS ELECTRONICAS ".$LPeriodo;

					$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
					$SQL = $SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FecAsisnto."','$xglosa','','','','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','$FolioComp','T','');";
					$mysqli->query($SQL);


					//////Generador de Pago
					$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
					$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FecAsisnto."','','$CAJA','$Tot','0','".date("Y-m-d")."','A','$KeyAs','PagBolEle','','0','I',''),";
					$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FecAsisnto."','','$L1','0','$Tot','".date("Y-m-d")."','A','$KeyAs','PagBolEle','','0','I','');";

					$mysqli->query($SQL);

						$TanoD = substr($LPeriodo,3,4);
						$FolioComp=0;
						$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='I' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
						$resultados = $mysqli->query($SQL1);
						while ($registro = $resultados->fetch_assoc()) {
							$FolioComp=$registro['valor'];
						}

						if ($FolioComp==0) {
							$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','".$_SESSION['RUTEMPRESA']."','$TanoD','I','2','A');");
							$FolioComp=1;
						}else{
							$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='I' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND ano='$TanoD'");
						}

					$xglosa="INGRESOS BOLETAS ELECTRONICAS ".$LPeriodo;

					$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
					$SQL = $SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FecAsisnto."','$xglosa','','','','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','$FolioComp','I','');";
					$mysqli->query($SQL);



					// $SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='$KeyAs'";
					// $resultados = $mysqli->query($SQL);
					// $row_cnt = $resultados->num_rows;
					// if ($row_cnt==0){
					// 	$SQL="DELETE FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND keyas='$KeyAs' AND Periodo='$LPeriodo'";
					// 	$mysqli->query($SQL);
					// }
				}

				if ($_POST['OptCentra']=='S' && $SumaMontos>0) {
					// echo $LPeriodo;
					$SQL="SELECT * FROM CTAsientoBolEle WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
					$resultados = $mysqli->query($SQL);
					$row_cnt = $resultados->num_rows;
					if ($row_cnt==0){
						$SQL="SELECT * FROM CTAsientoBolEle WHERE rut_empresa=''";
					}

					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$L1=$registro['L1'];
						$L2=$registro['L2'];
						$L3=$registro['L3'];

						$CAJA=$registro['pago'];
					}

					//$KeyAs=date("YmdHis");

					//// Asigan Keyas a Boletas Electronicas
					$SQL ="UPDATE CTBoletasDTE SET keyas='$KeyAs' WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND keyas='' AND Periodo='$LPeriodo'";
					$mysqli->query($SQL);


					function UltimoDiaMesD($LPer) { 
						$month = substr($LPer,0,2);
						$year = substr($LPer,3,4);
						$day = date("d", mktime(0,0,0, $month+1, 0, $year));

						return date('d', mktime(0,0,0, $month, $day, $year));
					};

					$UDia=UltimoDiaMesD($LPeriodo);
					$IniSemana="01-".$LPeriodo;

					$dia = substr($IniSemana,0,2);
					$mes = substr($IniSemana,3,2);
					$ano = substr($IniSemana,6,4);

					$IniSemana=$ano."-".$mes."-".$dia;

					$Domingo="XXX";
					while ($i <= $UDia) {

						$print="n";
						if ($i<=9) {
							$Dia="0".$i;
						}else{
							$Dia=$i;
						}
						
						$FinSemana=$Dia."-".$LPeriodo;

						$Domingo=date("D", strtotime($FinSemana));

						$dia = substr($FinSemana,0,2);
						$mes = substr($FinSemana,3,2);
						$ano = substr($FinSemana,6,4);

						$FinSemana=$ano."-".$mes."-".$dia;

						if($Domingo=="Sun" && $i!=""){

							$Net=0;
							$Iva=0;
							$Tot=0;
							$SQL="SELECT Sum(Neto) as XNeto, Sum(IVA) as XIVA, Sum(Total) as XTotal FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND keyas='".$KeyAs."' AND Periodo='".$LPeriodo."' AND Fecha BETWEEN '".$IniSemana."' AND '".$FinSemana."'";

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$Net=$registro['XNeto'];
								$Iva=$registro['XIVA'];
								$Tot=$registro['XTotal'];
							}
							$LSuma=$Net+$Iva+$Tot;				

							if ($LSuma>0) {

								$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
								$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L1','$Tot','0','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','',''),";
								$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L2','0','$Net','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','',''),";
								$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L3','0','$Iva','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','','');";

								$mysqli->query($SQL);

									$TanoD = substr($LPeriodo,3,4);
									$FolioComp=0;
									$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
									$resultados = $mysqli->query($SQL1);
									while ($registro = $resultados->fetch_assoc()) {
										$FolioComp=$registro['valor'];
									}

									if ($FolioComp==0) {
										$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','".$_SESSION['RUTEMPRESA']."','$TanoD','T','2','A');");
										$FolioComp=1;
									}else{
										$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND ano='$TanoD'");
									}

								$xglosa="CENTRALIZACION BOLETAS ELECTRONICAS ".$LPeriodo;

								$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
								$SQL = $SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','$xglosa','','','','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','$FolioComp','T','');";
								$mysqli->query($SQL);


								//////Generador de Pago
								$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
								$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$CAJA','$Tot','0','".date("Y-m-d")."','A','$KeyAs','PagBolEle','','0','I',''),";
								$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L1','0','$Tot','".date("Y-m-d")."','A','$KeyAs','PagBolEle','','0','I','');";

								$mysqli->query($SQL);

									$TanoD = substr($LPeriodo,3,4);
									$FolioComp=0;
									$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='I' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
									$resultados = $mysqli->query($SQL1);
									while ($registro = $resultados->fetch_assoc()) {
										$FolioComp=$registro['valor'];
									}

									if ($FolioComp==0) {
										$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','".$_SESSION['RUTEMPRESA']."','$TanoD','I','2','A');");
										$FolioComp=1;
									}else{
										$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='I' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND ano='$TanoD'");
									}

								$xglosa="INGRESOS BOLETAS ELECTRONICAS ".$LPeriodo;

								$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
								$SQL = $SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','$xglosa','','','','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','$FolioComp','I','');";
								$mysqli->query($SQL);
							}

							$IniSemana=date( "Y-m-d", strtotime("$FinSemana +1 day"));
							$print="s";

						}

						$i++;
					}

					if ($print=="n") {

						$Net=0;
						$Iva=0;
						$Tot=0;
						$SQL="SELECT Sum(Neto) as XNeto, Sum(IVA) as XIVA, Sum(Total) as XTotal FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND keyas='".$KeyAs."' AND Periodo='".$LPeriodo."' AND Fecha BETWEEN '".$IniSemana."' AND '".$FinSemana."'";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							$Net=$registro['XNeto'];
							$Iva=$registro['XIVA'];
							$Tot=$registro['XTotal'];
						}							
						$LSuma=$Net+$Iva+$Tot;				

						if ($LSuma>0) {
							///Asisneto de centralización
							$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
							$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L1','$Tot','0','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','T',''),";
							$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L2','0','$Net','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','T',''),";
							$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L3','0','$Iva','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','0','T','');";

							$mysqli->query($SQL);

								$TanoD = substr($LPeriodo,3,4);
								$FolioComp=0;
								$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
								$resultados = $mysqli->query($SQL1);
								while ($registro = $resultados->fetch_assoc()) {
									$FolioComp=$registro['valor'];
								}

								if ($FolioComp==0) {
									$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','".$_SESSION['RUTEMPRESA']."','$TanoD','T','2','A');");
									$FolioComp=1;
								}else{
									$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND ano='$TanoD'");
								}

							$xglosa="CENTRALIZACION BOLETAS ELECTRONICAS ".$LPeriodo;

							$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
							$SQL = $SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','$xglosa','','','','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','$FolioComp','T','');";
							$mysqli->query($SQL);


							//////Generador de Pago
							$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
							$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$CAJA','$Tot','0','".date("Y-m-d")."','A','$KeyAs','PagBolEle','','0','I',''),";
							$SQL =$SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','','$L1','0','$Tot','".date("Y-m-d")."','A','$KeyAs','PagBolEle','','0','I','');";

							$mysqli->query($SQL);

								$TanoD = substr($LPeriodo,3,4);
								$FolioComp=0;
								$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='I' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
								$resultados = $mysqli->query($SQL1);
								while ($registro = $resultados->fetch_assoc()) {
									$FolioComp=$registro['valor'];
								}

								if ($FolioComp==0) {
									$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','".$_SESSION['RUTEMPRESA']."','$TanoD','I','2','A');");
									$FolioComp=1;
								}else{
									$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='I' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND ano='$TanoD'");
								}

							$xglosa="INGRESOS BOLETAS ELECTRONICAS ".$LPeriodo;

							$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
							$SQL = $SQL."('$LPeriodo','".$_SESSION['RUTEMPRESA']."','".$FinSemana."','$xglosa','','','','".date("Y-m-d")."','A','$KeyAs','CenBolEle','','$FolioComp','I','');";
							$mysqli->query($SQL);
						}
					}
				}


				$SQL="SELECT * FROM CTRegLibroDiario WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND keyas='$KeyAs' AND periodo='$LPeriodo'";
				$resultados = $mysqli->query($SQL);
				$row_cnt = $resultados->num_rows;
				if ($row_cnt==0){
					$SQL ="DELETE FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND keyas='$KeyAs' AND Periodo='$LPeriodo'";
					$mysqli->query($SQL);
					$Msj='<div class="alert alert-danger"><strong>Advertencia!</strong> Los registros indicados no fueron procesados, favor revisar el archivo, no coincide fecha con periodo seleccionado.</div><br>';
				}else{
					$Msj='<div class="alert alert-success"><strong>Informativo</strong> El archivo fue procesado con Exito.</div><br>';
				}
				
			}
		}
	}
	$mysqli->close();





?>
<!DOCTYPE html>
<html>
	<head>
	<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">	

		<script type="text/javascript">
			function Proce(){
				alert("Proceso puede tomar tiempo, dependera de la cantidad de registro");
			}
		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-sm-12 text-left">

				<form enctype="multipart/form-data" method="post" action="" name="formbol" id="formbol">

					<div class="col-md-2"></div>
					<div class="col-md-8">
						<div class="panel panel-default">
						<div class="panel-heading text-center">Importar Libros</div>
						<div class="panel-body">

								<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Mes</span>
									<select class="form-control" id="messelect" name="messelect" required>
									<?php 
										$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
										$i=1;
										$dmes=$dmes*1;
										while($i<=12){

											if ($i==$dmes) {
												echo "<option value ='".$i."' selected>".$Meses[($i-1)]."</option>";
											}else{
												echo "<option value ='".$i."'>".$Meses[($i-1)]."</option>";
											}
											$i++;
										}
									?>
									</select>
								</div>
								</div>

								<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">A&ntilde;o</span>
									<select class="form-control" id="anoselect" name="anoselect" required>
									<?php 
										$yoano=date('Y');
										$tano="2010";

										while($tano<=($yoano+1)){
											if ($dano==$tano) {
												echo "<option value ='".$tano."' selected>".$tano."</option>";
											}else{
												echo "<option value ='".$tano."'>".$tano."</option>";
											}
											$tano=$tano+1;
										}
									?>
									</select>
								</div>
								</div>

								<div class="clearfix"></div>
								<br>


								<div class="col-md-8">
									<div class="input-group">
										<span class="input-group-addon">Seleccionar Archivo</span>
										<input type="file" name="file" id="file">
									</div>
									<small id="fileHelp" class="form-text text-muted">* Solo archivo CSV.</small><br>

									<!-- <div class="col-md-10"> -->
										<br>
										<a href="EjemploBE.csv" class="btn btn-default" role="button">
											<span class="glyphicon glyphicon-download-alt"></span> Ejemplo de Formato
										</a>
									<!-- </div> -->



									<h3>Tipo de Centralizaci&oacute;n</h3>

									<label class="radio-inline"><input type="radio" name="OptCentra" value="M" checked>Mensual</label>
									<label class="radio-inline"><input type="radio" name="OptCentra" value="S">Semanal</label>
									<br>
									<br>
									<?php
										$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
										$row_cnt=0;

										$SQL="SELECT * FROM CTAsientoBolEle WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
										$resultados = $mysqli->query($SQL);
										$row_cnt = $resultados->num_rows;
										if ($row_cnt==0){
											$SQL="SELECT * FROM CTAsientoBolEle WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
											$resultados = $mysqli->query($SQL);
											$row_cnt = $resultados->num_rows;											
										}

										if ($row_cnt==0) {
											echo 'No se cuenta con el asiento configurado para esta operaci&oacute;n';
										}else{
											echo '<input type="submit" value="Enviar" class="btn btn-success" onclick="Proce()" name="enviar" id="enviar">';
										}
										$mysqli->close();
									?>


									
									<br>
									<small id="fileHelp" class="form-text text-muted">* Los documentos se cancelaran de forma automatica</small>
								</div> 

								<div class="col-md-4">
									<img class="img-responsive" src="../images/SisBoletas.png" alt="">
								</div>

								<div class="clearfix"></div>
								<br>

								<div class="col-md-12">

									<div class="form-group">
										<label class="control-label col-md-12" for="file"></label>
										<div class="col-md-12">
										<?PHP
											if ($Msj!="") {
												echo $Msj;
											}
										?>
										</div>
									</div> 

								</div>

						</div>
						</div>
					</div>
				</form>				

			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>

