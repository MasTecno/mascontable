<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$textfecha=date("d-m-Y");

	if(is_array($_POST['check_list'])){
		if(count($_POST['check_list'])==1){
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
			foreach($_POST['check_list'] as $selected) {
				if ($_POST["tdocumentos"]=="H") {
					$SQL="SELECT * FROM CTHonorarios WHERE id='".$selected."' AND rutempresa='$RutEmpresa'";
				}else{
					$SQL="SELECT * FROM CTRegDocumentos WHERE id='".$selected."' AND tipo='".$_POST["tdocumentos"]."' AND rutempresa='$RutEmpresa'";				
				}
				// echo $SQL;
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$RutBanco=$registro["rut"];
				}
			}
			$mysqli->close();
		}
	}
?>	
	<div class="col-md-12 text-center">
		<h4>Informaci&oacute;n de Pago Documento</h4>
	</div>
	<div class="col-md-12">
	<div class="input-group">
		<span class="input-group-addon">Cuenta</span>
		<select class="form-control" id="cuentaasi" name="cuentaasi">
			<?php
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
				if ($_SESSION["PLAN"]=="S"){
					$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND auxiliar='B' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
				}else{
					$SQL="SELECT * FROM CTCuentas WHERE estado='A' AND auxiliar='B' ORDER BY detalle";
				}

				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					echo '<option value="'.$registro['numero'].'">'.$registro['detalle'].'</option>';
				}
				$mysqli->close();
			?>
		</select>
	</div>
	</div>
	<div class="clearfix"></div>
	<br>

	<div class="col-md-12">
	<div class="input-group">
		<span class="input-group-addon">Entidad</span>
		<select class="form-control" id="tentidad" name="tentidad">
			<option value="">Seleccione</option>
			<?php
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
				$xtipo="";
				if ($_POST['tdocumentos']=="C") {
					$xtipo="P";
				}
				if ($_POST['tdocumentos']=="V") {
					$xtipo="C";
				}
				if ($_POST['tdocumentos']=="H") {
					$xtipo="P";
				}

				$SQL="SELECT * FROM CTCliPro WHERE estado='A' AND tipo='$xtipo' ORDER BY razonsocial";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					if($registro['rut']==$RutBanco){
						echo '<option value="'.$registro['id'].'" selected>'.$registro['razonsocial'].'</option>';
					}else{
						echo '<option value="'.$registro['id'].'">'.$registro['razonsocial'].'</option>';						
					}
				}
				$mysqli->close();
			?>
		</select>
	</div>
	</div>
	<div class="clearfix"></div>
	<br>

	<div class="col-md-12">
	<div class="input-group">
		<label><input type="radio" name="opt1" value="C">Cheque</label> &nbsp
		<label><input type="radio" name="opt1" value="T" checked >Transferencia</label> &nbsp
		<label><input type="radio" name="opt1" value="O" >Otro Documento</label>
	</div>
	</div>

	<div class="col-md-12">
	<div class="input-group">
		<span class="input-group-addon">N&deg; Documento</span>
		<input type="text" class="form-control" name="ndocpago" id="ndocpago">
	</div>
	</div>
	<div class="clearfix"></div>
	<br>

	<div class="col-md-12">
	<div class="input-group">
		<span class="input-group-addon">Fecha</span>
		<input id="fdoc" name="fdoc" type="text" class="form-control text-right" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
	</div>
	</div>
	<div class="clearfix"></div>
	<br>

	<div class="col-md-12">
	<div class="input-group">
		<span class="input-group-addon">Fecha Vencimiento</span>
		<input id="fdocven" name="fdocven" type="text" class="form-control text-right" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
	</div>
	</div>
	<div class="clearfix"></div>
	<br>

	<div class="col-md-12">
		<!-- <p>linea 1</p>
		<table class="table">
			<thead>
			<tr>
				<th>#</th>
				<th>Numero</th>
				<th>Monto</th>
				<th>F/E</th>
				<th>F/V</th>
				<th></th>
			</tr>
			</thead>
			<tbody id="myTable">
		<?php
			echo "<tr>";

			echo $_SESSION['DATOS1'];
			// $longitud = count($array);

			// for($i=0; $i<$longitud; $i++){
			// 	echo $array[$i];
			// 	echo "<br>";
			// }	
			echo "</tr>"		

		?>
			</tbody>

		</table>
		<p>linea 2</p> -->
	</div>


