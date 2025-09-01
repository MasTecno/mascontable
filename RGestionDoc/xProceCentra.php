<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		echo ('<script> window.location.href="../?Msj=95";</script>');
		exit;
	}

	if ($_POST['frm']=="C") {
		$TipFol="E";
	}
	if ($_POST['frm']=="V") {
		$TipFol="I";
	}
	
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
		// $mysqli->close();
		return $FolioComp;
	}

	function BuscaKey($l){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$foliINI=$l;
		$swFolio="N";

		while ($swFolio=="N") {
			$sqlNF = "SELECT * FROM CTRegLibroDiario WHERE keyas='$l'";
			$ResulNF = $mysqli->query($sqlNF);										
			$row_cntNF = $ResulNF->num_rows;

			if ($l!=$foliINI && $row_cntNF==0) {
				$swFolio="S";
			}else{
				$l=$l+1;
			}
		}
		return $l;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SqlAux="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa='$RutEmpresa'";
	$resultados = $mysqli->query($SqlAux);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt==0) {
		$SqlAux="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa=''";
	}

	if ($_POST['frm']=="H"){

		$SqlAux="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa='$RutEmpresa'";
		$resultados = $mysqli->query($SqlAux);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$SqlAux="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa=''";
		}
		
		// $resultados = $mysqli->query($SQL);
		// while ($registro = $resultados->fetch_assoc()) {
		// 	$AUX=$registro["L1"];// AUXILIAR HONORARIOS
		// 	$RET=$registro["L2"];// RETENCION
		// 	$HXP=$registro["L3"];// HONORARIOS POR PAGAR
		// 	$RE3=$registro["L4"];// RETENCION 3%
		// }
	}
	$SwKey="";
	// $indice=1;
	$MonPag=0;
	foreach($_SESSION['ARRCENTRA'] as $indice=>$LAsiento){
		if ($LAsiento['Cta']!="xxxx"){

			if ($SwKey=="" || $SwKey!=$LAsiento['AKeyAs']) {
				$SwKey=$LAsiento['AKeyAs'];
			    $TanoD = substr($LAsiento['Peri'],3,4);
				$Folio=LFolio("T",$TanoD);
			}

			$xfecha=$LAsiento['FDocu'];

			if(strtotime($xfecha)<strtotime("01-".$LAsiento['Peri'])){
				$xfecha=date('Y-m-d',strtotime("01-".$LAsiento['Peri']));
			}

			$operador=1;
			
			$SrtSql="SELECT * FROM CTRegDocumentos WHERE estado='A' AND id='".$LAsiento['IdDoc']."' AND rutempresa='$RutEmpresa' AND lote=''";
			$Resul = $mysqli->query($SrtSql);
			while ($Regi = $Resul->fetch_assoc()) {
				$NumDocu=$Regi['numero'];
				$RutDocu=$Regi['rut'];
				$TipDocu=$Regi["id_tipodocumento"];

				
				$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$Regi["id_tipodocumento"]."'";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
					$NomDoc=strtoupper($registro1["nombre"]);
					if($registro1["operador"]=="R"){
						$operador=-1;
					}
				}
			}
			$HPref="";
			$NombHono="";
			
			if ($_POST['frm']=="H"){

				$SrtSql="SELECT * FROM CTHonorarios WHERE estado='A' AND id='".$LAsiento['IdDoc']."' AND rutempresa='$RutEmpresa' AND movimiento=''";
				$Resul = $mysqli->query($SrtSql);
				while ($Regi = $Resul->fetch_assoc()) {
					$NumDocu=$Regi['numero'];
					$RutDocu=$Regi['rut'];
					$TipDocu=$Regi["id_tipodocumento"];
				}
				$HPref="Hono";

				

				if(isset($_POST['SwNombreHono'])){
					$NombHono=", Rut no registrado en el mantenedor";			
					$Sqlhono="SELECT razonsocial FROM CTCliPro WHERE rut LIKE '$RutDocu' AND tipo='P'";
					$Reshono = $mysqli->query($Sqlhono);
					while ($Regihono = $Reshono->fetch_assoc()) {
						$NombHono=", ".$Regihono['razonsocial'];
					}
				}

			}


			if ($LAsiento['Glosa']=="") {
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$LAsiento['Cta']."','".$LAsiento['MDebe']."','".$LAsiento['MHaber']."','$FECHA','A','$SwKey','$Folio','T','".$LAsiento['CC']."','$HPref"."$NumDocu','$RutDocu')";
				$mysqli->query($SrtSql);

				if ($_POST['frm']=="C" || $_POST['frm']=="V"){
					$res1 = $mysqli->query($SqlAux);
					while ($reg1 = $res1->fetch_assoc()) {

						if ($_POST['frm']=="C" && $reg1["L4"]==$LAsiento['Cta']) {
							$MonPag=$MonPag+$LAsiento['MHaber'];
						}

						if ($_POST['frm']=="V" && $reg1["L1"]==$LAsiento['Cta']) {
							$MonPag=$MonPag+$LAsiento['MDebe'];
						}

						if ($operador=-1) {
							if ($_POST['frm']=="C" && $reg1["L4"]==$LAsiento['Cta']) {
								$MonPag=$MonPag+$LAsiento['MDebe'];
							}

							if ($_POST['frm']=="V" && $reg1["L1"]==$LAsiento['Cta']) {
								$MonPag=$MonPag+$LAsiento['MHaber'];
							}
						}
					}
				}

				if ($_POST['frm']=="H"){
					$res1 = $mysqli->query($SqlAux);
					while ($reg1 = $res1->fetch_assoc()) {
						if ($reg1["L3"]==$LAsiento['Cta']) {
							$MonPag=$MonPag+$LAsiento['MHaber'];
						}
					}
				}

			}else{
				$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','".$LAsiento['Glosa'].$NombHono."','0','0','0','$FECHA','A','$SwKey','$Folio','T','".$LAsiento['CC']."','$HPref"."$NumDocu','$RutDocu')";
				$mysqli->query($SrtSql);
				if ($_POST['frm']=="C" || $_POST['frm']=="V"){
					$SrtSql="UPDATE CTRegDocumentos SET lote='$SwKey', keyas='$SwKey' WHERE estado='A' AND id='".$LAsiento['IdDoc']."' AND rutempresa='$RutEmpresa' AND lote=''";
					$mysqli->query($SrtSql);

					/////Sección de Pagos de Documentos
					if (isset($_POST['SwPago']) && $LAsiento['Glosa']!="") {
						$key=$SwKey."000";
						$SwKeyT=BuscaKey($key);
						
						if ($_POST['frm']=="C") {
							$res1 = $mysqli->query($SqlAux);
							while ($reg1 = $res1->fetch_assoc()) {
								$AUX=$reg1["L4"];// AUXILIAR PROVEEDORES
							}
							$GlosaAsiPago=("PAGO CENTRALIZACIÓN COMPRAS ".$NumDocu);

							if ($operador<0) {
								$GlosaAsiPago=("PAGO NOTA DE CREDITO ".$NumDocu);
								$TipFol="I";
								$Folio=LFolio($TipFol,$TanoD);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','".$MonPag."','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','0','".$MonPag."','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
							}else{
								$TipFol="E";
								$Folio=LFolio($TipFol,$TanoD);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','".$MonPag."','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','0','".$MonPag."','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
							}
						}
						if ($_POST['frm']=="V") {
							$res1 = $mysqli->query($SqlAux);
							while ($reg1 = $res1->fetch_assoc()) {
								$AUX=$reg1["L1"];// AUXILIAR CLIENTES
							}

							$GlosaAsiPago=("PAGO CENTRALIZACIÓN VENTAS ".$NumDocu);

							if ($operador<0) {
								$GlosaAsiPago=("PAGO NOTA DE CREDITO ".$NumDocu);
								$TipFol="E";
								$Folio=LFolio($TipFol,$TanoD);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','".$MonPag."','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','0','".$MonPag."','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
							}else{
								$TipFol="I";
								$Folio=LFolio($TipFol,$TanoD);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','".$MonPag."','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','0','".$MonPag."','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
								$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
								$mysqli->query($SrtSql);
							}
						}

						$SrtSql="INSERT INTO CTControRegDocPago VALUES ('','$RutEmpresa','$RutDocu','".$LAsiento['Peri']."','$NumDocu',$TipDocu,'$SwKeyT',$MonPag,'$xfecha','".date('Y-m-d')."','".$_POST['frm']."','C','A')";
						$mysqli->query($SrtSql);
					}
					///Fin de pagos

					$MonPag=0;
				}
				if ($_POST['frm']=="H"){

					$SrtSql="UPDATE CTHonorarios SET movimiento='$SwKey' WHERE estado='A' AND id='".$LAsiento['IdDoc']."' AND rutempresa='$RutEmpresa' AND movimiento=''";
					$mysqli->query($SrtSql);

					/////Sección de Pagos de Documentos Honorarios
					if (isset($_POST['SwPago']) && $LAsiento['Glosa']!="") {
						$key=$SwKey."000";
						$SwKeyT=BuscaKey($key);			

						$res1 = $mysqli->query($SqlAux);
						while ($reg1 = $res1->fetch_assoc()) {
							$AUX=$reg1["L3"];// honorarios por pagar
						}

						$GlosaAsiPago=("PAGO CENTRALIZACIÓN HONORARIOS, N:".$NumDocu.$NombHono);

						$TipFol="E";
						$Folio=LFolio($TipFol,$TanoD);
						$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$AUX."','".$MonPag."','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
						$mysqli->query($SrtSql);
						$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','','".$_POST['SelCta']."','0','".$MonPag."','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
						$mysqli->query($SrtSql);
						$SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('".$LAsiento['Peri']."','$RutEmpresa','$xfecha','$GlosaAsiPago','0','0','0','$FECHA','A','$SwKeyT','$Folio','$TipFol','".$LAsiento['CC']."')";
						$mysqli->query($SrtSql);

						$SrtSql="INSERT INTO CTControRegDocPago VALUES ('','$RutEmpresa','$RutDocu','".$LAsiento['Peri']."','$NumDocu',0,'$SwKeyT',$MonPag,'$xfecha','".date('Y-m-d')."','".$_POST['frm']."','C','A')";
						$mysqli->query($SrtSql);
					}

					$MonPag=0;

				}
			}
		}
	}

	$mysqli->close();


	unset($_SESSION['ARRCENTRA']);
	unset($_SESSION['WhileCta']);
	// unset($_SESSION['ARRCTA']);

	header("location:index.php?".$_POST['frm']."&Pe=".$_POST['anoselect']."&Me=".$_POST['messelect']);
?>