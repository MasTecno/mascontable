<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($_POST['nomfrm']=="P"){
		$SQL="SELECT rut FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND (tipo='C' OR tipo='H') GROUP BY rut;";
	}else{
		$SQL="SELECT rut FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND tipo='V' GROUP BY rut;";
	}

	
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$resultados = $mysqli->query($SQL);
	$array_ruts = array();

	while ($registro = $resultados->fetch_assoc()) {
		$array_ruts[] = $registro['rut'];
	}

	if($_POST['nomfrm']=="P"){
		$SQL="SELECT rut FROM CTHonorarios WHERE rutempresa='$RutEmpresa' GROUP BY rut;";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			if (!in_array($registro['rut'], $array_ruts)) {
				$array_ruts[] = $registro['rut'];
			}
		}		
	}

echo'
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider" align="right" width="10%">Rut</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Razon Social</th>
								<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider">Direccion</th>
								<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider">Giro</th>
								<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider">Correo</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" align="right">Numero</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
							</tr>
						</thead>
						<tbody id="myTable">';

							foreach ($array_ruts as $rut) {

								$SQL="SELECT * FROM CTCliPro WHERE rut='$rut' AND tipo='".$_POST['nomfrm']."' ORDER BY razonsocial";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {

									$cuenta=$registro["cuenta"];
									$NuCuenta=$cuenta;
									$NCuenta="Sin Cuenta Asignada";

									if ($_SESSION['RUTEMPRESA']!="") {
										$SQL1="SELECT * FROM CTCliProCuenta WHERE rut='".$registro["rut"]."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
										$resultados1 = $mysqli->query($SQL1);
										while ($registro1 = $resultados1->fetch_assoc()) {
											$cuenta=$registro1['cuenta'];
											$NuCuenta=$registro1['cuenta'];
										}
										if ($cuenta!=0) {
											if ($_SESSION["PLAN"]=="S"){
												$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$cuenta."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";                  
											}else{
												$SQL1="SELECT * FROM CTCuentas WHERE numero='".$cuenta."'";                  
											}
											
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$NCuenta=$registro1['detalle'];
											}
										}
									}else{
										if ($_SESSION["PLAN"]=="S"){
											$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$cuenta."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";                  
										}else{
											$SQL1="SELECT * FROM CTCuentas WHERE numero='".$cuenta."'";                  
										}
										
										$resultados1 = $mysqli->query($SQL1);
										while ($registro1 = $resultados1->fetch_assoc()) {
											$NCuenta=$registro1['detalle'];
										}										
									}

									echo '
										<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
											<td class="px-6 py-1 whitespace-nowrap text-sm font-medium text-gray-900" align="center">'.$registro["rut"].'</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["razonsocial"].'</td>
											<td class="px-6 py-1 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["direccion"].'</td>
											<td class="px-6 py-1 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["giro"].'</td>
											<td class="px-6 py-1 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["correo"].'</td>
											<td class="px-6 py-1 whitespace-nowrap text-sm font-medium text-gray-900" align="right">'.$NuCuenta.'</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$NCuenta.'</td>
									';

									
									echo '
										</tr>
									';
								}
							}
							
echo'
						</tbody>
					</table>';
?>