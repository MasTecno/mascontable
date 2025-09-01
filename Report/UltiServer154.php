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

    $dmes = substr($Periodo,0,2);
    $danol = substr($Periodo,3,4);
    $Xfdesde="01-01-".$danol;
    $Xfhasta="31-12-".$danol;

?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
		<script src="../js/jquery.dataTables.min.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
        <script>
            $( function() {
                $("#fdesde").datepicker();
                $("#fhasta").datepicker();
            });
            
            function Updta(){
                if (confirm("Confirmar la Fecha de traspaso de informacion de Server Server154 a Server155") == true) {
                    form1.action="UltiServer154Insert.php";
                    form1.submit();
                }
            }

            function UpdtaEli(){
                if (confirm("Confirmar eliminar el rango de Fecha ingresada del Server155") == true) {
                    form1.action="UltiServer154Delete.php";
                    form1.submit();
                }
            }


        </script>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
            <form action="#" method="POST" name="form1" id="form1">
                <br>
                <div class="col-sm-12 text-left">
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Generar por rango de Fecha
                            </div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Desde</span>
                                        <input id="fdesde" name="fdesde" type="text" class="form-control text-right" value="<?php echo $Xfdesde; ?>" size="10" maxlength="10">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br>

                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Hasta</span>
                                        <input id="fhasta" name="fhasta" type="text" class="form-control text-right" value="<?php echo $Xfhasta; ?>" size="10" maxlength="10">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br>

                                <button type="button" class="btn btn-primary" onclick="Updta()">Generar</button>
                                <button type="button" class="btn btn-danger" onclick="UpdtaEli()">Eliminar</button>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
		</div>
		</div>
        <script type="text/javascript">
            $( "#fdesde" ).datepicker({
                // Formato de la fecha
                dateFormat: "dd-mm-yy",
                // Primer dia de la semana El lunes
                firstDay: 1,
                // Dias Largo en castellano
                dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
                // Dias cortos en castellano
                dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
                // Nombres largos de los meses en castellano
                monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
                // Nombres de los meses en formato corto 
                monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
                // Cuando seleccionamos la fecha esta se pone en el campo Input 
                onSelect: function(dateText) { 
                    // $('#d1').val(dateText);
                    // $('#d2').focus();
                    // $('#d2').select();
            }
            });

            $( "#fhasta" ).datepicker({
                // Formato de la fecha
                dateFormat: "dd-mm-yy",
                // Primer dia de la semana El lunes
                firstDay: 1,
                // Dias Largo en castellano
                dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
                // Dias cortos en castellano
                dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
                // Nombres largos de los meses en castellano
                monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
                // Nombres de los meses en formato corto 
                monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
                // Cuando seleccionamos la fecha esta se pone en el campo Input 
                onSelect: function(dateText) { 
                    // $('#d1').val(dateText);
                    // $('#d2').focus();
                    // $('#d2').select();
                }
                });       
            </script>



		<?php include '../footer.php'; ?>

	</body>

</html>