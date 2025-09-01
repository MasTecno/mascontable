<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';


	// echo $_POST['plancta'];
	// echo "<br>";
	// exit;


    if ($_POST['SeleMes']<=9) {
		$Xperiodo="0".$_POST['SeleMes']."-".$_POST['SeleAno'];
	}else{
		$Xperiodo=$_POST['SeleMes']."-".$_POST['SeleAno'];
    }

	if ($_POST['plancta']==""){
		$TPlanCta="S";
	}else{
		$TPlanCta=$_POST['plancta'];
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$FechaInicio = date('Y-m-d', strtotime($_POST['finicio']));

    if($_POST['idemp']!=""){
		$mysqli->query("UPDATE CTEmpresas SET razonsocial='".$_POST['rsocial']."',rut_representante='".$_POST['rutrep']."',representante='".$_POST['representante']."' ,direccion='".$_POST['direccion']."', ciudad='".$_POST['ciudad']."', giro='".$_POST['giro']."', fechainicio='".$FechaInicio."', correo='".$_POST['correo']."', periodo='".$Xperiodo."', comprobante='S', ccosto='S', plan='$TPlanCta' WHERE id='".$_POST['idemp']."'");
		$m="EmpActCor";
    }else{
		$SQL="SELECT * FROM CTEmpresas WHERE rut='".$_POST['rut']."'";
		$resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt>0) {
			$mysqli->close();
			header("location:./?Err=4");
			exit;
		}else{
			$mysqli->query("INSERT INTO CTEmpresas VALUES('','".$_POST['rsocial']."','".$_POST['rutrep']."','".$_POST['representante']."','".$_POST['rut']."','".$_POST['direccion']."','".$_POST['ciudad']."','".$_POST['correo']."','','".$_POST['giro']."','".$FechaInicio."','".$Xperiodo."','S','S','$TPlanCta','A','0')");
			
			if($_POST['clasii']!=""){
				$Pref=randomTextSV(35);
				$Suf=randomTextSV(8);

				$SqlCP="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_POST['rut']."' AND Estado='A'";
				$Resul = $mysqli->query($SqlCP);
				$row_cnt = $Resul->num_rows;
				if ($row_cnt==0) {
					$mysqli->query("INSERT INTO DTEParametros VALUES('','".$_POST['rut']."','".$_POST['rut']."','".$Pref.$_POST['clasii'].$Suf."','A');");
				}else{
					$SQL="SELECT * FROM DTEParametros WHERE RutEmpresa='".$_POST['rut']."' AND Estado='A' AND RutSii='".$_POST['rut']."' AND PasSii='".$_POST['clasii']."'";
					$Resul = $mysqli->query($SQL);
					$row_cnt = $Resul->num_rows;
					if ($row_cnt==0) {
						$mysqli->query("UPDATE DTEParametros SET RutSii='".$_POST['rut']."', PasSii='".$Pref.$_POST['clasii'].$Suf."' WHERE RutEmpresa='".$_POST['rut']."' AND Estado='A';");
					}
				}
			}
			$m="EmpCreCor";
		}
    }

	// echo $_POST['plancta'];
	// echo "<br>";
	// exit;

    if ($_POST['plancta']=="S") {
		$SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_POST['rut']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$SQL="SELECT * FROM CTCuentas WHERE estado='A'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				// echo "INSERT INTO CTCuentasEmpresa VALUES('','".$_POST['rut']."','".$registro['numero']."','".$registro['detalle']."','".$registro['id_categoria']."','".$registro['auxiliar']."','".$registro['ingreso']."','A')";
				// echo "<br>";
				// exit;

				$mysqli->query("INSERT INTO CTCuentasEmpresa VALUES('','".$_POST['rut']."','".$registro['numero']."','".$registro['detalle']."','".$registro['id_categoria']."','".$registro['auxiliar']."','".$registro['ingreso']."','A')");
			}
		}
    }



	/////Empresa a Remuneraciones

	// $mysqli = conectarRemu();
	// $mysqli->set_charset('utf8mb4');
	// $RemBaseSV = '';
	// $RemUsuariaSV = '';
	// $RemPassSV = '';

	// $stmt = $mysqli->prepare('SELECT * FROM UnionServer WHERE (Server=?) AND Estado="A"');
	// $stmt->bind_param('s', $_SESSION['NomServer']);
	// $stmt->execute();
	// $resultados = $stmt->get_result();
	// $NumServer = '';

	// if ($resultados->num_rows == 0) {
	// 	$mysqli->close();
	// 	// redirectToIndexWithMessage(95);
	// 	$m="Error al crear la empresa en el sistema de Remuneraciones.";
	// }else{
	// 	while ($registro = $resultados->fetch_assoc()) {
	// 		$RemBaseSV = $registro["Base"];
	// 		$RemUsuariaSV = $registro["Usuario"];
	// 		$RemPassSV = randomTextSV(35) . $registro["Clave"] . randomTextSV(8);
	// 	}
	// }


	// if($RemBaseSV!="" && $RemUsuariaSV!="" && $RemPassSV!=""){
	// 	$mysqli = xconectar($RemUsuariaSV, descriptSV($RemPassSV), $RemBaseSV);

	// 	$stmt = $mysqli->prepare('SELECT * FROM RMEmpresas WHERE (rut=?)');
	// 	$stmt->bind_param('s', $_POST['rut']);
	// 	$stmt->execute();
	// 	$resultados = $stmt->get_result();
	// 	$NumServer = '';

	// 	if ($resultados->num_rows == 0) {
	// 		$mysqli->query("INSERT INTO RMEmpresas (rut,razonsocial,rutrepresentante,representante,direccion,ciudad,giro,correo,estado, periodo)
	// 		VALUES('".$_POST['rut']."','".$_POST['rsocial']."','".$_POST['rutrep']."','".$_POST['representante']."','".$_POST['direccion']."','".$_POST['ciudad']."','".$_POST['giro']."','".$_POST['correo']."','A','".$Xperiodo."')");
	// 	}
	// }

	// $mysqli->close();

	header("location:./?Mjs=".$m);