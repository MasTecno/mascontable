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

    $Ano=substr($_SESSION['PERIODO'],3);

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	function UltimoDiaMesD($LPer) { 
		$month = substr($LPer,0,2);
		$year = substr($LPer,3,4);
		$day = date("d", mktime(0,0,0, $month+1, 0, $year));

		return date('d', mktime(0,0,0, $month, $day, $year));
	};

	$UDia=UltimoDiaMesD($Periodo);

	$Xfdesde="01-".$Periodo;
	$Xfhasta=$UDia."-".$Periodo;

	$SwMes="";
	if ($_POST['swImport']=="S") {
		if (isset($_POST['action'])) {
			$action=$_POST['action'];
		}

		$Msj="";
		$STRInseert="";
		if (isset($action)== "upload" && $_FILES['excel']['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
			//cargamos el fichero
			$archivo = $_FILES['excel']['name'];
			$tipo = $_FILES['excel']['type'];
			$destino = "Temp_".$archivo;//Le agregamos un prefijo para identificarlo el archivo cargado
			if (copy($_FILES['excel']['tmp_name'],$destino)){
				// echo "Archivo Cargado Con Éxito<br>";
			}else{
				$Msj="Error Al Cargar el Archivo";
			}

			if (file_exists ("Temp_".$archivo) && $Msj==""){
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

                $dia = substr($_POST['fdesde'],0,2);
                $mes = substr($_POST['fdesde'],3,2);
                $ano = substr($_POST['fdesde'],6,4);

                $LFdesde=$ano."/".$mes."/".$dia;

                $dia = substr($_POST['fhasta'],0,2);
                $mes = substr($_POST['fhasta'],3,2);
                $ano = substr($_POST['fhasta'],6,4);

                $LFhasta=$ano."/".$mes."/".$dia;
				$mysqli->query("INSERT INTO CTConciliacionCab VALUES('','$RutEmpresa','$LFdesde','$LFhasta','".$_POST['seleccue']."','".$_POST['nombre']."','".date('Y-m-d')."','A')");

				$FolioCab=0;
				$SqlStr="SELECT max(id) as MaxId FROM CTConciliacionCab LIMIT 1";
				$Resultado = $mysqli->query($SqlStr);
				while ($Registro = $Resultado->fetch_assoc()) {
					$FolioCab=$Registro['MaxId'];
				}

				for ($i=2;$i<=$filas;$i++){

					$format = "Y-m-d"; 

					$cell = $objPHPExcel->getActiveSheet()->getCell('A'. $i);	
					$InvDate= $cell->getValue();
					if(PHPExcel_Shared_Date::isDateTime($cell)) {
						$InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
						$Fecha = date("Y-m-d",strtotime($InvDate."+ 1 days")); 
					}

					$_DATOS_EXCEL[$i]['Descripcion'] = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Cargos'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Abonos'] = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Rut'] = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Numero'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();

					if($_DATOS_EXCEL[$i]['Descripcion']!="" && ($_DATOS_EXCEL[$i]['Cargos']>0 || $_DATOS_EXCEL[$i]['Abonos']>0))
		 			    $STRInseert=$STRInseert."INSERT INTO CTConciliacionDet VALUES('','$FolioCab','$RutEmpresa','$Fecha','".strtoupper($_DATOS_EXCEL[$i]['Descripcion'])."','".$_DATOS_EXCEL[$i]['Cargos']."','".$_DATOS_EXCEL[$i]['Abonos']."','".$_DATOS_EXCEL[$i]['Rut']."','".$_DATOS_EXCEL[$i]['Numero']."','A'); ";
				}

				if ($Msj=="") {
					$mysqli->multi_query($STRInseert);
				}

				unlink($destino);
				$mysqli->close();
				header("location:../Conciliacion");
			}else{
				$Msj="Primero debes cargar el archivo con extencion .xlsx";
			}
		}else{
			$Msj="Primero debes cargar el archivo con extencion .xlsx";
		}
	}
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
			function ActivaBtn(){
				if (document.getElementById("BtnVisual").style.visibility == "hidden") {
					document.getElementById("BtnVisual").style.visibility = "visible";
				}else{
					document.getElementById("BtnVisual").style.visibility = "hidden";
				}
			}
			function ImportaCart(){
				if(form1.seleccue.value==""){
					alert("Seleccione la cuenta de Banco");
				}else{
					form1.swImport.value="S";
					form1.submit();
				}
			}
            $( function() {
                $("#fdesde").datepicker();
                $("#fhasta").datepicker();
            });
		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form name="form1" method="post" action="" enctype="multipart/form-data">
			<br>

            <div class="col-sm-2">
			</div>
			<div class="col-sm-8">

				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">IMPORTAR CARTOLA BANCARIA</div>
					<div class="panel-body">

						<div class="col-md-12">
							<a href="EjemploConciliacion.xlsx" class="btn btn-exportar btn-block" role="button">Descargar plantilla de Carga Aqu&iacute;</a>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Cartola</span>
								<input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="CARTOLA PERIODO <?php echo $Xfdesde; ?> A <?php echo $Xfhasta; ?>"  autocomplete="off" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Cuenta</span>
								<select class="form-control" id="seleccue" name="seleccue" require>
								<option value="">Todas Cuentas</option>
								<?php
									$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
									if ($_SESSION["PLAN"]=="S"){
										$sqlin = "SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' AND auxiliar='B' ORDER BY detalle";
									}else{
										$sqlin = "SELECT * FROM CTCuentas WHERE estado='A' AND auxiliar='B' ORDER BY detalle";
									}
									$resultadoin = $mysqli->query($sqlin);

									while ($registro = $resultadoin->fetch_assoc()) {
										echo "<option value='".$registro['numero']."'>".$registro['numero']." - ". strtoupper($registro['detalle'])."</option>";
									}
									$mysqli->close();
								?>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Desde</span>
								<input id="fdesde" name="fdesde" type="text" class="form-control text-right" value="<?php echo $Xfdesde; ?>" size="10" maxlength="10">
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Hasta</span>
								<input id="fhasta" name="fhasta" type="text" class="form-control text-right" value="<?php echo $Xfhasta; ?>" size="10" maxlength="10">
							</div>
						</div>
						<div class="clearfix"></div>
						<br>



						<div class="col-md-12">
							<div class="text-center">
								<div class="form-group">
									<input type="file" class="filestyle" data-buttonText="Seleccione archivo" name="excel">
								</div>
								<input type="hidden" value="upload" name="action">
								<input type="hidden" name="swImport" id="swImport">

								<?php
									if ($Msj!="") {
										echo "<h2>".$Msj."</h2>";
									}
								?>

								<div class="clearfix"></div>
								<br><br>
							</div>

						</div>
						<div class="col-md-12 text-right">
							<input class="btn btn-grabar" type='button' name='BtnVisual' onclick="ImportaCart()" id="BtnVisual" value="Importar"  />
						</div>

					</div>
				</div>

				<div class="clearfix"></div>
				<br>				

			</div>
		</form>
		</div>
		</div>
		<script type="text/javascript">
		<?php
			if ($SwMes=="N") {
				echo 'alert("A ocurrido un error, favor contactar con soporte.")';
			}
			if ($SwMes=="S") {
				echo 'alert("Se a completado la operaci\u00F3n con exito.")';
			}
		?>
		</script>
		<script type="text/javascript">
			$( "#fdesde" ).datepicker({
				// Formato de la fecha
				dateFormat: "dd-mm-yy",
				// Primer dia de la semana El lunes
				firstDay: 1,
				// Dias Largo en castellano
				dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
				// Dias cortos en castellano
				dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
				// Nombres largos de los meses en castellano
				monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
				// Nombres de los meses en formato corto 
				monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
				// Cuando seleccionamos la fecha esta se pone en el campo Input 
				onSelect: function(dateText) { 
					// $('#d1').val(dateText);
					// $('#d2').focus();
					// $('#d2').select();
				}
			});

			$( "#fhasta" ).datepicker({
				// Formato de la fecha
				dateFormat: "dd-mm-yy",
				// Primer dia de la semana El lunes
				firstDay: 1,
				// Dias Largo en castellano
				dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
				// Dias cortos en castellano
				dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
				// Nombres largos de los meses en castellano
				monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
				// Nombres de los meses en formato corto 
				monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
				// Cuando seleccionamos la fecha esta se pone en el campo Input 
				onSelect: function(dateText) { 
					// $('#d1').val(dateText);
					// $('#d2').focus();
					// $('#d2').select();
				}
			});       
		</script>
		<?php include '../footer.php'; ?>

	</body>

</html>

