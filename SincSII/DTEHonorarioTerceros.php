<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

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
        
        curl_setopt($ch, CURLOPT_REFERER, 'https://loa.sii.cl/cgi_IMT/TMBCOC_InformeAnualBhe.cgi?rut_arrastre='.$rut.'&dv_arrastre='.$dv.'&cbanoinformeanual='.$aper);


        $result  = curl_exec($ch);
        // file_put_contents('debug_result.html', $result);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code != 200){
            exit(0);
        }
        // else{
        //     echo "Estoy en la url del informe mensual para el mes: " . $mper . "<br><br>";
        // }

        // Debug log the response
        error_log("SII Response: " . substr($result, 0, 1000)); // Log first 1000 chars

        $disabled = false;
        $pagina = 1;
        $informes = array();
        
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

            preg_match_all('/arr_informe_mensual\[.*/', $mscript, $coincidencias);

            $informes = [];
                foreach ($coincidencias[0] as $coincidencia){
                    $var = explode("=", $coincidencia);
                    $key = trim(preg_replace("/arr_informe_mensual\[\'|\'\]/", "", $var[0]));

                    $value = trim(preg_replace("/[(formatMiles\()(,'.')]/", "", $var[1]));
                    $value = trim(preg_replace("/;/", "", $value));
                    $informes[$key] = $value;
                }

                // if(count($informes) === 0){
                //     echo "No se encontraron informes en la respuesta del SII.";
                //     exit;
                // }

                $boletas = [];

                foreach ($informes as $claveCompleta => $valor) {
                    if (preg_match('/^(.*)_(\d+)$/', $claveCompleta, $matches)) {
                        $clave = $matches[1];
                        $numero = (int)$matches[2];

                        $boletas[$numero][$clave] = $valor;
                    }
                }

                return $boletas;
            
        }catch (Exception $e) {
            error_log("Error al obtener registros: " . $e->getMessage());
            exit;
        }
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

//* Base de datos
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Estado='A'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $ValRSII=$registro['RutSii']; 
        $ValCSII=descript($registro['PasSii']); 
    }

    $userrut=$ValRSII;
    $userpass=$ValCSII;

    $SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    $resultados = $mysqli->query($SQL);
    $cwe=$resultados->num_rows;
    if($cwe==0){
        $SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa=''";
    }

    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        if ($registro['tipo']=="R") { ///honorarios recividos
            $xcuenta=$registro['L1'];   
        }
    }

    $boletas = login($userrut, $userpass, $mesper, $anoper);

    //* Revisar aca
    // echo '<pre>';
    // print_r($boletas);
    // echo '</pre>';
    $ConDoc=0;

    foreach($boletas as $b) {
        // echo "<pre>";
        // print_r($b);
        // echo "</pre>";

        // var_dump($b["fecha_boleta"]);

        $fechaLimpia = str_replace('"', '', $b["fecha_boleta"]);
        $fecha = DateTime::createFromFormat("d/m/Y", $fechaLimpia);
        if ($fecha) $fecHon = $fecha->format("Y-m-d");

        $rut = $b["rutreceptor"];
        $dv = $b["dvreceptor"];

        $nomHon = $b["nombrereceptor"];
        $nomHon=str_replace("\"","",$nomHon);


        $rutHon = "{$rut}-{$dv}";
        $rutHon = str_replace('"', '', $rutHon);

        $numHon = $b["nroboleta"];
        $numHon=str_replace("\"","",$numHon);

        $totalHon = $b["totalhonorarios"];
        $totalHon=str_replace("\"","",$totalHon);
        $retHon = $b["retencion_receptor"];
        $retHon=str_replace("\"","",$retHon); 
        $liqHon = $b["honorariosliquidos"];
        $liqHon=str_replace("\"","",$liqHon);
        $estado = $b["estado"];
        $estado=str_replace("\"","",$estado);


        $xccostoOriginal=0;
        $xcuentaOriginal="";

        $SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$rutHon' AND tipo='H'";
        $Resul = $mysqli->query($SqlCP);
        $row_cnt = $Resul->num_rows;
        if ($row_cnt>0) {
            $SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$rutHon' AND tipo='H'";
            $Resul = $mysqli->query($SqlCP);
            while ($Reg = $Resul->fetch_assoc()) {
                if ($Reg['cuenta']!=""){
                    $xcuentaOriginal=$Reg['cuenta'];
                }
                if ($Reg['ccosto']!="0") {
                    $xccostoOriginal=$Reg['ccosto'];
                }
            }
        }
        
        if($xcuentaOriginal==""){
            $xcuentaOriginal=$xcuenta;
        }

        if($estado=="N"){  
            $SqlCP="SELECT * FROM CTHonorarios WHERE rut='$rutHon' AND numero='$numHon' AND fecha='$fecHon' AND rutempresa='".$_SESSION['RUTEMPRESA']."'"; 
            $Resul = $mysqli->query($SqlCP);
            $row_cnt1 = $Resul->num_rows;
            if ($row_cnt1==0) {
                $sql.= "INSERT INTO CTHonorarios VALUES('','$Periodo','".$_SESSION['RUTEMPRESA']."','$fecHon','$rutHon','$numHon','$xcuentaOriginal','$xccostoOriginal','$totalHon','$retHon','$liqHon','T','".date("Y-m-d")."','','A','');";
                $ConDoc++;
            }

            $SqlCP="SELECT * FROM CTCliPro WHERE rut='$rutHon' AND tipo='P'";
            $Resul = $mysqli->query($SqlCP);
            $row_cnt = $Resul->num_rows;
            if ($row_cnt==0) {				
                $SqlRCP="INSERT INTO CTCliPro (id, rut, razonsocial, tipo, estado) VALUES ('','$rutHon','".utf8_decode(strtoupper($nomHon))."','P','A');";
                $mysqli->query($SqlRCP);
            }
        }
    }

    unlink("cookiesTerc".$userrut.".txt");

    if($sql!=""){
        $mysqli->multi_query($sql);
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

    echo json_encode(
        array("dato1" => "$xDato1", 
        "dato2" => "$xDato2",
        "dato3" => "$xDato3",
        "dato4" => "$xDato4"
        )
    );
