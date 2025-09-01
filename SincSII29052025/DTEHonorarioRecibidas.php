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


    $cookie_file = "cookies".$userrut.".txt";
    if (file_exists($cookie_file)) {
        unlink($cookie_file);
    }

    function login($urut,$upass,$mper,$aper) {
        try {
            $ch = curl_init();
            $cookie = "cookies".$urut.".txt";
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
                'rut'      => $rut,
                'referencia' => 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContribRec.cgi',
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
            curl_setopt($ch, CURLOPT_REFERER, 'https://zeusr.sii.cl/AUT2000/InicioAutenticacion/IngresoRutClave.html?https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContribRec.cgi?');

            $result  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($code != 200){
                exit(0);
            }

            curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContribRec.cgi');
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
            
            curl_setopt($ch, CURLOPT_URL, 'https://loa.sii.cl/cgi_IMT/TMBCOC_InformeMensualBheRec.cgi');
            curl_setopt($ch, CURLOPT_HEADER,0);
            curl_setopt($ch, CURLINFO_HEADER_OUT,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'rut_arrastre'      => $rut,
                'dv_arrastre'         => $dv,
                'pagina_solicitada' => 0,
                'cbmesinformemensual'    => $mper,
                'cbanoinformemensual' => $aper
            ]));
            curl_setopt($ch, CURLOPT_REFERER, 'https://loa.sii.cl/cgi_IMT/TMBCOC_MenuConsultasContribRec.cgi');
            
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

            // echo '<pre>';
            // print_r($informes);
            // echo '</pre>';
            // exit;

            return $informes;

        } catch (\Throwable $th) {
            // return "Error al sincronizar documentos: <br>" . $th->getMessage(); //$th->__toString(); //
            //echo "Error al sincronizar documentos: <br>" . $th->getMessage(); //$th->__toString(); //
            ///mensaje de sistema
            // echo "Error al sincronizar documentos: <br>" . $th->getMessage();
            $rr="No hemos podido rescatar la información de los documentos, pueden ser varias Causas:<br>
            1. Rut y/o Clave Incorrectas.<br>
            2. No presentan documentos en el periodo.<br>
            3. El servicio de sincronización esta temporalmente desactivado ya sea por parte de MasTecno o Sii.<br>

            ";
            return $rr;
        }
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

    $SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        if ($registro['tipo']==$sdoc) { ///honorarios recividos
            $xcuenta=$registro['L1'];
        }
    }

    if($xcuenta==""){
        $SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa=''";
        $resultados = $mysqli->query($SQL);
    
        while ($registro = $resultados->fetch_assoc()) {
            if ($registro['tipo']==$sdoc) { ///honorarios recividos
                $xcuenta=$registro['L1'];
            }
        }
    }

    $userrut=$ValRSII;
    $userpass=$ValCSII;
    $loginArr = [];

    $loginArr = login($userrut, $userpass, $mesper, $anoper);

    $i=1;
    $Sql="";
    $ConDoc=0;
    $ConDoc2=0;
    foreach ($loginArr as $clave => $valor) {

        $valor=str_replace("\"","",$valor);

        if($i==1){
            $NumHon=$valor;
        }
        if($i==2){
            $RutHon=$valor;
        }
        if($i==3){
            $RutHon=$RutHon."-".$valor;
        }
        if($i==4){
            $NomHon=$valor;
        }
        if($i==5){
            $FecHon=$valor;
            $dia = substr($FecHon,0,2);
            $mes = substr($FecHon,3,2);
            $ano = substr($FecHon,6,4);
            $FecHon=$ano."-".$mes."-".$dia;
        }

        if($i==6){
            $TotHon=$valor;
        }
        if($i==7){
            $LiqHon=$valor;
        }
        if($i==10){
            $RetHon=$valor;
        }

        if($i==11){
            $VigHon=$valor;
        }

        if($i==13){
            // echo $VigHon;
            if($VigHon=="N"){
                $SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$RutHon' AND tipo='H'";
                $Resul = $mysqli->query($SqlCP);
                $row_cnt = $Resul->num_rows;
                if ($row_cnt>0) {
                    $SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$RutHon' AND tipo='H'";
                    $Resul = $mysqli->query($SqlCP);
                    while ($Reg = $Resul->fetch_assoc()) {
                        if ($Reg['cuenta']!=0) {
                            $xcuenta=$Reg['cuenta'];
                        }
                    }
                }

                $SqlCP="SELECT * FROM CTHonorarios WHERE rut='$RutHon' AND numero='$NumHon' AND fecha='$FecHon' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
                $Resul = $mysqli->query($SqlCP);
                $row_cnt1 = $Resul->num_rows;
                if ($row_cnt1==0) {
                    $Sql=$Sql."INSERT INTO CTHonorarios VALUES('','$Periodo','".$_SESSION['RUTEMPRESA']."','$FecHon','$RutHon','$NumHon','$xcuenta','','$TotHon','$RetHon','$LiqHon','R','".date("Y-m-d")."','','A','');";
                    $ConDoc++;
                }

                $SqlCP="SELECT * FROM CTCliPro WHERE rut='$RutHon' AND tipo='P'";
                $Resul = $mysqli->query($SqlCP);
                $row_cnt = $Resul->num_rows;
                if ($row_cnt==0) {				
                    $SqlRCP="INSERT INTO CTCliPro (id, rut, razonsocial, tipo, estado) VALUES ('','$RutHon','".utf8_decode(strtoupper($NomHon))."','P','A');";
                    $mysqli->query($SqlRCP);
                }
                
                $ConDoc2++;
            }
            $i=0;
        }
        $i++;
    }

    if($Sql!=""){
        $mysqli->multi_query($Sql);
        $mysqli->close();
        $xDato1 = "BOLETA DE HONORARIO: $ConDoc2";
    }else{
        if($row_cnt1>0){
            $xDato1 = "BOLETA DE HONORARIO: ".$ConDoc2;
        }
        if($row_cnt1==0){
            $xDato1 = "NO HAY DOCUMENTOS A SINCRONIZAR";
        }
    }

    $xDato4 = $ConDoc2;
    unlink("cookies".$userrut.".txt");

    echo json_encode(
        array("dato1" => "$xDato1", 
        "dato2" => "$xDato2",
        "dato3" => "$xDato3",
        "dato4" => "$xDato4"
        )
    );
    