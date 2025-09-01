<?php
    session_start();
    $KeyTransbank="";
    $_SESSION['KeyTransbank']="";
    $_SESSION['SumaMonto']="";

    include '../js/funciones.php';
    include '../conexion/conexionmysqli.php';
    


    if (isset($_POST['check_list']) && is_array($_POST['check_list']) && count($_POST['check_list'])>0) {

        function generarCodigo($longitud = 8) {
            $caracteres = '01rrr2345w78SSA340123e4567er789na67ene90sd0012';
            $codigo = '';
            
            $max = strlen($caracteres) - 1;
            
            for ($i = 0; $i < $longitud; $i++) {
                $codigo .= $caracteres[mt_rand(0, $max)];
            }
            return $codigo;
        }

        $KeyTransbank=generarCodigo(20);
        $SumaMonto=0;

        $mysqli = ConCobranza();
        $mysqli->query("DELETE FROM TransBankMovi WHERE NombreServer='".$_POST['NServer']."' AND Estado='P'");

        foreach($_POST['check_list'] as $selected) {
            $datos = explode(",", $selected);
            
            $mysqli->query("INSERT INTO TransBankMovi VALUES('','".$_POST['NServer']."','".date('Y-m-d')."','$datos[0]','$datos[1]','$datos[2]','$datos[3]','$KeyTransbank','P')");
            $SumaMonto=$SumaMonto+$datos[3];
        }

        $_SESSION['KeyTransbank']=$KeyTransbank;
        $_SESSION['SumaMonto']=$SumaMonto;
    }

    function pruebas($data, $method, $type, $endpoint) {
        $TbkApiKeyId = "";
        $TbkApiKeySecret = "";

        $curl = curl_init();

        /////INTREGRACION
        // $TbkApiKeyId = "597055555532"; //* Codigo de comercio Webpay Plus 
        // $TbkApiKeySecret = "579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C"; //* ApiKey
        // $url = "https://webpay3gint.transbank.cl".$endpoint; //* Integracion

        ///PRODUCCION
        //! Descomentar estas lineas y quitar el if de arriba para activar el entorno de producciom
        $mysqli = ConCobranza();
        $sqlin = "SELECT * FROM TransBankAPI WHERE Estado='A'";
        $resultadoin = $mysqli->query($sqlin);
        while ($registro = $resultadoin->fetch_assoc()) {
            $TbkApiKeyId = $registro['ApiKeyId'];
            $TbkApiKeySecret = $registro['ApiKeySecret'];
        }

        $url = "https://webpay3g.transbank.cl".$endpoint; //* Produccion

        if($TbkApiKeyId == "" || $TbkApiKeySecret == ""){
            echo "Error de autentización";
            exit;
        }

        // echo $TbkApiKeyId;
        // echo $TbkApiKeySecret;
        // exit;

        //* Personalizar el comportamiento del curl (para realizar la peticion http)
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url, //* Url que realizara la peticion
            CURLOPT_RETURNTRANSFER => true, //* Devolver la respuesta como string
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10, //* Cantidad de redirecciones http
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true, //* Seguimiento de redirecciones
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, //* Version de http a utilizar
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data, //* Metodo http
            
            //* Las keys se enviaran junto a la solicitud
            CURLOPT_HTTPHEADER => array(
                'Tbk-Api-Key-Id: '.$TbkApiKeyId.'',
                'Tbk-Api-Key-Secret: '.$TbkApiKeySecret.'',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl); //* Ejecutar el curl
    
        curl_close($curl); 
        return json_decode($response);
    }
    
        $baseurl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    
        $action = isset($_GET["action"]) ? $_GET["action"] : 'init';
    
        $message = null;
        $post_array = false;
    
        //* Iniciar la transaccion
        switch ($action) {
            case "init":
                // $message.= 'init';
                $buy_order = $_SESSION['KeyTransbank']; //? Colocar el Id de la reserva ///FAEL20663140*FAEL13301027*FAEL29950*BONOAFEC242
                $session_id = rand();
                $amount =  $_SESSION['SumaMonto'];
                $return_url = $baseurl."?action=getResult&KeyAs=".$buy_order."&KeyRs=".$session_id;
                $type = "sandbox";
                    $data='{
                            "buy_order": "'.$buy_order.'",
                            "session_id": "'.$session_id.'",
                            "amount": '.$amount.',
                            "return_url": "'.$return_url.'"
                            }';
                    $method='POST';
                    $endpoint='/rswebpaytransaction/api/webpay/v1.0/transactions';
                    
                    $response = pruebas($data,$method,$type,$endpoint);
    
                    // print_r($response);
                    // exit;
    
                    $message.= "<pre>";
                    $message.= print_r($response,TRUE);
                    $message.= "</pre>";
    
                    $url_tbk = $response->url;
                    $token = $response->token;
                    $submit = "Realizar Abono";
            break;
        
            //* Resultado de la transaccion
            case "getResult":
                $message.= "<pre>".print_r($_POST,TRUE)."</pre>";
                if (!isset($_POST["token_ws"]))
                    break;

                //* Sanitizar token
                $token = filter_input(INPUT_POST, 'token_ws');

                $request = array(
                    "token" => filter_input(INPUT_POST, 'token_ws')
                );
                
                $data='';
                $method='PUT';
                $type='sandbox';
                $endpoint='/rswebpaytransaction/api/webpay/v1.0/transactions/'.$token;
                
                $response = pruebas($data,$method,$type,$endpoint);
    
                
                // $message.= "<pre>";
                // $message.= print_r($response);

                // print_r($response);
                $_SESSION['Respuesta']=$response;

                // print_r($_SESSION['Respuesta']);
                // exit;
                $monto = $response->amount;
                
                if($response->status === "AUTHORIZED") {
    
                    // $_SESSION['Respuesta']=$response;
                    $pago = 1;
                    header("location: ./confirmaPago.php?action=getResult&KeyAs=".$response->buy_order."&KeyRs=".$response->session_id."&Pago=".$pago."&Monto=".$monto);
                    exit;
                }else {

                    $pago = -1;
                    header("location: ./pagoRechazado.php?action=getResult");
                    exit;
                    // header("location: ../Mensajes/confirma.php?action=getResult&Space=".$_SESSION['NAMESPACEAGENDA']."&KeyAs=".$keyAs."&KeyRs=".$response->buy_order."&Pago=".$pago."&Monto=".$monto);
                }
                // $message.= "</pre>";
                
                $url_tbk = $baseurl."?action=getStatus";
                $submit='Ver Status!';
                
            break;
                
            //* Estado de la transaccion
            case "getStatus":
                
                if (!isset($_POST["token_ws"]))
                    break;
        
                /** Token de la transacción */
                $token = filter_input(INPUT_POST, 'token_ws');
                
                $request = array(
                    "token" => filter_input(INPUT_POST, 'token_ws')
                );
    
                
                $data='';
                $method='GET';
                $type='sandbox';
                $endpoint='/rswebpaytransaction/api/webpay/v1.0/transactions/'.$token;
                
                $response = pruebas($data,$method,$type,$endpoint);
                $message.= "<pre>";
                $message.= print_r($response,TRUE);
                $message.= "</pre>";
    
                
                
                $url_tbk = $baseurl."?monto=$response->amount&action=refund";
                $submit='Refund!';
            break;
                
            //* Rechazo
            case "refund":
                
                if (!isset($_POST["token_ws"])) 
                    break;
                
                /** Token de la transacción */
                $token = filter_input(INPUT_POST, 'token_ws');
                
                
                $request = array(
                    "token" => filter_input(INPUT_POST, 'token_ws')
                );
    
                $abono = $_GET["monto"];
    
                
                $data='{
                        "amount": '.$abono.'
                        }';
                $method='POST';
                $type='sandbox';
                $endpoint='/rswebpaytransaction/api/webpay/v1.0/transactions/'.$token.'/refunds';
                
                $response = pruebas($data,$method,$type,$endpoint);
        
                $message.= "<pre>";
                $message.= print_r($response,TRUE);
                $message.= "</pre>";
                $submit='Crear nueva!';
                $url_tbk = $baseurl;
    
                // https://usb.tuagendapro.com/Mensajes/pago.php?action=refund
            break;
        }  

?>

<div class="col-12" style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
    <form hidden></form>

    <form name="brouterForm" id="brouterForm"  method="POST" action="<?=$url_tbk?>" style="display:block;">
        <input type="hidden" name="token_ws" value="<?=$token?>" />
        <input type="submit" style="visibility: hidden;" class="btn btn-success" value="<?= $submit ?>"/>
    </form>

    <script>
        document.brouterForm.submit();
    </script>
</div>