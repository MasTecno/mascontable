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

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQS="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQS);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];	
		}
		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];	
		}
		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];	
		}	
		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];	
		}
	}

	$Sw="S";
	$SQL="SELECT * FROM CTAsientoFondo WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt==0) {
		$SQL="SELECT * FROM CTAsientoFondo WHERE rut_empresa=''";

		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$Sw="N";
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

		<style>

		</style>
		<script type="text/javascript">

			function Asignaciones(){
				var url= "frmAsignaFondoResumen.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#TablaResumen').html(resp);
					}
				});	
			}
			$( function() {
				$( "#fdesde1" ).datepicker();
				$( "#fdesde" ).datepicker();
			} );
			function Ver(valor){
				form1.IdAsiga.value=valor;
				form1.action="frmAsignaFondoReporte.php";
				form1.submit();
			}
			function Config(){
				form1.action="frmAsignaFondoConf.php";
				form1.submit();				
			}
			function Cerrar(v1,v2,v3){
				form1.idcierre.value=v1;
				form1.montoc.value=v2;
				form1.opera.value=v3;
				$('#ModCierre').modal('show');
			}
			function GrabCie(){
				form1.action="xfrmAsignaFondoCierre.php";
				form1.submit();
			}

		</script>
	</head>
	<body onload="Asignaciones()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-sm-12 text-left">
			<form name="form1" id="form1" method="POST" action="xfrmAsignaFondo.php">

				<div class="col-md-6">

					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading text-center">Antecedentes</div>
						<div class="panel-body">

							<div  class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Fecha</span>
									<input id="fdesde" name="fdesde" type="text" class="form-control text-right" size="10" maxlength="10" value="<?php echo "01-".$Periodo; ?>">
									<input type="hidden" name="IdAsiga" id="IdAsiga">
								</div>								
							</div>

							<div  class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Asignar a</span>
									<select class="form-control" id="SelAsignar" name="SelAsignar" required onchange="Asignaciones()">
										<option value="">Seleccione</option>
										<?php
											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

											$SQL="SELECT * FROM CTFondoPersonal WHERE Estado='A' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' ORDER BY Nombre";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												echo '<option value="'.$registro['Id'].'">'.$registro['Rut'].' - '.$registro['Nombre'].'</option>';
											}
											$mysqli->close();
										?>
									</select>
								</div>
							</div>

							<div class="clearfix"></div>
							<br>

							<div  class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Titulo</span>
										<input class="form-control" type="text" name="titulo" id="titulo" onChange="javascript:this.value=this.value.toUpperCase();" required> 
								</div>
							</div>
							<div class="clearfix"></div>
							<br>

							<div  class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Monto</span>
										<input class="form-control text-right" type="number" name="monto" id="monto" required> 
								</div>
							</div>
							<div class="clearfix"></div>
							<br>
							<div class="col-md-12 text-right">
								<button type="submit" class="btn btn-grabar btn-sm" <?php if($Sw=="N"){ echo "disabled";} ?>>
									<span class="glyphicon glyphicon-ok"></span> Procesar 
								</button>
								<button type="button" class="btn btn-modificar btn-sm" onclick="Config()">
									<span class="glyphicon glyphicon-cog"></span> Configuraci&oacute;n 
								</button>
							</div>


						</div>
					</div>

				</div>


<!-- 				<div class="clearfix"></div>
				<br> -->
 
				<div class="col-md-6">

					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading text-center">Registros</div>
						<div class="panel-body">
							<!-- <div class="input-group"> -->
								<label class="checkbox-inline">
									<input type="checkbox" value="" id="Todos" name="Todos" onclick="Asignaciones()">Mostrar Todos
								</label>

								<table class="table table-hover">
								<thead>
								<tr>
									<th>Fecha</th>
									<th>Titulo</th>
									<th>Monto</th>
									<th>Utilizado</th>
									<th width="1%">Reporte</th>
									<th width="1%">Cerrar</th>
								</tr>
								</thead>
								<tbody id="TablaResumen">

								</tbody>
								</table>
							<!-- </div> -->
						</div>
					</div>
					
					<div class="modal fade" id="ModCierre" role="dialog">
						<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Cierre de Fondo</h4>
							</div>
							<div class="modal-body">
								<div  class="col-md-6">
									<div class="input-group">
										<span class="input-group-addon">Fecha</span>
										<input id="fdesde1" name="fdesde1" type="text" class="form-control hasDatepicker1 text-right" size="10" maxlength="10" value="<?php echo "01-".$Periodo; ?>">
										<input type="hidden" name="idcierre" id="idcierre">
										<input type="hidden" name="opera" id="opera">
									</div>								
								</div>
								<div  class="col-md-6">
									<div class="input-group">
										<span class="input-group-addon">Monto</span>
										<input id="montoc" name="montoc" type="text" class="form-control text-right" size="10" maxlength="10" value="" disabled>
									</div>								
								</div>
								<div class="clearfix"></div>
								<br>
								<div  class="col-md-12">
									<div class="input-group">
										<span class="input-group-addon">Glosa</span>
										<input id="titulo1" name="titulo1" type="text" class="form-control text-right" value="" onChange="javascript:this.value=this.value.toUpperCase();">
									</div>								
								</div>
								<div class="clearfix"></div>
								<br>
							</div>
							<div class="modal-footer">

								<button type="button" class="btn btn-grabar btn-sm" onclick="GrabCie()">
									<span class="glyphicon glyphicon-ok"></span> Grabar 
								</button>

								<button type="button" class="btn btn-cancelar btn-sm" data-dismiss="modal">
									<span class="glyphicon glyphicon-remove"></span> Cerrar
								</button>

							</div>
						</div>
						</div>
					</div>



				</div>

				<div class="clearfix"></div>
				<br>
			</form>
			</div>

		</div>
		</div>

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
				}
			});
			$( "#fdesde1" ).datepicker({
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
					$('#fdesde1').val(dateText);
				}
			});
		</script>


		<?php include '../footer.php'; ?>

	</body>

</html>

