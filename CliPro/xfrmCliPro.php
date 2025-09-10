<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    if($_POST['idemp']!=""){

		//* Modificar

		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		if($_SESSION['PLAN']=="S"){

			$SQL="SELECT * FROM CTCliProCuenta  WHERE rut='".$_POST['rut']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND tipo='".$_POST['nomfrm']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$mysqli->query("INSERT INTO CTCliProCuenta VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['rut']."','".$_POST['cuenta']."','".$_POST['nomfrm']."','A')");
			}else{
				$mysqli->query("UPDATE CTCliProCuenta SET cuenta='".$_POST['cuenta']."' WHERE rut='".$_POST['rut']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND tipo='".$_POST['nomfrm']."'");
			}

			$mysqli->query("UPDATE CTCliPro SET razonsocial='".$_POST['rsocial']."', direccion='".$_POST['direccion']."', ciudad='".$_POST['ciudad']."', correo='".$_POST['correo']."', giro='".$_POST['giro']."' WHERE id='".$_POST['idemp']."'");
		}else{
			$mysqli->query("UPDATE CTCliPro SET razonsocial='".$_POST['rsocial']."', direccion='".$_POST['direccion']."', ciudad='".$_POST['ciudad']."', correo='".$_POST['correo']."', giro='".$_POST['giro']."', cuenta='".$_POST['cuenta']."' WHERE id='".$_POST['idemp']."'");
		}

		// $row_cnt=0;
		// $SQL="SELECT * FROM CTFondoPersonal WHERE Rut='".$_POST['rut']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
		// $resultados = $mysqli->query($SQL);
		// $row_cnt = $resultados->num_rows;

		// if (isset($_POST['AFondo']) && $row_cnt==0) {
		// 	$mysqli->query("INSERT INTO CTFondoPersonal VALUES('','".$_POST['rut']."','".$_POST['rsocial']."','".$_SESSION['RUTEMPRESA']."','A')");
		// }

		// if (isset($_POST['AFondo']) && $row_cnt>0) {
		// 	$mysqli->query("UPDATE CTFondoPersonal SET Estado='A' WHERE Rut='".$_POST['rut']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'");
		// }

		// if (!isset($_POST['AFondo']) && $row_cnt>0) {
		// 	$mysqli->query("UPDATE CTFondoPersonal SET Estado='B' WHERE Rut='".$_POST['rut']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'");
		// }

		$mysqli->close();
    }else{

		//* Insertar
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTCliPro WHERE rut='".$_POST['rut']."' AND tipo='".$_POST['nomfrm']."'";
		// echo $SQL;
		// exit;
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$mysqli->close();
			header("location:index.php?nomfrm=".$_POST['nomfrm']."&Err=1");
			exit;
		}
		// echo "INSERT INTO CTCliPro VALUES('','".$_POST['rut']."','".$_POST['rsocial']."','".$_POST['direccion']."','".$_POST['ciudad']."','".$_POST['giro']."','".$_POST['correo']."','".$_POST['cuenta']."','".$_POST['nomfrm']."','A')";	
		// exit;
		
		$mysqli->query("INSERT INTO CTCliPro VALUES('','".$_POST['rut']."','".$_POST['rsocial']."','".$_POST['direccion']."','".$_POST['ciudad']."','".$_POST['giro']."','".$_POST['correo']."','".$_POST['cuenta']."','".$_POST['nomfrm']."','A')");


		// id
		// rut
		// razonsocial
		// direccion
		// ciudad
		// giro
		// correo
		// cuenta
		// otroimpuesto
		// tipo
		// estado

		// if (isset($_POST['AFondo'])) {
		// 	$mysqli->query("INSERT INTO CTFondoPersonal VALUES('','".$_POST['rut']."','".$_POST['rsocial']."','".$_SESSION['RUTEMPRESA']."','A')");
		// }
		$mysqli->close();
    }

	header("location:index.php?nomfrm=".$_POST['nomfrm']."");
	exit;
?>