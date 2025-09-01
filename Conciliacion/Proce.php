<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../?Msj=95");
		exit;
	}
	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
    
	$IdCab=$_POST['EdiCon'];
	$_SESSION['EdiCon']=$_POST['EdiCon'];
	
    $CCta=0;
    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
    
	$mysqli->query("DELETE FROM CTConciliacionLog WHERE IdCab='$IdCab' AND RutEmpresa='$RutEmpresa'");

	$SqlStr="SELECT * FROM CTConciliacionCab WHERE Id='$IdCab' AND RutEmpresa='$RutEmpresa'";
    $Resultado = $mysqli->query($SqlStr);
    while ($Registro = $Resultado->fetch_assoc()) {
		$CCta=$Registro['Cuenta'];
	}

	$CIdDet=0;
    $SqlStr="SELECT * FROM CTConciliacionDet WHERE IdCab='$IdCab' AND RutEmpresa='$RutEmpresa'";
	
    $Resultado = $mysqli->query($SqlStr);
    while ($Registro = $Resultado->fetch_assoc()) {
		$CIdDet=$Registro['Id'];
		$CFecha=$Registro['Fecha'];
		$CGlosa=$Registro['Glosa'];
		$CCargos=$Registro['Cargos'];
		$CAbonos=$Registro['Abonos'];
		$CRut=$Registro['Rut'];
		$CNumero=$Registro['Numero'];

		if($CAbonos>0){   ////egreso
			$keyas="";
			$IdLineaAsiento=0;
			$KeyAsLD="";

			/////Busca si existe el pago del documento (PRIMERA EVALUACIÓN)
			$SqlBus="SELECT * FROM CTControRegDocPago WHERE rut='$CRut' AND rutempresa='$RutEmpresa' AND tipo='C' AND ndoc='$CNumero' AND fecha='$CFecha' AND monto='$CAbonos'";
			$Resbus = $mysqli->query($SqlBus);
			while ($RegBus = $Resbus->fetch_assoc()) {
				$keyas=$RegBus['keyas'];
			}

			////Si esta el pago de documento busco en el diario si la keyas y el cta existen y obtengo el id fe Glosa (PRIMERA EVALUACIÓN)
			if($keyas!=""){
				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$keyas' AND cuenta='$CCta' AND rutempresa='$RutEmpresa'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt>0){
					$Resbus = $mysqli->query($SqlBus);
					while ($RegBus = $Resbus->fetch_assoc()) {
						$IdLineaAsiento=$RegBus['id'];
					}				
				}
			}

			///// Sino encontre nada en la (PRIMERA EVALUACIÓN), busco en el diario la fecha y glosa (SEGUNDA EVALUACIÓN)
			if($IdLineaAsiento==0){
				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND fecha='$CFecha' AND glosa='$CGlosa'";
				$Resbus = $mysqli->query($SqlBus);
				while ($RegBus = $Resbus->fetch_assoc()) {
					$KeyAsLD=$RegBus['keyas'];
				}

				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$KeyAsLD' AND cuenta='$CCta' AND haber='$CAbonos' AND rutempresa='$RutEmpresa'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt>0){
					// $SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$KeyAsLD' AND rutempresa='$RutEmpresa' AND glosa='$CGlosa'";
					$Resbus = $mysqli->query($SqlBus);
					while ($RegBus = $Resbus->fetch_assoc()) {
						$IdLineaAsiento=$RegBus['id'];
					}
				}

				$SqlBus="SELECT * FROM CTConciliacionLog WHERE RutEmpresa='$RutEmpresa' AND IdCab='$IdCab' AND IdDiario='$IdLineaAsiento'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt>0){
					$mysqli->query("DELETE FROM CTConciliacionLog WHERE RutEmpresa='$RutEmpresa' AND IdDiario='$IdLineaAsiento'");
				}
			}

			//////// SI fallaron las EVALUACIONES anteriores 
			if($IdLineaAsiento==0){
				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND fecha='$CFecha' AND cuenta='$CCta' AND haber='$CAbonos'";
				$Resbus = $mysqli->query($SqlBus);
				while ($RegBus = $Resbus->fetch_assoc()) {
					$KeyAsLD=$RegBus['keyas'];
					$IdLineaAsiento=$RegBus['id'];
				}
			}

			if($IdLineaAsiento>0){
				$SqlBus="SELECT * FROM CTConciliacionLog WHERE RutEmpresa='$RutEmpresa' AND IdCab='$IdCab' AND IdDiario='$IdLineaAsiento'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt==0){
					$mysqli->query("INSERT INTO CTConciliacionLog VALUES('','$RutEmpresa','$IdCab','$CIdDet','$IdLineaAsiento','$KeyAsLD','A')");
				}
			}
		}

		if($CCargos>0){   ////ingreso
			$keyas="";
			$IdLineaAsiento=0;
			$KeyAsLD="";

			/////Busca si existe el pago del documento (PRIMERA EVALUACIÓN)
			$SqlBus="SELECT * FROM CTControRegDocPago WHERE rut='$CRut' AND rutempresa='$RutEmpresa' AND tipo='C' AND ndoc='$CNumero' AND fecha='$CFecha' AND monto='$CCargos'";
			$Resbus = $mysqli->query($SqlBus);
			while ($RegBus = $Resbus->fetch_assoc()) {
				$keyas=$RegBus['keyas'];
			}

			////Si esta el pago de documento busco en el diario si la keyas y el cta existen y obtengo el id fe Glosa (PRIMERA EVALUACIÓN)
			if($keyas!=""){
				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$keyas' AND cuenta='$CCta' AND rutempresa='$RutEmpresa'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt>0){
					// $SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$keyas' AND cuenta='0' AND rutempresa='$RutEmpresa' AND glosa<>''";
					$Resbus = $mysqli->query($SqlBus);
					while ($RegBus = $Resbus->fetch_assoc()) {
						$IdLineaAsiento=$RegBus['id'];
					}				
				}
			}

			///// Sino encontre nada en la (PRIMERA EVALUACIÓN), busco en el diario la fecha y glosa (SEGUNDA EVALUACIÓN)
			if($IdLineaAsiento==0){
				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND fecha='$CFecha' AND glosa='$CGlosa'";
				$Resbus = $mysqli->query($SqlBus);
				while ($RegBus = $Resbus->fetch_assoc()) {
					$KeyAsLD=$RegBus['keyas'];
				}

				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$KeyAsLD' AND cuenta='$CCta' AND debe='$CCargos' AND rutempresa='$RutEmpresa'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt>0){
					// $SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$KeyAsLD' AND rutempresa='$RutEmpresa' AND glosa='$CGlosa'";
					$Resbus = $mysqli->query($SqlBus);
					while ($RegBus = $Resbus->fetch_assoc()) {
						$IdLineaAsiento=$RegBus['id'];
					}
				}

				$SqlBus="SELECT * FROM CTConciliacionLog WHERE RutEmpresa='$RutEmpresa' AND IdCab='$IdCab' AND IdDiario='$IdLineaAsiento'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt>0){
					$mysqli->query("DELETE FROM CTConciliacionLog WHERE RutEmpresa='$RutEmpresa' AND IdDiario='$IdLineaAsiento'");
				}
			}

			//////// SI fallaron las EVALUACIONES anteriores 
			if($IdLineaAsiento==0){
				$SqlBus="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND fecha='$CFecha' AND cuenta='$CCta' AND debe='$CCargos'";
				$Resbus = $mysqli->query($SqlBus);
				while ($RegBus = $Resbus->fetch_assoc()) {
					$KeyAsLD=$RegBus['keyas'];
					$IdLineaAsiento=$RegBus['id'];
				}
			}

			if($IdLineaAsiento>0){
				$SqlBus="SELECT * FROM CTConciliacionLog WHERE RutEmpresa='$RutEmpresa' AND IdCab='$IdCab' AND IdDiario='$IdLineaAsiento'";
				$Resbus = $mysqli->query($SqlBus);
				$row_cnt = $Resbus->num_rows;
				if($row_cnt==0){
					$mysqli->query("INSERT INTO CTConciliacionLog VALUES('','$RutEmpresa','$IdCab','$CIdDet','$IdLineaAsiento','$KeyAsLD','A')");
				}
			}
		}
	}

    $url = $_SERVER['HTTP_REFERER'];
    $path = parse_url($url, PHP_URL_PATH);
    $file = basename($path);

	header('Location:'.$file);