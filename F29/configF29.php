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

    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo=''";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        if($registro['Tipo']=="IVACredito"){
            $IvaCredito=$registro['Valor'];
        }
        if($registro['Tipo']=="IVADebito"){
            $IvaDebito=$registro['Valor'];
        }
        if($registro['Tipo']=="ImpUnico"){
            $CtaImpuestoUnico=$registro['Valor'];
        }
        if($registro['Tipo']=="RetHonorarios"){
            $CtaRetencionHonorarios=$registro['Valor'];
        }
        if($registro['Tipo']=="Prestamo3Sueldo"){   
            $CtaRetencion3Remuneraciones=$registro['Valor'];
        }
        if($registro['Tipo']=="Prestamo3Honorarios"){
            $CtaRetencion3Honorarios=$registro['Valor'];
        }
        if($registro['Tipo']=="PPM"){
            $CtaPPM=$registro['Valor'];
        }
        if($registro['Tipo']=="Remanente"){
            $CtaRemanente=$registro['Valor'];
        }
    }

    


    if ($_SESSION["PLAN"]=="S"){
        $SQLCta="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='$RutEmpresa'";
    }else{
        $SQLCta="SELECT * FROM CTCuentas WHERE estado='A'";
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">
        <script>
            function actualizar() {
                document.form1.submit();
            }

            $(document).ready(function() {
                // Inicializar Select2 para todos los select del formulario
                $('select').select2({
                    placeholder: "Seleccione una cuenta...",
                    allowClear: true
                });

                // Manejar el evento change para todos los select
                $('select').on('change', function() {
                    var selectId = $(this).attr('id');
                    var inputId = selectId.replace('Sel', '');
                    $('#' + inputId).val($(this).val());
                });
            });

        </script>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
            <form action="xconfigF29.php" name="form1" id="form1" method="POST">

                <div class="col-sm-10">
                    <h3>Configuración de F29</h3>
                </div>
                <div class="clearfix"></div>
                <br>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Iva Crédito</span>
                        <select id="SelIvaCredito" name="SelIvaCredito" class="form-control">
                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$IvaCredito){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Iva Débito</span>
                        <select id="SelIvaDebito" name="SelIvaDebito" class="form-control">
                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$IvaDebito){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="clearfix"></div>
                <br>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Cta Impuesto Unico</span>
                        <select id="SelCtaImpuestoUnico" name="SelCtaImpuestoUnico" class="form-control">
                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$CtaImpuestoUnico){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Cta Retención Honorarios</span>
                        <select id="SelCtaRetencionHonorarios" name="SelCtaRetencionHonorarios" class="form-control">

                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$CtaRetencionHonorarios){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Cta Retención 3% Remuneraciones</span>
                        <select id="SelCtaRetencion3Remuneraciones" name="SelCtaRetencion3Remuneraciones" class="form-control">

                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$CtaRetencion3Remuneraciones){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Cta Retención 3% Honorarios</span>
                        <select id="SelCtaRetencion3Honorarios" name="SelCtaRetencion3Honorarios" class="form-control">

                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$CtaRetencion3Honorarios){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Cta PPM</span>
                        <select id="SelCtaPPM" name="SelCtaPPM" class="form-control">
                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$CtaPPM){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">Cta Remanente</span>
                        <select id="SelCtaRemanente" name="SelCtaRemanente" class="form-control">
                            <option value="0">Seleccione...</option>
                            <?php
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $resultados = $mysqli->query($SQLCta);
                                while ($registro = $resultados->fetch_assoc()) {
                                    if($registro['numero']==$CtaRemanente){
                                        echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }else{
                                        echo "<option value='".$registro['numero']."'>".$registro['numero']." - ".$registro['detalle']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="clearfix"></div>
                <br>

                <div class="col-sm-12">
                    <button type="button" class="btn btn-primary" onclick="actualizar()">Guardar</button>
                </div>
                
            </form>
        </div>
        </div>
    </body>
    <?php
        include '../footer.php';
    ?>

</html>