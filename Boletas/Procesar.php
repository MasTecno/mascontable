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
		<script src="../js/jquery.min.js"></script>

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

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

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

			<div class="space-y-8">
			<form enctype="multipart/form-data" method="post" action="" name="formbol" id="formbol">

				<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-file-import text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Importar Libros
							</h3>
							<p class="text-sm text-gray-600">Procesar archivos CSV de boletas electrónicas</p>
						</div>
					</div> 
					
					<div class="p-6 pt-1 space-y-6">

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
							<div>
								<label for="messelect" class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="messelect" name="messelect" required>
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

							<div>
								<label for="anoselect" class="block text-sm font-medium text-gray-700 mb-2">A&ntilde;o</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="anoselect" name="anoselect" required>
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


						<div class="space-y-6">
							<div>
								<label for="file" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Archivo</label>
								<div class="flex items-center gap-2">
									<input type="file" name="file" id="file" accept=".csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-l-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200">
								</div>
								<p class="mt-1 text-sm text-gray-500">* Solo archivo CSV.</p>
							</div>

							<div class="flex items-center gap-4">
								<a href="EjemploBE.csv" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md border border-gray-300 transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" role="button">
									<i class="fa-solid fa-download mr-2"></i> Ejemplo de Formato
								</a>
							</div>

							<div>
								<h3 class="text-lg font-medium text-gray-900 mb-4">Tipo de Centralizaci&oacute;n</h3>
								<div class="flex gap-6">
									<label class="flex items-center">
										<input type="radio" name="OptCentra" value="M" checked class="border-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
										<span class="ml-2 text-sm text-gray-700">Mensual</span>
									</label>
									<label class="flex items-center">
										<input type="radio" name="OptCentra" value="S" class="border-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
										<span class="ml-2 text-sm text-gray-700">Semanal</span>
									</label>
								</div>
							</div>
							<div class="flex flex-col gap-6">
								<div class="flex-1">
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
											echo '<div class="bg-yellow-50 border border-yellow-200 rounded-md p-4"><p class="text-sm text-yellow-800">No se cuenta con el asiento configurado para esta operaci&oacute;n</p></div>';
										}else{
											echo '<button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" onclick="Proce()" name="enviar" id="enviar">
												<i class="fa-solid fa-upload mr-2"></i> Procesar Archivo
											</button>';
										}
										$mysqli->close();
									?>
									
									<p class="mt-3 text-sm text-gray-500">* Los documentos se cancelaran de forma automática</p>
								</div>

								<div class="flex-shrink-0 flex justify-center md:justify-start lg:mt-0 mt-3">
									<img class="w-64 h-64 lg:w-80 lg:h-80 object-contain" src="../images/SisBoletas.png" alt="Sistema de Boletas">
								</div>
							</div>
						</div>

						<?PHP
							if ($Msj!="") {
								echo '<div class="mt-6">' . $Msj . '</div>';
							}
						?>
					</div>
				</div>
			</form>				

		</div>
		</div>
		</div>

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>

</html>

