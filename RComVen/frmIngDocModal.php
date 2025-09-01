<style>
.modal-footer {
    padding: 15px;
    text-align: right;
    border-top: 1px solid #FFFFFF;
}
</style>
 
<?php 
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	if($frm=="C"){

		$SQL="SELECT * FROM CTAsiento WHERE tipo='C' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);

		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$SQL="SELECT * FROM CTAsiento WHERE tipo='C' AND rut_empresa=''";
		}

		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XL1=$registro['L1'];
			$XL2=$registro['L2'];
			$XL3=$registro['L3'];
			$XL4=$registro['L4'];
			$XL5=$registro['L5'];
		}
		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL1."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL1."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL1=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL2."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL2."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL2=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL3."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL3."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL3=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL4."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL4."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL4=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL5."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL5."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL5=$registro1['detalle'];
		}
	}else{
		$SQL="SELECT * FROM CTAsiento WHERE tipo='V' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);

		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$SQL="SELECT * FROM CTAsiento WHERE tipo='V' AND rut_empresa=''";
		}

		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XL1=$registro['L1'];
			$XL2=$registro['L2'];
			$XL3=$registro['L3'];
			$XL4=$registro['L4'];
			$XL5=$registro['L5'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL1."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL1."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL1=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL2."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL2."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL2=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL3."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL3."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL3=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL4."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL4."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL4=$registro1['detalle'];
		}

		if ($_SESSION["PLAN"]=="S"){
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$XL5."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='".$XL5."'";
		}
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnL5=$registro1['detalle'];
		}
	}
	$mysqli->close();
?>

<!-- line modal --> 
<div class="modal fade" id="squarespaceModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="lineModalLabel">Centralizar Factura</h4>
		</div>
		<div class="modal-body">
			<!-- content goes here -->
			<form action="#" method="POST" name="fmodal" id="fmodal">

			<div class="col-md-3">
				<label>Fecha</label>
				<input id="mfecha" name="mfecha" type="text" class="form-control" size="10" maxlength="10" value="<?php echo $textfecha1; ?>">
			</div>

			<?php
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
				
				$SwAFondo="N";
				$SQL="SELECT * FROM CTFondo WHERE estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='I'";
				$resultados = $mysqli->query($SQL);
				$row_cnt = $resultados->num_rows;
				if ($row_cnt>0) {
					$SwAFondo="S";
				}

				$mysqli->close();
			?>

			<?php if ($SwAFondo=="S" && $frm=="C"): ?>
				<?php
					if ($SwCCosto=="S"){
						echo '<div class="col-md-4">';
					}else{
						echo '<div class="col-md-8">';
					}

				?>

				<!-- <div class="col-md-4"> -->
					<!-- <labelto">Fondos Abierto</label>
					<select class="form-control" id="FAbierto" name="FAbierto">
						<option value=""></option>
						<?php
							// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

							// $SQL="SELECT * FROM CTFondo WHERE estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='I' ORDER BY Fecha";
							// $resultados = $mysqli->query($SQL);
							// while ($registro = $resultados->fetch_assoc()) {
								// $NomPersonal="";
								// $SQL1="SELECT * FROM CTFondoPersonal WHERE RutEmpresa='$RutEmpresa' AND id='".$registro['IdPersonal']."'";
								// $resultados1 = $mysqli->query($SQL1);
								// while ($registro1 = $resultados1->fetch_assoc()) {
								// 	$NomPersonal=$registro1['Nombre'];
								// }

								// $MontoEgreso=0;
								// $TotFondo=0;
								// $SQL1="SELECT * FROM CTFondo WHERE RutEmpresa='$RutEmpresa' AND IdPersonal='".$registro['Id']."' AND Tipo='E'";
								// $resultados1 = $mysqli->query($SQL1);
								// while ($registro1 = $resultados1->fetch_assoc()) {
								// 	$MontoEgreso=$MontoEgreso+$registro1['Monto'];
								// }

								// $TotFondo=$registro['Monto']-$MontoEgreso;

								// echo '<option value="'.$registro['Id'].'">'.$NomPersonal.' - '.$registro['Titulo'].' ('.$TotFondo.')</option>';
							// }
							// $mysqli->close();
						?>
					</select> -->
				</div>
			<?php endif ?>



			<div class="col-md-4">
				<input type="hidden" name="NFactura" id="NFactura" value="">
				<input type="hidden" name="TotalAsi" id="TotalAsi" value="">
				<input type="hidden" name="IdDoc" id="IdDoc" value="">
				<div id="msjx"></div>          
			</div>

			<div class="clearfix"></div>

			<div class="col-md-2">
				<labela">Cuenta</labelto>
				<input type="text" class="form-control" id="mcuenta1" name="mcuenta1" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL1; ?>">
				<input type="text" class="form-control" id="mcuenta2" name="mcuenta2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL2; ?>">
				<input type="text" class="form-control" id="mcuenta3" name="mcuenta3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL3; ?>">
				<input type="text" class="form-control" id="mcuenta4" name="mcuenta4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL4; ?>">
				<input type="text" class="form-control" id="mcuenta5" name="mcuenta5" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $XL5; ?>">
			</div>
			<div class="col-md-4">
				<labelle">Detalle</labela>  
				<input type="text" class="form-control" id="mdetalle1" name="mdetalle1" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL1); ?>"  readonly="false" >
				<input type="text" class="form-control" id="mdetalle2" name="mdetalle2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL2); ?>"  readonly="false" >
				<input type="text" class="form-control" id="mdetalle3" name="mdetalle3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL3); ?>"  readonly="false" >
				<input type="text" class="form-control" id="mdetalle4" name="mdetalle4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL4); ?>"  readonly="false" >
				<input type="text" class="form-control" id="mdetalle5" name="mdetalle5" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnL5); ?>"  readonly="false" >
			</div>
			<div class="col-md-2">
				<label>Debe</label>
				<input type="text" class="form-control text-right" id="mdebe1" name="mdebe1" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
				<input type="text" class="form-control text-right" id="mdebe2" name="mdebe2" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
				<input type="text" class="form-control text-right" id="mdebe3" name="mdebe3" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
				<input type="text" class="form-control text-right" id="mdebe4" name="mdebe4" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
				<input type="text" class="form-control text-right" id="mdebe5" name="mdebe5" maxlength="50" value="" onKeyPress="return soloNumeros(event)" >
			</div>
			<div class="col-md-2">
				<label>Haber</labelle>
				<input type="text" class="form-control text-right" id="mhaber1" name="mhaber1" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
				<input type="text" class="form-control text-right" id="mhaber2" name="mhaber2" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
				<input type="text" class="form-control text-right" id="mhaber3" name="mhaber3" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
				<input type="text" class="form-control text-right" id="mhaber4" name="mhaber4" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
				<input type="text" class="form-control text-right" id="mhaber5" name="mhaber5" maxlength="50" value="" onKeyPress="return soloNumeros(event)">
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
				<select class="form-control" id="tccosto5" name="tccosto5">
					<option value="0"></option>
					<?php
						$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
						}
						$mysqli->close();
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
				<input type="hidden" name="swboton" id="swboton">
			<p></p> 
			</div>

			</form>

		</div>
		<div class="modal-footer">
			<div class="btn-group btn-group-justified" role="group" aria-label="group button">
				<div class="btn-group" role="group">
					<button type="button" class="btn btn-cancelar" data-dismiss="modal" role="button" id="CMOD">Cancelar</button>
				</div>

				<div class="btn-group" role="group">
					<button type="button" id="BtnGraba" name="BtnGraba" class="btn btn-grabar" data-action="save" role="button" onclick="GBDocumCent()">Grabar</button>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>