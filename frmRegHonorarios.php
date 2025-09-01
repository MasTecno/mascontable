<?php

	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:frmMain.php");
		exit;
	}

	if(isset($_GET['Doc'])){
		if($_GET['Doc']==1){
			$frm="C"; 
		}
		if($_GET['Doc']==2){
			$frm="V";
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

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);

	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}
	}


	$SQL="SELECT * FROM CTParametros";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro['tipo']=='RETE_HONO') {
			$Val_Ret=$registro['valor'];
		}
		if ($registro['tipo']=='RETE_FACT') {
			$Val_FRet=$registro['valor'];
		}
	}

	if ($dano=="2020") {
		$Val_Ret="10.75";
		$Val_FRet="0.8925";
	}

	$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	}else{
		$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa=''";      
	}

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro['tipo']=="R") { ///honorarios Recibido
			$Rec1=$registro['L1'];
			$Rec2=$registro['L2'];
			$Rec3=$registro['L3'];

			if ($_SESSION["PLAN"]=="S"){
				$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$Rec1' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL1="SELECT * FROM CTCuentas WHERE numero='$Rec1'";
			}
			$res = $mysqli->query($SQL1);
			while ($reg = $res->fetch_assoc()) {
				$XnL1=$reg['detalle'];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$Rec2' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL1="SELECT * FROM CTCuentas WHERE numero='$Rec2'";
			}
			$res = $mysqli->query($SQL1);
			while ($reg = $res->fetch_assoc()) {
				$XnL2=$reg['detalle'];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$Rec3' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL1="SELECT * FROM CTCuentas WHERE numero='$Rec3'";
			}

			$res = $mysqli->query($SQL1);
			while ($reg = $res->fetch_assoc()) {
				$XnL3=$reg['detalle'];
			}
		}

		if ($registro['tipo']=="E") { ///Honorarios emitidos
			$Emi1=$registro['L1'];
			$Emi2=$registro['L2'];
			$Emi3=$registro['L3'];
		}
	}


	$mysqli->close();

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

	<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
	<script src="js/jquery.dataTables.min.js"></script>

	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<!-- <script type="text/javascript" src="js/jquery.maskedinput-1.2.2-co.min.js"></script> -->

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

	<script>
	$(document).ready(function() {
		$('#example').DataTable();
		$('#example1').DataTable();
	} );

	function BuscaRut(){
	var url= "buscadatos.php";
	var x1=$('#d2').val();
	var x2=$('#frm').val();
	$.ajax({
	type: "POST",
	url: url,
	data: ('dat1='+x1+'&dat2='+x2),
	success:function(resp){
	if(resp==""){
	form1.d3.value="";
	alert("Rut no encontrado");
	$('#d3').focus();
	$('#d3').select();
	}else{
	form1.d3.value=resp;
	}
	}
	});
	}
	function EliReg(valor){

	var url= "grillahonorarios.php";
	$.ajax({
	type: "POST",
	url: url,
	data: ('dat1='+valor),
	success:function(resp){
	CargGrilla();
	}
	});
	}


	function BuscaCuenta(){
	var url= "buscacuenta.php";
	var x1=$('#d7').val();
	$.ajax({
	type: "POST",
	url: url,
	data: ('dat1='+x1),
	success:function(resp)
	{
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

	function CargGrilla(){

	var url= "grillahonorarios.php";

	$.ajax({
	type: "POST",
	url: url,
	data: $('#form1').serialize(),
	success:function(resp)
	{
	$('#grilla').html(resp);
	}
	});
	}

	function GBDocum(){

	var url= "gbhonorario.php";

	$.ajax({
	type: "POST",
	url: url,
	data: $('#form1').serialize(),
	success:function(resp)
	{       
	if(resp!=""){
	$('#msj1').html(resp);
	}else{
	form1.d2.value="";
	form1.d3.value="";

	//form1.d5.value="";
	form1.d6.value="";
	// form1.d7.value="";
	// form1.d8.value="";

	form1.d10.value="";
	form1.d11.value="";
	form1.d12.value="";
	form1.d13.value="";

	$('#msj1').html(resp);
	CargGrilla(); 
	}
	}
	});
	}

	$(document).ready(function (eOuter) {

	$('input').bind('keypress', function (eInner) {
	//alert(eInner.keyCode);
	if (eInner.keyCode == 13) //if its a enter key
	{

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

	$('#d5').focus();
	$('#d5').select();
	}

	if(idinput=="d5"){
	$('#d6').focus();
	$('#d6').select();
	}

	if(idinput=="d6"){
	$('#d10').focus();
	$('#d10').select();
	}

	// if(idinput=="d7"){
	//   BuscaCuenta();
	//   $('#d10').focus();
	//   $('#d10').select();
	// }

	if(idinput=="d10"){
	RETEN();
	$('#d11').focus();
	$('#d11').select();
	}

	if(idinput=="mfecha"){
	$('#mcuenta1').focus();
	$('#mcuenta1').select();
	}

	//linea 1 model

	<?php 

	$i = 1;
	while ($i <= 3) {
	echo "
	if(idinput==\"mcuenta".$i."\"){
	BuscaCuentaMod(this.id);
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

	function BuscaCuentaMod(vall){
	var url= "buscacuentafact.php";
	var x1=$('#'+vall).val();
	$.ajax({
	type: "POST",
	url: url,
	data: ('dat1='+x1),
	success:function(resp)
	{

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

	function Lala(iddoc,canBruto,canRete,canLiqui,NDoc,NCuenta,TDoc,Cadena,RutHono){

	fmodal.iddoc.value=iddoc;
	fmodal.canBruto.value=canBruto;
	fmodal.canRete.value=canRete;
	fmodal.canLiqui.value=canLiqui;
	fmodal.NDoc.value=NDoc;
	fmodal.NCuenta.value=NCuenta;
	fmodal.TDoc.value=TDoc;
	fmodal.Cadena.value=Cadena;
	fmodal.RutHono.value=RutHono;

	if (TDoc=="R") {
	Cod1=<?php echo $Rec1; ?>;
	Cod2=<?php echo $Rec2; ?>;
	Cod3=<?php echo $Rec3; ?>;

	fmodal.mcuenta1.value=Cod1;
	fmodal.mcuenta2.value=Cod2;
	fmodal.mcuenta3.value=Cod3;

	fmodal.mdebe1.value=canBruto;
	fmodal.mhaber2.value=canRete;
	fmodal.mhaber3.value=canLiqui;
	fmodal.Glosa.value="Boleta de Honorarios Recibido "+NDoc;
	}

	// if (TDoc=="E") {
	//   Cod1=<?php echo $Rec1; ?>;
	//   Cod2=<?php echo $Rec2; ?>;
	//   Cod3=<?php echo $Rec3; ?>;

	//   fmodal.mcuenta1.value=Cod1;
	//   fmodal.mcuenta2.value=Cod2;
	//   fmodal.mcuenta3.value=Cod3;
	// }

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
	function data1(valor){
		form1.d2.value=valor;
		BuscaRut();
		document.getElementById("cmodel1").click();
		document.getElementById("d6").focus();
		document.getElementById("d6").select();
	} 

	function RETEN(){
	if (form1.d10.value!="") {
	if(document.getElementById("retinc").checked==true){
	if (form1.CTRETEF.value>0) {
	form1.d13.value=parseInt(form1.d10.value);
	form1.d11.value=Math.round((form1.d10.value/form1.CTRETEF.value));
	form1.d12.value= parseInt(form1.d11.value)- parseInt(form1.d13.value);
	}
	}else{
	if (form1.CTRETE.value>0) {
	form1.d11.value=parseInt(form1.d10.value);
	form1.d12.value=Math.round((form1.d11.value*form1.CTRETE.value)/100);
	form1.d13.value= parseInt(form1.d11.value)- parseInt(form1.d12.value);
	}
	}
	}
	}

	function Volver(){
	form1.action="frmMain.php";
	form1.submit();
	}

	$(document).ready(function() {
	$('#example').DataTable();
	} );

	function data(valor){
	var cas=form1.casilla.value;
	document.getElementById(cas).value=valor;

	//$('#'+cas).val()=valor;
	BuscaCuenta(form1.casilla.value);
	document.getElementById("cmodel").click();
	}


	$(document).ready(function (eOuter) {

	$('input').bind('keypress', function (eInner) {
	//alert(eInner.keyCode);
	if (eInner.keyCode == 13){

	var idinput = $(this).attr('id');

	<?php 

	$i = 1;
	while ($i <= 5) {
	echo "
	if(idinput==\"mdetalle".$i."\"){
	BuscaCuenta(this.id);
	$('#mdetalle".($i+1)."').focus();
	$('#mdetalle".($i+1)."').select();
	}
	";

	$i++; 
	}

	?>
	return false;
	}
	});
	});     

	function BuscaCuenta(vall){
	var url= "buscacuenta.php";
	var x1=$('#'+vall).val();
	$.ajax({
	type: "POST",
	url: url,
	data: ('dat1='+x1),
	success:function(resp)
	{

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

	function GBDocumCent(){
	var url= "xfrmRegHonorariosModal.php";
	$.ajax({
	type: "POST",
	url: url,
	data: $('#fmodal').serialize(),
	success:function(resp)
	{
	if(resp!=""){
	alert(resp);
	// $('#msjx').html(resp);
	}else{
	// $('#msjx').html(resp);
	CargGrilla();
	$("#CMOD").click();
	}
	}
	});
	}


	</script>

</head>
<body onload="CargGrilla()">


	<?php 
	include 'nav.php';
	?>

	<div class="container-fluid text-left">

	<div class="row content">

	<?php
		include 'frmRegHonorariosModal.php';
	?>


	<div class="col-sm-12 text-left">
	<br>

	<form action="#" method="POST" name="form1" id="form1">
		<div class="col-md-10">
			<div class="panel panel-default">
			<div class="panel-heading text-center"><strong>Registro de Honorarios</strong></div>
			<div class="panel-body">

			<div class="col-md-2">
			<label for="d1">Fecha</label>

			<input id="d1" name="d1" type="text" class="form-control" size="10" maxlength="10" value="<?php echo $textfecha; ?>">

			<input type="hidden" name="CTEmpre" id="CTEmpre">
			<input type="hidden" name="CTRETE" id="CTRETE" value="<?php echo $Val_Ret;?>">
			<input type="hidden" name="CTRETEF" id="CTRETEF" value="<?php echo $Val_FRet;?>">
			</div> 

			<!-- Modal  buscar rut-->
			<div class="modal fade" id="myModal1" role="dialog">
			<div class="modal-dialog modal-lg">
			<div class="modal-content">
			<div class="modal-header">
			<!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
			<h4 class="modal-title">Listado</h4>
			</div>

			<div class="modal-body">
			<div class="col-md-12">

			<table id="example1" class="display" cellspacing="0" width="100%">
			<thead>
			<tr>
			<th>Rut</th>
			<th>Raz&oacute;n Social</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
			<th>Rut</th>
			<th>Raz&oacute;n Social</th>
			</tr>
			</tfoot>
			<tbody>
			<?php 

			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

			$SQL="SELECT * FROM CTCliPro WHERE estado='A' AND tipo='P' ORDER BY razonsocial";
			$resultados = $mysqli->query($SQL);

			while ($registro = $resultados->fetch_assoc()) {
			echo '<tr onclick="data1(\''.$registro["rut"].'\')">
			<td>'.$registro["rut"].'</td>
			<td>'.$registro["razonsocial"].'</td>
			</tr>
			';
			}
			$mysqli->close();
			?>

			</tbody>
			</table>
			</div>
			</div>

			<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal" id="cmodel1">Cerrar</button>
			</div>
			</div>
			</div>
			</div>
			<!-- fin buscar rut -->      

			<div class="col-md-2">
			<label for="d7">Rut</label>
			<div class="input-group"> 
			<input type="text" class="form-control" id="d2" name="d2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $LRut;?>" required>
			<div class="input-group-btn"> 
			<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal1" onfocus="javascritp:document.getElementById('d6').focus();">
			<span class="glyphicon glyphicon-search"></span> 
			</button>
			</div> 
			</div> 
			</div>

			<div class="col-md-6">
			<label for="d3">Razon Social</label>  
			<input type="text" class="form-control" id="d3" name="d3"  value="<?php echo $LRSocial; ?>" onChange="javascript:this.value=this.value.toUpperCase();">
			</div>

			<div class="clearfix"> </div>


			<div class="col-md-2">
			<label for="d6">N&deg; Documento </label>
			<input type="text" class="form-control text-right" id="d6" name="d6" maxlength="50" value="<?php echo $LNumero; ?>" required>
			</div>          

			<div class="col-md-2">
			<label for="d10">Monto</label>  
			<input type="text" class="form-control text-right" id="d10" name="d10" onblur="RETEN()" >
			</div>

			<div class="clearfix"> </div>

			<div class="col-md-2">
			<label for="d11">Bruto</label>  
			<input type="text" class="form-control text-right" id="d11" name="d11" readonly="false">
			</div>

			<div class="col-md-2">
			<label for="d11">Retenci&oacute;n</label>  
			<input type="text" class="form-control text-right" id="d12" name="d12" readonly="false">
			</div>

			<div class="col-md-2">
			<label for="d11">Liquido</label>  
			<input type="text" class="form-control text-right" id="d13" name="d13" readonly="false">
			</div>  

			<div class="col-md-2">
			<label for="d11">Periodo</label>  
			<input type="text" class="form-control text-right" id="PERD" name="PERD" readonly="false" value="<?php echo $Periodo; ?>">
			</div> 

			<div class="clearfix"></div>
			<br>
			<label class="checkbox-inline">
			<input type="checkbox" id="retinc" name="retinc" onclick="RETEN()" checked> Retenci&oacute;n no incluida
			</label>

			<div class="clearfix"></div>
			<br>

			<div class="col-md-10" id="msj1">
			</div>
			<div class="clearfix"></div>

			<hr>

			<button type="button" class="btn" onclick="GBDocum()">
			<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
			</button>

			<button type="button" class="btn btn-default" onclick="Volver()">
			<span class="glyphicon glyphicon-remove"></span> Cancelar
			</button>  


			</div>
			</div>
		</div>

		<div class="col-md-2">
			<div class="panel panel-default">
			<div class="panel-heading text-center"><strong>Utilidades</strong></div>
			<div class="panel-body">

			</div>
			</div>
		</div>

		<!-- <div class="clearfix"></div> -->

	</form>



	<div class="clearfix"> </div>
	<hr>



	<div class="col-md-12">       

	<div class="col-md-4"> <h4>Documentos del Periodo <?php echo $Periodo; ?></h4></div>
	<div class="col-md-8"><input class="form-control" id="myInput" type="text" placeholder="Buscar..."></div>

	</div>
	<div class="clearfix"></div>
	<br>


	<table class="table table-hover TamGri" id="grilla">

	</table>




	</div>

	</div>
	</div> 

	<?php include 'footer.php'; ?>

</body>
<script type="text/javascript">
// document.getElementById("d1").focus();
// document.getElementById("d1").select();
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
dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
// Dias cortos en castellano
dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
// Nombres largos de los meses en castellano
monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
// Nombres de los meses en formato corto 
monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
// Cuando seleccionamos la fecha esta se pone en el campo Input 
onSelect: function(dateText) { 
// $('#d2').val(dateText);
}
});  

$(document).ready(function(){
$("#myInput").on("keyup", function() {
var value = $(this).val().toLowerCase();
$("#ListDoc tr").filter(function() {
$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
});
});
});

</script>
</html>