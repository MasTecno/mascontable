<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$xttmovimiento=$_POST['tmovi'];
	$xfecha=$_POST['Fecha'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	// $xtccosto=$_POST['SelCCosto'];
	
	if (isset($_POST['Fecha'])) {

		$diaF = substr($_POST['Fecha'],0,2);
		$mesF = substr($_POST['Fecha'],3,2);
		$anoF = substr($_POST['Fecha'],6,4);
		// $xfecha=$ano."/".$mes."/".$dia;
		$_SESSION['PERIODOPC']=$mesF."-".$anoF;
	}

	$Periodo=$_SESSION['PERIODOPC'];

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}


	if ($_POST['MarApe']!="") {
		$SqlSimple="SELECT * FROM CTAsientoApertura WHERE KeyAs='".$_POST['MarApe']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
		$Resul1 = $mysqli->query($SqlSimple);
		$row_cnt1 = $Resul1->num_rows;

		if ($row_cnt1==0) {
			$mysqli->query("INSERT INTO CTAsientoApertura VALUES('','$Periodo','$RutEmpresa','".$_POST['MarApe']."');");
		}else{
			$mysqli->query("DELETE FROM CTAsientoApertura WHERE keyas='".$_POST['MarApe']."' AND RutEmpresa='$RutEmpresa'");
		}
	}


	if ($_POST['NoBase']!="") {

		$SqlSimple="SELECT * FROM CTAsientoNoBase WHERE KeyAs='".$_POST['NoBase']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
		$Resul1 = $mysqli->query($SqlSimple);
		$row_cnt1 = $Resul1->num_rows;

		if ($row_cnt1==0) {
			$mysqli->query("INSERT INTO CTAsientoNoBase VALUES('','$Periodo','$RutEmpresa','".$_POST['NoBase']."');");
		}else{
			$mysqli->query("DELETE FROM CTAsientoNoBase WHERE keyas='".$_POST['NoBase']."' AND RutEmpresa='$RutEmpresa'");
		}
	}


	if($_POST['dat2']!=""){
		$lotefac=0;
		$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'";

		$resultados = $mysqli->query($SQL1);
		while ($registro1 = $resultados->fetch_assoc()) {

			if ($registro1["glosa"]!="" && $registro1["nfactura"]>0 && $registro1["rut"]=="") {
				$lotefac=$registro1["nfactura"];
			}else{
				$xrut=$registro1["rut"];
				$xdoc=$registro1["nfactura"];
			}

			$strHono=$registro1["nfactura"];
		}      

		$strHono=substr($strHono,0,4);
		if ($strHono=="Hono") {

			$mysqli->query("UPDATE CTHonorarios SET movimiento='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND movimiento='".$_POST['dat2']."'");

			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");
			$d=date("Y-m-d");
			$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','".$_POST['dat2']."','".date("H:i:s")."','".$_SESSION['NOMBRE']."');");

			$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['dat2']."'");
			
			$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTAsientoApertura WHERE KeyAs='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");

			$EKeyasH=$_POST['dat2']."H";
			$d=date("Y-m-d");
			$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','$EKeyasH','".date("H:i:s")."','".$_SESSION['NOMBRE']."');");

		}else{
			$mysqli->query("UPDATE CTRegDocumentos SET lote='', keyas='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND keyas='".$_POST['dat2']."'");

			$mysqli->query("DELETE FROM CTFondo WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");
			$d=date("Y-m-d");
			$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','".$_POST['dat2']."','".date("H:i:s")."','".$_SESSION['NOMBRE']."');");

			$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['dat2']."'");
			
			$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTAnticipos WHERE KeyAs='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTAnticipos WHERE KeyasDestino='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTBoletasDTE WHERE keyas='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTAsientoApertura WHERE KeyAs='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTRendicion WHERE KeyAs='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");

		}
	}

	if ($_POST['Glosa']!="" && $_POST['SwGrabar']=="S") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		// ******** CONSULTA CTA AUXILIARES
		$SQL="SELECT * FROM CTParametros WHERE estado='A'";
		$resultados = $mysqli->query($SQL);

		while ($registro = $resultados->fetch_assoc()) {
			if($registro['tipo']=="CUEN_REND"){
				$CTAREND=$registro['valor'];	
			}
			if($registro['tipo']=="ANTI_PROV"){
				$ANTIPRO=$registro['valor'];	
			}
			if($registro['tipo']=="ANTI_CLIE"){
				$ANTICLI=$registro['valor'];	
			}
		}
		// ******** CONSULTA CTA AUXILIARES

		$dia = substr($xfecha,0,2);
		$mes = substr($xfecha,3,2);
		$ano = substr($xfecha,6,4);

		$xfecha=$ano."/".$mes."/".$dia;
		$FECHA=date("Y/m/d");
		$TanoD = substr($Periodo,3,4);
		$KeyAs=date("YmdHis");

		$SwKey=0;

		while ($SwKey==0) {
			$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='$KeyAs'";
			$resultados = $mysqli->query($SQL1);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SwKey=1;
			}else{
				$KeyAs=$KeyAs+1;
			}
		}

		$FolioComp=1;

		$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='$xttmovimiento' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
		$resultados = $mysqli->query($SQL1);
		while ($registro = $resultados->fetch_assoc()) {
			$FolioComp=$registro['valor'];
		}

		$SwAuxiliar="N";
		$Asiga=0;
		$Gasto=0;
		$StrSql="SELECT * FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo' AND Codigo='$CTAREND' AND Usuario='".$_SESSION['NOMBRE']."'";
		$Resultado = $mysqli->query($StrSql);
		while ($Registro = $Resultado->fetch_assoc()) {
			$SwAuxiliar="S";
			$Asiga=$Registro["Debe"];
			$Gasto=$Registro["Haber"];
			$IdRendicion=$Registro["TipoDocumento"];

			$SQL="SELECT * FROM CTCliPro WHERE estado='A' AND rut='".$Registro['Rut']."' AND tipo='".$Registro['Tipo']."'";	
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$xSelAsignar=$registro['id'];
			}
		}

		if ($SwAuxiliar=="N") {
			$StrSql="SELECT * FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo' AND Codigo='$ANTIPRO' AND Usuario='".$_SESSION['NOMBRE']."'";
			$Resultado = $mysqli->query($StrSql);
			while ($Registro = $Resultado->fetch_assoc()) {
				$SwAuxiliar="P";
				$Asiga=$Registro["Debe"];
				$Gasto=$Registro["Haber"];
			}
		}

		if ($SwAuxiliar=="N") {
			$StrSql="SELECT * FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo' AND Codigo='$ANTICLI' AND Usuario='".$_SESSION['NOMBRE']."'";
			$Resultado = $mysqli->query($StrSql);
			while ($Registro = $Resultado->fetch_assoc()) {
				$SwAuxiliar="C";
				$Asiga=$Registro["Debe"];
				$Gasto=$Registro["Haber"];
			}
		}

		$SqlTemp="SELECT * FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo' AND Usuario='".$_SESSION['NOMBRE']."' ORDER BY id ASC";
		$ResTemp = $mysqli->query($SqlTemp);
		$CantTemp = $ResTemp->num_rows;

		if ($CantTemp>1) {
			$StrSql="SELECT * FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo' AND Usuario='".$_SESSION['NOMBRE']."' ORDER BY id ASC";
			$Resultado = $mysqli->query($StrSql);
			while ($Registro = $Resultado->fetch_assoc()) {
				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,rutreferencia,tiporeferencia,docreferencia) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$Registro['Codigo']."','".$Registro['Debe']."','".$Registro['Haber']."','$FECHA','A','$KeyAs','$FolioComp','$xttmovimiento','".$Registro['CCosto']."','".$Registro['Rut']."','".$Registro['TipoDocumento']."','".$Registro['Documento']."')");
				if ($SwAuxiliar=="S") {

					if ($Registro['Debe']>0 && $Registro['Haber']==0 && $Registro['Codigo']==$CTAREND) {
						$mysqli->query("INSERT INTO CTFondo VALUES('','$xSelAsignar','".$Registro['Rut']."','$RutEmpresa','".strtoupper($_POST['Glosa'])."','$xfecha','".$Registro['Codigo']."','".$Registro['Debe']."','$KeyAs','$FECHA','I','A');");
					}

					if ($Registro['Debe']==0 && $Registro['Haber']>0 && $Registro['Codigo']==$CTAREND) {
						$mysqli->query("INSERT INTO CTFondo VALUES('','$IdRendicion','".$Registro['Rut']."','$RutEmpresa','".strtoupper($_POST['Glosa'])."','$xfecha','".$Registro['Codigo']."','".$Registro['Haber']."','$KeyAs','$FECHA','E','A');");
					}


					if ($Registro['Debe']>0 && $Registro['Haber']==0 && $Registro['Codigo']!=$CTAREND) {
						if ($Registro['TipoDocumento']>0 && $Registro['Documento']>0) {
							$TDoc="SELECT * FROM CTTipoDocumento WHERE tiposii='".$Registro['TipoDocumento']."'";
							$RTDoc = $mysqli->query($TDoc);
							while ($Reg = $RTDoc->fetch_assoc()) {
									$idtipdoc=$Reg['id'];
							}
							$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','".$Registro['Rut']."','$Periodo','$idtipdoc','".$Registro['Documento']."','$KeyAs','".$Registro['Debe']."','$xfecha','$FECHA','C','X','A')");
						}
					}
				}

				////	Anticipo Proveedores
				if ($SwAuxiliar=="P") {
					if ($Asiga>0 && $Gasto==0 && $Registro['Codigo']==$ANTIPRO) {
						$mysqli->query("INSERT INTO CTAnticipos VALUES('','$xfecha','$RutEmpresa','".$Registro['Rut']."','".$Registro['Codigo']."','".strtoupper($_POST['Glosa'])."','".$Registro['Debe']."','$KeyAs','','$FECHA','I','0','P','A');");
					}

					if ($Asig==0 && $Gasto>0 && $Registro['Codigo']!=$ANTIPRO) {
						if ($Registro['TipoDocumento']>0 && $Registro['Documento']>0) {
							$TDoc="SELECT * FROM CTTipoDocumento WHERE tiposii='".$Registro['TipoDocumento']."'";
							$RTDoc = $mysqli->query($TDoc);
							while ($Reg = $RTDoc->fetch_assoc()) {
									$idtipdoc=$Reg['id'];
							}
							$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','".$Registro['Rut']."','$Periodo','$idtipdoc','".$Registro['Documento']."','$KeyAs','".$Registro['Debe']."','$xfecha','$FECHA','C','X','A')");
						}
					}

					if ($Asiga==0 && $Gasto>0 && $Registro['Codigo']==$ANTIPRO) {
							$mysqli->query("INSERT INTO CTAnticipos VALUES('','$xfecha','$RutEmpresa','".$Registro['Rut']."','".$Registro['Codigo']."','".strtoupper($_POST['Glosa'])."','".$Registro['Haber']."','$KeyAs','','$FECHA','E','".$Registro['TipoDocumento']."','P','A');");
					}
				}

				////	Anticipo Clientes
				if ($SwAuxiliar=="C") {
					if ($Asiga==0 && $Gasto>0 && $Registro['Codigo']==$ANTICLI) {
						$mysqli->query("INSERT INTO CTAnticipos VALUES('','$xfecha','$RutEmpresa','".$Registro['Rut']."','".$Registro['Codigo']."','".strtoupper($_POST['Glosa'])."','".$Registro['Haber']."','$KeyAs','','$FECHA','I','0','C','A');");
					}

					if ($Asiga>0 && $Gasto==0 && $Registro['Codigo']!=$ANTICLI) {
						if ($Registro['TipoDocumento']>0 && $Registro['Documento']>0) {
							$TDoc="SELECT * FROM CTTipoDocumento WHERE tiposii='".$Registro['TipoDocumento']."'";
							$RTDoc = $mysqli->query($TDoc);
							while ($Reg = $RTDoc->fetch_assoc()) {
									$idtipdoc=$Reg['id'];
							}

							$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','".$Registro['Rut']."','$Periodo','$idtipdoc','".$Registro['Documento']."','$KeyAs','".$Registro['Haber']."','$xfecha','$FECHA','V','X','A')");
						}
					}

					if ($Asiga>0 && $Gasto==0 && $Registro['Codigo']==$ANTICLI) {
							$mysqli->query("INSERT INTO CTAnticipos VALUES('','$xfecha','$RutEmpresa','".$Registro['Rut']."','".$Registro['Codigo']."','".strtoupper($_POST['Glosa'])."','".$Registro['Debe']."','$KeyAs','','$FECHA','E','".$Registro['TipoDocumento']."','C','A');");
					}
				}

				if ($SwAuxiliar=="N" && $Registro['Rut']!="" && $Registro['TipoDocumento']>0 && $Registro['Documento']>0 && ($Registro['Tipo']=="P" || $Registro['Tipo']=="C")) {
					$TDoc="SELECT * FROM CTTipoDocumento WHERE tiposii='".$Registro['TipoDocumento']."'";
					$RTDoc = $mysqli->query($TDoc);
					while ($Reg = $RTDoc->fetch_assoc()) {
							$idtipdoc=$Reg['id'];
					}

					if($Registro['Tipo']=="P"){
						$MontoCP=$Registro['Debe'];
						$ControlMo="C";
					}
					if($Registro['Tipo']=="C"){
						$MontoCP=$Registro['Haber'];
						$ControlMo="V";
					}
					

					$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','".$Registro['Rut']."','$Periodo','$idtipdoc','".$Registro['Documento']."','$KeyAs','$MontoCP','$xfecha','$FECHA','$ControlMo','X','A')");
				}
			}

			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','".strtoupper($_POST['Glosa'])."','','0','0','$FECHA','A','$KeyAs','$FolioComp','$xttmovimiento','0')");

			if ($FolioComp==1) {
				$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','$xttmovimiento','2','A');");
			}else{
				$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$xttmovimiento' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
			}

			$mysqli->query("DELETE FROM CTVoucherT WHERE RutEmpresa='$RutEmpresa' AND Usuario='".$_SESSION['NOMBRE']."'");
		}
	}



	if ($_SESSION["PLAN"]=="S"){
		$SQL1="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND ";
	}else{
		$SQL1="SELECT * FROM CTCuentas WHERE ";
	}

	$Axi=array();
	$Con=1;

	$SQL2=$SQL1." auxiliar='X'";
	$Resul = $mysqli->query($SQL2);
	while ($registro = $Resul->fetch_assoc()) {
		$Axi[$Con]=$registro["numero"];
		$Con++;
	}

	echo '
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading text-center">Registro - Periodo '.$Periodo.'</div>
					<div class="panel-body">

						<table class="table table-condensed table-hover">
							<thead>
								<tr>
									<th width="10%">Fecha</th>
									<th width="5%" style="text-align: center;">Comprobante</th>
									<th width="5%" style="text-align: center;">Tipo</th>
									<th width="10%">Codigo</th>
									<th>Cuenta</th>
									<th width="10%" style="text-align: right;">Debe</th>
									<th width="10%" style="text-align: right;">Haber</th>
									<th width="1%"> </th>
								</tr>
							</thead>

							<tbody>
	';

		// Get total count of records with glosa!=''
		// $SQL_COUNT = "SELECT COUNT(*) as total FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND glosa!=''";
		// $result_count = $mysqli->query($SQL_COUNT);
		// $total_records = $result_count->fetch_assoc()['total'];

		// // Calculate pagination
		// $records_per_page = 100;
		// $total_pages = ceil($total_records / $records_per_page);

		// // Get current page from URL parameter, default to 1
		// $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

		// $offset = ($current_page - 1) * $records_per_page;


		$SQL = "SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND glosa!='' GROUP BY id,keyas ORDER BY fecha, id, debe ASC";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$AKeyAs[]=$registro["keyas"];
		}

		$ContKeyAs=1;

		$current_page=count($AKeyAs)/10;
		$total_pages=ceil($current_page);

		$records_per_page = 100;
		$total_pages = ceil(count($AKeyAs) / $records_per_page);

		$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

		//echo $_GET['page'];
		
		//// Pagination
		$d1=($current_page - 1) * $records_per_page; //// Desde que Inicio
		$d2=$records_per_page; ///// cuando registro muestras


		foreach(array_slice($AKeyAs, $d1, $d2) as $keyas){
			// Main query with pagination
			//$SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND keyas='$keyas' GROUP BY id,keyas ORDER BY fecha, id, debe ASC LIMIT $offset, $records_per_page";
			$SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND keyas='$keyas' GROUP BY id,keyas ORDER BY fecha, id, debe ASC ";
			// echo "<br>";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {

				$SwBoleta=0;
				if ($registro["nfactura"]=="CenBolEle" || $registro["nfactura"]=="PagBolEle"){
					$SwBoleta=1;
				}

				$SQL2="";
				$ncuenta="<strong>Cta NO Existe</strong>";
				$SQL2=$SQL1." numero='".$registro["cuenta"]."'";
				$resultados1 = $mysqli->query($SQL2);
				while ($registro1 = $resultados1->fetch_assoc()) { 
					$ncuenta=strtoupper($registro1["detalle"]);
				}

				if($registro["glosa"]==""){
					echo '
					<tr>
						<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
							<td></td>
							<td></td>';

					$TextRef='';

					$clave = array_search($registro["cuenta"], $Axi);

					if ($clave!="" && $clave>0) {
						$SqlSimple="SELECT * FROM `CTRegDocumentos` WHERE keyas='".$registro["keyas"]."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
						$Resul1 = $mysqli->query($SqlSimple);
						$row_cnt1 = $Resul1->num_rows;

						$SqlSimple="SELECT * FROM CTControRegDocPago WHERE keyas='".$registro['keyas']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
						$Resul1 = $mysqli->query($SqlSimple);
						$row_cnt2 = $Resul1->num_rows;

						$SqlSimple="SELECT * FROM CTHonorarios WHERE movimiento='".$registro['keyas']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
						$Resul1 = $mysqli->query($SqlSimple);
						$row_cnt3 = $Resul1->num_rows;


						if ($row_cnt1>0) {
							$TextRef=' <a href="" onclick="Info(\''.$registro['keyas'].'\',\'C\')" class="btn btn-xs btn-default" data-toggle="modal" data-target="#RefDocumentos"><span class="glyphicon glyphicon-info-sign"></span></a>';
						}else{
							if ($row_cnt2>0) {
								$TextRef=' <a href="" onclick="Info(\''.$registro['keyas'].'\',\'P\')" class="btn btn-xs btn-default" data-toggle="modal" data-target="#RefDocumentos"><span class="glyphicon glyphicon-info-sign"></span></a>';
							}else{
								if ($row_cnt3>0) {
									$TextRef=' <a href="" onclick="Info(\''.$registro['keyas'].'\',\'H\')" class="btn btn-xs btn-default" data-toggle="modal" data-target="#RefDocumentos"><span class="glyphicon glyphicon-info-sign"></span></a>';
								}
							}
						}
					}

					if ($registro["nfactura"]=="CenBolEle" || $registro["nfactura"]=="PagBolEle"){
						$TextRef=' *Boletas Electronicas';
					}

					$swCC='';
					$SqlSimple="SELECT * FROM CTCCosto WHERE id='".$registro["ccosto"]."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
					$Resul1 = $mysqli->query($SqlSimple);
					while ($Regi1 = $Resul1->fetch_assoc()) { 
						$swCC='<d style="font-size: 10px;">('.$Regi1['nombre'].')</d>';
					}


					$SQL3="SELECT * FROM CTFondo WHERE keyas='".$registro["keyas"]."' AND Cuenta='".$registro["cuenta"]."'";
					$resultados3 = $mysqli->query($SQL3);
					while ($registro3 = $resultados3->fetch_assoc()) {
						$NRazSoc="";
						$RRazSoc="";
						if($registro3["Rut"]!=""){
							$RRazSoc=$registro3["Rut"];
							$SQL4="SELECT * FROM CTCliPro WHERE rut='".$registro3["Rut"]."'";
							$resultados4 = $mysqli->query($SQL4);
							while ($registro4 = $resultados4->fetch_assoc()) { 
								$NRazSoc=$registro4["razonsocial"];
							}
						}else{

							$SQL4="SELECT * FROM CTFondo WHERE keyas='".$registro["keyas"]."' ORDER BY Id LIMIT 1";
							$resultados4 = $mysqli->query($SQL4);
							while ($registro4 = $resultados4->fetch_assoc()) { 
								$RRazSoc=$registro4["Rut"];
							}

							$SQL4="SELECT * FROM CTCliPro WHERE rut='$RRazSoc'";
							$resultados4 = $mysqli->query($SQL4);
							while ($registro4 = $resultados4->fetch_assoc()) { 
								$NRazSoc=$registro4["razonsocial"];
							}
							break;
						}

						$TextRef=" (".$RRazSoc." - ".$NRazSoc.")";
					}
					
					echo '
						<td>'.$registro["cuenta"].'</td>
						<td>'.$ncuenta.'<l style="font-size: 10px;"> '.$TextRef.' '.$swCC.'</l></td>
						<td align="right">'.number_format($registro["debe"], $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format($registro["haber"], $NDECI, $DDECI, $DMILE).'</td>
						<td align="center"></td>
					</tr>
					';
					$tgdebe=$tgdebe+$registro["debe"];
					$tghaber=$tghaber+$registro["haber"];
				}

				$swAp='';
				if($registro["glosa"]!=""){
					$swAp2='';
					$swAp3='';

					$SqlSimple="SELECT * FROM CTAsientoApertura WHERE KeyAs='".$registro["keyas"]."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
					$Resul1 = $mysqli->query($SqlSimple);
					$row_cnt1 = $Resul1->num_rows;
					$swAp="";
					if ($row_cnt1>0) {
						$swAp2=' <d style="font-size: 10px;">(Apertura)</d>';
					}


					$SqlSimple="SELECT * FROM CTAsientoNoBase WHERE KeyAs='".$registro["keyas"]."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
					$Resul1 = $mysqli->query($SqlSimple);
					$row_cnt1 = $Resul1->num_rows;
					if ($row_cnt1>0) {
						$swAp3=' <d style="font-size: 10px;">(No Base Imponible)</d>';
					}

					$swAp=$swAp2.$swAp3;
					if ($swAp=="()") {
						$swAp='';
					}

					if ($registro["tipo"]=="E") {
						$xMen="Egreso";
					}
					if ($registro["tipo"]=="I") {
						$xMen="Ingreso";	
					}
					if ($registro["tipo"]=="T") {
						$xMen="Traspaso";
					}

					$GlosaElim=str_replace('"','',$registro["glosa"]);
					$GlosaElim=str_replace("'","",$registro["glosa"]);

					echo '
					<tr class="info"> 
						<td>

							<div class="btn-group">
								<button type="button" class="btn btn-mastecno btn-xs">Opciones</button>
								<button type="button" class="btn btn-mastecno btn-xs dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li><a href="#" style="font-size: 12px;" data-toggle="modal" data-target="#ModalComent" onclick="ImpCom(\''.$registro["keyas"].'\')">Anotaci&oacute;n y/o Imprimir</a></li>
									<li><a href="javascript:ModAsiento(\''.$registro["keyas"].'\');" style="font-size: 12px;" onclick="">Modificar</a></li>
									<li><a href="#" style="font-size: 12px;" data-toggle="modal" data-target="#ModalPlantilla" onclick="GuaPlan(\''.$registro["keyas"].'\')">Guardar como Plantilla</a></li>
									<li><a href="#" style="font-size: 12px;" onclick="GuaAper(\''.$registro["keyas"].'\')">Marcar como Apertura</a></li>
									<li><a href="#" style="font-size: 12px;" onclick="NoBase(\''.$registro["keyas"].'\')">No Afecta Base Imponible</a></li>
								</ul>
							</div>

						</td>
						<td align="center">'.number_format($registro["ncomprobante"], $NDECI, $DDECI, $DMILE).'</td>
						<td align="center">'.$xMen.'</td>
						<td></td>
						<td><strong>'.strtoupper($registro["glosa"]).$swAp.'</strong></td>
						<td align="right">'.number_format($tgdebe, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format($tghaber, $NDECI, $DDECI, $DMILE).'</td>
						<td align="center" ><button type="button" class="btn btn-cancelar btn-xs" onclick="EliRegA(\''.$registro["keyas"].'\',\''.strtoupper($GlosaElim).'\')">X</button></td>
					</tr>
					';
					
					$tgdebe=0;
					$tghaber=0;
				}
			}
			$ContKeyAs++;
		}

		// Display pagination links
		echo '<tr><td colspan="8" align="center">';
		echo '<ul class="pagination">';
		
		// Previous page link
		if($current_page > 1) {
			echo '<li><a href="?page='.($current_page-1).'">&laquo;</a></li>';
		}
		
		// Page numbers
		for($i = 1; $i <= $total_pages; $i++) {
			if($i == $current_page) {
				echo '<li class="active"><a href="#">'.$i.'</a></li>';
			} else {
				echo '<li><a href="?page='.$i.'">'.$i.'</a></li>';
			}
		}
		
		// Next page link
		if($current_page < $total_pages) {
			echo '<li><a href="?page='.($current_page+1).'">&raquo;</a></li>';
		}
		
		echo '</ul>';
		echo '</td></tr>';
	$mysqli->close();
?>
							</tbody>
						</table>

					</div>				
				</div>