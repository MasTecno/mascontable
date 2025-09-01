<?php

class ChipaxAuth {
    private $baseUrl = 'https://api.chipax.com/v2';
    
    public function login($appId, $secretKey) {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . '/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'app_id' => $appId,
                'secret_key' => $secretKey
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        if ($err) {
            throw new Exception("Error en login: " . $err);
        }

        return json_decode($response, true);
    }
}



// {
//     "app_id": "6601637a-0b7c-4fc8-a40a-6b9fac1f19d4",
//     "secret_key": "71853e4a-8478-4db8-9473-a501bd0031ca"
//   }
// Uso
// try {
//     $auth = new ChipaxAuth();
//     $result = $auth->login('6601637a-0b7c-4fc8-a40a-6b9fac1f19d4', '71853e4a-8478-4db8-9473-a501bd0031ca');
//     print_r($result);
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }


class ChipaxClient {
    private $baseUrl = 'https://api.chipax.com/v2';
    private $token;

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * Valida el formato del RUT chileno (11111111-1)
     */
    private function validarFormatoRut($rut) {
        return preg_match('/^\d{7,8}-[0-9kK]$/', $rut);
    }

    /**
     * Obtiene los clientes, opcionalmente filtrado por RUT
     */
    public function getClientes($rut = null) {
        if ($rut !== null && !$this->validarFormatoRut($rut)) {
            throw new Exception("Formato de RUT inválido. Debe ser como: 11111111-1");
        }

        $curl = curl_init();
        
        $url = $this->baseUrl . '/clientes';
        if ($rut) {
            // $url .= '?' . http_build_query(['rut' => $rut]);
            $url .= '?rut='."$rut";
        }

        // echo $url.$this->token;

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);



        // //* Personalizar el comportamiento del curl (para realizar la peticion http)
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => $url, //* Url que realizara la peticion
        //     CURLOPT_RETURNTRANSFER => true, //* Devolver la respuesta como string
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10, //* Cantidad de redirecciones http
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true, //* Seguimiento de redirecciones
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, //* Version de http a utilizar
        //     CURLOPT_CUSTOMREQUEST => $method,
        //     CURLOPT_POST => true,
        //     CURLOPT_POSTFIELDS => $data, //* Metodo http
            
        //     //* Las keys se enviaran junto a la solicitud
        //     CURLOPT_HTTPHEADER => array(
        //         'Tbk-Api-Key-Id: '.$TbkApiKeyId.'',
        //         'Tbk-Api-Key-Secret: '.$TbkApiKeySecret.'',
        //         'Content-Type: application/json'
        //     ),
        // ));



        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        
        curl_close($curl);

        if ($err) {
            throw new Exception("Error en la consulta: " . $err);
        }

        if ($httpCode === 401) {
            throw new Exception("Error de autenticación. Verifique su token.");
        }

        return json_decode($response, true);
    }
}


try {
    // Primero necesitas obtener el token mediante el login
    $auth = new ChipaxAuth();
    $result = $auth->login('6601637a-0b7c-4fc8-a40a-6b9fac1f19d4', '71853e4a-8478-4db8-9473-a501bd0031ca'); // El Morado
    // $result = $auth->login('65e8ba9a-3300-40f3-b0a5-0af2ac1f1a5c', '0450b10d-8179-415a-8755-c2e1cb64b931'); //servicios mineros
    print_r($result);
    echo "<br>";
    echo $token = $result['token']; // Ajusta según la respuesta real del login
    echo "<br>";

    // Luego puedes consultar clientes
    $chipax = new ChipaxClient($token);

    // Consultar todos los clientes
    // $todosLosClientes = $chipax->getClientes();
    // print_r($todosLosClientes);
    echo "<br>";
    echo "<br>";

    // Consultar un cliente específico por RUT
    $clientePorRut = $chipax->getClientes('76969359-9');
    print_r($clientePorRut);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
