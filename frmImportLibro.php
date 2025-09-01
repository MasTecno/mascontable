<?php
    include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];

    if($Periodo==""){
      header("location:frmMain.php");
      exit;
    }

	$dmes = substr($Periodo,0,2);
	$dano = substr($Periodo,3,4);

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


    $SQL="SELECT * FROM CTPlantillas WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    $resultados = $mysqli->query($SQL);
    $row_cnt = $resultados->num_rows;
    if ($row_cnt==0 && $_SESSION["PLAN"]=="S") {
        $SQL="SELECT * FROM CTPlantillas WHERE rut_empresa=''";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            $xnombre=$registro["nombre"];
            $xtdocumento=$registro["tipodocumento"];
            $xrut=$registro["rut"];
            $xrsocial=$registro["rsocial"];
            $xnumero=$registro["numero"];
            $xfecha=$registro["fecha"];
            $xexento=$registro["exento"];
            $xneto=$registro["neto"];
            $xiva=$registro["iva"];
            $xretencion=$registro["retencion"];
            $xtotal=$registro["total"];
            $xtipo=$registro["tipo"];
            $xcuenta=$registro["cuenta"];
            $mysqli->query("INSERT INTO CTPlantillas VALUE('','".$_SESSION['RUTEMPRESA']."','$xnombre','$xrut','$xrsocial','$xcuenta','$xtdocumento','$xnumero','$xfecha','$xexento','$xneto','$xiva','$xretencion','$xtotal','$xtipo','A')");
        }
    }


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

      	////Cuento Linea del Archivo
      	$LArchivo=0;
      	$fp = fopen ($archivo,"r"); 
       	while ($data = fgetcsv ($fp, 0, $_POST['separador'])){
       		$LArchivo=$LArchivo+1;
       	}

       	//Estructura del Archivo a cargar y posiciones
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTPlantillas WHERE estado='A' AND id='".$plantilla."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$LRut=$registro["rut"];
			$LRSocial=$registro["rsocial"];
			$LCuenta=$registro["cuenta"];
			$LTipoDocumento=$registro["tipodocumento"];
			$LNumero=$registro["numero"];
			$LFecha=$registro["fecha"];
			$LExento=$registro["exento"];
			$LNeto=$registro["neto"];
			$LIva=$registro["iva"];
			$LRetencion=$registro["retencion"];
			$LTotal=$registro["total"];
			$LTipo=$registro["tipo"];
		}

		// $LPeriodo=$tpediodo;

		if ($messelect<=9) {
			$messelect="0".$messelect;
		}

		$LPeriodo=$messelect."-".$anoselect;

		$dmes = substr($LPeriodo,0,2);
		$dano = substr($LPeriodo,3,4);

		// if ($LCuenta=="999"){
		// 	$LCuenta="999999999999";
		// }

		/// Datos para procesar archivo

		// $str="A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AR,AS,AT,AU,AV,AW,AX,AY,AZ,BA,BB,BC,BD,BE,BF,BG,BH,BI,BJ,BK,BL,BM,BN,BO,BP,BQ,BR,BS,BT,BU,BV,BW,BX,BY,BZ,CA,CB,CC,CD,CE,CF,CG,CH,CI,CJ,CK,CL,CM,CN,CO,CP,CQ,CR,CS,CT,CU,CV,CW,CX,CY,CZ,DA,DB,DC,DD,DE,DF,DG,DH,DI,DJ,DK,DL,DM,DN,DO,DP,DQ,DR,DS,DT,DU,DV,DW,DX,DY,DZ,EA,EB,EC,ED,EE,EF,EG,EH,EI,EJ,EK,EL,EM,EN,EO,EP,EQ,ER,ES,ET,EU,EV,EW,EX,EY,EZ,FA,FB,FC,FD,FE,FF,FG,FH,FI,FJ,FK,FL,FM,FN,FO,FP,FQ,FR,FS,FT,FU,FV,FW,FX,FY,FZ,GA,GB,GC,GD,GE,GF,GG,GH,GI,GJ,GK,GL,GM,GN,GO,GP,GQ,GR,GS,GT,GU,GV,GW,GX,GY,GZ";
		$str="A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AR,AS,AT,AU,AV,AW,AX,AY,AZ,BA,BB,BC,BD,BE,BF,BG,BH,BI,BJ,BK,BL,BM,BN,BO,BP,BQ,BR,BS,BT,BU,BV,BW,BX,BY,BZ";

		$ListError="";
		$NFacturasError="";
       	$STRSQL = "INSERT INTO CTRegDocumentos VALUES ";
       	$LFechaReg=date("Y/m/d");
		$row = 1; 
      	
				/////BUSCO COLUMNA DEL RUT
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				while ($SW == 1) {
					$porciones = explode(",", $LRut);
					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						$LRut=$contp;
						$cont++;
						$SW1=1;
					}else{
						$SW=0;
						// break;
					}
				}

				/////BUSCO COLUMNA DEL RUT
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				while ($SW == 1) {
					$porciones = explode(",", $LRSocial);
					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						$LRSocial=$contp;
						$cont++;
						$SW1=1;
					}else{
						$SW=0;
						// break;
					}
				}



				/////BUSCO COLUMNA DEL tipo documento
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				while ($SW == 1) {
					$porciones = explode(",", $LTipoDocumento);
					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						$LTipoDocumento=$contp;
						$cont++;
						$SW1=1;
					}else{
						$SW=0;
						// break;
					}
				}

				/////BUSCO COLUMNA DEL NUMERO documento
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				while ($SW == 1) {
					$porciones = explode(",", $LNumero);
					if ($porciones[$cont]!="") {
						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						$LNumero=$contp;
						$cont++;
						$SW1=1;
					}else{
						$SW=0;
						// break;
					}
				}      	
				$NewRSocial=0;
		$fp = fopen ($archivo,"r");
		while ($data = fgetcsv ($fp, 0, $_POST['separador'])){
	        if($row>=2){
	        	$texento=0;
	        	$tneto=0;
	        	$tiva=0;
	        	$tretencion=0;
	        	$ttotal=0;
	        	$SwInsrtDife=0;
	        	///Determino si el tiepo de documento esta en el mantenedor y es un codigo del SII
	        	$IDDoc=0;
						$SQL="SELECT * FROM CTTipoDocumento  WHERE tiposii='$data[$LTipoDocumento]'";
						$resultados = $mysqli->query($SQL);
		        		while ($registro = $resultados->fetch_assoc()) {
							$IDDoc=$registro['id'];
						}

						if ($IDDoc==0) {
							$ListError=$ListError.'<div class="alert alert-danger"><strong>Advertencia!</strong> Error documento en el documento <strong>'.$data[$LNumero].'</strong>, el tipo de documento no es valido o no esta cargado como documento SII, revisar mantenedor. <br>Operaci&oacute;n Cancelada</div><br>';
							break;
						}

						/// Verifico que la factura no esta ingresada y 
						$SQL="SELECT * FROM CTRegDocumentos WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$data[$LRut]' AND id_tipodocumento='$IDDoc' AND numero='$data[$LNumero]' AND tipo='$LTipo'";
						$resultados = $mysqli->query($SQL);
						$row_cnt = $resultados->num_rows;
						if ($row_cnt>0) {
							if (isset($_POST['check2'])) {
								$SwInsrtDife=1;
							}else{

								$SQL="SELECT * FROM CTRegDocumentos WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$data[$LRut]' AND id_tipodocumento='$IDDoc' AND numero='$data[$LNumero]' AND tipo='$LTipo'";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									$PerDocumento=$registro['periodo'];
								}

								$NFacturasError .=$data[$LNumero].' -> '.$PerDocumento."<br> ";
								$ListError='<div class="alert alert-danger"><strong>Advertencia!</strong> El o los siguientes documento ya estan ingresados <br> <strong>'.$NFacturasError.'</strong>. <br>Operaci&oacute;n Cancelada.</div><br>';
							}
						}

				if (isset($_POST['check2'])) {
					$ListError='';
				}

				/////SUMA COLUMNAD DEL FECHA
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				while ($SW == 1) {
					$porciones = explode(",", $LFecha);

					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						if($data[$contp]>0){
							$xLFecha="$data[$contp]";
						}
						$cont++;
						$SW1=1;
					}else{
						//$contp=0;
						$SW =0;
						// break;
					}
				}

				$dia = substr($xLFecha,0,2);
			    $mes = substr($xLFecha,3,2);
			    $ano = substr($xLFecha,6,4);

			    $xfecha=$ano."-".$mes."-".$dia;

			    /// Valido formato de fecha
				if (checkdate($mes,$dia,$ano)==1) {
				}else{
					$ListError=$ListError.'<div class="alert alert-danger"><strong>Advertencia!</strong> El formato fecha del documento no es compatible.<br>Operaci&oacute;n Cancelada</div><br>';
					break;
				}

				/////SUMA COLUMNAD DEL NETO
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				$tneto=0;
				while ($SW == 1) {
					$porciones = explode("+", $LNeto);

					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						if($data[$contp]>0){
							$tneto=$tneto+"$data[$contp]";
						}
						$cont++;
						$SW1=1;
					}else{
						//$contp=0;
						$SW=0;
						// break;
					}
				}

				/////SUMA COLUMNAD DEL Exento
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				$texento=0;
				while ($SW == 1) {
					$porciones = explode("+", $LExento);

					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						//echo $cont." $data[$contp]<br>";
						if($data[$contp]>0){
							$texento=$texento+"$data[$contp]";
						}
						$cont++;
						$SW1=1;
					}else{
						//$contp=0;
						$SW =0;
						// break;
					}
				}
				/////SUMA COLUMNAD DEL IVA
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				$tiva=0;
				while ($SW == 1) {
					$porciones = explode("+", $LIva);
					
					if ($porciones[$cont]!="") {
						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						if($data[$contp]>0){
							$tiva=$tiva+"$data[$contp]";
						}
						$cont++;
						$SW1=1;
					}else{
						//$contp=0;
						$SW=0;
						// break;
					}
				}
				/////SUMA COLUMNAD DEL RETENCION
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				$tretencion=0;
				while ($SW == 1) {
					$porciones = explode("+", $LRetencion);

					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						if($data[$contp]>0){
							$tretencion=$tretencion+"$data[$contp]";
						}
						$cont++;
						$SW1=1;
					}else{
						//$contp=0;
						$SW =0;
						// break;
					}
				}
				/////SUMA COLUMNAD DEL TOTAL
				$SW=1;
				$SW1=1;
				$cont=0;
				$contp=0;
				$ttotal=0;
				while ($SW == 1) {
					$porciones = explode("+", $LTotal);

					if ($porciones[$cont]!="") {

						while($SW1==1){
							$pletra = explode(",", $str);
							if ($pletra[$contp]==$porciones[$cont]) {
								$SW1=0;
							}else{
								$contp++;
							}
						}
						if($data[$contp]>0){
							$ttotal=$ttotal+"$data[$contp]";
						}
						$cont++;
						$SW1=1;
					}else{
						//$contp=0;
						$SW =0;
						// break;
					}
				}

				if ($LTipo=="C") {
					$TCliPro="P";
				}else{
					$TCliPro="C";
				}

				$SQL="SELECT * FROM CTCliPro WHERE rut='$data[$LRut]' AND tipo='$TCliPro'";
				$resultados = $mysqli->query($SQL);
				$row_cnt = $resultados->num_rows;
				if ($row_cnt==0) {
			        $mysqli->query("INSERT INTO CTCliPro VALUES('','$data[$LRut]','".utf8_decode(strtoupper($data[$LRSocial]))."','','','','','','$TCliPro','A')");
			        $NewRSocial=$NewRSocial+1;
				}				

				$LCuentaDef="";
				$SQL="SELECT * FROM CTCliPro WHERE rut='$data[$LRut]' AND cuenta<>'0' AND tipo='$TCliPro'";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$LCuentaDef=$registro['cuenta'];
				}

				$SQL="SELECT * FROM CTCliProCuenta WHERE rut='$data[$LRut]' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND estado='A' AND tipo='$TCliPro'";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					if ($registro['cuenta']!=0) {
						$LCuentaDef=$registro['cuenta'];
					}
				}

				if ($LCuentaDef=="") {
					$LCuentaDef=$LCuenta;
				}

				// if ($DocExiste==0) {


					//if (isset($_POST['check2'])) {


				// if ($SwInsrtDife==0 && isset($_POST['check2'])) { /////Si no esta ya en la table y el check esta true
				// 	$NFacturasError1 .=$data[$LNumero].'';
				// 	$ListError1='<div class="alert alert-warning"><strong>Advertencia!</strong> Documentos Insertados <br><strong>'.$NFacturasError1.'</strong>.</div><br>';

				// 	$STRSQL = $STRSQL." ('','$LPeriodo','".$_SESSION['RUTEMPRESA']."','$data[$LRut]','$LCuentaDef','$IDDoc','$data[$LNumero]','$xfecha','".$texento."','".$tneto."','".$tiva."','".$tretencion."','".$ttotal."','$LTipo','$LFechaReg','A','','','')";
				// }


				if ($SwInsrtDife==0) {

					if($data[0]==""){
						$tneto=0;
						$tiva=0;
						$ttotal=0;
					}

					$STRSQL = $STRSQL." ('','$LPeriodo','".$_SESSION['RUTEMPRESA']."','$data[$LRut]','$LCuentaDef',0,'$IDDoc','$data[$LNumero]','$xfecha','".$texento."','".$tneto."','".$tiva."','".$tretencion."','".$ttotal."','','','$LTipo','$LFechaReg','A','','','')";

					if ($LArchivo==$row){
						$STRSQL = $STRSQL.";";
					}else{
						$STRSQL = $STRSQL.",";
					}
				}
					//}

				// }

	        }
	        $row=$row+1; 
	    }

	  	if ($ListError=="") {

	  		// echo substr($STRSQL,-1);
	  		if (substr($STRSQL,-1)==",") {
	  			$STRSQL=substr($STRSQL,0,-1).";";
	  		}else{
	  		}

// 	  		echo "<br>";
// echo $STRSQL;
// exit;

		if (!$resultado = $mysqli->query($STRSQL)) {
			$ListError='<div class="alert alert-danger"><strong>Informativo</strong> Error al intentar procesar el archivo; <br>* Puede ser que no contenga datos. <br>* Si utilizo "Solo registrar diferencial", puede no existan diferencias. <br>Verifique la estructura del mismo y vuelva a procesar.<br> Si el Error persiste Contacte al administrador del Sistema...</div><br>';
		}
       	$SQL="SELECT COUNT(numero) as cfact, numero, id_tipodocumento, rut FROM CTRegDocumentos WHERE periodo= '$LPeriodo' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND tipo='$LTipo' AND fechareg='$LFechaReg' GROUP by numero, id_tipodocumento, rut";
// echo $SQL;
// exit;
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {

							if ($registro["cfact"]>1) {
								$suexento=0;
								$suneto=0;
								$suiva=0;
								$suretencion=0;
								$sutotal=0;

					       		$SQLint="SELECT * FROM CTRegDocumentos WHERE periodo= '$LPeriodo' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND fechareg='$LFechaReg' AND tipo='$LTipo' AND numero='".$registro["numero"]."' AND rut='".$registro["rut"]."' AND tipo='$LTipo'";
								$resultadosint = $mysqli->query($SQLint);
								while ($registroint = $resultadosint->fetch_assoc()) {
									$tperiodo=$registroint['periodo'];
									$tperiodo=$registroint['rutempresa'];
									$tperiodo=$registroint['rut'];
									$tperiodo=$registroint['cuenta'];
									$tperiodo=$registroint['id_tipodocumento'];
									$tperiodo=$registroint['fecha'];
									$tperiodo=$registroint['tipo'];

									$suexento=$suexento+$registroint['exento'];
									$suneto=$suneto+$registroint['neto'];
									$suiva=$suiva+$registroint['iva'];
									$suretencion=$suretencion+$registroint['retencion'];
									$sutotal=$sutotal+$registroint['total'];
								}

			        			$mysqli->query("DELETE FROM CTRegDocumentos  WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo= '$LPeriodo' AND fechareg='$LFechaReg' AND numero='".$registro["numero"]."' AND total='0'");

			        			$mysqli->query("UPDATE CTRegDocumentos SET exento='$suexento', neto='$suneto', iva='$suiva', retencion='$suretencion', total='$sutotal' WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND fechareg='$LFechaReg' AND periodo= '$LPeriodo' AND numero='".$registro["numero"]."'");
							}

						}
						if ($ListError=="") {
          		$ListError='<div class="alert alert-success"><strong>Informativo</strong> El archivo fue procesado con Exito. <br> Se han cargado '.$NewRSocial.', Razon(es) Social(es) nueva(s)...</div><br>';
          	}

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
		<br>
		<form name="importar" method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" class="form-horizontal">
			<!-- <div class="col-md-2">
			</div> -->
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading text-center">Importar Libros</div>
				<div class="panel-body">
						<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Plantilla</span>
							<select class="form-control" id="plantilla" name="plantilla" required>
							<option value="">Selecciones</option>
							<?php 
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								
									$RutEmpresa="";
									if ($_SESSION["PLAN"]=="S"){
										$RutEmpresa=" AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}

									$SQL="SELECT * FROM CTPlantillas WHERE estado='A' $RutEmpresa";
									$resultados = $mysqli->query($SQL);
									while ($registro = $resultados->fetch_assoc()) {
										echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
									}
								$mysqli->close();
							?>
							</select>
						</div>
						</div>
						<div class="clearfix"></div>
						<br>

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

						<div class="col-md-12">
							<div class="input-group">
								<input type="checkbox" id="check2" name="check2"> Solo registrar diferencial
							</div>
						</div>

						<div class="clearfix"></div>
						<br>



						<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Seleccionar Archivo</span>
							<input type="file" class="form-control-file" id="file" name="file" aria-describedby="fileHelp" required>
								
							<input type="hidden" value="upload" name="action" />
						</div>
						<small id="fileHelp" class="form-text text-muted">* Solo archivo CSV.</small><br>
						</div> 

						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Separador</span>
								<input type="text" name="separador" value="<?php echo $DLIST; ?>" id="separador" required>
						</div>
						</div>

						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
						<div class="input-group">
								<button type="submit" class="btn btn-grabar btn-block">Procesar</button>
						</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-12" for="file"></label>
							<div class="col-md-12">
							<?PHP
								if ($ListError!="") {
									echo $ListError;
									// echo "<br>";
									// echo $ListDocDuplicados;
								}

								if (isset($_POST['check2']) && $ListError1!="") {
									// $NFacturasError1 .=$data[$LNumero].'<br> ';
									echo $ListError1;
								}

							?>
							</div>
						</div> 
				</div>
				</div>
			</div>

			<!-- <div class="col-md-2">
			</div> -->
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include 'footer.php'; ?>

	</body>
</html>