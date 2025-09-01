<?php

class JsParserException extends Exception {}
function parse_jsobj($str, &$data) {
    $str = trim($str);
    if(strlen($str) < 1) return;

    if($str{0} != '{') {
        throw new JsParserException('The given string is not a JS object');
    }
    $str = substr($str, 1);

    /* While we have data, and it's not the end of this dict (the comma is needed for nested dicts) */
    while(strlen($str) && $str{0} != '}' && $str{0} != ',') {
        /* find the key */
        if($str{0} == "'" || $str{0} == '"') {
            /* quoted key */
            list($str, $key) = parse_jsdata($str, ':');
        } else {
            $match = null;
            /* unquoted key */
            if(!preg_match('/^\s*[a-zA-z_][a-zA-Z_\d]*\s*:/', $str, $match)) {
                throw new JsParserException('Invalid key ("'.$str.'")');
            }
            $key = $match[0];
            $str = substr($str, strlen($key));
            $key = trim(substr($key, 0, -1)); /* discard the ':' */
        }

        list($str, $data[$key]) = parse_jsdata($str, '}');
    }
    "Finshed dict. Str: '$str'\n";
    return substr($str, 1);
}

function comma_or_term_pos($str, $term) {
    $cpos = strpos($str, ',');
    $tpos = strpos($str, $term);
    if($cpos === false && $tpos === false) {
        throw new JsParserException('unterminated dict or array');
    } else if($cpos === false) {
        return $tpos;
    } else if($tpos === false) {
        return $cpos;
    }
    return min($tpos, $cpos);
}

function parse_jsdata($str, $term="}") {
    $str = trim($str);


    if(is_numeric($str{0}."0")) {
        /* a number (int or float) */
        $newpos = comma_or_term_pos($str, $term);
        $num = trim(substr($str, 0, $newpos));
        $str = substr($str, $newpos+1); /* discard num and comma */
        if(!is_numeric($num)) {
            throw new JsParserException('OOPSIE while parsing number: "'.$num.'"');
        }
        return array(trim($str), $num+0);
    } else if($str{0} == '"' || $str{0} == "'") {
        /* string */
        $q = $str{0};
        $offset = 1;
        do {
            $pos = strpos($str, $q, $offset);
            $offset = $pos;
        } while($str{$pos-1} == '\\'); /* find un-escaped quote */
        $data = substr($str, 1, $pos-1);
        $str = substr($str, $pos);
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos+1);
        return array(trim($str), $data);
    } else if($str{0} == '{') {
        /* dict */
        $data = array();
        $str = parse_jsobj($str, $data);
        return array($str, $data);
    } else if($str{0} == '[') {
        /* array */
        $arr = array();
        $str = substr($str, 1);
        while(strlen($str) && $str{0} != $term && $str{0} != ',') {
            $val = null;
            list($str, $val) = parse_jsdata($str, ']');
            $arr[] = $val;
            $str = trim($str);
        }
        $str = trim(substr($str, 1));
        return array($str, $arr);
    } else if(stripos($str, 'true') === 0) {
        /* true */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos+1); /* discard terminator */
        return array(trim($str), true);
    } else if(stripos($str, 'false') === 0) {
        /* false */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos+1); /* discard terminator */
        return array(trim($str), false);
    } else if(stripos($str, 'null') === 0) {
        /* null */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos+1); /* discard terminator */
        return array(trim($str), null);
    } else if(strpos($str, 'undefined') === 0) {
        /* null */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos+1); /* discard terminator */
        return array(trim($str), null);
    } else {
        throw new JsParserException('Cannot figure out how to parse "'.$str.'" (term is '.$term.')');
    }
}

// $urut = "76917161-4";
// $upass= "@Samito007";

// $urut = "77027328-5";
// $upass= "zambrano77";



$urut =$_POST['rut'];
$upass =$_POST['clasii'];

try {
    $ch = curl_init();
    $cookie = "cookies" . $urut . ".txt";
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36';
    $url = 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi';

    $rut = substr(str_replace(".", "", $urut), 0, -2);
    $dv = substr($urut, -1);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'rutcntr' => $urut,
        'clave' => $upass,
        'rut' => $rut,
        'referencia' => 'https://misiir.sii.cl/cgi_misii/siihome.cgi',
        'dv' => $dv,
        '411' => ''
    ]));

    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, -1);
    curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl//AUT2000/InicioAutenticacion/IngresoRutClave.html?https://misiir.sii.cl/cgi_misii/siihome.cgi');

    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code != 200) {
        exit(0);
    }

    curl_setopt($ch, CURLOPT_URL, 'https://misiir.sii.cl/cgi_misii/siihome.cgi');
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



    // curl_setopt($ch, CURLOPT_URL, 'https://misiir.sii.cl/cgi_misii/CViewCarta.cgi');
    // curl_setopt($ch, CURLOPT_HEADER,0);
    // curl_setopt($ch, CURLINFO_HEADER_OUT,1);
    // curl_setopt($ch, CURLOPT_POST , false);
    // curl_setopt($ch, CURLOPT_HTTPGET , true);
    // curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi');
    

    // $result  = curl_exec($ch);
    // $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // if($code != 200){
    //     exit(0);
    // }


    curl_close($ch);


    // 

    // echo $result;


    $dom = new DOMDocument;
    // $dom->loadHTML(utf8_decode($result));


    libxml_use_internal_errors(true);
    $dom->loadHTML(utf8_decode($result), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();


    $scripts = $dom->getElementsByTagName('script');

    $mscript = null;
    foreach ($scripts as $script) {
        $code = $script->nodeValue;
        if (substr_count($code, 'DatosCntrNow') > 0) {
            $mscript = $code;
        }
    }

    preg_match_all('/(?<=\sDatosCntrNow\s=\s)(.*?)(?=;)/', $mscript, $coincidencias);

    $script = $coincidencias[0][0];

    $parsed = json_decode($script, true);

    
    // echo '<pre>';
    // print_r($parsed);
    // echo '</pre>';

    // echo "############################";


    //$RutEmpresa= $parsed['contribuyente']['rut']."-". $parsed['contribuyente']['dv'];
    $razonSocial= strtoupper($parsed['contribuyente']['razonSocial']);
    $eMail= strtolower($parsed['contribuyente']['eMail']);
    $glosaActividad= $parsed['contribuyente']['glosaActividad'];
    // $RutEmpresa= $parsed['contribuyente']['rut'];
    // $RutEmpresa= $parsed['contribuyente']['rut'];

    $calle = strtoupper($parsed['direcciones'][0]['calle']);
    $ciudad = strtoupper($parsed['direcciones'][0]['ciudad']);
    
    if($ciudad==""){
        $ciudad = strtoupper($parsed['direcciones'][0]['comunaDescripcion']);
    }

    // $rRepresentante = strtoupper($parsed['direcciones'][0]['rutPropietario'])."-".$parsed['direcciones'][0]['dvPropietario'];
    $rRepresentante=$urut;

    if($razonSocial==""){
        $razonSocial = strtoupper($parsed['contribuyente']['nombres']." ".$parsed['contribuyente']['apellidoPaterno']." ".$parsed['contribuyente']['apellidoMaterno']);
    }

    $RazonRepresentante = strtoupper($parsed['contribuyente']['nombres']." ".$parsed['contribuyente']['apellidoPaterno']." ".$parsed['contribuyente']['apellidoMaterno']);
    
    $fechaConstitucion = $parsed['contribuyente']['fechaConstitucion'];
    $fechaConstitucion = date('d-m-Y', strtotime($fechaConstitucion));
    // if($rRepresentante==$urut || $rRepresentante=="-"){
    //     $rRepresentante="SinRut";
    // }
    

    // $regimen = $parsed['atributos'][0]['descAtrCodigo'];
    // $calle = $parsed['direcciones'][0]['calle'];
    // $razonSocial = $parsed['contribuyente']['razonSocial'];
    
    // echo "Régimen: " . $regimen . "\n";
    // echo "Calle: " . $calle . "\n";
    // echo "Razón Social: " . $razonSocial . "\n";


    unlink("cookies".$urut.".txt");

	echo json_encode(
    array(
        "razonSocial" => "$razonSocial",
        "RazonRepresentante" => "$RazonRepresentante",
        "eMail" => "$eMail",
        "calle" => "$calle",
        "ciudad" => "$ciudad",
        "glosaActividad" => "$glosaActividad",
        "rRepresentante" => "$rRepresentante",
        "fechaConstitucion" => "$fechaConstitucion"
        )
    );


    // ,
    // "glosaActividad" => "$glosaActividad",
    // "rRepresentante" => "$rRepresentante"

} catch (\Throwable $th) {

}