<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

?>	
	<div class="col-md-12 text-center">
		<h4>Informaci&oacute;n de Pago Efectivo</h4>
	</div>

	<div class="col-md-12">
	<div class="input-group">
		<span class="input-group-addon">Cuenta</span>
		<select class="form-control" id="cuentaasi" name="cuentaasi">
			<?php
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
				if ($_SESSION["PLAN"]=="S"){
					$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND auxiliar='E' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
				}else{
					$SQL="SELECT * FROM CTCuentas WHERE estado='A' AND auxiliar='E' ORDER BY detalle";
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