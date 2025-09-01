<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();

		$mysqli=ConCobranza();

		 $SQL="SELECT * FROM Contacto WHERE IdServer='".$_SESSION['xIdServer']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("INSERT INTO Contacto VALUES('','".$_SESSION['xIdServer']."','".$_POST['nombre']."','".$_POST['correo']."','".$_POST['telefono']."','A')");
		}else{
			$mysqli->query("UPDATE Contacto SET Nombre='".$_POST['nombre']."', Correo='".$_POST['correo']."', Telefono='".$_POST['telefono']."', Estado='A' WHERE IdServer='".$_SESSION['xIdServer']."';");
		}

		// if ($_POST['SelDoc']=="E") {
		// 	$Factura="1";
		// }
		// if ($_POST['SelDoc']=="A") {
		// 	$Factura="";
		// }
		// if ($_POST['SelDoc']=="I") {
			$Factura=$_POST['SelDoc'];
		// }

		$SQL="SELECT * FROM Maestra WHERE IdServer='".$_SESSION['xIdServer']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("INSERT INTO Maestra (Id,IdServer,RutFactura,RSocial,Direccion,Comuna,Giro,Telefono,Correo,Exenta,IdPlan,Valor,IdTecnico,FechaReg,Estado)VALUES('','".$_SESSION['xIdServer']."','".$_POST['rut']."','".$_POST['rsocial']."','".$_POST['direccion']."','".$_POST['comuna']."','".$_POST['giro']."','".$_POST['etelefono']."','".$_POST['cenvio']."','".$Factura."','','','1','".date('Y-m-d H:i:s')."','A')");
		}else{

			$RFactura="";
			
			$SQL="SELECT * FROM Maestra WHERE IdServer='".$_SESSION['xIdServer']."'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$RFactura=$registro['RutFactura'];
			}

			$SQL="SELECT * FROM FacturasRut WHERE IdServer='".$_SESSION['xIdServer']."' AND RutFactura='$RFactura'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;

			if($RFactura<>$_POST['rut']){
				if($row_cnt==0){
					if($RFactura!=""){
						$mysqli->query("INSERT INTO FacturasRut VALUES ('','".$_SESSION['xIdServer']."','$RFactura','A');");
					}
				}
			}

			$mysqli->query("
			INSERT INTO Maestra_Resp (IdServer,RutFactura,RSocial,Direccion,Comuna,Giro,Telefono,Correo,Exenta,IdPlan,Valor,FechaReg,Estado)
			SELECT IdServer,RutFactura,RSocial,Direccion,Comuna,Giro,Telefono,Correo,Exenta,IdPlan,Valor,FechaReg,Estado
			FROM Maestra 
			WHERE IdServer='".$_SESSION['xIdServer']."' AND Estado='A';");

			$TTecni=0;
			$SQL="SELECT * FROM Maestra WHERE IdServer='".$_SESSION['xIdServer']."'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$TTecni=$registro['IdTecnico'];
			}

			if ($TTecni==0) {
				$TTecni=1;
			}

			$mysqli->query("UPDATE Maestra SET RutFactura='".$_POST['rut']."', RSocial='".$_POST['rsocial']."', Direccion='".$_POST['direccion']."', Comuna='".$_POST['comuna']."', Giro='".$_POST['giro']."', Telefono='".$_POST['etelefono']."', Correo='".$_POST['cenvio']."', Exenta='".$Factura."', IdTecnico='".$TTecni."', FechaReg='".date('Y-m-d H:i:s')."', Estado='A' WHERE IdServer='".$_SESSION['xIdServer']."';");
		}

// Id,IdServer,RutFactura,RSocial,Dirección,Comuna,Giro,Telefono,Correo,Exenta,IdPlan,Valor,Estado


	// if($_POST['idccosto']!=""){
	// 	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// 	$mysqli->query("UPDATE CTCCosto SET nombre='".$_POST['nombre']."' WHERE id='".$_POST['idccosto']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'");
	// 	$mysqli->close();
	// }else{
	// 	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	// 	$SQL="SELECT * FROM CTCCosto WHERE codigo='".$_POST['codigo']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
	// 	$resultados = $mysqli->query($SQL);
	// 	$row_cnt = $resultados->num_rows;
	// 	if ($row_cnt>0) {
	// 		$mysqli->close();
	// 		header("location:index.php?Err=1");
	// 		exit;
	// 	}

	// 	$mysqli->query("INSERT INTO CTCCosto (id,rutempresa,codigo,nombre,estado) VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['codigo']."','".$_POST['nombre']."','A')");
	// 	$mysqli->close();
	// }

	header("location:../Facturas");
?>