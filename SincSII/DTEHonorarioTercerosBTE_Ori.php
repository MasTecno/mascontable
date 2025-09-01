<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

    // * Ver si interfiere con otras partes del codigo
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
            $cookie = "cookiesTercBTE".$urut.".txt";
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
            }

            error_log("SII Response: " . substr($result, 0, 1000));


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

            //* El length es 4 (boletas, emisor, receptor, honorarios), todo asociado a una boleta
            //* Se requieren mas registros para ordenar segun boleta (boleta_1, boleta2, etc)
            for ($i = 2; $i < $dataTable->length - 1; $i++) {
                $element = $dataTable[$i];
                // print_r($element);
                
                $informe[] = [
                    // "boleta" => [
                        "n_boleta" => trim($element->getElementsByTagName("td")[1]->nodeValue),
                        "estado_boleta" => trim($element->getElementsByTagName("td")[2]->nodeValue),
                        "fecha_boleta" => trim($element->getElementsByTagName("td")[3]->nodeValue),
                    // ],

                    // "emisor" => [
                        "rut_emisor" => trim($element->getElementsByTagName("td")[4]->nodeValue),
                        "nombre_emisor" => trim($element->getElementsByTagName("td")[5]->nodeValue),
                        "fecha_emisor" => trim($element->getElementsByTagName("td")[6]->nodeValue),
                    // ],

                    // "receptor" => [
                        "rut_receptor" => trim($element->getElementsByTagName("td")[7]->nodeValue),
                        "nombre_receptor" => trim($element->getElementsByTagName("td")[8]->nodeValue),
                    // ],

                    // "honorarios" => [
                        "brutos" => trim($element->getElementsByTagName("td")[9]->nodeValue),
                        "retenidos" => trim($element->getElementsByTagName("td")[10]->nodeValue),
                        "pagado" => trim($element->getElementsByTagName("td")[11]->nodeValue),
                    // ]
                ];
            }


            // if(count($informe) === 0) {
            //     echo "No hay boletas";
            //     exit;
            // }


            return $informe;
            
            
        }catch (Exception $e) {
            error_log("Error al obtener registros: " . $e->getMessage());
            exit;
        }
    }
    
    //* Credenciales de login
    // $userrut = "76.036.407-K";
    // $userpass = "idds2023";

    // $mesper = "01";
    // $anoper = "2025";
    // $Periodo = $mesper."-".$anoper;

    $mesper = substr($Periodo,0,2);
    $anoper = substr($Periodo,3,4);


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
    foreach($boletas as $boleta){

        $numHon = $boleta["n_boleta"];
        $fecHon = date("Y-m-d", strtotime($boleta["fecha_boleta"]));
        $rutHon = $boleta["rut_receptor"];
        


        $totHon = $boleta["brutos"];
        $totHon=str_replace(".","",$totHon);
        $retHon = $boleta["retenidos"];
        $retHon=str_replace(".","",$retHon);
        $liqHon = $boleta["pagado"]; 
        $liqHon=str_replace(".","",$liqHon);

        $estHono=$boleta["estado_boleta"];

        $nomHon = $boleta["nombre_receptor"];

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

        if($estHono=="VIG"){  
            $SqlCP="SELECT * FROM CTHonorarios WHERE rut='$rutHon' AND numero='$numHon' AND fecha='$fecHon' AND rutempresa='".$_SESSION['RUTEMPRESA']."'"; 
            $Resul = $mysqli->query($SqlCP);
            $row_cnt1 = $Resul->num_rows;
            if ($row_cnt1==0) {
                $sql.= "INSERT INTO CTHonorarios VALUES('','$Periodo','".$_SESSION['RUTEMPRESA']."','$fecHon','$rutHon','$numHon','$xcuentaOriginal','$xccostoOriginal','$totHon','$retHon','$liqHon','T','".date("Y-m-d")."','','A','');";
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

    $cookie = "cookiesTercBTE".$userrut.".txt";
    unlink($cookie);

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



    //$sql = "INSERT INTO CTHonorarios VALUES('','','','$fecHon','$rutHon','$numHon','','','$totHon','$retHon','$liqHon','T','".date("Y-m-d")."','','A','');";

    // echo $sql;

