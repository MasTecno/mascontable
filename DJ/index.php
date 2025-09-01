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

	if (isset($_POST['ace'])) {

	    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

        $mysqli->query("DELETE FROM CTHonoGene WHERE periodo='".$_POST['ano']."' AND rutempresa='$RutEmpresa'");
        $mysqli->query("DELETE FROM CTHonoGeneDeta WHERE periodo like '%-".$_POST['ano']."' AND rutempresa='$RutEmpresa'");

	    $SQL="SELECT * FROM CTFactores WHERE periodo='".$_POST['ano']."'";
	    $resultados = $mysqli->query($SQL);
	    while ($registro = $resultados->fetch_assoc()) {
			$m1=$registro['mes1'];
			$m2=$registro['mes2'];
			$m3=$registro['mes3'];
			$m4=$registro['mes4'];
			$m5=$registro['mes5'];
			$m6=$registro['mes6'];
			$m7=$registro['mes7'];
			$m8=$registro['mes8'];
			$m9=$registro['mes9'];
			$m10=$registro['mes10'];
			$m11=$registro['mes11'];
			$m12=$registro['mes12'];
	    }

		$FECHA=date("Y/m/d");

        $mysqli->query("INSERT INTO CTHonoGene VALUE('','".$_POST['ano']."','$RutEmpresa','$FECHA','$m1','$m2','$m3','$m4','$m5','$m6','$m7','$m8','$m9','$m10','$m11','$m12','A')");

	    $SQL="SELECT max(id) as mid FROM CTHonoGene";
	    $resultados = $mysqli->query($SQL);
	    while ($registro = $resultados->fetch_assoc()) {
			$mid=$registro['mid'];
	    }

	    $certif=0;
		$crut="";
		
			if ($_POST['ano']=="2019") {
				$facto=10;
			}
			if ($_POST['ano']=="2020") {
				$facto=10.75;
			}
			if ($_POST['ano']=="2021") {
				$facto=11.5;
			}
			if ($_POST['ano']=="2022") {
				$facto=12.25;
			}
			if ($_POST['ano']=="2023") {
				$facto=13;
			}
			if ($_POST['ano']=="2024") {
				$facto=13.75;
			}
			if ($_POST['ano']=="2025") {
				$facto=14.5;
			}
			if ($_POST['ano']=="2026") {
				$facto=15.25;
			}
			if ($_POST['ano']=="2027") {
				$facto=16;
			}
			if ($_POST['ano']=="2028") {
				$facto=17;
			}

	    $SQL="SELECT * FROM CTHonorarios WHERE periodo LIKE '%-".$_POST['ano']."' AND rutempresa='$RutEmpresa' ORDER BY rut, numero";
	    $resultados = $mysqli->query($SQL);
	    while ($registro = $resultados->fetch_assoc()) {
			$Rete=0;
			$Rete=$registro['retencion'];

	    	if($crut!=$registro['rut']){
	    		$certif=$certif+1;
	    		$crut=$registro['rut'];
	    	}

	    	// echo $registro['numero'];
	    	// echo "<br>";
	    	
	    	// echo "<br>";
	    	// echo $facto;
	    	// echo "<br>";
	    	$Prest=round($registro['bruto']*$facto/100);
	    	// echo "<br>";
	    	$dif=$Rete-$Prest;
	    	// echo "<br>";
	    	// echo "<br>";

	    	if ($dif<=2) {
	    		$Rete=$registro['retencion'];
	    		$Prest=0;
	    	}else{
	    		if ($Rete>$Prest) {
	    			$Rete=$Prest;
	    			$Prest=$registro['retencion']-$Prest;
	    		}
	    	}

	    	// if ($Rete>$Prest) {

	    	// }

	    	// echo "INSERT INTO CTHonoGeneDeta VALUE('','$mid','$certif','".$registro['periodo']."','".$registro['rutempresa']."','".$registro['fecha']."','".$registro['rut']."','".$registro['numero']."','".$registro['bruto']."','".$Rete."','".$Prest."','".$registro['liquido']."','A')";
	    	// echo "<br>";
	    	// echo "<br>";
			// if($Rete>0){
				$mysqli->query("INSERT INTO CTHonoGeneDeta VALUE('','$mid','$certif','".$registro['periodo']."','".$registro['rutempresa']."','".$registro['fecha']."','".$registro['rut']."','".$registro['numero']."','".$registro['bruto']."','".$Rete."','".$Prest."','".$registro['liquido']."','A')");
			// }
	    }

	    $mysqli->close();
	    // exit;
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
			function Comprueba(){

			    var url= "buscafactor.php";
			    
			    $.ajax({
			      type: "POST",
			      url: url,
			      data: $('#form1').serialize(),
			      success:function(resp)
			      {
			          $('#mjs').html(resp);
			      }
			    });

			    var url= "buscagcertifi.php";
			    
			    $.ajax({
			      type: "POST",
			      url: url,
			      data: $('#form1').serialize(),
			      success:function(resp)
			      {
			          $('#mjs1').html(resp);
			      }
			    });

			    var url= "buscadocum.php";
			    
			    $.ajax({
			      type: "POST",
			      url: url,
			      data: $('#form1').serialize(),
			      success:function(resp)
			      {
			          $('#mjs2').html(resp);
			      }
			    });


			    CargGrilla();
			}		


			function CargGrilla(){

				var url= "frmHonorariosGenFactores.php";

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
				sw = document.getElementById("ace").checked;

				if (sw==true) {				
					form1.submit();
				}
			}
			function acept(){
				sw = document.getElementById("ace").checked;

				if (sw==false) {
					document.getElementById("bt").classList.remove("active");
					document.getElementById("bt").classList.add("disabled");
				}else{
					document.getElementById("bt").classList.remove("disabled");
					document.getElementById("bt").classList.add("active");
				}
			}


		</script>
	</head>

	<body onload="Comprueba()">


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<h4>Emisi&oacute;n de Certificado de Honorarios</h4>
				<hr>

          <div class="col-md-4">
            <label for="d6">Periodo </label>
             <select class="form-control" id="ano" name="ano" required onchange="Comprueba()">
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
           
          <div class="col-md-4">
            <div id="mjs"></div>
            <div id="mjs2"></div>
            <div id="mjs1"></div>
         </div> 
          <div class="col-md-4" id="grilla">
          	
          </div>

          <div class="clearfix"></div>
          <div class="col-sm-12 text-center">
			<label class="checkbox-inline"><input type="checkbox" onclick="acept()" id="ace" name="ace">Aceptar</label> 
			<p>* Este proceso Genera los certificados correspondientes al periodo seleccionado, de todas aquellas boletas registradas</p>         	
			<p>** En caso que el proceso se vuelva a ejecurar, el anterior sera elimnado y no se podra recuperar.</p>         	
          </div>

          <div class="clearfix"></div>
          <div class="col-sm-12">
			<button type="button" class="btn btn-primary btn-block disabled" id="bt" name="bt" onclick="Porce()">Generar Certificado</button>          	
          </div>

			</div>
			<div class="col-sm-2">
			</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>
	<br>

	<?php include '../footer.php'; ?>

	</body>
</html>