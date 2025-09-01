<?php
	$sw=1;
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTEmpresas WHERE rut='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$rut=$registro["rut"];
		$razonsocial=$registro["razonsocial"];
		$rutrep=$registro["rut_representante"];
		$representante=$registro["representante"];
		$direccion=$registro["direccion"];
		$giro=$registro["giro"];
		$ciudad=$registro["ciudad"];
		$correo=$registro["correo"];
		$pinicio=$registro["periodo"];
		$pcomprobante=$registro["comprobante"];
		$pplancta=$registro["plan"];
	}
	$mysqli->close();
?>

<div class="modal fade" id="ModDatosEmp" role="dialog">
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Actualizar Datos Empresa</h4>
		</div>
		<div class="modal-body">



				<div class="panel panel-default">
				<div class="panel-heading">Informaci&oacute;n de la Empresa</div>
				<div class="panel-body">

					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Rut </span>
							<input type="text" class="form-control" id="rut" autocomplete="off" name="rut" onChange="javascript:this.value=this.value.toUpperCase();" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" value="<?php echo $rut; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>
						</div>

						

					</div> 
					<div class="clearfix"> </div>
					<br>

					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Raz&oacute;n Social</span>
							<input type="text" class="form-control" autocomplete="off" id="rsocial" name="rsocial" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $razonsocial; ?>" required>
						</div>
					</div>

					<div class="clearfix"> </div>
					<br>
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Rut Repre.</span>
							<input type="text" class="form-control" id="rutrep" autocomplete="off" name="rutrep" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" value="<?php echo $rutrep; ?>" required>
						</div>
					</div> 
					<div class="clearfix"> </div>
					<br>

					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Representante Legal</span>
							<input type="text" class="form-control" autocomplete="off" id="representante" name="representante" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $representante; ?>" required>
						</div>
					</div>

					<div class="clearfix"> </div>
					<br>
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Direcci&oacute;n</span>
							<input type="text" class="form-control" autocomplete="off" id="direccion" name="direccion" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $direccion; ?>" required>
						</div>
					</div>

					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Giro</span>
							<input type="text" class="form-control" autocomplete="off" id="giro" name="giro" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $giro; ?>" required>
						</div>            
					</div>

					<div class="clearfix"> </div>
					<br>
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Ciudad</span>
							<input type="text" class="form-control" id="ciudad" autocomplete="off" name="ciudad" maxlength="50" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $ciudad; ?>" required>
						</div>
					</div>

					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Correo</span>
							<input type="email" class="form-control" id="correo" autocomplete="off" name="correo" maxlength="50" value="<?php echo $correo; ?>">
						</div>
					</div>
				</div>
				</div>


		</div>
		<div class="modal-footer">

			<script type="text/javascript">
				function DatEmp(){
					form1.ModUpdEmp.value="Empr";
					form1.action="xfrmMainModDatos.php";
					form1.submit();
				}
			</script>

			<button type="button" class="btn btn-default" onclick="DatEmp()">Actualizar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

		</div>
	</div>
</div>
</div>