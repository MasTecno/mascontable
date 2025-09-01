<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	// Configurar el tiempo máximo de ejecución a 2 minutos
	set_time_limit(30); //(120);
	$start_time = time();
	$max_execution_time = 30; // 120; // 2 minutos en segundos

	function checkTimeout($start_time, $max_execution_time) {
		if (time() - $start_time > $max_execution_time) {
			// Limpiar cookies y cerrar sesión
			global $userrut;
			if (file_exists("cookiesT".$userrut.".txt")) {
				unlink("cookiesT".$userrut.".txt");
			}
			echo json_encode(array(
				"dato1" => "ERROR: Tiempo de ejecución excedido",
				"dato2" => "",
				"dato3" => "",
				"dato4" => "0"
			));
			exit;
            
		}
	}

	if ($_POST['messelect']<=9) {
		$xmesselect="0".$_POST['messelect'];
	}else{
		$xmesselect=$_POST['messelect'];
	}

	$Periodo=$xmesselect."-".$_POST['anoselect'];

    $cookie_file = "cookiesT".$userrut.".txt";
    if (file_exists($cookie_file)) {
        unlink($cookie_file);
    }


function login($urut,$upass,$mper,$aper) {
    global $start_time, $max_execution_time;
    
    checkTimeout($start_time, $max_execution_time);

    $ch = curl_init();
    $cookie = "cookiesT".$urut.".txt";

    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36';
    $url = 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi';

    $rut = substr(str_replace(".", "", $urut),0,-2);
    $dv = substr($urut,-1);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'rutcntr' => $urut,
        'clave'    => $upass,
        'rut'      => $rut,
        'referencia' => 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi',
        'dv'         => $dv
    ]));

    curl_setopt($ch, CURLOPT_USERAGENT,$userAgent);
    curl_setopt($ch, CURLOPT_COOKIE,$cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    //curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLINFO_HEADER_OUT,1);
    curl_setopt($ch, CURLOPT_TIMEOUT,-1);


    $result  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code != 200){
        exit(0);
    }

    // $dummy = rand(13);
    $dummy = rand(1, 13);

    curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi?dummy='.$dummy);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLINFO_HEADER_OUT,1);
    curl_setopt($ch, CURLOPT_POST , false);
    curl_setopt($ch, CURLOPT_HTTPGET , true);
    curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi');

    $result  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code != 200){
        exit(0);
    }

    $pagina = 0;
    curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_InformeMensualBhe.cgi');
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLINFO_HEADER_OUT,1);
    curl_setopt($ch, CURLOPT_REFERER, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi?dummy='.$dummy);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'rut_arrastre'      => $rut,
        'dv_arrastre'         => $dv,
        'pagina_solicitada' => $pagina,
        'cbmesinformemensual'    => $mper,
        'cbanoinformemensual' => $aper
    ]));

    

    $result  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code != 200){
        exit(0);
    }

    curl_close($ch);

    $dom = new DOMDocument;
    $dom->loadHTML(utf8_encode($result));

    $scripts = $dom->getElementsByTagName('script');


    $mscript = null;
    foreach ($scripts as $script) {
        $code = $script->nodeValue;
        if(substr_count($code, 'document.write') == 0 && substr_count($code, 'var arr_informe_mensual = new Array()') > 0){
            $mscript = $code;
        }
    }

    preg_match_all('/arr_informe_mensual\[.*/', $mscript, $coincidencias);

    $informes = [];
    foreach ($coincidencias[0] as $coincidencia){
        $var = explode("=", $coincidencia);
        $keyx = trim(preg_replace("/arr_informe_mensual\[\'|\'\]/", "", $var[0]));
        $parts = explode('_', $keyx);
        $id = array_pop($parts);

        $key = implode('_', $parts);

        $value = trim(preg_replace("/[(formatMiles\()(,'.')]/", "", $var[1]));
        $value = trim(preg_replace("/;/", "", $value));

        $informes[$id][$key] = $value;

    }

    return $informes;
}

$mesper=substr($Periodo,0,2);
$anoper=substr($Periodo,3,4);

$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
$SQL="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Estado='A'";
$resultados = $mysqli->query($SQL);
while ($registro = $resultados->fetch_assoc()) {
    $ValRSII=$registro['RutSii']; 
    $ValCSII=descript($registro['PasSii']); 
}

$sdoc="R";
$xcuenta="";
$SQL="SELECT * FROM CTAsientoHono";
$resultados = $mysqli->query($SQL);

while ($registro = $resultados->fetch_assoc()) {
    if ($registro['tipo']==$sdoc) { ///honorarios recividos
      $xcuenta=$registro['L1'];
    }
    if ($registro['tipo']==$sdoc) { ///Honorarios emitidos
      $xcuenta=$registro['L1'];
    }
}

$userrut=$ValRSII;
$userpass=$ValCSII;

$registros = login($userrut, $userpass, $mesper, $anoper);

// echo '<pre>';
// print_r($registros);
// echo '</pre>';
// exit;

$ConDoc=0;

foreach ($registros as $dato){
    checkTimeout($start_time, $max_execution_time);
    
    // Accede a los datos de la boleta
    $NumHon = $dato['nroboleta'];
    $estado = $dato['estado'];
    $FecHon = $dato['fechaemision'];

    // Datos del receptor
    $RutHon = $dato['rutreceptor']."-".$dato['dvreceptor'];
    $NomHon = $dato['nombrereceptor'];

    // Datos de los honorarios
    $TotHon = $dato['totalhonorarios'];
    $RetHon = $dato['retencion_receptor'];
    $LiqHon = $dato['honorariosliquidos'];

    $TotHon = str_replace(".","",$TotHon);
    $RetHon = str_replace(".","",$RetHon);
    $LiqHon = str_replace(".","",$LiqHon);

    $NumHon=str_replace('"','',$NumHon);
    $estado=str_replace('"','',$estado);
    $FecHon=str_replace('"','',$FecHon);
    $RutHon=str_replace('"','',$RutHon);
    $NomHon=str_replace('"','',$NomHon);
    $TotHon=str_replace('"','',$TotHon);
    $RetHon=str_replace('"','',$RetHon);
    $LiqHon=str_replace('"','',$LiqHon);
    $TotHon=str_replace('"','',$TotHon);
    $RetHon=str_replace('"','',$RetHon);
    $LiqHon=str_replace('"','',$LiqHon);

    $dia = substr($FecHon,0,2);
    $mes = substr($FecHon,3,2);
    $ano = substr($FecHon,6,4);
    $FecHon=$ano."-".$mes."-".$dia;

    $SqlCP="SELECT * FROM CTHonorarios WHERE rut='$RutHon' AND numero='$NumHon' AND fecha='$FecHon' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
    $Resul = $mysqli->query($SqlCP);
    $row_cnt1 = $Resul->num_rows;
    if ($row_cnt1==0 && $estado=="N") {
        $Sql=$Sql."INSERT INTO CTHonorarios VALUES('','$Periodo','".$_SESSION['RUTEMPRESA']."','$FecHon','$RutHon','$NumHon','$xcuenta','','$TotHon','$RetHon','$LiqHon','T','".date("Y-m-d")."','','A','');";
        $ConDoc++;
    }

    $ConDoc2++;

    $SqlCP="SELECT * FROM CTCliPro WHERE rut='$RutHon' AND tipo='P'";
    $Resul = $mysqli->query($SqlCP);
    $row_cnt = $Resul->num_rows;
    if ($row_cnt==0) {				
        $SqlRCP="INSERT INTO CTCliPro (id, rut, razonsocial, tipo, estado) VALUES ('','$RutHon','".utf8_decode(strtoupper($NomHon))."','P','A');";
        $mysqli->query($SqlRCP);
    }
}

// [nroboleta] => "8790"
// [usuemisor] => "ASESORIA TRIBUTARIA Y CONTABLES JAIE LOPEZ TAPIA EIRL"
// [fechaemision] => "03/02/2025"
// [rutreceptor] => "77277549"
// [dvreceptor] => "0"
// [nombrereceptor] => "COERCIAL ELSA ALEJANDRA CARO"
// [fecha_boleta] => "30/01/2025"
// [totalhonorarios] => "23392"
// [es_soc_profesional] => "NO"
// [email_envio] => " "
// [retencion_emisor] => "0"
// [retencion_receptor] => "3392"
// [honorariosliquidos] => "20000"
// [estado] => "S"
// [fechaanulacion] => "03/02/2025"
// [codigobarras] => "76188199087909051009"
// exit;

if($Sql!=""){
    $mysqli->multi_query($Sql);
    $mysqli->close();
    $xDato1 = "BOLETA DE HONORARIO: $ConDoc";
}else{
    // if($row_cnt1>0){
    //     $xDato1 = "BOLETA DE HONORARIO: ".$ConDoc;
    // }
    if(count($registros)==0){
        $xDato1 = "NO HAY DOCUMENTOS A SINCRONIZAR";
    }
}

$xDato4 = $ConDoc;

unlink("cookiesT".$userrut.".txt");

echo json_encode(
    array("dato1" => "$xDato1", 
    "dato2" => "$xDato2",
    "dato3" => "$xDato3",
    "dato4" => "$xDato4"
    )
);