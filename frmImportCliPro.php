<?php
	include 'conexion/conexion.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
	
    $Periodo=$_SESSION['PERIODO'];


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
    $mysqli->close();

    extract($_POST);

    if ($action == "upload") {

    	$archivo = $_FILES['file']['tmp_name'];

		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		/// Datos para procesar archivo
		$ListError="";
		$NFacturasError="";
      	$row = 1; 
		$fp = fopen ($archivo,"r");
		while ($data = fgetcsv ($fp, 1000, $separador)){       
	        if($row>=2){
	        	if ($data[0]!="" && $data[1]!="") {
					$SQL="SELECT * FROM CTCliPro  WHERE rut='$data[0]' AND tipo='".$_POST['plantilla']."'";
					$resultados = $mysqli->query($SQL);
					$row_cnt = $resultados->num_rows;
					if ($row_cnt==0) {
						$STRSQL="INSERT INTO CTCliPro VALUES ('','$data[0]','".strtoupper($data[1])."','".strtoupper($data[2])."','".strtoupper($data[3])."','".strtoupper($data[4])."','$data[5]','','".$_POST['plantilla']."','A');";
						$mysqli->query($STRSQL);
					}	
				}
			}
	        $row++; 
	    }

      $mysqli->close();

      fclose ($fp); 
    }
?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
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


		</style>


		<script type="text/javascript">
			function CargaArc(){
				var r = confirm("El proceso puede tomar tiempo");
				if (r == true) {
					importar.action="";
					//importar.submit();
				}else{
					alert("Operacion Cancelada");
					importar.action.value="";
				}

			}
		</script>

	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">
		<h3 class="text-center">Importar Libros</h3>
		<form name="importar" method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" class="form-horizontal">
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">

				<div class="form-group">
					<label class="control-label col-sm-2" for="email">Cargas:</label>
					<div class="col-sm-10">
			            <select class="form-control" id="plantilla" name="plantilla" required>
			              <option value="">Selecciones</option>
			              <option value ='C'>Clientes</option>
			              <option value ='P'>Proveedores</option>

			            </select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2" for="file">Seleccionar Archivo</label>
					<div class="col-sm-10">
						<input type="file" class="form-control-file" id="file" name="file" aria-describedby="fileHelp" required>
						<small id="fileHelp" class="form-text text-muted">Solo archivo CSV.</small>
						<input type="hidden" value="upload" name="action" />
					</div>
				</div> 


				<div class="form-group">
					<label class="control-label col-sm-2" for="file">Separador</label>
					<div class="col-sm-10">
						<input type="text" name="separador" value="<?php echo $DLIST; ?>" id="separador" required>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2" for="file">Seleccionar Archivo</label>
					<div class="col-sm-10">
						<button type="submit" class="btn btn-success btn-block">Procesar</button>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2" for="file"></label>
					<div class="col-sm-10">
					<?PHP
						if ($ListError!="") {
							echo $ListError;
						}
					?>
					</div>
				</div> 

				<div class="form-group">
					<label class="control-label col-sm-2" for="file"></label>
					<div class="col-sm-10">
						<img src="images/FormatoImpCliPro.png" class="img-thumbnail" alt="Cinque Terre"> 
						<p>* Archivo CSV con Rut y Razon Social minimo para importar</p>
						<p>* La primera linea no se procesa, ya que es la cabecera</p>
					</div>
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