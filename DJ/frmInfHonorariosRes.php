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
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">
	
			function Porce(valor,per){
				if (per>="2023") {
					window.open('GenReportRes2023.php?per='+valor, '_blank');
				}else{
					if (per=="2022") {
						window.open('GenReportRes2022.php?per='+valor, '_blank');
					}else{
						if (per=="2021") {
							window.open('GenReportRes2021.php?per='+valor, '_blank');
						}else{
							window.open('GenReportRes.php?per='+valor, '_blank');	
						}
					}
				}
			}		
			function Sii(valor,per){
				if (per>="2023") {
					window.open('GenReportResSii2023.php?per='+valor, '_blank');
				}else{
					if (per=="2022") {
						window.open('GenReportResSii2022.php?per='+valor, '_blank');
					}else{
						if (per=="2021") {
							window.open('GenReportResSii2021.php?per='+valor, '_blank');
						}else{
							window.open('GenReportResSii.php?per='+valor, '_blank');	
						}
					}
				}
				
			}		

		</script>
	</head>

	<body>


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<br>

			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<table class="table table-striped table-bordered" width="100%">
				<thead>
					<tr>
						<th>Periodo</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
              	<?php              
                	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

					$SQL="SELECT * FROM CTHonoGene WHERE rutempresa='$RutEmpresa' and estado='A' order by id desc";
					$resultados = $mysqli->query($SQL);
        			while ($registro = $resultados->fetch_assoc()) {

						echo '
							<tr>
								<td>'.$registro['periodo'].'</td>
								<td class="text-right"><button type="button" class="btn btn-success" onclick="Porce(\''.$registro['id'].'\',\''.$registro['periodo'].'\')">Visualizar</button></td>
								<td class="text-right"><button type="button" class="btn btn-success" onclick="Sii(\''.$registro['id'].'\',\''.$registro['periodo'].'\')">SII</button></td>
							</tr>
						';
					}

                	$mysqli->close();
               	?>

				</tbody>
				</table>

			</div>	
			<div class="col-sm-2">
			</div>					
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include '../footer.php'; ?>

	</body>
</html>