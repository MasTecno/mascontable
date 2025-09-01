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
	if(isset($_SESSION['EdiCon']) && $_SESSION['EdiCon']!=""){
		$IdCab=$_SESSION['EdiCon'];
	}else{
		$IdCab=$_POST['EdiCon'];
	}

	if(isset($_POST['IdModal']) && $_POST['IdModal']!="" && $_POST['IdModal']>0){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTConciliacionDet SET Glosa='".$_POST['tglosa']."', Rut='".$_POST['trut']."', Numero='".$_POST['tnumero']."' WHERE Id='".$_POST['IdModal']."'");
		$mysqli->close();
	}

	if(isset($_POST['TEliCar']) && $_POST['TEliCar']!=""){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("DELETE FROM CTConciliacionDet WHERE Id='".$_POST['TEliCar']."'");
		$mysqli->close();
	}
	if(isset($_POST['IdAsociar']) && $_POST['IdAsociar']!=""){

		$keyas=$_POST['IdAsociar'];

		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		// $SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '".$_POST['IdAsociar']."' AND cuenta='0' AND rutempresa='$RutEmpresa' AND glosa<>''";
		// $Resbus = $mysqli->query($SqlBus);
		// while ($RegBus = $Resbus->fetch_assoc()) {
		// 	$Glosa=$RegBus['glosa'];
		// 	$ncomprobante=$RegBus['ncomprobante'];
		// 	$fecha=$RegBus['fecha'];
		// }

		// echo $RutEmpresa."rut";
		// echo "<br>";
		// echo $IdCab."id cartola";
		// echo "<br>";
		$IdDet=$_POST['iddetconci'];
		// echo $_POST['iddetconci']."<br>";





		
		
		$SqlStr="SELECT * FROM CTConciliacionCab WHERE Id='$IdCab' AND RutEmpresa='$RutEmpresa'";
		$Resultado = $mysqli->query($SqlStr);
		while ($Registro = $Resultado->fetch_assoc()) {
			$CCta=$Registro['Cuenta'];
		}

		$SqlStr="SELECT * FROM CTConciliacionDet WHERE IdCab='$IdCab' AND Id='$IdDet' AND RutEmpresa='$RutEmpresa'";
		$Resultado = $mysqli->query($SqlStr);
		while ($Registro = $Resultado->fetch_assoc()) {
			$FUpdateLD=$Registro['Fecha'];
		}

		

		$SqlBus="SELECT * FROM CTRegLibroDiario WHERE keyas LIKE '$keyas' AND cuenta='$CCta' AND rutempresa='$RutEmpresa'";
		$Resbus = $mysqli->query($SqlBus);
		$row_cnt = $Resbus->num_rows;
		if($row_cnt>0){
			$Resbus = $mysqli->query($SqlBus);
			while ($RegBus = $Resbus->fetch_assoc()) {
				$IdLineaAsiento=$RegBus['id'];
			}				
		}

		$Sql="UPDATE CTRegLibroDiario SET fecha='$FUpdateLD' WHERE keyas LIKE '$keyas' AND rutempresa='$RutEmpresa'";
		$mysqli->query($Sql);


		$Sql="INSERT INTO CTConciliacionLog VALUES('','$RutEmpresa','$IdCab','$IdDet','$IdLineaAsiento','$keyas','A')";
		$mysqli->query($Sql);
		// exit;
	}	

	$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
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
			function reprocesar(){
				form1.action="Proce.php";
				form1.submit();
			}
			function reporte(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmCartolaPDF.php";
				form1.submit();
				form1.target="";
				form1.action="#";
			}
			function exportar(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmCartolaXLS.php";
				form1.submit();
				form1.target="";
				form1.action="#";
			}
			function volver(){
				form1.action="./";
				form1.submit();
			}

			function ModCar(v1){
				form1.IdCart.value=v1;
				var url= "DatosModal.php";
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					success:function(resp){
						$("#tfecha").val(resp.dato1);
						$("#tglosa").val(resp.dato2);
						$("#tcargo").val(resp.dato3);
						$("#tabono").val(resp.dato4);
						$("#trut").val(resp.dato5);
						$("#tnumero").val(resp.dato6);
						$("#IdModal").val(resp.dato7);
						form1.IdCart.value="";
					}
				});	
			}

			function Visualizar(){
				var url= "modalAsientos.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#modalAsientos').html(resp);
					}
				});	
			}
			
			function EliCar(v1){
				if (confirm("Esta seguro de eliminar el registro!") == true) {
					form1.TEliCar.value=v1;
					form1.submit();		
				}
			}

			function Grmodal(){
				form1.submit();
			}

			function Asociar(u1,u2,u3){
				form1.iddetconci.value=u1;
				form1.monconciAbono.value=u2;
				form1.monconciCargo.value=u3;
				Visualizar();
			} 

			function AsociarAsiento(i1){
				if (confirm("Esta seguro de asociar el movimiento con el asiento?") == true) {
					form1.IdAsociar.value=i1;
					form1.submit();		
				}	
			}

		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<br>

            <div class="col-sm-12">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading text-center">Detalle Cartolas </div>
					<div class="panel-body">
						<input type="hidden" name="TEliCar" id="TEliCar">
						<input type="hidden" name="IdAsociar" id="IdAsociar">

						<div class="col-md-12 text-right">
							<input type="hidden" name="EdiCon" id="EdiCon" value="<?php echo $IdCab; ?>">
							<input type="hidden" name="IdCart" id="IdCart" value="">

							<button type="button" class="btn btn-grabar" onclick="reprocesar()">
								<span class="glyphicon glyphicon-edit"></span>  ReProcesar
							</button>

							<button type="button" class="btn btn-exportar" onclick="reporte()">
								<span class="glyphicon glyphicon-file"></span>  Descargar Reporte XLS
							</button>

							<button type="button" class="btn btn-exportar" onclick="exportar()">
								<span class="glyphicon glyphicon-file"></span>  Exportar Base XLS
							</button>

							<button type="button" class="btn btn-cancelar" onclick="volver()">
								<span class="glyphicon glyphicon-remove"></span>  Volver
							</button>
						</div>

							<div class="modal fade" id="myModal" role="dialog">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Modificación de Registro Bancario</h4>
									</div>
									<div class="modal-body">

										<div class="col-md-6">
										<div class="input-group">
												<span class="input-group-addon">Fecha</span>
												<input id="tfecha" name="tfecha" type="text" class="form-control text-right" value="" maxlength="10" readonly>
											</div>
										</div>
										<div class="clearfix"></div>
										<br>									


										<div class="col-md-12">
										<div class="input-group">
											<span class="input-group-addon">Glosa</span>
											<input type="text" class="form-control" id="tglosa" name="tglosa" onChange="javascript:this.value=this.value.toUpperCase();" value=""  autocomplete="off" required>
										</div>
										</div> 									
										<div class="clearfix"></div>
										<br>

										<div class="col-md-6">
										<div class="input-group">
												<span class="input-group-addon">Cargo</span>
												<input id="tcargo" name="tcargo" type="text" class="form-control text-right" value="" readonly>
										</div>
										</div>

										<div class="col-md-6">
										<div class="input-group">
												<span class="input-group-addon">Abono</span>
												<input id="tabono" name="tabono" type="text" class="form-control text-right" value="" readonly>
										</div>
										</div>
										<div class="clearfix"></div>
										<br>

										<div class="col-md-6">
										<div class="input-group">
												<span class="input-group-addon">Rut</span>
												<input id="trut" name="trut" type="text" class="form-control text-right" maxlength="10" value="">
										</div>
										</div>
										<div class="col-md-6">
										<div class="input-group">
												<span class="input-group-addon">Numero</span>
												<input id="tnumero" name="tnumero" type="numero" class="form-control text-right" value="">
										</div>
										</div>
										<div class="clearfix"></div>
										<br>

									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-grabar" data-dismiss="modal" onclick="Grmodal()" >Grabar</button>
										<button type="button" class="btn btn-cancelar" data-dismiss="modal">Cancelar</button>
										<input type="hidden" name="IdModal" id="IdModal">
									</div>
								</div>
							</div>
							</div>
					
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<th></th>
									<th colspan="6" style="text-align: center;">Cartola</th>
									<th colspan="2" style="text-align: center;" widht="1%">&nbsp;</th>
									<th colspan="3" style="text-align: center;">Contabilidad</th>
								</tr>
								<tr>
									<th>N°</th>
									<th style="text-align: center;">Fecha</th>
									<th>Glosa</th>
									<th style="text-align: right;">Abono</th>
									<th style="text-align: right;">Cargo</th>
									<th style="text-align: right;">Rut</th>
									<th style="text-align: right;">Número</th>
									<th style="text-align: center;" widht="1%"></th>
									<th style="text-align: center;" widht="1%"></th>
									<th style="text-align: center;">Comprobante</th>
									<th style="text-align: center;">Tipo</th>
									<th>Glosa</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$Cont=1;
									
									$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
									$SqlStr="SELECT * FROM CTConciliacionDet WHERE IdCab='$IdCab' ORDER BY Fecha ASC, Id ASC";
									$Resultado = $mysqli->query($SqlStr);
									while ($Registro = $Resultado->fetch_assoc()) {

										$IdDiario="";
										$keyas="";
										$SStr="SELECT * FROM CTConciliacionLog WHERE IdCab='$IdCab' AND IdDet='".$Registro['Id']."'";
										// echo "<br>";
										$Res = $mysqli->query($SStr);
										while ($Reg = $Res->fetch_assoc()) {
											$IdDiario=$Reg['IdDiario'];
											$keyas=$Reg['keyas'];
										}

										if($keyas==""){
											$SStr="SELECT * FROM CTRegLibroDiario WHERE id='$IdDiario' AND rutempresa='$RutEmpresa' AND glosa<>''";
											$Res = $mysqli->query($SStr);
											while ($Reg = $Res->fetch_assoc()) {
												$keyas=$Reg['keyas'];
											}
										}

										$DComp="";
										$DGlosa="";
										$DTMovio="";
										$SStr="SELECT * FROM CTRegLibroDiario WHERE keyas='".$keyas."' AND rutempresa='$RutEmpresa' AND glosa<>''";
										$Res = $mysqli->query($SStr);
										while ($Reg = $Res->fetch_assoc()) {
											$DComp=$Reg['ncomprobante'];
											$DGlosa=$Reg['glosa'];		

											if ($Reg["tipo"]=="E") {
												$DTMovio="Egreso";
											}
											if ($Reg["tipo"]=="I") {
												$DTMovio="Ingreso";	
											}
											if ($Reg["tipo"]=="T") {
												$DTMovio="Traspaso";
											}
										}
										if($Registro['Numero']>0){
											$DNumDoc=$Registro['Numero'];
										}else{
											$DNumDoc="";
										}

										if($DComp==""){

											$DComp='<button type="button" class="btn btn-modificar btn-xs" onclick="Asociar('.$Registro['Id'].','.$Registro['Abonos'].','.$Registro['Cargos'].')" data-toggle="modal" data-target="#ModAsocia">Asociar Manual</button>';
										}

										echo '
											<tr>
												<td>'.$Cont.'</td>
												<td style="text-align: center;">'.date('d-m-Y',strtotime($Registro['Fecha'])).'</td>
												<td>'.$Registro['Glosa'].'</td>
												<td style="text-align: right;">'.number_format($Registro['Abonos'], $NDECI, $DDECI, $DMILE).'</td>
												<td style="text-align: right;">'.number_format($Registro['Cargos'], $NDECI, $DDECI, $DMILE).'</td>
												<td style="text-align: right;">'.$Registro['Rut'].'</td>
												<td style="text-align: right;">'.$DNumDoc.'</td>
												<td style="text-align: center;"><button type="button" title="Modificar Registro" class="btn btn-modificar btn-xs" onclick="ModCar('.$Registro['Id'].')" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-edit"></span>  </button></td>
												<td style="text-align: center;"><button type="button" title="Eliminar Registro" class="btn btn-cancelar btn-xs" onclick="EliCar('.$Registro['Id'].')"><span class="glyphicon glyphicon-trash"></span>  </button></td>
												<td style="text-align: center;">'.$DComp.'</td>
												<td style="text-align: center;">'.$DTMovio.'</td>
												<td>'.$DGlosa.'</td>
											</tr>
										';
										$Cont++;
									}
									// $mysqli->close();
								?>
							</tbody>
						</table>

							<div id="ModAsocia" class="modal fade" role="dialog">
							<div class="modal-dialog modal-lg">

								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Asociación Manual</h4>
									</div>
									<div class="modal-body">
										<input type="hidden" id="iddetconci" name="iddetconci">
										<input type="hidden" id="monconciAbono" name="monconciAbono">
										<input type="hidden" id="monconciCargo" name="monconciCargo">

										<table class="table table-hover table-bordered">
											<thead>
												<tr>
													<th>N°</th>
													<th style="text-align: center;">Fecha</th>
													<th>Glosa</th>
													<th style="text-align: right;">Monto</th>
													<th></th>
												</tr>
											</thead>
											<tbody id="modalAsientos">

											</tbody>
										</table>
										* El asiento contable será actualizado la fecha a la fecha del movimiento de la cartola<br>
										<!-- * Solo se puede asociar un moviento a  -->
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									</div>
								</div>

							</div>
							</div>

					</div>
				</div>
			</div>

		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>
</html>