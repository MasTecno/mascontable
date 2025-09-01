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
	
			function Porce(valor){

				
				if (form1.ano.value>=2023) {
					window.open('GenReport2023.php?id='+valor+'&per='+form1.ano.value, '_blank');
				}else{
					if (form1.ano.value==2022) {
						window.open('GenReport2022.php?id='+valor+'&per='+form1.ano.value, '_blank');
					}else{
						if (form1.ano.value==2021) {
							window.open('GenReport2021.php?id='+valor+'&per='+form1.ano.value, '_blank');
						}else{
							window.open('GenReport.php?id='+valor+'&per='+form1.ano.value, '_blank');	
						}
					}
				}
			}

			function CargGrilla(){
				var url= "frmInfHonorariosDet.php";

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
					}
				});
			}
			
		</script>
	</head>

	<body onload="CargGrilla()">


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<br>
			<div class="col-sm-2">
			</div>
			<div class="col-md-8">
				<label for="d6">Periodo </label>
				<select class="form-control" id="ano" name="ano" required onchange="CargGrilla()">
					<option value="">Seleccione</option>
					<?php
						$i=2010;
						$dano=date("Y");
						$dano=$dano-1;

						while ( $i<= 2030){
						if($i==$dano){
						echo "<option value='".$i."' selected>".$i."</option>";
						}else{
						echo "<option value='".$i."'>".$i."</option>";
						}
						$i=$i+1;
					}

					?>
				</select>
			</div>
			<div class="col-sm-2">
			</div>
			<div class="clearfix"></div>

			<div class="col-sm-2">
			</div>
			<div class="col-sm-8" id="grilla">

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