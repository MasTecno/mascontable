<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if ($_POST['messelect']<=9) {
		$xmesselect="0".$_POST['messelect'];
	}else{
		$xmesselect=$_POST['messelect'];
	}

	$Periodo=$_POST['anoselect']."".$xmesselect;


	if($Periodo=="" || !isset($_SESSION['UsuariaSV'])){
		header("location:../frmMain.php");
		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$Pref=randomTextSV(35);
	$Suf=randomTextSV(8);


	// $SQL="SELECT * FROM CTCliPro WHERE tipo='P' ORDER BY id ASC;";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {

	// 	$PRut=$registro['rut'];

	// 	$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P'";
	// 	$Resul = $mysqli->query($SQL1);
	// 	$row_cnt = $Resul->num_rows;
	// 	if($row_cnt>1){
	// 		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P' ORDER BY id ASC LIMIT 1;";
	// 		$resultados1 = $mysqli->query($SQL1);
	// 		while ($registro1 = $resultados1->fetch_assoc()) {
	// 			$IdReg=$registro1['id'];
	// 		}			
	// 		$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='P'");
	// 		// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
	// 	}

	// }

	// $SQL="SELECT * FROM CTCliPro WHERE tipo='C' ORDER BY id ASC;";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {

	// 	$PRut=$registro['rut'];

	// 	$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C'";
	// 	$Resul = $mysqli->query($SQL1);
	// 	$row_cnt = $Resul->num_rows;
	// 	if($row_cnt>1){
	// 		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C' ORDER BY id ASC LIMIT 1;";
	// 		$resultados1 = $mysqli->query($SQL1);
	// 		while ($registro1 = $resultados1->fetch_assoc()) {
	// 			$IdReg=$registro1['id'];
	// 		}			
	// 		$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='C'");
	// 		// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
	// 	}

	// }

	// $SQL="SELECT * FROM CTCliPro WHERE tipo='2' ORDER BY id ASC;";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {

	// 	$PRut=$registro['rut'];

	// 	$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2'";
	// 	$Resul = $mysqli->query($SQL1);
	// 	$row_cnt = $Resul->num_rows;
	// 	if($row_cnt>1){
	// 		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2' ORDER BY id ASC LIMIT 1;";
	// 		$resultados1 = $mysqli->query($SQL1);
	// 		while ($registro1 = $resultados1->fetch_assoc()) {
	// 			$IdReg=$registro1['id'];
	// 		}			
	// 		$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='2'");
	// 		// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
	// 	}

	// }

	// exit;

	// $Resul = $mysqli->query($SQL);
	// $row_cnt = $Resul->num_rows;
	// if($row_cnt>1){


	// 	$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '77398220-1' AND tipo='P' ORDER BY id ASC LIMIT 1;";
	// 	$resultados = $mysqli->query($SQL1);
	// 	while ($registro = $resultados->fetch_assoc()) {
	// 		$IdReg=$registro['id'];
	// 	}



	// }




	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {

	// }



	// $SqlCP="SELECT * FROM CTCliPro WHERE rut='$LRutCompleo' AND tipo='$TReg'";



	$SqlCP="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Estado='A'";
	$Resul = $mysqli->query($SqlCP);
	$row_cnt = $Resul->num_rows;
	if ($row_cnt==0) {
		$mysqli->query("INSERT INTO DTEParametros VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['rutsii']."','".$Pref.$_POST['CSiiCrip'].$Suf."','A');");
	}else{
		$SQL="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Estado='A' AND RutSii='".$_POST['rutsii']."' AND PasSii='".$_POST['CSiiCrip']."'";
		$Resul = $mysqli->query($SQL);
		$row_cnt = $Resul->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("UPDATE DTEParametros SET RutSii='".$_POST['rutsii']."', PasSii='".$Pref.$_POST['CSiiCrip'].$Suf."' WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Estado='A';");
		}
	}

	$SQL="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$ValRSII=$registro['RutSii']; 
		$ValCSII=descript($registro['PasSii']); 
	}

	if (strlen($ValRSII)==10) {
		$d1 = substr($ValRSII,0,2);
		$d2 = substr($ValRSII,2,3);
		$d3 = substr($ValRSII,5,3);
		$d4 = substr($ValRSII,-1);

		$ValRSII=$d1.".".$d2.".".$d3."-".$d4;
	}else{
		if (strlen($ValRSII)==9) {

			$d1 = substr($ValRSII,0,1);
			$d2 = substr($ValRSII,1,3);
			$d3 = substr($ValRSII,4,3);
			$d4 = substr($ValRSII,-1);

			$ValRSII=$d1.".".$d2.".".$d3."-".$d4;
		}else{
			$mysqli->close();
			echo "Rut no compatible, contactar con MasTecno";
			exit;
		}
	}

	$object="";

	$Rut=$ValRSII;
	$PassW=$ValCSII;
	$Per= $Periodo;
	$Ope = $_POST['SWOperacion'];

	// echo $Rut;
	// echo $PassW;
	// echo $Per;
	// echo $Ope;
	// exit;

	header('Content-Type: text/html; charset=utf-8');
	require 'vendor/autoload.php';

	function getDtesDesdeSii($company, $rutempresa, $passw, $periodo, $operacion){    
		try {
			$query = [
				'rutCompany' => $rutempresa,
				'password' => $passw,
				'periodo' => $periodo,
				'operacion' => $operacion,
			];

			$urlDocker = "http://201.217.243.31:8000"; ///mascloud
			//$urlDocker = "http://200.73.113.41:8000"; ///mastecno

			$client = new \GuzzleHttp\Client([
				'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json, text/plain, */*', 'Accept-Encoding' => 'gzip, deflate, br'],
				'base_uri' => $urlDocker,
				'defaults' => [
				'exceptions' => false,
				'allow_redirects' => false,
			],
			]);
		
			$response = $client->request('POST', '/api/sync_sii',  ['json' => $query]);

			$theContents = json_decode($response->getBody()->getContents());
			$theContents1 = json_decode($response->getBody());

			if(!$theContents->success == true){
				$xDato2 = $theContents->message;
			}
			// echo print_r($theContents);
			// $Error500 = 

			// // var_dump($Error500);
			// // die;

			// $SinServicio=$Error500->message;
			// if($SinServicio=="El servicio no se encuentra disponible"){
			// 	$xDato2="La API del SII en este momento no esta disponible..";
			// 	// die;
			// }
			// realizar consulta curl
			$response_code = $response->getStatusCode();
			if ($response_code != 200)
			return false;



			$data = $theContents->data;
			//$data = json_decode($response->getBody()->getContents());

			// 	echo '<pre>';
			// 	print_r($data);
			// 	echo '</pre>';
			// exit; 

			$LTip="";
			if ($operacion=="COMPRA") {
				$LTip="C";
				$TReg="P";
			}

			if ($operacion=="VENTA") {
				$LTip="V";
				$TReg="C";
			}

			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);	    
			$Error="";
			$RutEmp=str_replace(".","",$rutempresa);
			$LPer=substr($periodo,-2)."-".substr($periodo,0,4);

			function UltimoDiaMesD($periodo) { 
				$month = substr($periodo,0,2);
				$year = substr($periodo,3,4);
				$day = date("d", mktime(0,0,0, $month+1, 0, $year));

				return date('d', mktime(0,0,0, $month, $day, $year));
			};


			function valida_rut($rut){
				$rut = preg_replace('/[^k0-9]/i', '', $rut);
				$dv  = substr($rut, -1);
				$numero = substr($rut, 0, strlen($rut)-1);
				$i = 2;
				$suma = 0;
				foreach(array_reverse(str_split($numero)) as $v)
				{
					if($i==8)
						$i = 2;

					$suma += $v * $i;
					++$i;
				}

				$dvr = 11 - ($suma % 11);
				
				if($dvr == 11)
					$dvr = 0;
				if($dvr == 10)
					$dvr = 'K';


				return strtoupper($dvr);

				// if($dvr == strtoupper($dv))
				//     return true;
				// else
				//     return false;
			}

			$SqlLoteDet="";
			$TRes="";
			$CantDoc=0;
			$xDato4=1;
			$Cont=0;
			$sw="";

			foreach($data as $key => $dtes){

				$SQL="SELECT * FROM CTTipoDocumento WHERE tiposii='$key'";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {
					$LIdDoc=$registro['id'];
					$LNomDoc=$registro['nombre'];
				}

				// $TRes=$TRes.$LNomDoc.": ".count($dtes)."<br>";
				// $CantDoc=$CantDoc+count($dtes);
				$CantDte=0;
				foreach($dtes as $dte){

					if($key=="35" || $key=="39" || $key=="48" || $key=="41"){
						
						$dte->detRutDoc=0;
						$dte->detDvDoc="";

						$dte->detRznSoc="";
						$dte->detNroDoc=$dte->det_nro_doc;
						$dte->detFchDoc="01-01-1001";

						$dte->detMntExe=0;
						$dte->detTabPuros=0;
						$dte->detTabCigarrillos=0;
						$dte->detTabElaborado=0;
						$dte->detMntNeto=$dte->det_mnt_neto;
						$dte->detMntIVA=$dte->det_mnt_iva;
						$dte->detIVAUsoComun=0;
						$dte->detMntIVANoRec=0;
						$dte->totalDtoiMontoImp=0;
						$dte->detMntTotal=$dte->det_mnt_total;
						$dte->detTipoDocRef=0;
						$dte->detFolioDocRef=0;

						$LExe=$dte->detMntExe + $dte->detTabPuros + $dte->detTabCigarrillos +  $dte->detTabElaborado;

						if($dte->detMntExe== 0 && $dte->det_mnt_exe>0){
							$LExe=$LExe+$dte->det_mnt_exe;
						}

						$LNet=$dte->detMntNeto;
						$LIva=$dte->detMntIVA + $dte->detIVAUsoComun;
						$LNet=$dte->detMntNeto;
						$LIva=$dte->detMntIVA + $dte->detIVAUsoComun;
						$LRet=$dte->detMntIVANoRec + $dte->totalDtoiMontoImp;
						$LTot=$dte->detMntTotal;

						$TRef=$dte->detTipoDocRef;
						$NRef=$dte->detFolioDocRef;
					}else{

						$LRut=$dte->detRutDoc."-".$dte->detDvDoc;
						$LRaz=$dte->detRznSoc;
						$LNum=$dte->detNroDoc;
						$LFec=$dte->detFchDoc;
						$dia = substr($LFec,0,2);
						$mes = substr($LFec,3,2);
						$ano = substr($LFec,6,4);
						$LFec=$ano."-".$mes."-".$dia;

						$LExe=$dte->detMntExe + $dte->detTabPuros + $dte->detTabCigarrillos +  $dte->detTabElaborado;
						$LNet=$dte->detMntNeto;
						$LIva=$dte->detMntIVA + $dte->detIVAUsoComun;
						$LRet=$dte->detMntIVANoRec + $dte->totalDtoiMontoImp;
						$LTot=$dte->detMntTotal;
						if($LTot>0){
							$CantDoc=$CantDoc+1;
							$CantDte=$CantDte+1;
						}
						$TRef=$dte->detTipoDocRef;
						$NRef=$dte->detFolioDocRef;

						if (isset($_POST['EmpExt'])) {
							$LNet=$LNet+$LRet+$LIva;
							$LRet=0;
							$LIva=0;
						}

						if (($LExe+$LNet+$LIva) != $LTot) {
							$LRet=$LTot-($LExe+$LNet+$LIva);
						}

						if ($key=="46" && $LRet>0) {
							$LRet=$LRet*-1;
						}
					}

					if ($dte->detRutDoc>0 && $key=="39" && $dte->detDvDoc=="") {
						$R=$dte->detRutDoc."-0";
						$D=valida_rut($R);
						$R=$dte->detRutDoc."-".$D;
						$LRut=$R;
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $key=="39") {
						$LRut="11111111-1";
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz="Ventas con Boletas";
						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $key=="38") {
						$LRut="11111111-1";
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz="Ventas con Boletas";
						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $key=="41") {
						$LRut="11111111-1";
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz="Ventas con Boletas";
						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc>0 && $key=="41" && $dte->detDvDoc=="") {
						$R=$dte->detRutDoc."-0";
						$D=valida_rut($R);
						$R=$dte->detRutDoc."-".$D;
						$LRut=$R;
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $key=="35") {
						$LRut="11111111-1";
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz="Ventas con Boletas";
						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $dte->detDvDoc=="" && $key=="919") {
						$LRut="3333333-3";
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz="Resumen Ventas con Boletas Pasaje Nac/Int";

						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $dte->detDvDoc=="" && $key=="924") {
						$LRut="3333333-3";
						// $LNum=(substr($periodo,-2)*1)."-".$dte->detNroDoc;
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz="Resumen Ventas con Boletas Pasaje Nac/Int";

						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $key=="48") {
						$LRut="22222222-2";
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz="Ventas Transbank";

						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $key=="47") {
						$LRut=$_SESSION['RUTEMPRESA'];
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz=strtoupper($_SESSION['RAZONSOCIAL']);

						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($dte->detRutDoc==0 && $key=="920") {
						$LRut=$_SESSION['RUTEMPRESA'];
						$LNum=substr($periodo,0,4).substr($periodo,-2)."-".$dte->detNroDoc;
						$LRaz=strtoupper($_SESSION['RAZONSOCIAL']);

						$UtlDia=UltimoDiaMesD($LPer);
						$LFec=substr($periodo,0,4)."-".substr($periodo,-2)."-".$UtlDia;
					}

					if ($LRaz=="" && $LRut==$_SESSION['RUTEMPRESA']) {
						$LRaz=strtoupper($_SESSION['RAZONSOCIAL']);
					}

					$SqlCP="SELECT * FROM CTCliPro WHERE rut='$LRut' AND tipo='$TReg'";
					$Resul = $mysqli->query($SqlCP);
					$row_cnt = $Resul->num_rows;
					if ($row_cnt==0) {				
						$SqlRCP="INSERT INTO CTCliPro (id, rut, razonsocial, tipo, estado) VALUES ('','$LRut','".utf8_decode(strtoupper($LRaz))."','$TReg','A');";
						$mysqli->query($SqlRCP);
					}

					$LCta="";
					$LCCosto=0;
					$SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='$RutEmp' AND rut='$LRut' AND tipo='$TReg'";
					$Resul = $mysqli->query($SqlCP);
					while ($Reg = $Resul->fetch_assoc()) {
						if ($Reg['cuenta']!=""){
							$LCta=$Reg['cuenta'];
						}
						if ($Reg['ccosto']!="") {	
							$LCCosto=$Reg['ccosto'];
						}
					}

					if($LCta==""){				
						$SqlCP1="SELECT * FROM CTCliPro WHERE rut='$LRut' AND tipo='$TReg'";
						$Resul = $mysqli->query($SqlCP1);
						while ($Reg = $Resul->fetch_assoc()) {
							$LCta=$Reg['cuenta'];
						}
					}

					if($LCta=="" || $LCta==0){
						$SqlCP1="SELECT * FROM CTPlantillas WHERE estado='A' AND tipo='$LTip' AND nombre LIKE '%2017 SII%' AND rut_empresa=''";
						$Resul = $mysqli->query($SqlCP1);
						while ($Reg = $Resul->fetch_assoc()) {
							$LCta=$Reg['cuenta'];
						}
					}

					if($LCta=="" || $LCta==0){
						$SqlCP1="SELECT * FROM CTPlantillas WHERE estado='A' AND tipo='$LTip' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
						$Resul = $mysqli->query($SqlCP1);
						while ($Reg = $Resul->fetch_assoc()) {
							$LCta=$Reg['cuenta'];
						}
					}

					$SqlCP="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEmp' AND rut='$LRut' AND numero='$LNum' AND tipo='$LTip' AND id_tipodocumento='$LIdDoc'";
					$Resul = $mysqli->query($SqlCP);
					$row_cnt = $Resul->num_rows;
					if ($row_cnt==0) {		
						if($LTot>0){
							// $CantDoc++;
							$SqlLoteDet=$SqlLoteDet."('','$LPer','$RutEmp','$LRut','$LCta','$LCCosto','$LIdDoc','$LNum','$LFec','$LExe','$LNet','$LIva','$LRet','$LTot','$TRef','$NRef','$LTip','".date("Y-m-d")."','A','','',''),";
						}
					}else{

						$Error=$Error.'<tr>
							<td>'.$LNum.'</td>
							<td>'.date('d-m-Y',strtotime($LFec)).'</td>
							<td>'.$LNomDoc.'</td>
							<td>'.$LRut.'</td>
							<td>'.$LRaz.'</td>
						</tr> 
						';
					}

				}

				if($key!=$sw){
					$TRes=$TRes.$LNomDoc.": ".$CantDte."<br>";
					$sw=$key;
				}

				$SqlLote="INSERT INTO CTRegDocumentos (id, periodo, rutempresa, rut, cuenta, ccosto, id_tipodocumento, numero, fecha, exento, neto, iva, retencion, total, TipoDocRef, FolioDocRef, tipo, fechareg, estado, origen, lote, keyas) VALUES ".$SqlLoteDet;

				$SqlLote=substr($SqlLote, 0, -1).";";

				if ($SqlLote!="INSERT INTO CTRegDocumentos (id, periodo, rutempresa, rut, cuenta, ccosto, id_tipodocumento, numero, fecha, exento, neto, iva, retencion, total, TipoDocRef, FolioDocRef, tipo, fechareg, estado, origen, lote, keyas) VALUES;") {
					
					// if($_SESSION['NomServer']=="server212"){
					// 	echo $SqlLote;
					// 	exit;
					// }

					$mysqli->query($SqlLote);    

					// $SqlLote1=$SqlLote1."<br>".$SqlLote;
				}

				$SqlLote="";
				$SqlLoteDet="";
				// $xDato4=2;
			}

			$sqlin = "SELECT * FROM CTCliPro WHERE `rut` LIKE '%-k%'";
			$resultadoin = $mysqli->query($sqlin);
			while ($registro = $resultadoin->fetch_assoc()) {
				$mysqli->query("UPDATE CTCliPro SET rut='".strtoupper($registro["rut"])."' WHERE id='".$registro["id"]."'");
			}

			$sqlin = "SELECT * FROM CTRegDocumentos WHERE `rut` LIKE '%-k%'";
			$resultadoin = $mysqli->query($sqlin);
			while ($registro = $resultadoin->fetch_assoc()) {
				$mysqli->query("UPDATE CTRegDocumentos SET rut='".strtoupper($registro["rut"])."' WHERE id='".$registro["id"]."'");
			}

			$mysqli->close();
			// header
			// echo $SqlLote1;
			// exit; 

			if ($Error!=""){
				$xDato1 = $TRes;				
				//$xDato2 = "<strong>Exito, Documentos procesados satisfactoriamente (Algunos Documentos ya Registrados)</strong>";
				$xDato3 = "OK";
				$xDato4  = $CantDoc;

			}else{
				$xDato1 = $TRes;
				//$xDato2 = "<strong>Exito, Documentos procesados satisfactoriamente</strong>";
				$xDato3 = "OK";
				$xDato4  = $CantDoc;
			}

		} catch (\Throwable $th) {
			// return "Error al sincronizar documentos: <br>" . $th->getMessage(); //$th->__toString(); //
			// echo "Error al sincronizar documentos: <br>" . $th->getMessage(); //$th->__toString(); //
			// /mensaje de sistema
			// echo "Error al sincronizar documentos: <br>" . $th->getMessage();
			$xDato1="S/I";//. $th->getMessage();
			

			$xDato2= "No hemos podido rescatar la información de los documentos, pueden ser varias Causas:<br>
			1. Rut y/o Clave Incorrectas.<br>
			2. No presentan documentos en el periodo.<br>
			3. El servicio de sincronización esta temporalmente desactivado ya sea por parte de MasTecno o Sii.<br>
			";
		}

		// $xDato3=print_r($data);

		if($urlDocker=="http://201.217.243.31:8000"){
			$urlDocker="MasCloud";
		}else{
			$urlDocker="MasTecno";
		}

		$xDato2=$xDato2.'<br><l style="color:#00000000">'.$urlDocker."</l>";

		if(!isset($data) || $data==""){
			$data="NoXML";
		}

		echo json_encode(
			array("dato1" => "$xDato1", 
			"dato2" => "$xDato2",
			"dato3" => "$xDato3",
			"dato4" => "$xDato4",
			"XML" => $data
			)
		);
	}

	getDtesDesdeSii($object,$Rut,$PassW,$Per,$Ope);
?>
