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

		if (isset($action) && $action == "upload" && $_FILES['excel']['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
			// Cargamos el archivo
			$archivo = $_FILES['excel']['name'];
			$tipo = $_FILES['excel']['type'];
			$destino = "Temp_" . $archivo; // Prefijo para identificar el archivo cargado
			if (copy($_FILES['excel']['tmp_name'], $destino)) {
				// Archivo cargado con éxito
			} else {
				header("location:./?FileError=Error, El tipo de archivo no es el correcto.");
				exit;
			}
		
			if (file_exists("Temp_" . $archivo)) {
				$mysqli = xconectar($_SESSION['UsuariaSV'], descript($_SESSION['PassSV']), $_SESSION['BaseSV']);
				
				// Utilizamos PhpSpreadsheet en lugar de PHPExcel
				require '../vendor/autoload.php'; // Asumiendo que instalaste via Composer
				
				// Cargar la hoja de Excel
				try {
					$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("Temp_" . $archivo);
					$sheet = $spreadsheet->getActiveSheet();
					
					// Obtener las filas y columnas
					$filas = $sheet->getHighestRow();
					$columnasLetra = $sheet->getHighestColumn();
					$columnas = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnasLetra);
					
					// Array para almacenar celdas con fórmulas
					$celdasConFormulas = [];
					
					// Verificar si hay celdas con fórmulas
					for ($fila = 1; $fila <= $filas; $fila++) {
						for ($columna = 1; $columna <= $columnas; $columna++) {
							$cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columna) . $fila;
							$cell = $sheet->getCell($cellCoordinate);
							
							// PhpSpreadsheet tiene un método nativo isFormula()
							if ($cell->isFormula()) {
								$celdasConFormulas[] = [
									'celda' => $cellCoordinate,
									'formula' => $cell->getValue()
								];
							}
						}
					}
					
					// Si hay fórmulas, mostrar advertencia
					if (!empty($celdasConFormulas)) {
						$mensajeFormulas = "El archivo contiene fórmulas en las siguientes celdas: ";
						foreach ($celdasConFormulas as $index => $datoFormula) {
							$mensajeFormulas .= $datoFormula['celda'] . " (" . $datoFormula['formula'] . ")";
							if ($index < count($celdasConFormulas) - 1) {
								$mensajeFormulas .= ", ";
							}
						}
						
						// Almacenar mensaje en sesión para mostrarlo después
						$_SESSION['MensajeFormulas'] = $mensajeFormulas;
					}
					
					$KeyAsO = date("YmdHis");
					
					$glosa = "";
					$sdebe = 0;
					$shaber = 0;
					$CuentaAsiento = 0;
					$KeyAsTemp = "MASI555";
					$STRInsert = "";
					$Msj = "";
					$Per = "";
					$Ano = "";
					$Fec = "";
					$RutEmpresa = $_SESSION['RUTEMPRESA'];
					
					for ($i = 2; $i <= $filas; $i++) {
						// Leer los valores de cada celda con el nuevo método
						// Usar getCalculatedValue() para obtener el resultado de las fórmulas
						$_DATOS_EXCEL[$i]['Cuenta'] = $sheet->getCell('B' . $i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Glosa'] = $sheet->getCell('C' . $i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Debe'] = $sheet->getCell('D' . $i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Haber'] = $sheet->getCell('E' . $i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['CCosto'] = $sheet->getCell('F' . $i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Apertura'] = $sheet->getCell('G' . $i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Tributable'] = $sheet->getCell('H' . $i)->getCalculatedValue();

						$glosa = $_DATOS_EXCEL[$i]['Glosa'];
						$glosa = str_replace('"', "", $glosa);
						$glosa = str_replace("'", "", $glosa);



						// Realizar cálculos
						if ($_DATOS_EXCEL[$i]['Debe'] > 0) {
							$sdebe += $_DATOS_EXCEL[$i]['Debe'];
						}
		
						if ($_DATOS_EXCEL[$i]['Haber'] > 0) {
							$shaber += $_DATOS_EXCEL[$i]['Haber'];
						}
		
						$format = "Y-m-d";
						$cell = $sheet->getCell('A' . $i);
						$InvDate = $cell->getValue();
						
						// PhpSpreadsheet usa método diferente para determinar si es fecha
						if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
							$InvDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($InvDate)->format($format);
							$Fec = date("Y-m-d", strtotime($InvDate));
						}

						if ($_DATOS_EXCEL[$i]['Cuenta'] == "I" || $_DATOS_EXCEL[$i]['Cuenta'] == "E" || $_DATOS_EXCEL[$i]['Cuenta'] == "T") {
							if ($sdebe != $shaber) {
								header("location:./?ErrorSum=" . urlencode("Error en la suma de los débitos y créditos en la fila " . $i));
								exit;
							}
		
							$Folio = LFolio($_DATOS_EXCEL[$i]['Cuenta'], $Ano);
							$KeyAs = "MASI" . BuscaKey($KeyAsO . $Folio);
		
							$STRInsert .= "INSERT INTO CTRegLibroDiario VALUES ('','$Per','$RutEmpresa','$Fec','" . strtoupper($Glosa) . "','0','0','0','" . date("Y-m-d") . "','A','$KeyAs','','','$Folio','" . $_DATOS_EXCEL[$i]['Cuenta'] . "','0','','0','','0','0');";
							$STRInsert .= "UPDATE CTRegLibroDiario SET keyas='$KeyAs', ncomprobante='$Folio', tipo='" . $_DATOS_EXCEL[$i]['Cuenta'] . "' WHERE rutempresa ='$RutEmpresa' AND ncomprobante='1011' AND tipo='X' AND keyas='MASI555';";
		
							if ($_DATOS_EXCEL[$i]['Apertura'] == "S") {
								$mysqli->query("INSERT INTO CTAsientoApertura VALUES('','$Per','$RutEmpresa','$KeyAs');");
							}
		
							$sdebe = 0;
							$shaber = 0;
							$CuentaAsiento++;
						} else {
							$Per = date("m-Y", strtotime($Fec));
							$Ano = date("Y", strtotime($Fec));
		
							if (ExisteCC($_DATOS_EXCEL[$i]['CCosto'], $RutEmpresa) == "NO") {
								header("location:./?NExiteCC=" . $_DATOS_EXCEL[$i]['CCosto']);
								exit;
							} else {
								$r1 = $_DATOS_EXCEL[$i]['CCosto'];

								$SQL = "SELECT * FROM CTCCosto WHERE estado<>'X' AND rutempresa='$RutEmpresa' AND codigo='$r1' ORDER BY codigo ASC";
								$resultado = $mysqli->query($SQL);
								$row_cnt = $resultado->num_rows;
								if ($row_cnt > 0) {
									$Regi = $resultado->fetch_assoc();
									$IdCC = $Regi['id'];
								} else {
									$IdCC = "";
								}
							}


							$STRInsert .= "INSERT INTO CTRegLibroDiario VALUES ('','$Per','$RutEmpresa','$Fec','','" . $_DATOS_EXCEL[$i]['Cuenta'] . "','" . $_DATOS_EXCEL[$i]['Debe'] . "','" . $_DATOS_EXCEL[$i]['Haber'] . "','" . date("Y-m-d") . "','A','$KeyAsTemp','','','1011','X','0','','$IdCC','','0','0');";
						}
		
						if (ExisteCta($_DATOS_EXCEL[$i]['Cuenta'], $RutEmpresa) == "NO") {
							header("location:./?NExite=" . $_DATOS_EXCEL[$i]['Cuenta']);
							exit;
						}
					}


					// exit;
					if ($Msj == "") {
						$mysqli->multi_query($STRInsert);
						// echo $STRInsert;
						// exit;
					}
		
					unlink($destino);
					$mysqli->close();
					
					// Si hay fórmulas, agregar el mensaje a la redirección
					if (!empty($celdasConFormulas)) {
						header("location:./?OK=$CuentaAsiento&Formulas=1");
					} else {
						header("location:./?OK=$CuentaAsiento");
					}
					exit;
					
				} catch (Exception $e) {
					$mysqli->close();
					unlink($destino);
					header("location:./?Error=" . urlencode("Error al procesar el archivo: " . $e->getMessage()));
					exit;
				}
				
			} else {
				$Msj = "Primero debes cargar el archivo con extensión .xlsx";
			}
		} else {
			$Msj = "Primero debes cargar el archivo con extensión .xlsx";
		}
		
		// Si hay un mensaje de error, mostrarlo
		if ($Msj != "") {
			header("location:./?Error=" . urlencode($Msj));
			exit;
		}


	}