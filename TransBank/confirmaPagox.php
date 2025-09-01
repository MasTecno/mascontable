<?php
session_start();

// print_r($_SESSION['Respuesta']);

// echo $_SESSION['KeyTransbank'];


// echo $_SESSION['SumaMonto'];


$response=$_SESSION['Respuesta'];

$vci = $response->vci;                     // "TSY"
$amount = $response->amount;               // 178500
$status = $response->status;               // "AUTHORIZED"
$buyOrder = $response->buy_order;          // "60890101829379177465"
$sessionId = $response->session_id;        // "1119595809"
$cardNumber = $response->card_detail->card_number;  // "7763"
$accountingDate = $response->accounting_date;       // "1227"
$transactionDate = $response->transaction_date;     // "2024-12-27T20:36:11.613Z"
$authorizationCode = $response->authorization_code; // "1415"
$paymentTypeCode = $response->payment_type_code;    // "VD"
$responseCode = $response->response_code;           // 0
$installmentsNumber = $response->installments_number; // 0

include '../js/funciones.php';
include '../conexion/conexionmysqli.php';
$mysqli = ConCobranza();

$SumaMonto=0;
$SQL="SELECT sum(MontoDoc) as xMontoDoc FROM TransBankMovi WHERE KeyTransbank='$buyOrder'";
$resultados = $mysqli->query($SQL);
while ($registro = $resultados->fetch_assoc()) {
    $SumaMonto=$registro['xMontoDoc'];
}
// echo $buyOrder;
// echo "<br>";
// echo $SumaMonto;
// echo "<br>";
// echo $amount;
// exit;
if($SumaMonto==$amount){

    $nOpera="TBK_".$buyOrder;
    $NomServer="";
    $SQL="SELECT * FROM TransBankMovi WHERE KeyTransbank='$buyOrder' AND Estado='P'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $NomServer=$registro["NombreServer"];

        $XTipo="";
        if ($registro["TipoDoc"]=="FacExe") {
            $XTipo="34";
        }
        if ($registro["TipoDoc"]=="FacAfe") {
            $XTipo="33";
        }
        if ($registro["TipoDoc"]=="NotCre") {
            $XTipo="61";
        }
        if ($registro["TipoDoc"]=="BolEle") {
            $XTipo="39";
        }

        $SQL1="SELECT * FROM Facturas WHERE Folio='".$registro["NumeroDoc"]."' AND IdDocumento='$XTipo' AND Total='".$registro["MontoDoc"]."'";
        $resultados1 = $mysqli->query($SQL1);
        while ($registro1 = $resultados1->fetch_assoc()) {
            $IdFact=$registro1["Id"];
            $MonFac=$registro1["Total"];
            $NFactura=$registro1["Folio"];
            $RFactura=$registro1["Rut"];
        }

        $mysqli->query("INSERT INTO Transferencias VALUES('','".date('Y-m-d')."','$nOpera','$MonFac','TRANSBANK','$sessionId','$RFactura','A','".date('Y-m-d')."','".date("H:i:s")."');");

        $SQL1="SELECT max(Id) as FId FROM Transferencias WHERE Id>0";
        $resultados1 = $mysqli->query($SQL1);
        while ($registro1 = $resultados1->fetch_assoc()) {
            $IdTrans=$registro1["FId"];
        }

        $mysqli->query("INSERT INTO FactTrans VALUES('','$IdFact','$NFactura','$MonFac','$IdTrans','$nOpera','$MonFac','".date('Y-m-d')."');");
    }

    if($NomServer!=""){
        $SQL="SELECT * FROM Bloqueos WHERE Nombre='$NomServer' AND Estado='A'";
        $resultados = $mysqli->query($SQL);
        $SwBloqueo = $resultados->num_rows;
        if ($SwBloqueo>0) {
            $mysqli->query("UPDATE Bloqueos SET Estado='X' WHERE Nombre='$NomServer' AND Estado='A'");
        }
    }

    $mysqli->query("UPDATE TransBankMovi SET Estado='A' WHERE KeyTransbank='$buyOrder' AND Estado='P'");

    $SQL="SELECT * FROM TransBankRespuesta WHERE sessionId='$sessionId' AND vci='$vci' AND buyOrder='$buyOrder'";
    $resultados = $mysqli->query($SQL);
    $row_cnt = $resultados->num_rows;
    if ($row_cnt==0) {
        $mysqli->query("INSERT INTO TransBankRespuesta VALUES('','$NomServer','".date('Y-m-d')."','".date("H:i:s")."','$vci','$amount','$status','$buyOrder','$sessionId','$cardNumber','$accountingDate','$transactionDate','$authorizationCode','$paymentTypeCode','$responseCode','$installmentsNumber')");
    }

    $mysqli->close();
    $Sw=1;
}else{
    $mysqli->query("UPDATE TransBankMovi SET Estado='R' WHERE KeyTransbank='$buyOrder' AND Estado='P'");
    echo "Error en monto";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago - MasTecno</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/StConta.css">

    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .payment-card {
            max-width: 500px;
            margin: 2rem auto;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        .payment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.47);
        }
        .logo-container {
            width: 200px;
            margin: 0 auto;
            padding: 1.5rem 0;
        }
        .success-icon {
            font-size: 4rem;
            color: #008245;
            animation: pulse 2s infinite;
        }
        .status-badge {
            background: linear-gradient(45deg,rgb(0, 161, 3),rgb(0, 255, 42));
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .transaction-row {
            padding: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        .transaction-row:hover {
            background: rgba(0, 0, 0, 0.03);
        }
        .transaction-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #ff0000;
        }
        .btn-mastecno {
            background: linear-gradient(45deg, #ff0000, #ff3333);
            border: none;
            color: white;
            padding: 0.8rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-mastecno:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
            color: white;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
            if($Sw==1 || $_POST['Sw']==1){
        ?>
            <div class="payment-card p-4">
                <!-- Logo -->
                <div class="logo-container text-center">
                    <img src="../images/Mastecno450x100.png" alt="MasTecno Logo" width="200px" class="img-fluid">
                </div>
                
                <!-- Success Icon -->
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle success-icon"></i>
                </div>
                
                <!-- Confirmation Title -->
                <h2 class="text-center mb-4 fw-bold">¡Pago Confirmado!</h2>
                
                <!-- Transaction Details -->
                <div class="transaction-details p-4">
                    <div class="transaction-row">
                        <i class="fas fa-hashtag transaction-icon"></i>
                        <div class="d-flex justify-content-between w-100">
                            <span class="text-secondary">ID de Transacción:</span>
                            <span class="fw-bold"><?php echo $buyOrder; ?></span>
                        </div>
                    </div>
                    
                    <div class="transaction-row">
                        <i class="fas fa-dollar-sign transaction-icon"></i>
                        <div class="d-flex justify-content-between w-100">
                            <span class="text-secondary">Monto:</span>
                            <span class="fw-bold">$<?php echo $amount; ?></span>
                        </div>
                    </div>
                    
                    <div class="transaction-row">
                        <i class="fas fa-calendar transaction-icon"></i>
                        <div class="d-flex justify-content-between w-100">
                            <span class="text-secondary">Fecha:</span>
                            <span class="fw-bold"><?php $date = new DateTime($transactionDate); echo $date->format('d-m-Y');?></span>
                        </div>
                    </div>
                    
                    <div class="transaction-row">
                        <i class="fas fa-shield-alt transaction-icon"></i>
                        <div class="d-flex justify-content-between w-100">
                            <span class="text-secondary">Estado:</span>
                            <span class="status-badge">
                                <i class="fas fa-check"></i>
                                Completado
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="text-center mt-4">
                    <button onclick="window.location.href='https://mascontable.maserp.cl'" class="btn btn-mastecno">
                        <i class="fas fa-arrow-circle-left me-2"></i>
                        Volver MasContable
                    </button>
                </div>
                <div class="text-center mt-4">
                    <button onclick="window.location.href='https://masremu.maserp.cl'" class="btn btn-mastecno">
                        <i class="fas fa-arrow-circle-left me-2"></i>
                        Volver MasRemu
                    </button>
                </div>

            </div>
        <?php
            }
        ?>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    </div>

</body>
</html>