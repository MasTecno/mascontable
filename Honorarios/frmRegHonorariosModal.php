<style>
.modal-footer {
    padding: 15px;
    text-align: right;
    border-top: 1px solid #FFFFFF;
}
</style>
 

<!-- line modal --> 
<div class="modal fade" id="squarespaceModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="lineModalLabel">Centralizar Honorario</h4>
		</div>
		<div class="modal-body">
			<!-- content goes here -->
			<form action="#" method="POST" name="fmodal" id="fmodal">

			<div class="col-md-3">
				<label>Fecha</label>
				<input id="mfecha" name="mfecha" type="text" class="form-control" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
			</div> 
			
			<div class="col-md-4">
				<input type="hidden" name="iddoc" id="iddoc" value="">
				<input type="hidden" name="canBruto" id="canBruto" value="">
				<input type="hidden" name="canRete" id="canRete" value="">
				<input type="hidden" name="canRete3" id="canRete3" value="">
				<input type="hidden" name="canLiqui" id="canLiqui" value="">
				<input type="hidden" name="NDoc" id="NDoc" value="">
				<input type="hidden" name="NCuenta" id="NCuenta" value="">
				<input type="hidden" name="TDoc" id="TDoc" value="">
				<input type="hidden" name="Cadena" id="Cadena" value="">
				<input type="hidden" name="RutHono" id="RutHono" value="">
			</div>

			<div class="clearfix"></div>

			<div class="col-md-2">
				<label>Cuenta</label>
				<input type="text" class="form-control" id="mcuenta1" name="mcuenta1" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL1; ?>"> 
				<input type="text" class="form-control" id="mcuenta2" name="mcuenta2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL2; ?>">
				<input type="text" class="form-control" id="mcuenta3" name="mcuenta3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL3; ?>">
				<input type="text" class="form-control" id="mcuenta4" name="mcuenta4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL4; ?>">
			</div>
			<div class="col-md-4">
				<label>Detalle</label>  
				<input type="text" class="form-control" id="mdetalle1" name="mdetalle1" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL1); ?>"  readonly="false" >
				<input type="text" class="form-control" id="mdetalle2" name="mdetalle2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL2); ?>"  readonly="false" >
				<input type="text" class="form-control" id="mdetalle3" name="mdetalle3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL3); ?>"  readonly="false" >
				<input type="text" class="form-control" id="mdetalle4" name="mdetalle4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL4); ?>"  readonly="false" >
			</div>
			<div class="col-md-2">
				<label>Debe</label>
				<input type="text" class="form-control text-right" id="mdebe1" name="mdebe1" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
				<input type="text" class="form-control text-right" id="mdebe2" name="mdebe2" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
				<input type="text" class="form-control text-right" id="mdebe3" name="mdebe3" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
				<input type="text" class="form-control text-right" id="mdebe4" name="mdebe4" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
			</div>
			<div class="col-md-2">
				<label>Haber</label>
				<input type="text" class="form-control text-right" id="mhaber1" name="mhaber1" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
				<input type="text" class="form-control text-right" id="mhaber2" name="mhaber2" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
				<input type="text" class="form-control text-right" id="mhaber3" name="mhaber3" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
				<input type="text" class="form-control text-right" id="mhaber4" name="mhaber4" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
			</div>
			<div class="col-md-2">
				<label>C. Costo</label>
				<select class="form-control" id="tccosto1" name="tccosto1">
					<option value="0"></option>
					<?php
						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

						$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
						}
					?>
				</select>
				<select class="form-control" id="tccosto2" name="tccosto2">
					<option value="0"></option>
					<?php
						$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
						}
					?>
				</select>
				<select class="form-control" id="tccosto3" name="tccosto3">
					<option value="0"></option>
					<?php
						$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
						}
					?>
				</select>
				<select class="form-control" id="tccosto4" name="tccosto4">
					<option value="0"></option>
					<?php
						$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
						}
					?>
				</select>
			</div>

			<div class="clearfix"></div>
			
			<div class="col-md-8">
				<div id="msj"></div>         
			</div>

			<div class="clearfix"> </div>
			<div class="col-md-12">
				<label>Glosa</label>
				<input type="text" class="form-control" id="Glosa" name="Glosa" maxlength="50" value="" onChange="javascript:this.value=this.value.toUpperCase();">
			<p></p> 
			</div>

			</form>

		</div>
		<div class="modal-footer">
			<div class="btn-group btn-group-justified" role="group" aria-label="group button">
			<div class="btn-group" role="group">
			<button type="button" class="btn btn-danger" data-dismiss="modal" role="button" id="CMOD">Cancelar</button>
			</div>

			<div class="btn-group" role="group">
			<button type="button" id="saveImage" class="btn btn-success btn-hover-green" data-action="save" role="button" onclick="GBDocumCent()">Grabar</button>
			</div>
			</div>
		</div>
	</div>
	</div>
</div>