<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if($_SESSION['KEYASIENTOFAC']==""){
		$_SESSION['KEYASIENTOFAC']=date("YmdHis");
	}

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	if($_GET['Doc']==1 || $_GET['Doc']==2){
	}else{
		header("location:../frmMain.php");
		exit;
	}

	if(isset($_GET['Doc'])){
		if($_GET['Doc']==1){
			$frm="C"; 
			$f="P";
		}
		if($_GET['Doc']==2){
			$frm="V";
			$f="C";
		}
	}

	$dmes = substr($Periodo,0,2);
	$dano = substr($Periodo,3,4);

	if(isset($_POST['d1'])){
		if ($_POST['d1']!="") {
			$textfecha=$_POST['d1'];
		}else{
			$textfecha="01-".$dmes."-".$dano;
		}      
	}else{
		$textfecha="01-".$dmes."-".$dano;
	}

	$textfecha1="01-".$dmes."-".$dano;

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTParametros WHERE tipo='IVA'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Val_Iva=$registro['valor'];
	}

	$mysqli->close();


	if(isset($_POST['ModReg'])){
		if ($_POST['ModReg']!="") {

			$NModCue=$_POST['ModReg'];
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
			$SQL="SELECT * FROM CTRegDocumentos WHERE id='$NModCue'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$textfecha=date('d-m-Y',strtotime($registro["fecha"]));
				$LRut=$registro["rut"];
				$LCuenta=$registro["cuenta"];
				$Ltdoc=$registro["id_tipodocumento"];
				$LNumero=$registro["numero"];
				$LExento=$registro["exento"];
				$LNeto=$registro["neto"];
				$LIva=$registro["iva"];
				$LRete=$registro["retencion"];
			}

			$SQL="SELECT * FROM CTCliPro WHERE rut='$LRut' AND tipo='$f'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$LRSocial=$registro["razonsocial"];
			}

			$SQL="SELECT * FROM CTTipoDocumento WHERE id='$Ltdoc'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$LDocumen=$registro["nombre"];
				$Ltdoc=$registro["tiposii"];
			}
			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$LCuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$LCuenta'";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$LNCuenta=$registro["detalle"];
			}

		$mysqli->close();
		}
	}

	if (isset($_POST['IdMovDoc'])) {
		if ($_POST['IdMovDoc']!="") {
			$messelect=$_POST['messelect'];
			$anoselect=$_POST['anoselect'];

			if ($messelect<=9) {
				$messelect="0".$messelect;
			}
			$PerMov=$messelect."-".$anoselect;

			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        	$mysqli->query("UPDATE CTRegDocumentos SET periodo='$PerMov' WHERE id='". $_POST['IdMovDoc']."'");
        	$mysqli->close();
		}
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
	<script src="../js/propio.js"></script>

	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">	

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

		.input-group .form-control {
			z-index: 1;
		}

		.input-group-btn:last-child>.btn, .input-group-btn:last-child>.btn-group {
			z-index: 1; 
		}

	</style>

	<script>

		function BuscaRut(){
			var url= "../buscadatos.php";
			var x1=$('#d2').val();
			var x2=$('#frm').val();
			$.ajax({
				type: "POST",
				url: url,
				data: ('dat1='+x1+'&dat2='+x2),
				success:function(resp){
					if(resp==""){
						alert("Rut no encontrado");
						$('#d2').focus();
						$('#d2').select();
					}else{
						form1.d3.value=resp;
					}
				}
			});
		}

		function EliReg(valor,conrut,num){
			var r = confirm("Esta seguro de Elimanar el Documento!\r\nRut: "+conrut+", Documento: "+num);
			if (r == true) {

				var url= "grilla.php";
				$.ajax({
					type: "POST",
					url: url,
					data: ('dat1='+valor),
					success:function(resp){
						CargGrilla();
					}
				});
			}
		}

		function EliRef(valor){
			var r = confirm("Este Documento est\u00E1 asociado a una Nota de Cr\u00E9dito, y su monto anula documento lo que no genera un Voucher en sistema.\r\n\r\nEsta seguro de Elimanar el Referencia!");
			if (r == true) {
				var url= "grilla.php";
				$.ajax({
					type: "POST",
					url: url,
					data: ('EliRefX='+valor),
					success:function(resp){
						CargGrilla();
					}
				});
			}
		}

		function BuscaDoc(){
			var url= "../buscatdoc.php";
			var x1=$('#d4').val();
			$.ajax({
				type: "POST",
				url: url,
				data: ('dat1='+x1),
				success:function(resp){
					if(resp==""){
						alert("Id documento no encontrado");
						$('#d4').focus(); 
						$('#d4').select();
					}else{
						form1.d5.value=resp;
					}
				}
			});
		}

		function BuscaCuenta(){
			var url= "../buscacuenta.php";
			var x1=$('#d7').val();
			$.ajax({
				type: "POST",
				url: url,
				data: ('dat1='+x1),
				success:function(resp){
				if(resp==""){
				alert("No se encontro cuenta");
					$('#d7').focus(); 
					$('#d7').select();
				}else{
					form1.d8.value=resp;
					$('#d9').focus(); 
					$('#d9').select();
				}
				}
			}); 
		}

		function BuscaCuentaFact(vall){
			var url= "../buscacuentafact.php";
			var x1=$('#'+vall).val();
			$.ajax({
				type: "POST",
				url: url,
				data: ('dat1='+x1),
				success:function(resp){
					var r=Number(vall.substr(7, 1));
					var r='mdetalle'+r;

					if(resp==""){
						alert("No se encontro cuenta");
						$('#'+vall).focus(); 
						$('#'+vall).select();
						document.getElementById(r).value="";
					}else{
						document.getElementById(r).value=resp;
					}
				}
			}); 
		}

		function GBDocum(){

			if (form1.ModReg.value!="" && form1.rd7.value!="") {
				if (form1.rd7.value!=form1.d7.value) {

					var r = confirm("Esta asignado una nueva cuenta el Rut: "+form1.d2.value+"\r\nRaz\u00F3n Social: "+form1.d3.value+"\r\n\r\nDesea dejar predeterminada y aplicar a todos los documentos que no han sido procesados?\r\nEsto afectara a todos los periodos");
					if (r == true) {
						form1.swrd7.value="S";
					}else{
						form1.rd7.value="";
						form1.swrd7.value="";          
					}
				}
			}

			var url= "gbdocum.php";

			$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					if(resp!=""){
						$('#msj1').html(resp);
					}else{
						form1.d1.value="<?php echo $textfecha1; ?>"
						form1.d2.value="";
						form1.d3.value="";
						form1.d4.value="";
						form1.d5.value="";
						form1.d6.value="";
						form1.d7.value="";
						form1.rd7.value="";
						form1.swrd7.value="";

						form1.d8.value="";
						form1.d9.value="";
						form1.d10.value="";
						form1.d11.value="";
						form1.d12.value="";
						form1.ModReg.value="";
						$('#msj1').html(resp);
						CargGrilla(); 
						document.getElementById("MovDoc").disabled = true;
					}
				}
			});
		}


		function GBCompr(){
			var url= "xfrmIngComprobante.php";
			$.ajax({
				type: "POST",
				url: url,
				data: $('#fCompr').serialize(),
				success:function(resp){
					if(resp!=""){
						alert(resp);
					}else{
						CargGrilla(); 
					}
				}
			});
		}

		var dobleClick = false;

		function GBDocumCent() {
			t=fmodal.Glosa.value;
			var r = confirm("Esta centralizando un nuevo documento en la contabilidad.");
			if (r == true) {
				GBDocumCentMod();
			}
		}


		function GBDocumCentMod(){

			if (fmodal.swboton.value==1) {
				alert("Procesando Registro");
			}else{
				fmodal.swboton.value=1;
				var url= "xfrmIngDocModal.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#fmodal').serialize(),
					success:function(resp){
						if(resp=="Off"){
							location.href ="../index.php?Msj=95";
						}else{
							if(resp!="Comp" && resp!=""){
								alert(resp);
							}else{
								$("#CMOD").click();
								CargGrilla();
								fmodal.swboton.value="";
							}
						}

					}
				});
			}
		}

		function CargGrilla(){
			var url= "grilla.php";

			$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#grilla').html(resp);
				}
			});
		}

		function CargComprobante(){

			var url= "frmIngComprobante.php";

			$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#grilla').html(resp);
					return false;
				}
			});
		}

		function Lala(valor,cuenta,nomcue,valormer1,valoriva,valormer2,valortotal,nfact,tipoDoc,fdoc){
			fmodal.NFactura.value=valor;
			document.getElementById("mfecha").focus();
			document.getElementById("mfecha").select();
			fmodal.mfecha.value=fdoc;

			fmodal.mdebe1.value="";
			fmodal.mdebe2.value="";
			fmodal.mdebe3.value="";
			fmodal.mdebe4.value="";
			fmodal.mdebe5.value="";

			fmodal.mhaber1.value="";
			fmodal.mhaber2.value="";
			fmodal.mhaber3.value="";
			fmodal.mhaber4.value="";
			fmodal.mhaber5.value="";

			fmodal.TotalAsi.value="";

			if (valormer2<0) {
				fmodal.mhaber3.value=(valormer2*-1);
				valormer2="";
			}

			<?php  
			if ($frm=="C") {
				echo "if (tipoDoc==1) {\n";
				echo '
					fmodal.mcuenta1.value=cuenta;
					fmodal.mdetalle1.value=nomcue;
					fmodal.mdebe1.value=valormer1;
					fmodal.mdebe2.value=valoriva;
					fmodal.mdebe3.value=valormer2;
					fmodal.mhaber4.value=valortotal;
					fmodal.TotalAsi.value=valortotal;
				';
				echo "}else{\n";
				echo '
					fmodal.mhaber1.value=valormer1;
					fmodal.mhaber2.value=valoriva;
					fmodal.mhaber3.value=valormer2;
					fmodal.mdebe4.value=valortotal;
					fmodal.mcuenta1.value=cuenta;
					fmodal.mdetalle1.value=nomcue;
					fmodal.TotalAsi.value=valortotal;
				';
				echo "}\n";
			}

			if ($frm=="V") {
				echo "if (tipoDoc==1) {\n";
				echo "
					fmodal.mcuenta2.value=cuenta;
					fmodal.mdetalle2.value=nomcue;
					fmodal.mdebe1.value=valortotal;
					fmodal.mhaber2.value=valormer1;
					fmodal.mhaber3.value=valoriva;
					fmodal.mhaber4.value=valormer2;
					fmodal.TotalAsi.value=valortotal;
				";
				echo "}else{\n";
				echo "
					fmodal.mhaber1.value=valortotal;
					fmodal.mdebe2.value=valormer1;
					fmodal.mdebe3.value=valoriva;
					fmodal.mdebe4.value=valormer2;
					fmodal.mcuenta2.value=cuenta;
					fmodal.mdetalle2.value=nomcue;
					fmodal.TotalAsi.value=valortotal;
				";
				echo " }\n";
			}
		
			?>
			fmodal.Glosa.value=nfact;
		}

		$(document).ready(function (eOuter) {

			$('input').bind('keypress', function (eInner) {
			//alert(eInner.keyCode);
				if (eInner.keyCode == 13){

					var idinput = $(this).attr('id');

					if(idinput=="d1"){
						$('#d2').focus();
						$('#d2').select();
					}

					if(idinput=="d2"){
						BuscaRut();
						var str =form1.d1.value;
						var mes= Number(str.slice(3, 5));
						var ano= Number(str.slice(6, 10));

						var sumfe=(ano*12)+mes;

						var mesc=<?php echo $dmes; ?>;
						var anoc=<?php echo $dano; ?>;

						var sumpe=(anoc*12)+mesc;
						var res =sumpe-sumfe;

						if (res>=0 && res<=3) {
						}else{
							alert("Este documento tiene diferencias de fecha con respecto Periodo que trabajas");
						}

						$('#d4').focus();
						$('#d4').select();
					}

					if(idinput=="d4"){
						BuscaDoc();
						$('#d6').focus();
						$('#d6').select();
					}

					if(idinput=="d5"){
						$('#d6').focus();
						$('#d6').select();
					}

					if(idinput=="d6"){
						$('#d7').focus();
						$('#d7').select();
					}

					if(idinput=="d7"){
						BuscaCuenta();
						$('#d9').focus();
						$('#d9').select();
					}

					if(idinput=="d9"){
						$('#d10').focus();
						$('#d10').select();
					}

					if(idinput=="d10"){
						if (form1.CTIVA.value>0) {
							var L=form1.d10.value;
							L=L.substring(0,1);

							var t=form1.d10.value;
							t=t.substr(1);

							if (L=="T" || L=="t") {
								form1.d10.value=Math.ceil(t/1.19);
								form1.d11.value=Math.ceil(t)-form1.d10.value;
							}else{
								form1.d11.value=Math.round((form1.d10.value*form1.CTIVA.value)/100);
							}


						}
						$('#d11').focus();
						$('#d11').select();
					}

					if(idinput=="d11"){
						$('#d12').focus();
						$('#d12').select();
					}

					if(idinput=="d12"){
						var Xvar=form1.d9.value+form1.d10.value+form1.d11.value+form1.d12.value;

						if (Xvar<=0){
							alert("Debe Ingresar monto del Documento");
						}else{
							GBDocum();
							$('#d2').focus();
							$('#d2').select();
						}
					}

					if(idinput=="mfecha"){
						$('#mcuenta1').focus();
						$('#mcuenta1').select();
					}

				//linea 1 model
				<?php 
					$i = 1;
					while ($i <= 5) {
						echo "
						if(idinput==\"mcuenta".$i."\"){
							BuscaCuentaFact(this.id);
							$('#mdebe".$i."').focus();
							$('#mdebe".$i."').select();
						}

						if(idinput==\"mdebe".$i."\"){
							$('#mhaber".$i."').focus();
							$('#mhaber".$i."').select();
						}

						if(idinput==\"mhaber".$i."\"){
							$('#mcuenta".($i+1)."').focus();
							$('#mcuenta".($i+1)."').select();
						}
						";

						$i++; 
					}
				?>

				return false;
				}
			});
		}
		);

		function Btg(){
			var Xvar=form1.d9.value+form1.d10.value+form1.d11.value+form1.d12.value;

			if (Xvar<=0){
				alert("Debe Ingresar monto del Documento");
			}else{
				GBDocum();
				$('#d2').focus();
				$('#d2').select();
			}
		}

		function ModReg(valor){
			form1.ModReg.value=valor;
			form1.action="#";
			form1.submit();  
		}

		function Volver(){
			form1.action="../frmMain.php";
			form1.submit();
		}

		function soloNumeros(e){
			var key = window.Event ? e.which : e.keyCode
			return (key >= 48 && key <= 57)
		}
		$( function() {
			$( "#d1" ).datepicker();
			$( "#mfecha" ).datepicker();
		} );

		function data(valor){
			form1.d7.value=valor;
			BuscaCuenta();
			document.getElementById("cmodel").click();
		}

		function dataTD(valor){
			form1.d4.value=valor;
			BuscaDoc();
			document.getElementById("cmodel2").click();
		}

		function data1(valor){
			form1.d2.value=valor;
			BuscaRut();
			document.getElementById("cmodel1").click();
			document.getElementById("d4").focus();
			document.getElementById("d4").select();
		} 
		function Proce(){
			form1.action="frmIngDocCentralizador.php";
			form1.submit();
		}

		function EliDocu(){
			var r = confirm("Esta seguro de Eliminar los Documentos!, Solo afectara a aquellos que no estan centralizados...");
			if (r == true) {
				form1.EliRegi.value="S";
				CargGrilla();
				form1.EliRegi.value="";
			}     
		}
		function GrMov(){
			form1.IdMovDoc.value=form1.ModReg.value;
			form1.ModReg.value="";
			form1.submit();
		}
		jQuery(document).ready(function(e) {
			$('#myModalTD').on('shown.bs.modal', function() {
				$('input[name="BTDocumento"]').focus();
			});

			$('#myModal1').on('shown.bs.modal', function() {
				$('input[name="BRut"]').focus();
			});

			$('#myModal').on('shown.bs.modal', function() {
				$('input[name="BCuenta"]').focus();
			});
		});
	</script>

</head>
<body onload="CargGrilla()">
	<?php 
		include '../nav.php';
	?>

	<div class="container-fluid text-left">
	<div class="row content">

	<?php
		include 'frmIngDocModal.php';
	?>

	<div class="col-sm-12 text-left">
		<div class="col-md-12 text-center">
		<?php 
			if(isset($_GET['Doc'])){
				if($_GET['Doc']==1){
					$StrTitulo='Registro de Documentos Compras o Egresos';
					$frm="C";
					$filRut="P";
				}

				if($_GET['Doc']==2){
					$StrTitulo='Registro de Documentos Ventas o Ingresos';
					$frm="V";
					$filRut="C";
				}
			}
		?>
		</div>
		<div class="clearfix"></div>
		<br></br>
		<!-- <hr> -->
		<form action="#" method="POST" name="form1" id="form1">
			<div class="col-md-10">

				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading text-center"><strong><?php echo $StrTitulo; ?></strong></div>
					<div class="panel-body">

					<div class="col-md-3">
						<label>Fechas</label>

						<input id="d1" name="d1" type="text" class="form-control text-right" size="10" maxlength="10" value="<?php echo $textfecha; ?>" >

						<input type="hidden" name="CTEmpre" id="CTEmpre">
						<input type="hidden" name="CTIVA" id="CTIVA" value="<?php echo $Val_Iva;?>">
						<input type="hidden" name="ModReg" id="ModReg" value="<?php echo $_POST['ModReg']; ?>">
						<input type="hidden" name="EliRegi" id="EliRegi">
						<input type="hidden" name="IdMovDoc" id="IdMovDoc">
					</div> 

					<!-- Modal  buscar rut-->
					<div class="modal fade" id="myModal1" role="dialog">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Listado</h4>
								</div>

								<div class="modal-body">
									<div class="col-md-12">
										<input class="form-control" id="BRut" name="BRut" type="text" placeholder="Buscar...">
									</div>
									<div class="col-md-12">

										<table class="table table-condensed table-hover">
											<thead>
												<tr>
												<th>Rut</th>
												<th>Raz&oacute;n Social</th>
												</tr>
											</thead>
											<tbody id="TableRut">
												<?php 
													$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
													$SQL="SELECT * FROM CTCliPro WHERE tipo='$filRut' AND estado='A' ORDER BY razonsocial";
													$resultados = $mysqli->query($SQL);
													while ($registro = $resultados->fetch_assoc()) {

														echo '
															<tr onclick="data1(\''.$registro["rut"].'\')">
															<td>'.$registro["rut"].'</td>
															<td>'.$registro["razonsocial"].'</td>
															</tr>
														';
													}
													$mysqli->close();
												?>
											</tbody>
										</table>

										<script>
											$(document).ready(function(){
												$("#BRut").on("keyup", function() {
												var value = $(this).val().toLowerCase();
													$("#TableRut tr").filter(function() {
													$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
												});
												});
											});
										</script>

									</div>
									<div class="clearfix"></div>
									<br>
								</div>

								<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal" id="cmodel1">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- fin buscar rut -->        
					<div class="col-md-3">
						<label>Rut</label>
						<div class="input-group"> 
							<input type="text" class="form-control text-right" maxlength="10" id="d2" name="d2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $LRut;?>" required>
							<div class="input-group-btn"> 
								<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal1" onfocus="javascript:document.getElementById('d4').focus();">
									<span class="glyphicon glyphicon-search"></span> 
								</button>
							</div> 
						</div> 
					</div>

					<div class="col-md-6">
						<label>Razon Social</label>  
						<input type="text" class="form-control" id="d3" name="d3"  value="<?php echo $LRSocial; ?>" readonly="false">
					</div>

					<div class="clearfix"> </div>

					<div class="col-md-3">
						<label>Documento SII </label>
						<div class="input-group"> 
							<input type="text" class="form-control text-right" id="d4" name="d4" maxlength="50" value="<?php echo $Ltdoc; ?>" required>
							<div class="input-group-btn"> 
								<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalTD" onfocus="javascript:document.getElementById('d6').focus();">
									<span class="glyphicon glyphicon-search"></span> 
								</button>
							</div> 
						</div> 
					</div>

					<!-- Modal  buscar tipo documento-->

					<div class="modal fade" id="myModalTD" role="dialog">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Listado</h4>
								</div>

								<div class="modal-body">
									<div class="col-md-12">
										<input class="form-control" id="BTDocumento" name="BTDocumento" type="text" placeholder="Buscar...">
									</div>
									<div class="col-md-12">

										<table class="table table-condensed table-hover">
											<thead>
												<tr>
													<th>SII</th>
													<th>Detalle</th>
												</tr>
											</thead>
											<tbody id="TablaTdd">
												<?php 
													$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
													$SQL="SELECT * FROM `CTTipoDocumento` WHERE tiposii>0 AND estado='A' ORDER BY id";
													$resultados = $mysqli->query($SQL);
													while ($registro = $resultados->fetch_assoc()) {
														echo '
															<tr onclick="dataTD(\''.$registro["tiposii"].'\')">
															<td>'.$registro["tiposii"].'</td>
															<td>'.strtoupper($registro["nombre"]).'</td>
															</tr>
														';
													}
													$mysqli->close();
												?>
											</tbody>
										</table>

										<script>
											$(document).ready(function(){
												$("#BTDocumento").on("keyup", function() {
												var value = $(this).val().toLowerCase();
													$("#TablaTdd tr").filter(function() {
													$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
												});
												});
											});
										</script>

									</div>
									<div class="clearfix"></div>
									<br>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-danger" data-dismiss="modal" id="cmodel2">Cerrar</button>
								</div>
							</div>
						</div>
					</div>

					<!-- fin buscar tipo documento --> 

					<div class="col-md-6">
						<label>Documento</label>
						<input type="text" class="form-control" id="d5" name="d5" maxlength="50" value="<?php echo $LDocumen; ?>" readonly="false">
					</div>

					<div class="col-md-3">
						<label>N&deg; Documento </label>
						<input type="text" class="form-control text-right" id="d6" name="d6" maxlength="50" value="<?php echo $LNumero; ?>" required>
						<span class="label label-default">Rango 152-268 /// Lote 2-102 (Mes-Cantidad) </span>
					</div>

					<div class="clearfix"> </div>

					<!-- Modal  buscar codigo-->

					<div class="modal fade" id="myModal" role="dialog">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Listado</h4>
								</div>

								<div class="modal-body">
									<div class="col-md-12">
										<input class="form-control" id="BCuenta" name="BCuenta" type="text" placeholder="Buscar...">
									</div>
									<div class="col-md-12">

										<table class="table table-condensed table-hover">
											<thead>
												<tr>
												<th>Codigo</th>
												<th>Detalle</th>
												<th>Tipo de Cuenta</th>
												</tr>
											</thead>
											<tbody id="TableCta">
												<?php 
													$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
													if ($_SESSION["PLAN"]=="S"){
														$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
													}else{
														$SQL="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
													}
													$resultados = $mysqli->query($SQL);
													while ($registro = $resultados->fetch_assoc()) {
														$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
														$resultados1 = $mysqli->query($SQL1);
														while ($registro1 = $resultados1->fetch_assoc()) {
															$tcuenta=$registro1["nombre"];
														}
														echo '
															<tr onclick="data(\''.$registro["numero"].'\')">
															<td>'.$registro["numero"].'</td>
															<td>'.strtoupper($registro["detalle"]).'</td>
															<td>'.$tcuenta.'</td>
															</tr>
														';
													}
													$mysqli->close();
												?>
											</tbody>
										</table>

										<script>
											$(document).ready(function(){
												$("#BCuenta").on("keyup", function() {
												var value = $(this).val().toLowerCase();
													$("#TableCta tr").filter(function() {
													$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
												});
												});
											});
										</script>

									</div>
									<div class="clearfix"></div>
									<br>
								</div>

								<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal" id="cmodel">Cerrar</button>
								</div>
							</div>
						</div>
					</div>

					<!-- fin buscar codigo -->  

					<div class="col-md-3">
						<label>Cuenta</label>
						<div class="input-group"> 
							<input type="text" class="form-control text-right" id="d7" name="d7" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $LCuenta;?>">
							<input type="hidden" name="rd7" id="rd7" value="<?php echo $LCuenta;?>">
							<input type="hidden" name="swrd7" id="swrd7"> 
							<div class="input-group-btn"> 
								<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" onfocus="javascript:document.getElementById('d9').focus();">
									<span class="glyphicon glyphicon-search"></span> 
								</button>
							</div> 
						</div> 
					</div>


					<div class="col-md-6">
						<label>Descripci&oacute;n</label>
						<input type="text" class="form-control" id="d8" name="d8" maxlength="50" value="<?php echo $LNCuenta; ?>" readonly="false">
					</div>

					<div class="clearfix"> </div>
					<div class="col-md-3">
						<label>Exento</label>
						<input type="text" class="form-control text-right" id="d9" name="d9" value="<?php echo $LExento; ?>">
					</div> 
					<div class="col-md-3">
						<label>Neto  &nbsp;  &nbsp;</label><span class="glyphicon glyphicon-eye-open" title="* Si tiene el monto Total y desea obtener el monto Neto debe antepone la letra T y presionar Enter, para realizar el c&aacute;lculo del Neto e IVA, ejemplo 'T135000' "></span>
						<input type="text" class="form-control text-right" id="d10" name="d10" value="<?php echo $LNeto; ?>" >
						<input type="hidden" class="form-control" id="frm" name="frm" value="<?php echo $frm;?>" >
					</div>

					<div class="col-md-3">
						<label>Iva</label>  
						<input type="text" class="form-control text-right" id="d11" name="d11" value="<?php echo $LIva; ?>" >
					</div>

					<div class="col-md-3">
						<label>Reten/Imp.Esp</label>  
						<input type="text" class="form-control text-right" id="d12" name="d12" value="<?php echo $LRete; ?>" >
					</div>  

					<div class="clearfix"></div>
					<br>
					<!-- <div class="col-md-3"> -->

							<!-- <button type="button" class="btn btn-block btn-grabar" onclick="()">
								<span class="glyphicon glyphicon-saved"></span> Grabar
							</button> -->

							<button type="button" class="btn btn-grabar" onclick="Btg()">
								<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
							</button>

							<button type="button" class="btn btn-cancelar" onclick="Volver()">
								<span class="glyphicon glyphicon-remove"></span> Cancelar
							</button>  


					<!-- </div> -->
					<br>
					<div id="msj1" class="col-md-9">
					</div>
				</div>
				</div>
			</div>

			<div class="col-md-2">

				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Utilidades</div>
					<div class="panel-body text-center">

						<button type="button" class="btn btn-block btn-grabar" id="ProceDoc" name="ProceDoc" onclick="Proce()" title="Con este proceso realizaras un solo Voucher de todos los documentos sin procesar, el cual ser&aacute; propuesta a partir de la informaci&oacute;n indicada en la Tabla">
							<span class="glyphicon glyphicon-saved"></span> Centralizaci&oacute;n Masiva
						</button>
						<br>

						<button type="button" class="btn btn-block btn-modificar" id="MovDoc" name="MovDoc" <?php if ($_POST['ModReg']!=""){}else{ echo "disabled"; }?> title="Mover documento de Periodo" data-toggle="modal" data-target="#MovDocumento">
							<span class="glyphicon glyphicon-remove"></span> Mover Documento
						</button>

							<div class="modal fade" id="MovDocumento" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Mover Documento de Periodo</h4>
									</div>
									<div class="modal-body">
										
										<div class="col-md-6 text-right">
										<div class="input-group">
											<span class="input-group-addon">Mes</span>
											<select class="form-control" id="messelect" name="messelect" required>
											<?php 
												$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
												$i=1;
												$dmes=$dmes*1;
												while($i<=12){

													if ($i==$dmes) {
														echo "<option value ='".$i."' selected>".$Meses[($i-1)]."</option>";
													}else{
														echo "<option value ='".$i."'>".$Meses[($i-1)]."</option>";
													}
													$i++;
												}
											?>
											</select>
										</div>
										</div>

										<div class="col-md-6">
										<div class="input-group">
											<span class="input-group-addon">A&ntilde;o</span>
											<select class="form-control" id="anoselect" name="anoselect" required>
											<?php 
												$yoano=date('Y');
												$tano="2010";

												while($tano<=($yoano+1)){
													if ($dano==$tano) {
														echo "<option value ='".$tano."' selected>".$tano."</option>";
													}else{
														echo "<option value ='".$tano."'>".$tano."</option>";
													}
													$tano=$tano+1;
												}
											?>
											</select>
										</div>
										</div>

										<div class="clearfix"></div>
										<br>

									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-moficicar" data-dismiss="modal" onclick="GrMov()">Mover</button>
										<button type="button" class="btn btn-cancelar" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
							</div>


						<br>

						<button type="button" class="btn btn-block btn-cancelar" onclick="EliDocu()" title="Eliminar&aacute; todos los documentos que no est&aacute;n procesados">
							<span class="glyphicon glyphicon-remove"></span> Eliminar Documentos
						</button>


					</div>
				</div>

				<br>
				<br>
			</div>

	<!-- 		<div class="col-md-2">
				
			</div> -->

		</form>

		<div class="clearfix"> </div>
		<hr>


		<div class="col-md-4"><h4>Documentos del Periodo <?php echo $Periodo; ?></h4></div><div class="col-md-8"><input class="form-control" id="myInput" type="text" placeholder="Buscador..."></div>

		<div class="col-md-12" id="grilla">

		</div>

	</div>

	</div>
	</div> 

	<?php include '../footer.php'; ?>

</body>

	<script type="text/javascript">
		$( "#d1" ).datepicker({
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
				$('#d1').val(dateText);
				$('#d2').focus();
				$('#d2').select();
			}
		});  

		$( "#mfecha" ).datepicker({
			// Formato de la fecha
			dateFormat: "dd-mm-yy",
			// Primer dia de la semana El lunes
			firstDay: 1,
			// Dias Largo en castellano
			dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
			// Dias cortos en castellano
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
			// Nombres largos de los meses en castellano
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
			// Nombres de los meses en formato corto 
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec"],
			// Cuando seleccionamos la fecha esta se pone en el campo Input 
			onSelect: function(dateText) {
				// $('#d2').val(dateText);
			}
		});
		form1.d2.focus();
	</script>

	<script>
		$(document).ready(function(){
			$("#myInput").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#myTable tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});
		});
	</script>
</html>