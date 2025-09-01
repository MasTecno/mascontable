<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$MonAcu="S";
	if (isset($_POST['swacu']) && $_POST['swacu']=="S") {
		if ($_POST['checkbox2']==true) {
			$mysqli->query("UPDATE CTParametros SET valor='S' WHERE tipo='SALD_ACUM'");
			$MonAcu="S";
		}else{
			$mysqli->query("UPDATE CTParametros SET valor='N' WHERE tipo='SALD_ACUM'");
			$MonAcu="N";
		}
	}

	$SQL="SELECT * FROM CTParametros WHERE tipo='SALD_ACUM'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$MonAcu=$registro["valor"];
	}

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];  
		}
	}

	$mysqli->close();
?>
	<div class="col-md-12">
		<div class="input-group">
		<span class="input-group-addon">N&uacute;mero</span>
		<select class="form-control" id="seleccue" name="seleccue" onchange="ConsultaS()">
		<option value="">Todas</option>
			<?php 
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

				$dmes = substr($Periodo,0,2);
				$dano = substr($Periodo,3,4);

				$CantCuenta="";
				$SQL="SELECT cuenta, COUNT(cuenta) as Cant FROM `CTRegLibroDiario` WHERE periodo LIKE '%-$dano' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND cuenta<>'0' GROUP by cuenta ORDER BY `Cant` DESC LIMIT 1";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$CantCuenta=$registro["cuenta"];
				}

				if ($_SESSION["PLAN"]=="S"){
					$sqlin = "SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
				}else{
					$sqlin = "SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
				}

				$resultadoin = $mysqli->query($sqlin);
				while ($registro = $resultadoin->fetch_assoc()) {
					if($_POST['seleccue']==$registro['numero']){
						echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".strtoupper($registro['detalle'])."</option>";
						$CantCuenta=$_POST['seleccue'];
					}else{
						if ($CantCuenta!="" && $CantCuenta==$registro['numero']) {
							echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ". strtoupper($registro['detalle'])."</option>";
						}else{
							echo "<option value='".$registro['numero']."'>".$registro['numero']." - ". strtoupper($registro['detalle'])."</option>";
						}
					}
				}

				$mysqli->close();
			?>
		</select>
		</div>
	</div> 

		<div class="col-md-12">
		<div class="checkbox checkbox-success text-center">
			<input id="checkbox2" type="checkbox" name="checkbox2" <?php if ($MonAcu=="S") { echo "checked"; } ?> onclick="Acu()">
			<label for="checkbox2">
				Monto Acumulado
			</label>
			
		</div>
		</div>




	<table class="table table-striped table-bordered TamGri" width="100%" style="font-size: 12px;">
	<thead>
		<tr>
			<th width="">Periodo</th>
			<th width="20%">Debe</th>
			<th width="20%">Haber</th>
			<th width="20%">Saldo</th>
		</tr>
	</thead>
<?php



			$dmes = substr($Periodo,0,2);
			$dano = substr($Periodo,3,4);

			$i=1;
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
			$CantCuenta="";
			$SQL="SELECT cuenta, COUNT(cuenta) as Cant FROM `CTRegLibroDiario` WHERE periodo LIKE '%-$dano' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND cuenta<>'0' GROUP by cuenta ORDER BY `Cant` DESC LIMIT 1";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$CantCuenta=$registro["cuenta"];
			}

			if ($_POST['seleccue']!="") {
				$CantCuenta=$_POST['seleccue'];
			}

			while ($i <= 12) {
				if ($i<=9) {
					$Mmes="0".$i;
				}else{
					$Mmes=$i;
				}
				$Lperiodo=$Mmes."-".$dano;

				$xsdebe=0;
				$xshaber=0;
				if ($MonAcu!="S") {
					$Saldo=0;
				}
				$SQL="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM  CTRegLibroDiario WHERE cuenta='$CantCuenta' AND periodo ='$Lperiodo' AND rutempresa='".$_SESSION['RUTEMPRESA']."' GROUP by cuenta";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$xsdebe=$registro["sdebe"];
					$xshaber=$registro["shaber"];
					if ($MonAcu=="S") {
						$Saldo=$Saldo+($xsdebe-$xshaber);
					}else{
						$Saldo=$xsdebe-$xshaber;
					}

				}

				if ($Periodo==$Lperiodo) {
					$colo='style="background-color: tomato;"';
				}else{
					$colo="";
				}

				echo '
					<tr '.$colo.'>
						<td>'.$Lperiodo.'</td>
						<td class="text-right">'.number_format($xsdebe, $NDECI, $DDECI, $DMILE).'</td>
						<td class="text-right">'.number_format($xshaber, $NDECI, $DDECI, $DMILE).'</td>
						<td class="text-right">'.number_format($Saldo, $NDECI, $DDECI, $DMILE).'</td>
					</tr> 
				';
				$i++;
			}

			$mysqli->close();
?>
	<tbody>

	</tbody>
	</table>