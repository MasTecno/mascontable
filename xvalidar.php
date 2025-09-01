<?php
    // session_cache_limiter('nocache,private');
    session_cache_limiter('private_no_expire');
    date_default_timezone_set('America/Santiago');
    session_start();

    

    // echo $_GET['destroy'];
    // exit;
    
	if (isset($_GET["destroy"]) && $_GET['destroy'] == 'S'){


        if(!isset($_SESSION['UsuariaSV'])){
            session_destroy();
            header('Location: ../');
            die();    
        }

        include 'js/funciones.php';
        include 'conexion/conexionmysqli.php';
    
        $mysqliX=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $sqlin = "UPDATE CTEmpresas SET user=0 WHERE user='".$_SESSION['XId']."'";
        $resultadoin = $mysqliX->query($sqlin);

		session_destroy();
		header('Location: ../');
		die();
	}

	include 'js/funciones.php';
	include 'conexion/conexionmysqli.php';
    // include 'conexion/secciones.php';

    $mysqli = ConCobranza();

    $server = $_POST['server'];

    // echo $server;
    // exit;
    
    
    // Verificar bloqueos
    $sql = "SELECT * FROM Bloqueos WHERE Nombre = ? AND Estado = 'A'";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $server);
    $stmt->execute();
    $resultados = $stmt->get_result();
    $row_cnt = $resultados->num_rows;
    $stmt->close();
    
    if ($row_cnt > 0) {
        session_destroy();
        $mysqli->close();
        header("Location: index.php?Msj=53");
        exit;
    }

    // Obtener IdServer
    $IdServer = "";
    $sql = "SELECT Id FROM Servidores WHERE Nombre = ? AND Estado = 'A' LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $server);
    $stmt->execute();
    $resultados = $stmt->get_result();
    if ($registro = $resultados->fetch_assoc()) {
        $IdServer = $registro["Id"];
    }
    $stmt->close();
     
    // Obtener IdTecnico
    $IdTecnico = "";
    $IdPlan = "";
    $XValorMaestra = 0;
    $Contrato = "N";
    $sql = "SELECT * FROM Maestra WHERE IdServer = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $IdServer);
    $stmt->execute();
    $resultados = $stmt->get_result();
    if ($registro = $resultados->fetch_assoc()) {
        $IdTecnico = $registro["IdTecnico"];
        $IdPlan = $registro["IdPlan"];
        $XValorMaestra = $registro["Valor"];
        if($registro["TCAcepta"]=="N"){
            $Contrato =  $registro["TCAcepta"];
        }else{
            $Contrato = "S";
        }
    }
    $stmt->close();
    
    // Obtener Plan Contratado
    $PlanConta = 0;
    $PlanRemu = 0;
    $XValor = 0;
    $sql = "SELECT * FROM Sistemas WHERE Id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $IdPlan);
    $stmt->execute();
    $resultados = $stmt->get_result();
    if ($registro = $resultados->fetch_assoc()) {
        $PlanConta = $registro["Conta"];
        $PlanRemu = $registro["Remu"];
        $XValor = $registro["Valor"];
    }
    $stmt->close();

    // $Divide=floor($XValorMaestra/$XValor);

    $_SESSION['PlanConta']=$PlanConta;//*$Divide;
    $_SESSION['PlanRemu']=$PlanRemu;//*$Divide;

	$SQL="SELECT * FROM SistemasUser WHERE IdServer='$IdServer'";
    $resultados = $mysqli->query($SQL);
    $row_cnt = $resultados->num_rows;
    if ($row_cnt>0) {
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            $_SESSION['PlanConta']=$registro['Conta'];
            $_SESSION['PlanRemu']=$registro['Remu'];
        }
    }

    // Obtener información de Tecnicos y almacenar en variables de sesión
    $_SESSION['ServTecnico'] = "";
    $_SESSION['ServTelefono'] = "";
    $_SESSION['ServCorreo'] = "";
    $_SESSION['ServSexo'] = "";
    
    $sql = "SELECT Nombre, Telefono, Correo, Sexo FROM Tecnicos WHERE Id = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $IdTecnico);
    $stmt->execute();
    $resultados = $stmt->get_result();
    if ($registro = $resultados->fetch_assoc()) {
        $_SESSION['ServTecnico'] = $registro["Nombre"];
        $_SESSION['ServTelefono'] = $registro["Telefono"];
        $_SESSION['ServCorreo'] = $registro["Correo"];
        $_SESSION['ServSexo'] = $registro["Sexo"];
    }
    $stmt->close();
    
    // Obtener noticias y almacenar en variable de sesión
    $_SESSION['NOTICIAS'] = array();
    $Indece = 0;
    $sql = "SELECT Fecha, Mensaje, Link FROM Noticias WHERE Sistema = 'Contable' AND Estado = 'A' ORDER BY Id DESC LIMIT 5";
    $resultados = $mysqli->query($sql);
    while ($registro = $resultados->fetch_assoc()) {
        $LisNoticias = array(
            'Fecha' => $registro['Fecha'],
            'Mensaje' => $registro['Mensaje'],
            'Link' => $registro['Link']
        );
        $_SESSION['NOTICIAS'][$Indece] = $LisNoticias;
        $Indece++;
    }
    
    $ConFac = 0;
    $fecha_actual = date("Y-m-d");
    
    // Verificar si existen avisos para la fecha actual
    $sql = "SELECT * FROM Avisos WHERE IdServer = ? AND fecha = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $IdServer, $fecha_actual);
    $stmt->execute();
    $resultados = $stmt->get_result();
    $ContFecha = $resultados->num_rows;
    $stmt->close();
    
    if ($ContFecha == 0) {
        // Limpiar documentos en sesión
        $_SESSION['DOCUMENTOS'] = array();
    
        // Obtener RutFactura
        $RutFactura = "";
        $sql = "SELECT RutFactura FROM Maestra WHERE IdServer = ? LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $IdServer);
        $stmt->execute();
        $resultados = $stmt->get_result();
        if ($registro = $resultados->fetch_assoc()) {
            $RutFactura = $registro['RutFactura'];
        }
        $stmt->close();
    
        $Cadera = "";
        $sql = "SELECT RutFactura FROM FacturasRut WHERE IdServer = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $IdServer);
        $stmt->execute();
        $resultados = $stmt->get_result();
        while ($registro = $resultados->fetch_assoc()) {
            $Cadera .= " OR Rut = '".$registro['RutFactura']."'";
        }
        $stmt->close();

        $ToPe = date('Y-m')."-10";
        // echo $RutFactura;

        if (date('Y-m-d') >= $ToPe) {
            $sql = "SELECT * FROM Facturas WHERE Fecha BETWEEN '2023-01-01' AND ? AND (Rut = ? ".$Cadera.") ORDER BY Fecha DESC";
            // exit;

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $ToPe, $RutFactura);
            $stmt->execute();
            $resultados = $stmt->get_result();
    
            while ($registro = $resultados->fetch_assoc()) {
                $SumTrans = 0;
                $SQL1 = "SELECT SUM(MontoTrans) AS SumTrans FROM FactTrans WHERE IdFactura = ?";
                $stmt1 = $mysqli->prepare($SQL1);
                $stmt1->bind_param("s", $registro["Id"]);
                $stmt1->execute();
                $resultados1 = $stmt1->get_result();
    
                if ($registro1 = $resultados1->fetch_assoc()) {
                    $SumTrans = $registro1["SumTrans"];
                }
                $stmt1->close();
    
                $XTipo = "";
                if ($registro["IdDocumento"] == "34") {
                    $XTipo = "FacExe";
                } 
                if($registro["IdDocumento"] == "33") {
                    $XTipo = "FacAfe";
                } 
                if($registro["IdDocumento"] == "39") {
                    $XTipo = "BolEle";
                } 
                if ($registro["IdDocumento"] == "61") {
                    $XTipo = "NotCre";
                }
    
                $NC = 0;
    
                if ($registro["CnNuRefe"] > 0 && ($registro["CnRefe"] == "34" || $registro["CnRefe"] == "33" || $registro["CnRefe"] == "39")) {
                    $NC = 1;
                } else {
                    $SQL1 = "SELECT * FROM Facturas WHERE CnNuRefe = ? AND (CnRefe = '34' OR CnRefe = '33' OR CnRefe = '39')";
                    $stmt1 = $mysqli->prepare($SQL1);
                    $stmt1->bind_param("s", $registro["Folio"]);
                    $stmt1->execute();
                    $Res = $stmt1->get_result();
                    $NC = $Res->num_rows;
                    $stmt1->close();
                }
    
                if ($SumTrans < $registro["Total"] && $NC == 0) {
                    $ConFac++;
                    $ConLin = count($_SESSION['DOCUMENTOS']);
                    $LCta = array(
                        'Docu' => $registro["Folio"],
                        'TDoc' => $XTipo,
                        'Rut' => $registro["Rut"],
                        'RSocial' => $registro["RSocial"],
                        'Fecha' => $registro["Fecha"],
                        'Monto' => $registro["Total"]
                    );
                    $_SESSION['DOCUMENTOS'][$ConLin] = $LCta;
                }
            }
            $stmt->close();
        }
    
        $_SESSION['DocInpagos'] = $ConFac;
    }
    
    $dia = date('d');
    
    if(isset($_SESSION['DOCUMENTOS'])){
        if (count($_SESSION['DOCUMENTOS']) >= 1 && $dia >= 25) {
            $sql = "INSERT INTO Bloqueos VALUES ('', ?, '".date("Y-m-d")."', 'A')";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $_POST['server']);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    $mysqli->close();


	$server = $_POST['server'];
	$TxtCorreo = $_POST['email'];
	$password = $_POST['pwd'];

	$mysqli = conectarUnion();
	$mysqli->set_charset('utf8mb4');

	$stmt = $mysqli->prepare('SELECT * FROM UnionServer WHERE (Server=?) AND Estado="A"');
	$stmt->bind_param('s', $server);
	$stmt->execute();
	$resultados = $stmt->get_result();
	$NumServer = '';

	if ($resultados->num_rows == 0) {
		$mysqli->close();
		redirectToIndexWithMessage(95);
	}

	while ($registro = $resultados->fetch_assoc()) {
		$NumServer = $registro["Numero"];
		$_SESSION['NomServer'] = $registro["Server"];
		$_SESSION['BaseSV'] = $registro["Base"];
		$_SESSION['UsuariaSV'] = $registro["Usuario"];
		$_SESSION['PassSV'] = randomTextSV(35) . $registro["Clave"] . randomTextSV(8);
	}
	
	$mysqli = xconectar($_SESSION['UsuariaSV'], descriptSV($_SESSION['PassSV']), $_SESSION['BaseSV']);
    // $mysqli = xconectar("root", "", "mastecno_server08");

	$stmt = $mysqli->prepare('SELECT * FROM CTContadores WHERE Correo=? AND Estado="X"');
	$stmt->bind_param('s', $TxtCorreo);
	$stmt->execute();
	$resultados = $stmt->get_result();

	if ($resultados->num_rows > 0) {
		$mysqli->close();
		redirectToIndexWithMessage(55);
	}

	$stmt = $mysqli->prepare('SELECT * FROM CTContadores WHERE Correo=? AND Estado="A"');
	$stmt->bind_param('s', $TxtCorreo);
	$stmt->execute();
	$resultados = $stmt->get_result();

	$XClav = '';
	while ($registro = $resultados->fetch_assoc()) {
		$XClav = $registro['clave'];
		$_SESSION['Xkey'] = $registro['Xkey'] ?? session_id();
		$_SESSION['XId'] = $registro['id'];
		$_SESSION['SECCION'] = session_id();
		$_SESSION['CORREO'] = $TxtCorreo;
		$_SESSION['NOMBRE'] = $registro['nombre'];
		$_SESSION['ROL'] = $registro['tipo'];
		$_SESSION['IDEMPRESA'] = $registro['idempresa'];
		$_SESSION['RAZONSOCIAL'] = '';
		$_SESSION['PERIODO'] = $registro['periodo'];
	}

	if (empty($XClav)) {
		$mysqli->close();
		redirectToIndexWithMessage(95);
	}

	if ($XClav != $password) {
		$mysqli->close();
		redirectToIndexWithMessage(95);
	}

    $_SESSION['CONTRATO'] = $Contrato;
    $_SESSION['IDSERVER'] = $IdServer;

	$stmt = $mysqli->prepare('UPDATE CTContadores SET Xkey=?, ingreso=? WHERE Correo=?');
	$fecha = date('Y-m-d H:i:s');
	$stmt->bind_param('sss', $_SESSION['Xkey'], $fecha, $TxtCorreo);
	$stmt->execute();

    // $_SESSION['loggedin'] = true;
    // $_SESSION['start_time'] = time();

    // $_SESSION['inactive'] = 3;
    // $tipo="TIME_SESI";
    // $sql = "SELECT tipo, valor FROM CTParametros WHERE tipo = ? AND estado = 'A' LIMIT 1";
    // $stmt = $mysqli->prepare($sql);
    // $stmt->bind_param("s", $tipo);
    // $stmt->execute();
    // $resultados = $stmt->get_result();
    // if ($registro = $resultados->fetch_assoc()) {
    //     $_SESSION['inactive'] = $registro["valor"];
    // }
    // $stmt->close();


    $_SESSION['loggedin'] = true;
    
    $_SESSION['inactive'] = 2;

    $tipo="TIME_SESI";
    $sql = "SELECT tipo, valor FROM CTParametros WHERE tipo = ? AND estado = 'A' LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $resultados = $stmt->get_result();
    if ($registro = $resultados->fetch_assoc()) {
        $_SESSION['inactive'] = $registro["valor"];
    }
    $stmt->close();

    $_SESSION['time_off'] = strtotime ( '+'.$_SESSION['inactive'].' hour' , strtotime (date('H:i') ));


	header('Location: frmMain.php');
	die();

	function redirectToIndexWithMessage($messageCode) {
		header('Location: index.php?Msj=' . $messageCode);
		die();
	}