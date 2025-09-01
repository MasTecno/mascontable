<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if ($_POST['messelect']<=9) {
		$xmesselect="0".$_POST['messelect'];
	}else{
		$xmesselect=$_POST['messelect'];
	}

	$Periodo=$xmesselect."-".$_POST['anoselect'];

function login($urut,$upass,$mper,$aper) {

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
        'referencia' => 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1',
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
    curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/AUT2000/InicioAutenticacion/IngresoRutClave.html?https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1');

    $result  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code != 200){
        exit(0);
    }

    curl_setopt($ch, CURLOPT_URL, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1');
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
    $disabled = false;
    $pagina = 1;
    $informes = array();
    $now = new DateTime();
    curl_setopt($ch, CURLOPT_URL, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons2');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    curl_setopt($ch, CURLOPT_REFERER, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1');

    do {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'CNTR' => 1,
            'PAGINA' => $pagina,
            'AUTEN' => 'RUTCLAVE',
            'TIPO' => 'mensual',
            'MESM' => $mper,
            'ANOM' => $aper
        ]));

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code != 200) {
            echo $code;
            exit(0);
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML(utf8_encode($result));
        libxml_use_internal_errors($internalErrors);

        $xpath = new DOMXpath($dom);
        $elements = $xpath->query('//input[@type="button" and @name="NEXT"]/@disabled');
        $disabled = !is_null($elements->item(0));

        $tables = $dom->getElementsByTagName('table');
        $dataTable = $tables[3]->getElementsByTagName('tr');
        for ($i = 2; $i < $dataTable->length - 1; $i++) {
            $element = $dataTable[$i];

            $informe = [
                'boleta' => [
                    'N' => trim($element->childNodes[2]->nodeValue),
                    'estado' => trim($element->childNodes[4]->nodeValue),
                    'fecha' => trim($element->childNodes[5]->nodeValue)
                ],
                'emision' => [
                    'rut' => trim($element->childNodes[7]->nodeValue),
                    'nombre' => trim($element->childNodes[9]->nodeValue),
                    'fecha' => trim($element->childNodes[11]->nodeValue)
                ],
                'receptor' => [
                    'rut' => trim($element->childNodes[13]->nodeValue),
                    'nombre' => trim($element->childNodes[15]->nodeValue),
                ],
                'honorarios' => [
                    'brutos' => trim($element->childNodes[17]->nodeValue),
                    'retenidos' => trim($element->childNodes[19]->nodeValue),
                    'pagados' => trim($element->childNodes[21]->nodeValue),
                ]
            ];

            $informes['boletas'][] = $informe;
        }

        $pagina++;
        sleep(3);
    }while($disabled == false);

    curl_close($ch);

    return $informes;

}

// $mesper="09";
// $anoper="2023";

// $userrut="76.982.219-4";
// $userpass="asd13522";

// $mesper="04";
// $anoper="2023";
// $Periodo=$mesper."-".$anoper;

// $userrut="77.566.467-3";
// $userpass="SERVI77566";

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

$boletas[] = login($userrut, $userpass, $mesper, $anoper);

// echo '<pre>';
// print_r($boletas);
// echo '</pre>';

// boletas
$ConDoc=0;
// $xDato1 ="xxx".$boletas;
foreach($boletas['boletas'] as $boleta){
    // Accede a los datos de la boleta
    $NumHon = $boleta['boleta']['N'];
    // $estado = $boleta['boleta']['estado'];
    $FecHon = $boleta['boleta']['fecha'];
    
    $dia = substr($FecHon,0,2);
    $mes = substr($FecHon,3,2);
    $ano = substr($FecHon,6,4);
    $FecHon=$ano."-".$mes."-".$dia;

    // Datos de la emisión
    // $rut_emision = $boleta['emision']['rut'];
    // $nombre_emision = $boleta['emision']['nombre'];
    // $fecha_emision = $boleta['emision']['fecha'];

    // Datos del receptor
    $RutHon = $boleta['receptor']['rut'];
    $NomHon = $boleta['receptor']['nombre'];

    // Datos de los honorarios
    $TotHon = $boleta['honorarios']['brutos'];
    $RetHon = $boleta['honorarios']['retenidos'];
    $LiqHon = $boleta['honorarios']['pagados'];

    $TotHon = str_replace(".","",$TotHon);
    $RetHon = str_replace(".","",$RetHon);
    $LiqHon = str_replace(".","",$LiqHon);

    // $xDato1 =1;
    $SqlCP="SELECT * FROM CTHonorarios WHERE rut='$RutHon' AND numero='$NumHon' AND fecha='$FecHon' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
    $Resul = $mysqli->query($SqlCP);
    $row_cnt1 = $Resul->num_rows;
    if ($row_cnt1==0) {
        $Sql=$Sql."INSERT INTO CTHonorarios VALUES('','$Periodo','".$_SESSION['RUTEMPRESA']."','$FecHon','$RutHon','$NumHon','$xcuenta','','$TotHon','$RetHon','$LiqHon','T','".date("Y-m-d")."','','A','');";
        $ConDoc++;
        // $xDato1 =2;
    }

    $i=0;

    $SqlCP="SELECT * FROM CTCliPro WHERE rut='$RutHon' AND tipo='P'";
    $Resul = $mysqli->query($SqlCP);
    $row_cnt = $Resul->num_rows;
    if ($row_cnt==0) {				
        $SqlRCP="INSERT INTO CTCliPro (id, rut, razonsocial, tipo, estado) VALUES ('','$RutHon','".utf8_decode(strtoupper($NomHon))."','P','A');";
        $mysqli->query($SqlRCP);
    }
}

if($Sql!=""){
    $mysqli->multi_query($Sql);
    $mysqli->close();
    $xDato1 = "BOLETA HONORARIO TERCEROS: $ConDoc";
    
}else{
    if($row_cnt1>0){
        $xDato1 = "<strong>Exito, Documentos procesados satisfactoriamente (Algunos Documentos ya Registrados)</strong>";
    }
    if($row_cnt1==0){
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