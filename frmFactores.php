<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:index.php?Msj=95");
		exit;
	}

	if ($_POST['d01']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    	$SQL="SELECT * FROM CTFactores WHERE periodo='".$_POST['ano']."'";
        $resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt>0) {
			$mysqli->query("UPDATE CTFactores SET mes1='".$_POST['d01']."', mes2='".$_POST['d02']."', mes3='".$_POST['d03']."', mes4='".$_POST['d04']."', mes5='".$_POST['d05']."', mes6='".$_POST['d06']."', mes7='".$_POST['d07']."', mes8='".$_POST['d08']."', mes9='".$_POST['d09']."', mes10='".$_POST['d10']."', mes11='".$_POST['d11']."', mes12='".$_POST['d12']."' WHERE periodo='".$_POST['ano']."'");
			$mysqli->close();
			header("location:frmMain.php");
			exit;
		}else{
			$mysqli->query("INSERT INTO CTFactores VALUE('','".$_POST['ano']."','".$_POST['d01']."','".$_POST['d02']."','".$_POST['d03']."','".$_POST['d04']."','".$_POST['d05']."','".$_POST['d06']."','".$_POST['d07']."','".$_POST['d08']."','".$_POST['d09']."','".$_POST['d10']."','".$_POST['d11']."','".$_POST['d12']."','A')");
			$mysqli->close();
			header("location:frmMain.php");
			exit;
		}

	}
?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/select2.css">

		<script type="text/javascript">
			function CargGrilla(){

				var url= "frmFactoresDet.php";

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
					}
				});
			}
			function Porce(){
				form1.submit();

			}

		</script>
	</head>

	<body onload="CargGrilla()">


	<?php include 'nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
			<br>
		        <div class="well well-sm">
		          <strong>Emisi&oacute;n de Certificado de Honorarios</strong>
		        </div>

          <div class="col-md-4">
            <label for="d6">Periodo </label>
             <select class="form-control" id="ano" name="ano" required onchange="CargGrilla()">
             	<option value="">Seleccione</option>
              <?php              
                  $i=2010;
                  $dano=date("Y");

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
           

          <div class="clearfix"></div>
          <hr>
          <div class="col-sm-12" id="grilla">
			          	
          </div>

          <div class="clearfix"></div>
          <hr>
          <div class="col-sm-12">
			<button type="button" class="btn btn-primary btn-block" onclick="Porce()">Confirmar</button> 
			<br>
			<br>         	
          </div>

			</div>
			<div class="col-sm-2">
			</div>
		</form>
	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include 'footer.php'; ?>

	</body>
</html>