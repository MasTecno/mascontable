<?php

$apikes="qeC053uPxjZiMswNbL7kR7xsmjWNSyzq";


// $headers = ['Authorization' => base64_encode('X:' . $apikes)];

// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://libredte.cl/api/dte/dte_emitidos/buscar/76917161',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS =>'{
// 	"receptor": null,
// 	"razon_social": null,
// 	"dte": null,
// 	"folio": null,
// 	"fecha": null,
// 	"total": null,
// 	"usuario": null,
// 	"fecha_desde": null,
// 	"fecha_hasta": null,
// 	"total_desde": null,
// 	"total_hasta": null,
// 	"sucursal_sii": null,
// 	"periodo": null,
// 	"receptor_evento": null,
// 	"cedido": null,
// 	"xml": {
//         "Detalle/NmbItem": "abono"
//     }
// }',
//   CURLOPT_HTTPHEADER => array(
//     'Accept: application/json',
//     'Content-Type: application/json',
//     'Authorization: Basic '. base64_encode('X:' . $apikes)
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://libredte.cl/api/dte/dte_emitidos/info/39/2952/76917161?getXML=0&getDetalle=0&getDatosDte=0&getTed=0&getResolucion=0&getEmailEnviados=0&getLinks=0&getReceptor=0&getSucursal=0&getUsuario=0',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json',
    'Authorization: Basic '. base64_encode('X:' . $apikes)
  ),
));

$response = curl_exec($curl);

curl_close($curl);

echo "<pre>";
echo print_r($response);
echo "</pre>";