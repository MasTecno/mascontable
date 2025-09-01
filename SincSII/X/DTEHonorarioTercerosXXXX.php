<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    // Aumentar el tiempo máximo de ejecución
    set_time_limit(900);
    $start_time = time();
    $max_execution_time = 870; // Para dar margen al PHP-FPM (900s típico)

    function checkTimeout($start_time, $max_execution_time) {
        if (time() - $start_time > $max_execution_time) {
            global $userrut;
            if (file_exists("cookiesTerc".$userrut.".txt")) {
                unlink("cookiesTerc".$userrut.".txt");
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

    if ($_POST['messelect'] <= 9) {
        $xmesselect = "0" . $_POST['messelect'];
    } else {
        $xmesselect = $_POST['messelect'];
    }

    $Periodo = $xmesselect . "-" . $_POST['anoselect'];

    function login($urut, $upass, $mper, $aper) {
        global $start_time, $max_execution_time;

        checkTimeout($start_time, $max_execution_time);

        $ch = curl_init();
        $cookie = "cookiesTerc" . $urut . ".txt";
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36';
        $url = 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi';

        $rut = substr(str_replace(".", "", $urut), 0, -2);
        $dv = substr($urut, -1);

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'rutcntr' => $urut,
                'clave' => $upass,
                'rut' => $rut,
                'referencia' => 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1',
                'dv' => $dv
            ]),
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_COOKIE => $cookie,
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_COOKIEFILE => $cookie,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_REFERER => 'https://zeusr.sii.cl/AUT2000/InicioAutenticacion/IngresoRutClave.html?https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1'
        ));

        $result = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) exit(0);

        // Acceder a la página de consulta
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1',
            CURLOPT_POST => false,
            CURLOPT_HTTPGET => true
        ));
        $result = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) exit(0);

        $pagina = 1;
        $paginaMax = 20;
        $disabled = false;
        $informes = [];

        curl_setopt($ch, CURLOPT_URL, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons2');

        do {
            checkTimeout($start_time, $max_execution_time);
            $inicio_pagina = microtime(true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'CNTR' => 1,
                'PAGINA' => $pagina,
                'AUTEN' => 'RUTCLAVE',
                'TIPO' => 'mensual',
                'MESM' => $mper,
                'ANOM' => $aper
            ]));

            $result = curl_exec($ch);
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
                error_log("HTTP Error en página $pagina");
                break;
            }

            $dom = new DOMDocument('1.0', 'UTF-8');
            libxml_use_internal_errors(true);
            $dom->loadHTML(utf8_encode($result));
            libxml_clear_errors();
            $xpath = new DOMXpath($dom);

            $disabled = $xpath->query('//input[@type="button" and @name="NEXT"]/@disabled')->length > 0;
            $tables = $dom->getElementsByTagName('table');
            if ($tables->length < 4) continue;

            $dataTable = $tables[3]->getElementsByTagName('tr');
            for ($i = 2; $i < $dataTable->length - 1; $i++) {
                $element = $dataTable[$i];
                $informes[] = [
                    'boleta' => [
                        'N' => trim($element->childNodes[2]->nodeValue)
                    ]
                ];
            }

            $duracion = microtime(true) - $inicio_pagina;
            error_log("Página $pagina procesada en {$duracion} segundos");
            $pagina++;
            sleep(1); // evitar sobrecarga al SII
        } while (!$disabled && $pagina <= $paginaMax);

        curl_close($ch);
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

    // echo $userrut;
    // echo "<br>";
    // echo $userpass;
    // echo "<br>";
    // echo $mesper;
    // echo "<br>";
    // echo $anoper;   
    // echo "<br>";
    // echo $Periodo;
    // echo "<br>";
    // exit;

    $boletas = login($userrut, $userpass, $mesper, $anoper);

    echo '<pre>';
    print_r($boletas);
    echo '</pre>';

    // boletas
    $ConDoc=0;
    // $xDato1 ="xxx".$boletas;
    foreach($boletas['boletas'] as $boleta){
        checkTimeout($start_time, $max_execution_time);
        
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

    unlink("cookiesTerc".$userrut.".txt");

    echo json_encode(
        array("dato1" => "$xDato1", 
        "dato2" => "$xDato2",
        "dato3" => "$xDato3",
        "dato4" => "$xDato4"
        )
    );