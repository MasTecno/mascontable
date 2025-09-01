<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	if($_POST['frm']=="C"){
		$Tit="Compras";
	}else{
		$Tit="Ventas";
	}
?>
<!DOCTYPE html>
<html>
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

        <script>
			function CGrilla(){
				var url= "AsociarDocGrilla.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#ListFactura').html(resp);
						CNCredito();
					}
				});
			}

			function CNCredito(){
				mensaje= form1.ListSinDoc.value;
				arr = mensaje.split(',');
				form1.ModFNC.value=arr[2];
				form1.rsocial.value=arr[4];
				form1.ModNNC.value=arr[5];
				form1.Mod01.value=arr[0];

				mensaje=arr[2];
				arr = mensaje.split('-');
				ano=arr[0];
				mes=arr[1];
				dia=arr[2];
				form1.ModFNC.value=dia+"-"+mes+"-"+ano;
				
			}

			function CFactura(){
				mensaje= form1.ListFactura.value;
				arr = mensaje.split(',');
				form1.ModFFC.value=arr[2];
				form1.ModNFC.value=arr[1];
				form1.Mod02.value=arr[0];

				mensaje=arr[2];
				arr = mensaje.split('-');
				ano=arr[0];
				mes=arr[1];
				dia=arr[2];
				form1.ModFFC.value=dia+"-"+mes+"-"+ano;
				
			}

			function Procesa(){
				if(form1.Mod01.value=="" || form1.Mod02.value==""){
					alert("Para asignar documento debe seleccionar un elemento de cada listado");
				}else{
					var r = confirm("Confirmar la asigaci\u00F3n de estos documentos?");
					if (r == true) {
						form1.action="xAsociarDoc.php";
						form1.submit();						
					}else{
						alert("Operaci\u00F3n cancelada")
					}
				}


			}
        </script>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<br>
			<div class="col-sm-12">
				<div class="col-md-12 text-center">
					<h3>Asociar Notas de Cr&eacute;dito - <?php echo $Tit; ?></h3>
				</div>
				<div class="clearfix"></div>
				<br>

                <div class="col-md-2">
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="frm" value="<?php echo $_POST['frm'];?>" />
					<div class="col-md-12 text-center"><strong>Notas de Credito</strong></div>
                    <select class="form-control" id="ListSinDoc" name="ListSinDoc" size="10" onclick="CGrilla()">

                        <?php
                            $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

                            $SQL="SELECT * FROM CTParametros WHERE estado='A'";
                            $resultados = $mysqli->query($SQL);
                            while ($registro = $resultados->fetch_assoc()) {
                                if($registro['tipo']=="SEPA_MILE"){
                                    $DMILE=$registro['valor'];  
                                }
                                if($registro['tipo']=="SEPA_DECI"){
                                    $DDECI=$registro['valor'];  
                                }
                                if($registro['tipo']=="NUME_DECI"){
                                    $NDECI=$registro['valor'];  
                                } 
                            }        

                            $SQLx="SELECT * FROM CTRegDocumentos WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND FolioDocRef=0 AND keyas='' AND tipo='".$_POST['frm']."' AND (id_tipodocumento='4' OR id_tipodocumento='5' OR id_tipodocumento='32' OR id_tipodocumento='37')";
                            $resultadosx = $mysqli->query($SQLx);
                            while ($registrox = $resultadosx->fetch_assoc()) {
                                $rrut="";
                                $rsoc="";
                                $SQL="SELECT * FROM CTCliPro WHERE rut='".$registrox["rut"]."'";
                                $resultados = $mysqli->query($SQL);
                                while ($registro = $resultados->fetch_assoc()) {
                                    $rrut=$registro["rut"];
                                    $rsoc=$registro["razonsocial"];
                                }
								$cadena=$registrox["id"].','.$rrut.','.$registrox["fecha"].','.$registrox["total"].','.$rsoc.','.$registrox["numero"];

                                echo '<option value="'.$cadena.'"> NC: '.$registrox["numero"].', Fecha: '.date('d-m-Y',strtotime($registrox["fecha"])).', '.$rrut.' '.$rsoc.', Monto: '.number_format($registrox["total"], $NDECI, $DDECI, $DMILE).'</option>';
                            }
                            $mysqli->close();
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
					<div class="col-md-12 text-center"><strong>Facturas</strong></div>
                    <select class="form-control" id="ListFactura" name="ListFactura" size="10" onclick="CFactura()">
                    </select>
                </div>
				<div class="clearfix"></div>
				<br>

                <div class="col-md-2">
                </div>
				
				<div class="col-md-8">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Confirmarci&oacute;n</div>
						<div class="panel-body">

							<input id="Mod01" name="Mod01" type="hidden" class="form-control" value="">
							<input id="Mod02" name="Mod02" type="hidden" class="form-control" value="">

							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Raz&oacute;n Social</span>
									<input id="rsocial" name="rsocial" type="text" class="form-control" readonly value="">
								</div>							
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">N&reg; NC</span>
									<input id="ModNNC" name="ModNNC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Fecha NC</span>
									<input id="ModFNC" name="ModFNC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">N&reg; Factura</span>
									<input id="ModNFC" name="ModNFC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Fecha Factura</span>
									<input id="ModFFC" name="ModFFC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-6">
								<button type="button" class="btn btn-modificar" onclick="Procesa()">Confirmar</button>						
							</div>
							<div class="clearfix"></div>
							<br>


						</div>
					</div>
				</div>


			</div>
		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>

