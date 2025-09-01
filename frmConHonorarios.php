<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    if($Periodo==""){
      header("location:frmMain.php");
      exit;
    }

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

      if($registro['tipo']=="IVA"){
        $DIVA=$registro['valor']; 
      }

      if($registro['tipo']=="SEPA_MILE"){
        $DMILE=$registro['valor'];  
      }

      if($registro['tipo']=="SEPA_DECI"){
        $DDECI=$registro['valor'];  
      }

      if($registro['tipo']=="SEPA_LIST"){
        $DLIST=$registro['valor'];  
      }

      if($registro['tipo']=="TIPO_MONE"){
        $DMONE=$registro['valor'];  
      }

      if($registro['tipo']=="NUME_DECI"){
        $NDECI=$registro['valor'];  
      } 
    }

	$SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xNOM=$registro['razonsocial']; 
		$xRUT=$registro['rut']; 
		$xDIR=$registro['direccion'];   
		$xCUI=$registro['ciudad'];  
		$xGIR=$registro['giro'];    
		$xRrep=$registro['rut_representante'];    
		$xRep=$registro['representante'];    
	}


    $mysqli->close();

	
?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/StConta.css">

		<style>
			/* Remove the navbar's default margin-bottom and rounded borders */
			.navbar {
				margin-bottom: 0;
				border-radius: 0;
			}

			/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
			.row.content {height: 450px}

			/* Set gray background color and 100% height */
			.sidenav {
				padding-top: 20px;
				background-color: #f1f1f1;
				height: 100%;
			}

			/* Set black background color, white text and some padding */
			footer {
				background-color: #555;
				color: white;
				padding: 15px;
			}

			/* On small screens, set height to 'auto' for sidenav and grid */
			@media screen and (max-width: 767px) {
				.sidenav {
					height: auto;
					padding: 15px;
				}
				.row.content {height:auto;}
			}
			.TamGri{
				font-size: 12px;
			} 
		</style>

	<script type="text/javascript">
		function printDiv(nombreDiv) {
			var contenido= document.getElementById(nombreDiv).innerHTML;
			var contenidoOriginal= document.body.innerHTML;

			document.body.innerHTML = contenido;

			window.print();

			document.body.innerHTML = contenidoOriginal;

			
		}
		function RefCom(){
			form1.submit();
		}
		function RefMen(){
			form1.submit();
		}
	</script>

	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">

			<div class="col-md-12">       
			<!--  -->
				<br>
				<input class="form-control" id="myInput" type="text" placeholder="Buscar...">

				<dir class="col-md-12 text-center">
					<label class="checkbox-inline"><input type="checkbox" value="ACompleto" name="ACompleto" onclick="RefCom()" <?php if (isset($_POST['ACompleto']) && $_POST['ACompleto']!="") { echo "checked"; } ?> >Visualizar A&ntilde;o Completo</label>
					<label class="checkbox-inline"><input type="checkbox" value="MMenbrete" name="MMenbrete" onclick="RefMen()" <?php if (isset($_POST['MMenbrete']) && $_POST['MMenbrete']!="") { echo "checked"; } ?> >Visualizar Membrete</label>
				</dir>		

				<div class="col-md-12 text-center">
					<br>
					<input type="button" class="btn btn-default btn-sm" onclick="printDiv('DivImp')" value="Imprimir">
				</div>
			</div>

				<!-- <div class="clearfix"></div> -->

			<div class="col-md-12">  
				<div class="table-responsive" id="DivImp">
					
				<br>
				<?php if (isset($_POST['MMenbrete']) && $_POST['MMenbrete']!="") { ?>
					
				<div class="col-md-12" style="font-size: 12px;">
					<div class="col-sm-2">
						Contribuyente:
					</div>
					<div class="col-sm-10">
						<?php echo $xNOM; ?>
					</div>

					<div class="col-sm-2">
						Rut:
					</div>
					<div class="col-sm-10">
						<?php 
							//$RutPunto1=substr($xRUT,-10,2);

							if (strlen($xRUT)==9) {
								$RutPunto1=substr($xRUT,-10,1);
							}else{
								$RutPunto1=substr($xRUT,-10,2);
							}
			


							$RutPunto2=substr($xRUT,-5);
							$RutPunto3=substr($xRUT,-8,3);
							echo $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;
						 ?>
					</div>

					<div class="col-sm-2">
						Domicilio:
					</div>
					<div class="col-sm-10">
						<?php echo $xDIR; ?>
					</div>

					<div class="col-sm-2">
						Cuidad:
					</div>
					<div class="col-sm-10">
						<?php echo $xCUI; ?>
					</div>

					<div class="col-sm-2">
						Rep. Legal:
					</div>
					<div class="col-sm-10">
						<?php echo $xRep; ?>
					</div>

					<div class="col-sm-2">
						Rep. Rut:
					</div>
					<div class="col-sm-10">
						<?php 

						//	$RutPunto1=substr($xRrep,-10,2);

							if (strlen($xRrep)==9) {
								$RutPunto1=substr($xRrep,-10,1);
							}else{
								$RutPunto1=substr($xRrep,-10,2);
							}
			
						
							$RutPunto2=substr($xRrep,-5);
							$RutPunto3=substr($xRrep,-8,3);
							echo $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;
						?>
					</div>
					<div class="clearfix"></div>
					<br>
				</div>


				<?php } ?>
				<br>



				<div class="col-md-12 text-center">

					<h3>Libro de Honorarios <br> 
						<?php 
							if (isset($_POST['ACompleto']) && $_POST['ACompleto']!="") {
								$AnoCor="01-".$Periodo;
								$AComp=date('Y',strtotime($AnoCor));
								echo $AComp;
							}else{
								echo $Periodo;
							}
						?>
					</h3>
				</div>
					<table class="table table-hover TamGri">
						<thead>	
							<tr>
								<th>Fecha</th>
								<th>Rut</th>
								<th>Razon Social</th>
								<th>Cuenta</th> 
								<th>Periodo</th> 
								<th>N&deg; Doc</th>
								<th>Tipo Documento</th>
								<th>Bruto</th>
								<th>Retenci&oacute;n</th>
								<th>Liquido</th>
							</tr>
						</thead>
						<tbody id="ListDoc"> 
<?php 

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    if (isset($_POST['ACompleto']) && $_POST['ACompleto']!="") {
    	$AnoCor="01-".$Periodo;
    	$AComp=date('Y',strtotime($AnoCor));
        $SQL="SELECT * FROM CTHonorarios WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo like '%".$AComp."' ORDER BY fecha";
    }else{
        $SQL="SELECT * FROM CTHonorarios WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY fecha";
    }

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
           
      $rsocial="";
      $SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
      $resultados1 = $mysqli->query($SQL1);
      while ($registro1 = $resultados1->fetch_assoc()) {
        $rsocial=$registro1["razonsocial"];
      }

      $nomcuenta="";
      if ($_SESSION["PLAN"]=="S"){
      		$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
      }else{
      		$SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
      }

      $resultados1 = $mysqli->query($SQL1);
      while ($registro1 = $resultados1->fetch_assoc()) {
        $nomcuenta=$registro1["detalle"];
      }


      if ($registro["tdocumento"]=="R") {
        $tdocum="Recibido";
      }else{
        $tdocum="Emitido";
      }

echo '
              <tr>
                <td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
                <td>'.$registro["rut"].'</td>
                <td>'.utf8_encode($rsocial).'</td>
                <td>'.$registro["cuenta"]." - ".strtoupper($nomcuenta).'</td>
                <td>'.$registro["periodo"].'</td>
                <td align="right">'.$registro["numero"].'</td>
                <td>'.$tdocum.'</td>
                <td align="right">$'.number_format(($registro["bruto"]), $NDECI, $DDECI, $DMILE).'</td>
                <td align="right">$'.number_format(($registro["retencion"]), $NDECI, $DDECI, $DMILE).'</td>
                <td align="right">$'.number_format(($registro["liquido"]), $NDECI, $DDECI, $DMILE).'</td>
              </tr>
';

      $tbruto=$tbruto+($registro["bruto"]);
      $tretencion=$tretencion+($registro["retencion"]);
      $tliquido=$tliquido+($registro["liquido"]);

    }

    $mysqli->close();

echo'
              <tr class="success">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><strong>Totales</strong></td>
                <td align="right"><strong>$'.number_format($tbruto, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td align="right"><strong>$'.number_format($tliquido, $NDECI, $DDECI, $DMILE).'</strong></td>
              </tr>
';

?>


						</tbody>
					</table>
				</div>



			</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#ListDoc tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>

	<?php include 'footer.php'; ?>

	</body>
</html>