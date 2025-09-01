<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	// print_r($_SESSION['ARRCENTRA']);
	// echo "<br>";
	// print_r($_SESSION['DOCUCENTRA']);
	// exit;

	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$FECHA=date("Y-m-d");

	$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SqlAux="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa='$RutEmpresa'";
	$resultados = $mysqli->query($SqlAux);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt==0) {
		$SqlAux="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa=''";
	}
// echo $SqlAux;

	if ($_POST['frm']=="C") {
		$TipFol="E";
		$res1 = $mysqli->query($SqlAux);
		while ($reg1 = $res1->fetch_assoc()) {
			$AUX=$reg1["L4"];// AUXILIAR PROVEEDORES
		}		
	}

	if ($_POST['frm']=="V") {
		$TipFol="I";
		$res1 = $mysqli->query($SqlAux);
		while ($reg1 = $res1->fetch_assoc()) {
			$AUX=$reg1["L1"];// AUXILIAR CLIENTES
		}
	}

	$HPref="";
	if ($_POST['frm']=="H"){

		$SqlAux="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa='$RutEmpresa'";
		$resultados = $mysqli->query($SqlAux);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$SqlAux="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa=''";
		}
		
		$Resul = $mysqli->query($SqlAux);
		while ($reg1 = $Resul->fetch_assoc()) {
			$AUX=$reg1["L3"];// honorarios por pagar
		}
		$HPref="Hono";
	}


// echo $AUX;

	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$FECHA=date("Y-m-d");

	function LFolio($x1,$x2){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$RutEmpresa=$_SESSION['RUTEMPRESA'];

		$FolioComp=0;
		$SrtSql="SELECT * FROM CTComprobanteFolio WHERE tipo='$x1' AND rutempresa='$RutEmpresa' AND ano='$x2'";
		$Resul = $mysqli->query($SrtSql);
		while ($Regi = $Resul->fetch_assoc()) {
			$FolioComp=$Regi['valor'];
		}

		if ($FolioComp==0) {
			$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$x2','$x1','2','A');");
			$FolioComp=1;
		}else{
			$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$x1' AND rutempresa='$RutEmpresa' AND ano='$x2'");
		}
		$mysqli->close();
		return $FolioComp;
	}

	unset($_SESSION['ARRCTA']);

	$LCta=array(
		'Peri'=>'xxxx',
		'Cta'=>'xxxx',
		'MDebe'=>'xxxx',
		'MHaber'=>'xxxx',
		'AKeyAs'=>'xxxx'
	);
	$_SESSION['ARRCTA'][0]=$LCta;

	$MonPag=0;
	foreach($_SESSION['WhileCta'] as $indice=>$LAs){
		$CtaResp="";
		$CtaDeb=0;
		$CtaHab=0;
		// $MonPag=0;
		foreach($_SESSION['ARRCENTRA'] as $indice=>$LAsiento){
			if ($LAsiento['Cta']!="xxxx"){

				if ($LAs['numero']==$LAsiento['Cta']) {
					$CtaPer=$LAsiento['Peri'];
					$CtaResp=$LAsiento['Cta'];
					$CtaDeb=$CtaDeb+$LAsiento['MDebe'];
					$CtaHab=$CtaHab+$LAsiento['MHaber'];
					$SwKey=$LAsiento['AKeyAs'];
					if ($AUX==$LAsiento['Cta'] && $_POST['frm']=="V") {
						$MonPag=$MonPag+($LAsiento['MDebe']-$LAsiento['MHaber']);
					}
					if ($AUX==$LAsiento['Cta'] && $_POST['frm']=="C") {
						$MonPag=$MonPag+($LAsiento['MHaber']-$LAsiento['MDebe']);
					}
					if ($AUX==$LAsiento['Cta'] && $_POST['frm']=="H") {
						$MonPag=$MonPag+($LAsiento['MHaber']-$LAsiento['MDebe']);
					}
				}
			}
		}

		if ($CtaResp!="") {
			$NLCta=count($_SESSION['ARRCTA']);
			$LCta=array(
					'Peri'=>$CtaPer,
					'Cta'=>$CtaResp,
					'MDebe'=>$CtaDeb,
					'MHaber'=>$CtaHab,
					'AKeyAs'=>$SwKey
			);
			$_SESSION['ARRCTA'][$NLCta]=$LCta;
		}
	}

	function UltimoDiaMesD($periodo) { 
		$month = substr($periodo,0,2);
		$year = substr($periodo,3,4);
		$day = date("d", mktime(0,0,0, $month+1, 0, $year));

		return date('d', mktime(0,0,0, $month, $day, $year));
	};

    $dmes = substr($CtaPer,0,2);
    $dano = substr($CtaPer,3,4);

    $xfecha=UltimoDiaMesD($CtaPer)."-".$dmes."-".$dano;
	$dia = substr($xfecha,0,2);
    $mes = substr($xfecha,3,2);
    $ano = substr($xfecha,6,4);

    $xfecha=$ano."/".$mes."/".$dia;

	$TanoD = substr($CtaPer,3,4);
	$Folio=LFolio("T",$TanoD);
	$GKeyAs=date("YmdHis");

	if($_POST['frm']=="C"){
		$GlosaAsi="CENTRALIZACIÓN ".$FijoGlosa."COMPRAS ".strtoupper($xMes)." ".$dano;
	}
	if($_POST['frm']=="V"){
		$GlosaAsi="CENTRALIZACIÓN ".$FijoGlosa."VENTAS ".strtoupper($xMes)." ".$dano;
	}

	if($_POST['frm']=="H"){
		$GlosaAsi="CENTRALIZACIÓN ".$FijoGlosa."HONORARIOS ".strtoupper($xMes)." ".$dano;
	}

	foreach($_SESSION['ARRCTA'] as $indice=>$ListaCta){
		if ($ListaCta['Peri']!="xxxx"){
			if ($ListaCta['MDebe']>0) {
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('".$ListaCta['Peri']."','$RutEmpresa','$xfecha','','".$ListaCta['Cta']."','".$ListaCta['MDebe']."','".$ListaCta['MHaber']."','$FECHA','A','".$SwKey."','$Folio','T','".$LAsiento['CC']."','$HPref"."$SwKey','')";
				$mysqli->query($SrtSql);
			}
		}
	}

	foreach($_SESSION['ARRCTA'] as $indice=>$ListaCta){
		if ($ListaCta['Peri']!="xxxx"){
			if ($ListaCta['MHaber']>0 && $ListaCta['MDebe']==0) {
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('".$ListaCta['Peri']."','$RutEmpresa','$xfecha','','".$ListaCta['Cta']."','".$ListaCta['MDebe']."','".$ListaCta['MHaber']."','$FECHA','A','".$SwKey."','$Folio','T','".$LAsiento['CC']."','$HPref"."$SwKey','')";
				$mysqli->query($SrtSql);
			}
		}
	}

	if ($dmes=="01") {
		$xMes="Enero";
	}
	if ($dmes=="02") {
		$xMes="Febrero";
	}
	if ($dmes=="03") {
		$xMes="Marzo";
	}
	if ($dmes=="04") {
		$xMes="Abril";
	}
	if ($dmes=="05") {
		$xMes="Mayo";
	}
	if ($dmes=="06") {
		$xMes="Junio";
	}
	if ($dmes=="07") {
		$xMes="Julio";
	}
	if ($dmes=="08") {
		$xMes="Agosto";
	}
	if ($dmes=="09") {
		$xMes="Septiembre";
	}
	if ($dmes=="10") {
		$xMes="Octubre";
	}
	if ($dmes=="11") {
		$xMes="Noviembre";
	}
	if ($dmes=="12") {
		$xMes="Diciembre";
	}

	$FijoGlosa="";
	$ConDoc=1;
	foreach($_SESSION['ARRCENTRA'] as $indice=>$LAsiento){
		if ($LAsiento['Cta']!="xxxx"){

			$SrtSql="SELECT * FROM CTRegDocumentos WHERE estado='A' AND id='".$LAsiento['IdDoc']."' AND rutempresa='$RutEmpresa' AND lote=''";
			$Resul = $mysqli->query($SrtSql);
			while ($Regi = $Resul->fetch_assoc()) {
				if($Regi['id_tipodocumento']== 4 || $Regi['id_tipodocumento']== 5 || $Regi['id_tipodocumento']==30 || $Regi['id_tipodocumento']==35){
					$ConDoc++;
				}
			}
		}
	}

	if($ConDoc==count($_SESSION['ARRCENTRA']) && $ConDoc>1){
		$FijoGlosa="NOTAS DE CREDITO, ";
	}

	$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$CtaPer','$RutEmpresa','$xfecha','$GlosaAsi','','0','0','$FECHA','A','$SwKey','$HPref"."$SwKey','','$Folio','T')";
	$mysqli->query($SrtSql);

	if($_POST['frm']=="H"){
		$HPref="Hono";
		foreach($_SESSION['ARRCENTRA'] as $indice=>$LAsiento){
			if ($LAsiento['Cta']!="xxxx"){
				$SrtSql="UPDATE CTHonorarios SET movimiento='$SwKey' WHERE estado='A' AND id='".$LAsiento['IdDoc']."' AND rutempresa='$RutEmpresa' AND movimiento=''";
				$mysqli->query($SrtSql);
			}
		}
	}else{
		foreach($_SESSION['ARRCENTRA'] as $indice=>$LAsiento){
			if ($LAsiento['Cta']!="xxxx"){
				$SrtSql="UPDATE CTRegDocumentos SET lote='$SwKey', keyas='$SwKey' WHERE estado='A' AND id='".$LAsiento['IdDoc']."' AND rutempresa='$RutEmpresa' AND lote=''";
				$mysqli->query($SrtSql);
			}
		}
	}

	if (isset($_POST['SwPago']) && $LAsiento['Glosa']!="") {
		$SwKey=$SwKey+1;

		if ($_POST['frm']=="C") {
			$GlosaAsiPago="PAGO CENTRALIZACIÓN ".$FijoGlosa."COMPRAS ".strtoupper($xMes)." ".$dano;
			if ($MonPag<0) {
				$TipFol="I";
				$Folio=LFolio($TipFol,$TanoD);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','".($MonPag*-1)."','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','0','".($MonPag*-1)."','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
			}else{
				$Folio=LFolio($TipFol,$TanoD);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','".$MonPag."','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','0','".$MonPag."','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
			}
		}
		if ($_POST['frm']=="V") {
			$GlosaAsiPago="PAGO CENTRALIZACIÓN ".$FijoGlosa."VENTAS ".strtoupper($xMes)." ".$dano;

			if ($MonPag<0) {
				$TipFol="E";
				$Folio=LFolio($TipFol,$TanoD);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','0','".($MonPag*-1)."','$FECHA','A','$SwKey','$Folio','$TipFol','".$LAsiento['CC']."')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','".($MonPag*-1)."','0','$FECHA','A','$SwKey','$Folio','$TipFol','".$LAsiento['CC']."')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKey','$Folio','$TipFol','".$LAsiento['CC']."')";
				$mysqli->query($SrtSql);
			}else{
				$Folio=LFolio($TipFol,$TanoD);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','".$MonPag."','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','0','".$MonPag."','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
				$mysqli->query($SrtSql);
			}
		}

		if ($_POST['frm']=="H") {
			$GlosaAsiPago="PAGO CENTRALIZACIÓN ".$FijoGlosa."HONORARIOS, ".strtoupper($xMes)." ".$dano;
			$TipFol="E";
			$Folio=LFolio($TipFol,$TanoD);
			$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','".$MonPag."','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
			$mysqli->query($SrtSql);
			$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','0','".$MonPag."','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
			$mysqli->query($SrtSql);
			$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKey','$Folio','$TipFol','0')";
			$mysqli->query($SrtSql);

		}

		// print_r($_SESSION['DOCUCENTRA']);
		////genero LOS PAGOS
		foreach($_SESSION['DOCUCENTRA'] as $indice=>$LAsiento){
			if ($LAsiento['IdDocX']!=""){

				if ($_POST['frm']=="H") {
					$SrtSql="SELECT * FROM CTHonorarios WHERE estado='A' AND id='".$LAsiento['IdDocX']."' AND rutempresa='$RutEmpresa'";
					$Resul = $mysqli->query($SrtSql);
					while ($Regi = $Resul->fetch_assoc()) {
						$NumDocu=$Regi['numero'];
						$RutDocu=$Regi['rut'];
						$TipDocu=$Regi["id_tipodocumento"];
						$TotPaga=$Regi['liquido'];
					}
	
					$SrtSql="INSERT INTO CTControRegDocPago VALUES ('','$RutEmpresa','$RutDocu','".$LAsiento['Peri']."','$NumDocu',0,'$SwKey',$TotPaga,'$xfecha','".date('Y-m-d')."','".$_POST['frm']."','C','A')";
					$mysqli->query($SrtSql);
	
				}else{
					$SrtSql="SELECT * FROM CTRegDocumentos WHERE estado='A' AND id='".$LAsiento['IdDocX']."' AND rutempresa='$RutEmpresa'";
					$Resul = $mysqli->query($SrtSql);
					while ($Regi = $Resul->fetch_assoc()) {
						$NumDocu=$Regi['numero'];
						$RutDocu=$Regi['rut'];
						$TipDocu=$Regi["id_tipodocumento"];
						$TotPaga=$Regi['total'];
					}
	
					$SrtSql="INSERT INTO CTControRegDocPago VALUES ('','$RutEmpresa','$RutDocu','".$LAsiento['Peri']."','$NumDocu',$TipDocu,'$SwKey',$TotPaga,'$xfecha','".date('Y-m-d')."','".$_POST['frm']."','C','A')";
					$mysqli->query($SrtSql);
				}
			}
		}
	}

	$mysqli->close();

	unset($_SESSION['ARRCENTRA']);
	unset($_SESSION['WhileCta']);
	unset($_SESSION['ARRCTA']);

// exit;
	header("location:index.php?".$_POST['frm']);
