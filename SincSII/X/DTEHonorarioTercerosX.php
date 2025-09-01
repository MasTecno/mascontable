<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

    //* Este codigo es el que debo revisar
    // $userrut=$_POST['rut'];

    //* Ver si interfiere con otras partes del codigo
	if ($_POST['messelect']<=9) {
		$xmesselect="0".$_POST['messelect'];
	}else{
		$xmesselect=$_POST['messelect'];
	}

	$Periodo=$xmesselect."-".$_POST['anoselect'];

//* Recibe rut, contraseña, mes y año
function login($urut,$upass,$mper,$aper) {

    try {

        $ch = curl_init();
        $cookie = "cookiesTerc".$urut.".txt";
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36';
        $url = 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi';

        $rut = substr(str_replace(".", "", $urut),0,-2);
        $dv = substr($urut,-1);
        $rut = $rut*1;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'rutcntr' => $urut,
            'clave'    => $upass,
            'rut'      => $rut, //* Rut sin digito verificador
            // 'referencia' => 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1',
            'referencia' => 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi',
            'dv'         => $dv //* Digito verificador
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
        curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/AUT2000/InicioAutenticacion/IngresoRutClave.html?https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi?1');

        $result  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code != 200){
            exit(0);
        }

        //* Acceder a esta url despues del login
        curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi');
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

        //* Url informe anual
        curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_InformeAnualBhe.cgi');
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLINFO_HEADER_OUT,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'rut_arrastre'      => $rut,
            'dv_arrastre'         => $dv,
            // 'pagina_solicitada' => 0,
            'cbmesinformemensual'    => $mper,
            // 'cbanoinformemensual' => $aper
            'cbanoinformeanual' => $aper
        ]));
        curl_setopt($ch, CURLOPT_REFERER, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi');

        $result  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code != 200){
            exit(0);
        }

        //* Url mes especifico
        curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_InformeMensualBhe.cgi');
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLINFO_HEADER_OUT,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'cbanoinformemensual' => $aper,
            'cbmesinformemensual'    => $mper,
            'dv_arrastre'         => $dv,
            'pagina_solicitada' => 0,
            'rut_arrastre'      => $rut,
        ]));

        // echo "Mes: " . $mper . "<br>";
        // echo "Año: " . $aper . "<br>";
        // echo "Rut: " . $rut . "<br>";
        // echo "DV: " . $dv . "<br>";
    
        // curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi');
        curl_setopt($ch, CURLOPT_REFERER, 'https://loa.sii.cl/cgi_IMT/TMBCOC_InformeAnualBhe.cgi?rut_arrastre='.$rut.'&dv_arrastre='.$dv.'&cbanoinformeanual='.$aper);


        $result  = curl_exec($ch);
        // file_put_contents('debug_result.html', $result);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code != 200){
            exit(0);
        }
        


        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";

        // Debug log the response
        error_log("SII Response: " . substr($result, 0, 1000)); // Log first 1000 chars

        $disabled = false;
        $pagina = 1;
        $informes = array();
        // $now = new DateTime();
        // curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi');
        // curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        // // curl_setopt($ch, CURLOPT_REFERER, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1');
        // curl_setopt($ch, CURLOPT_REFERER, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContrib.cgi');


    
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $htmlUtf8 = '<?xml encoding="UTF-8">' . $result;
        $dom->loadHTML($htmlUtf8, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $scripts = $dom->getElementsByTagName('script');

        $mscript = null;
        foreach ($scripts as $script) {
            $code = $script->nodeValue;
            if(substr_count($code, 'codigobarras_1') > 0){
                $mscript = $code;
            }

        }
        // echo "<pre>";
        // print_r($mscript);
        // echo "</pre>";

        preg_match_all('/arr_informe_mensual\[.*/', $mscript, $coincidencias);
        // echo "+++";
        $informes = [];

        foreach ($coincidencias[0] as $coincidencia){
            $var = explode("=", $coincidencia);
            $key = trim(preg_replace("/arr_informe_mensual\[\'|\'\]/", "", $var[0]));

            $value = trim(preg_replace("/[(formatMiles\()(,'.')]/", "", $var[1]));
            $value = trim(preg_replace("/;/", "", $value));
            $informes[$key] = $value;
        }

        if(count($informes) === 0){
            echo "No se encontraron informes en la respuesta del SII.";
            exit;
        }

        // echo '<pre>';
        // print_r($informes);
        // echo '</pre>';

        return $informes;
        
    }catch (Exception $e) {
        error_log("Error al obtener registros: " . $e->getMessage());
        exit;
    }

    // do {
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    //         'CNTR' => 1,
    //         'PAGINA' => $pagina,
    //         'AUTEN' => 'RUTCLAVE',
    //         'TIPO' => 'mensual',
    //         'MESM' => $mper,
    //         'ANOM' => $aper
    //     ]));


    //     $result = curl_exec($ch);
    //     $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //     if ($code != 200) {
    //         error_log("HTTP Error: " . $code);
    //         echo $code;
    //         exit(0);
    //     }

    //     // Debug log the response
    //     error_log("SII Response: " . substr($result, 0, 1000)); // Log first 1000 chars

    //     $dom = new DOMDocument('1.0', 'UTF-8');
    //     $internalErrors = libxml_use_internal_errors(true);
    //     // $dom->loadHTML(utf8_encode($result));
    //     $dom->loadHTML(mb_convert_encoding($result, 'HTML-ENTITIES', 'UTF-8'));
    //     libxml_use_internal_errors($internalErrors);
    //     $xpath = new DOMXpath($dom);
    //     $elements = $xpath->query('//input[@type="button" and @name="NEXT"]/@disabled');
    //     $disabled = !is_null($elements->item(0));

    //     $tables = $dom->getElementsByTagName('table');
    //     //error_log("Number of tables found: " . $tables->length);

    //     if ($tables->length < 4) {
    //         //error_log("Table structure not found in response");
    //         continue;
    //     }

    //     $dataTable = $tables[3]->getElementsByTagName('tr');
    //     //error_log("Number of rows in table: " . $dataTable->length);
        
    //     for ($i = 2; $i < $dataTable->length - 1; $i++) {
    //         $element = $dataTable[$i];

    //         $informe = [
    //             'boleta' => [
    //                 'N' => trim($element->childNodes[2]->nodeValue),
    //                 'estado' => trim($element->childNodes[4]->nodeValue),
    //                 'fecha' => trim($element->childNodes[5]->nodeValue)
    //             ],
    //             'emision' => [
    //                 'rut' => trim($element->childNodes[7]->nodeValue),
    //                 'nombre' => trim($element->childNodes[9]->nodeValue),
    //                 'fecha' => trim($element->childNodes[11]->nodeValue)
    //             ],
    //             'receptor' => [
    //                 'rut' => trim($element->childNodes[13]->nodeValue),
    //                 'nombre' => trim($element->childNodes[15]->nodeValue),
    //             ],
    //             'honorarios' => [
    //                 'brutos' => trim($element->childNodes[17]->nodeValue),
    //                 'retenidos' => trim($element->childNodes[19]->nodeValue),
    //                 'pagados' => trim($element->childNodes[21]->nodeValue),
    //             ]
    //         ];

    //         $informes['boletas'][] = $informe;
    //     }

    //     $pagina++;
    //     sleep(3);
    // }while($disabled == false);

    // curl_close($ch);

    // return $informes;

}

// $mesper="09";
// $anoper="2023";

//* Credenciales de login
// $userrut="76.188.199-K";
// $userpass="JALOTA2";

// $userrut="76.036.407-K";
// $userpass="idds2023";

// $mesper="02";
// $anoper="2025";
// $Periodo=$mesper."-".$anoper;

// $userrut="77.566.467-3";
// $userpass="SERVI77566";

$mesper=substr($Periodo,0,2);
$anoper=substr($Periodo,3,4);

$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
$SQL="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Estado='A'";
// exit;

$resultados = $mysqli->query($SQL);
while ($registro = $resultados->fetch_assoc()) {
    $ValRSII=$registro['RutSii']; 
    $ValCSII=descript($registro['PasSii']); 
}

$userrut=$ValRSII;
$userpass=$ValCSII;

// echo "999944444".$mesper.$SQL;
// exit;

$boletas = login($userrut, $userpass, $mesper, $anoper);

//* Revisar aca
echo '<pre>';
print_r($boletas);
echo '</pre>';
$i=1;
$Sql="";
foreach ($boletas as $clave => $valor) {

    $valor=str_replace("\"","",$valor);

    if($i==1){
        $NumHon=$valor;
    }

    if($i==3){
        $FecHon=$valor;
        $dia = substr($FecHon,0,2);
        $mes = substr($FecHon,3,2);
        $ano = substr($FecHon,6,4);
        $FecHon=$ano."-".$mes."-".$dia;
    }

    if($i==4){
        $RutHon=$RutHon;
    }

    if($i==5){
        $RutHon=$RutHon."-".$valor;
    }

    if($i==16){
        $Sql=$Sql."INSERT INTO CTHonorarios VALUES('','$Periodo','".$_SESSION['RUTEMPRESA']."','$FecHon','$RutHon','$NumHon','$xcuenta','$xccosto','$TotHon','$RetHon','$LiqHon','T','".date("Y-m-d")."','','A','');";
        $i=0;
        $RutHon="";
    }


    $i++;
}

echo $Sql;
exit;

// boletas
// $ConDoc=0;
// foreach($boletas as $boleta){
//     $xcuenta=$xcuentaOriginal;
//     $xccosto=$xccostoOriginal;

//     // checkTimeout($start_time, $max_execution_time);
    
//     // Accede a los datos de la boleta
//     $NumHon = $boleta['boleta']['N'];
//     // $estado = $boleta['boleta']['estado'];
//     $FecHon = $boleta['boleta']['fecha'];
    
//     $dia = substr($FecHon,0,2);
//     $mes = substr($FecHon,3,2);
//     $ano = substr($FecHon,6,4);
//     $FecHon=$ano."-".$mes."-".$dia;

//     // Datos de la emisión
//     // $rut_emision = $boleta['emision']['rut'];
//     // $nombre_emision = $boleta['emision']['nombre'];
//     // $fecha_emision = $boleta['emision']['fecha'];

//     // Datos del receptor
//     $RutHon = $boleta['receptor']['rut'];
//     $NomHon = $boleta['receptor']['nombre'];

//     // Datos de los honorarios
//     $TotHon = $boleta['honorarios']['brutos'];
//     $RetHon = $boleta['honorarios']['retenidos'];
//     $LiqHon = $boleta['honorarios']['pagados'];

//     $TotHon = str_replace(".","",$TotHon);
//     $RetHon = str_replace(".","",$RetHon);
//     $LiqHon = str_replace(".","",$LiqHon);


//     $SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$RutHon' AND tipo='H'";
//     $Resul = $mysqli->query($SqlCP);
//     $row_cnt = $Resul->num_rows;
//     if ($row_cnt>0) {
//         $SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$RutHon' AND tipo='H'";
//         $Resul = $mysqli->query($SqlCP);
//         while ($Reg = $Resul->fetch_assoc()) {
//             if ($Reg['cuenta']!=""){
//                 $xcuenta=$Reg['cuenta'];
//             }
//             if ($Reg['ccosto']!="") {
//                 $xccosto=$Reg['ccosto'];
//             }
//         }
//     }

//     // $xDato1 =1;
//     $SqlCP="SELECT * FROM CTHonorarios WHERE rut='$RutHon' AND numero='$NumHon' AND fecha='$FecHon' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
//     $Resul = $mysqli->query($SqlCP);
//     $row_cnt1 = $Resul->num_rows;
//     if ($row_cnt1==0) {
        $Sql=$Sql."INSERT INTO CTHonorarios VALUES('','$Periodo','".$_SESSION['RUTEMPRESA']."','$FecHon','$RutHon','$NumHon','$xcuenta','$xccosto','$TotHon','$RetHon','$LiqHon','T','".date("Y-m-d")."','','A','');";
//         $ConDoc++;
//         // $xDato1 =2;
//     }

//     $i=0;

//     $SqlCP="SELECT * FROM CTCliPro WHERE rut='$RutHon' AND tipo='P'";
//     $Resul = $mysqli->query($SqlCP);
//     $row_cnt = $Resul->num_rows;
//     if ($row_cnt==0) {				
//         $SqlRCP="INSERT INTO CTCliPro (id, rut, razonsocial, tipo, estado) VALUES ('','$RutHon','".utf8_decode(strtoupper($NomHon))."','P','A');";
//         $mysqli->query($SqlRCP);
//     }
// }

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

unlink("cookiesTerc".$userrut.".txt");

echo json_encode(
    array("dato1" => "$xDato1", 
    "dato2" => "$xDato2",
    "dato3" => "$xDato3",
    "dato4" => "$xDato4"
    )
);