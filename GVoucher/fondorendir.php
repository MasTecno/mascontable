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
		<h4>Informaci&oacute;n de Fondo a Rendir</h4>
	</div>

	<div class="col-md-12">
	<div class="input-group">
		<span class="input-group-addon">Fondos Abiertos</span>
		<select class="form-control" id="cuentaasi" name="cuentaasi">
			<option value="">Seleccione</option>
			<?php
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

				$SQL="SELECT * FROM CTFondo WHERE Estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='I' ORDER BY Fecha";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$NomPersonal="";

					$SQL1="SELECT * FROM CTCliPro WHERE id='".$registro['IdPersonal']."'";
					$resultados1 = $mysqli->query($SQL1);
					while ($registro1 = $resultados1->fetch_assoc()) {
						$NomPersonal=$registro1['razonsocial'];
					}

					$MontoEgreso=0;
					$TotFondo=0;
					$SQL1="SELECT * FROM CTFondo WHERE RutEmpresa='$RutEmpresa' AND IdPersonal='".$registro['Id']."' AND Tipo='E'";
					$resultados1 = $mysqli->query($SQL1);
					while ($registro1 = $resultados1->fetch_assoc()) {
						$MontoEgreso=$MontoEgreso+$registro1['Monto'];
					}

					$TotFondo=$registro['Monto']-$MontoEgreso;

					echo '<option value="'.$registro['Id'].'">'.$NomPersonal.' - '.$registro['Titulo'].' ('.$TotFondo.')</option>';
				}
				$mysqli->close();
			?>
		</select>
	</div>
	</div>
	<div class="clearfix"></div>
	<br>