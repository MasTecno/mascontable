<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    if($Periodo==""){
      header("location:../frmMain.php");
      exit;
    }
?>



				<table class="table table-striped table-bordered" width="100%">
				<thead>
					<tr>
						<th>Emisor</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
              	<?php              
                	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

					$SQL="SELECT * FROM CTCliPro WHERE tipo ='P'";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {

						$SQL1="SELECT rut FROM CTHonoGeneDeta WHERE rutempresa='$RutEmpresa' AND periodo like '%-".$_POST['ano']."' AND rut='".$registro['rut']."' AND estado='A' GROUP BY rut";
						$resultados1 = $mysqli->query($SQL1);
				        $row_cnt = $resultados1->num_rows;
				        if ($row_cnt>0) {
							echo '
								<tr>
									<td>'.$registro['razonsocial'].'</td>
									<td class="text-right"><button type="button" class="btn btn-success" onclick="Porce(\''.$registro['id'].'\')">Visualizar</button></td>
								</tr>
							';
						}
					}

                	$mysqli->close();
               	?>

				</tbody>
				</table>