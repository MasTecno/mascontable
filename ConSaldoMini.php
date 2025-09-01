<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';
	
    $Periodo=$_SESSION['PERIODO'];


	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$MonAcu="S";

	$mysqli->close();
?>
	<div class="col-md-12">
		<div class="input-group">
		<span class="input-group-addon">N&uacute;mero</span>
		<select class="form-control" id="seleccueMini" name="seleccueMini" onchange="ConsulMini()">
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
					if($_POST['seleccueMini']==$registro['numero']){
						echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".strtoupper($registro['detalle'])."</option>";
						$CantCuenta=$_POST['seleccueMini'];
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

			if ($_POST['seleccueMini']!="") {
				$CantCuenta=$_POST['seleccueMini'];
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