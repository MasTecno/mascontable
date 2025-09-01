<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="DELETE FROM CTRegLibroDiarioTemp WHERE rutempresa='$RutEmpresa';";
	$mysqli->query($SQL);
	
	function LFolio($x1,$x2){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$RutEmpresa=$_SESSION['RUTEMPRESA'];

		$FolioComp=0;
		$SrtSql="SELECT * FROM CTComprobanteFolio WHERE tipo='$x1' AND rutempresa='$RutEmpresa' AND ano='$x2'";
		$Resul = $mysqli->query($SrtSql);
		while ($Regi = $Resul->fetch_assoc()) {
			$FolioComp=$Regi['valor'];
		}

		if ($FolioComp==0) {
			$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$x2','$x1','2','A');");
			$FolioComp=1;
		}else{
			$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$x1' AND rutempresa='$RutEmpresa' AND ano='$x2'");
		}
		return $FolioComp;
	}

	function BuscaKey($l){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$foliINI=$l;
		$swFolio="N";

		while ($swFolio=="N") {
			$sqlNF = "SELECT * FROM CTRegLibroDiario WHERE keyas='$l'";
			$ResulNF = $mysqli->query($sqlNF);										
			$row_cntNF = $ResulNF->num_rows;

			if ($l!=$foliINI && $row_cntNF==0) {
				$swFolio="S";
			}else{
				$l=$l+1;
			}
		}
		return $l;
	}

	function ExisteCta($r1,$RutEmpresa){
			
		if($r1=="I" || $r1=="E" || $r1=="T"){
			return "SI";
		}else{
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado<>'X' AND rut_empresa='$RutEmpresa' AND numero='$r1' ORDER BY numero ASC";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE estado<>'X' AND numero='$r1' ORDER BY numero ASC";
			}

			$resultado = $mysqli->query($SQL);

			$row_cnt = $resultado->num_rows;
			if ($row_cnt==0) {
				return "NO";
			}else{
				return "SI";
			}
		}
	}

	function ExisteCC($r1,$RutEmpresa){
		if($r1!=""){
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
			$SQL="SELECT * FROM CTCCosto WHERE rutempresa='$RutEmpresa' AND codigo='$r1'";
			$resultado = $mysqli->query($SQL);
			$row_cnt = $resultado->num_rows;
			if ($row_cnt==0) {
				return "NO";
			}
		}
	}

	$SwMes="";
	if ($_POST['swImport']=="S") {
		if (isset($_POST['action'])) {
			$action=$_POST['action'];
		}

		if (isset($action)== "upload" && $_FILES['excel']['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
			//cargamos el fichero
			$archivo = $_FILES['excel']['name'];
			$tipo = $_FILES['excel']['type'];
			$destino = "Temp_".$archivo;//Le agregamos un prefijo para identificarlo el archivo cargado
			if (copy($_FILES['excel']['tmp_name'],$destino)){
				// echo "Archivo Cargado Con Éxito<br>";
			}else{
				header("location:./?FileError=Error, El tipo de archivo no es el correcto.");
				exit;
			}

			if (file_exists ("Temp_".$archivo)){
				$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
				/** Llamamos las clases necesarias PHPEcel */
				require_once('../Classes/PHPExcel.php');
				require_once('../Classes/PHPExcel/Reader/Excel2007.php');                  
				// Cargando la hoja de excel
				$objReader = new PHPExcel_Reader_Excel2007();
				$objPHPExcel = $objReader->load("Temp_".$archivo);
				$objFecha = new PHPExcel_Shared_Date();       
				// Asignamon la hoja de excel activa
				$objPHPExcel->setActiveSheetIndex(0);

				$columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
				$filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

				$KeyAsO=date("YmdHis");

				$glosa="";
				$sdebe=0;
				$shaber=0;
				$CuentaAsiento=0;
				$KeyAsTemp=555;
				for ($i=2;$i<=$filas;$i++){
					$_DATOS_EXCEL[$i]['Cuenta']= $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Glosa'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Debe'] = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Haber'] = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['CCosto'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Apertura'] = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Tributable'] = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();

					if($_DATOS_EXCEL[$i]['Debe']>0){
						$sdebe=$sdebe+$_DATOS_EXCEL[$i]['Debe'];
					}
					
					if($_DATOS_EXCEL[$i]['Haber']>0){
						$shaber=$shaber+$_DATOS_EXCEL[$i]['Haber'];
					}

					if($_DATOS_EXCEL[$i]['Cuenta']=="I" || $_DATOS_EXCEL[$i]['Cuenta']=="E" || $_DATOS_EXCEL[$i]['Cuenta']=="T"){
						if($sdebe<>$shaber){
							header("location:./?ErrorSum=\"".$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue().", de Fecha: ".date("d-m-Y",strtotime($Fec))."\"");
							exit;
						}
						
						$Folio=LFolio($_DATOS_EXCEL[$i]['Cuenta'],$Ano);
						$KeyAs="MASI".BuscaKey($KeyAsO.$Folio);

						$STRInseert=$STRInseert. "UPDATE CTRegLibroDiario SET keyas='$KeyAs', ncomprobante='$Folio', tipo='".$_DATOS_EXCEL[$i]['Cuenta']."' WHERE rutempresa ='$RutEmpresa' AND keyas='$KeyAsTemp' AND tipo='X' AND ncomprobante='2147483647';";

						$STRInseert=$STRInseert. "INSERT INTO CTRegLibroDiario VALUES ('','$Per','$RutEmpresa','$Fec','".strtoupper($_DATOS_EXCEL[$i]['Glosa'])."','0','0','0','".date("Y-m-d")."','A','$KeyAs','','','$Folio','".$_DATOS_EXCEL[$i]['Cuenta']."','0','','0','','0','0');";

						if($_DATOS_EXCEL[$i]['Apertura']=="S"){
							$mysqli->query("INSERT INTO CTAsientoApertura VALUES('','$Per','$RutEmpresa','$KeyAs');");
						}

						$sdebe=0;
						$shaber=0;

						$CuentaAsiento++;
					}else{
						$format = "Y-m-d";
						
						$cell = $objPHPExcel->getActiveSheet()->getCell('A'. $i);	
						$InvDate= $cell->getValue();
						if(PHPExcel_Shared_Date::isDateTime($cell)) {
							$InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
							$Fec =date("Y-m-d",strtotime($InvDate."+ 1 days")); 
						}
	
						$Per=date("m-Y",strtotime($Fec));
						$Ano=date("Y",strtotime($Fec));
						
						if(ExisteCC($_DATOS_EXCEL[$i]['CCosto'],$RutEmpresa)=="NO"){
							header("location:./?NExiteCC=".$_DATOS_EXCEL[$i]['CCosto']);
							exit;
						}else{
							$CC=ExisteCC($_DATOS_EXCEL[$i]['CCosto'],$RutEmpresa);
						}

						$STRInseert=$STRInseert. "INSERT INTO CTRegLibroDiario VALUES ('','$Per','$RutEmpresa','$Fec','','".$_DATOS_EXCEL[$i]['Cuenta']."','".$_DATOS_EXCEL[$i]['Debe']."','".$_DATOS_EXCEL[$i]['Haber']."','".date("Y-m-d")."','A','$KeyAsTemp','','','2147483647','X','0','','0','','0','0');";
					}

					if(ExisteCta($_DATOS_EXCEL[$i]['Cuenta'],$RutEmpresa)=="NO"){
						header("location:./?NExite=".$_DATOS_EXCEL[$i]['Cuenta']);
						exit;
					}
				}

				echo $STRInseert;
				exit;

				if ($Msj=="") {
					echo $STRInseert;
					exit;
					$mysqli->multi_query($STRInseert);
				}

				unlink($destino);
				$mysqli->close();
				header("location:./?OK=$CuentaAsiento");
				exit;
			}else{
				$Msj="Primero debes cargar el archivo con extencion .xlsx";
			}
		}else{
			$Msj="Primero debes cargar el archivo con extencion .xlsx";
		}
	}