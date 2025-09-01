<?php

    //* CONSULTA BTE's EMITIDAS


    set_time_limit(30);
	$start_time = time(); //* Inicio, fecha y hora actual en formato timestamp
    
	$max_execution_time = 30; //* 30 segundos


	function checkTimeout($start_time, $max_execution_time) {
		if (time() - $start_time > $max_execution_time) {
			// Limpiar cookies y cerrar sesión
			global $userrut;
			$cookieFile = "cookiesTerc" . $userrut . ".txt";
            if (file_exists($cookieFile)) {
                unlink($cookieFile);
            } else {
                error_log("Archivo de cookies no encontrado: " . $cookieFile);
            }
			
            // Eliminar cookies del navegador (si están configuradas)
            foreach ($_COOKIE as $key => $value) {
                setcookie($key, '', time() - 3600, '/');
                unset($_COOKIE[$key]);
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


    $Periodo = "01" . "-" . "2025";
    echo "Periodo: " . $Periodo . "<br>";

    //* Recibe rut, contraseña, mes y año
    function login($urut,$upass,$mper,$aper) {
        global $start_time, $max_execution_time;
        
        checkTimeout($start_time, $max_execution_time);

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
                'referencia' => 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1',
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
            curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/AUT2000/InicioAutenticacion/IngresoRutClave.html?https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1');

            $result  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($code != 200){
                exit(0);
            }else{
                echo "Se inicio sesion con el rut: " . $urut . "<br>";
            }

            //* Acceder a esta url despues del login
            curl_setopt($ch, CURLOPT_URL, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1');
            curl_setopt($ch, CURLOPT_HEADER,0);
            curl_setopt($ch, CURLINFO_HEADER_OUT,1);
            curl_setopt($ch, CURLOPT_POST , false);
            // curl_setopt($ch, CURLOPT_HTTPGET , true);
            curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi');

            $result  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($code != 200){
                exit(0);
            }

            // print_r($result);
            $pagina = 1;
            //* Url informe anual
            curl_setopt($ch, CURLOPT_URL, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons2');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER,0);
            curl_setopt($ch, CURLINFO_HEADER_OUT,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'CNTR' => 1,
                'PAGINA' => $pagina,
                'AUTEN' => 'RUTCLAVE',
                'TIPO' => 'mensual',
                'MESM' => $mper,
                'ANOM' => $aper
            ]));
            curl_setopt($ch, CURLOPT_REFERER, 'https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons?1');

            $result  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // print_r($result);

            if($code != 200){
                exit(0);
            }else{
                echo "Estoy en la url del informe anual del: {$mper}-{$aper}";
            }

            error_log("SII Response: " . substr($result, 0, 1000)); // Log first 1000 chars


            libxml_use_internal_errors(true);
            $dom = new DOMDocument("1.0", "UTF-8");

            //* Convertir el resultado a UTF-8
            $htmlUtf8 = "<?xml encoding='UTF-8'>" . $result;
            
            //* Evitar etiquetas html innecesarias
            $dom->loadHTML($htmlUtf8, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            
            $informe = [];
            $resumen = [];
            $boletas = [];
            $totales = [];

            $tables = $dom->getElementsByTagName("table");
            

            $dataTable2 = $tables[2]->getElementsByTagName('tr');
            foreach ($dataTable2 as $row) {
                $cells = $row->getElementsByTagName('td');

                if ($cells->length > 0) {
                    preg_match('/\d+/', $cells[0]->nodeValue, $m1);
                    preg_match('/\d+/', $cells[1]->nodeValue, $m2);
                    preg_match('/\d+/', $cells[2]->nodeValue, $m3);
                    $resumen = [
                        "total_boletas" => trim($m1[0]),
                        "total_boletas_vigentes" => trim($m2[0]),
                        "total_boletas_anuladas" => trim($m3[0]),
                    ];
                }
            }

            $dataTable = $tables[3]->getElementsByTagName("tr");
            $tercerTr = $dataTable[2];


            // $lastIndex = $dataTable[3]->length - 1;

            // if ($lastIndex >= 0) {
            //     $lastRow = $dataTable[$lastIndex];
            //     $tds = $lastRow->getElementsByTagName('td');

            //     if ($tds->length >= 3) {
            //         $totales = [
            //             "total_bruto" => trim($tds[1]->nodeValue),
            //             "total_retenidos" => trim($tds[2]->nodeValue),
            //             "total_pagado" => trim($tds[3]->nodeValue),
            //         ];
            //     }
            // }

            //* El length es 4 (boletas, emisor, receptor, honorarios), todo asociado a una boleta
            //* Se requieren mas registros para ordenar segun boleta (boleta_1, boleta2, etc)
            for ($i = 2; $i < $dataTable->length - 1; $i++) {
                $element = $dataTable[$i];
                // print_r($element);
                $informe[] = [
                    // "boleta" => [
                        "n" => trim($element->getElementsByTagName("td")[1]->nodeValue),
                        "estado_b" => trim($element->getElementsByTagName("td")[2]->nodeValue),
                        "fecha_b" => trim($element->getElementsByTagName("td")[3]->nodeValue),
                    // ],

                    // "emisor" => [
                        "rut_e" => trim($element->getElementsByTagName("td")[4]->nodeValue),
                        "nombre_e" => trim($element->getElementsByTagName("td")[5]->nodeValue),
                        "fecha_e" => trim($element->getElementsByTagName("td")[6]->nodeValue),
                    // ],

                    // "receptor" => [
                        "rut_r" => trim($element->getElementsByTagName("td")[7]->nodeValue),
                        "nombre_r" => trim($element->getElementsByTagName("td")[8]->nodeValue),
                    // ],

                    // "honorarios" => [
                        "brutos" => trim($element->getElementsByTagName("td")[9]->nodeValue),
                        "retenidos" => trim($element->getElementsByTagName("td")[10]->nodeValue),
                        "pagado" => trim($element->getElementsByTagName("td")[11]->nodeValue),
                    // ]
                ];
            }


            if(count($informe) === 0) {
                echo "No hay boletas";
                exit;
            }


            echo "<pre>";
            print_r($informe);
            echo "</pre>";
            
            
        }catch (Exception $e) {
            error_log("Error al obtener registros: " . $e->getMessage());
            exit;
        }

       

    }
    
    //* Credenciales de login
    // $userrut="76.188.199-K";
    // $userpass="JALOTA2";

    $userrut="76.036.407-K";
    $userpass="idds2023";

    $mesper="01";
    $anoper="2025";
    $Periodo=$mesper."-".$anoper;

    // $userrut="77.566.467-3";
    // $userpass="SERVI77566";

    $mesper=substr($Periodo,0,2);
    $anoper=substr($Periodo,3,4);

    $boletas = login($userrut, $userpass, $mesper, $anoper);

    //* Revisar aca
    echo '<pre>';
    print_r($boletas);
    echo '</pre>';