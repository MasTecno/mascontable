<?php 
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

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

    if($_POST['idemp']!=""){
	    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	 	$mysqli->query("UPDATE CTEmpresas SET razonsocial='".$_POST['rsocial']."',rut_representante='".$_POST['rutrep']."',representante='".$_POST['representante']."' ,direccion='".$_POST['direccion']."', ciudad='".$_POST['ciudad']."', giro='".$_POST['giro']."', correo='".$_POST['correo']."', periodo='".$Xperiodo."', comprobante='S', ccosto='S', plan='$TPlanCta' WHERE id='".$_POST['idemp']."'");
	    $mysqli->close();
    }else{
	    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQL="SELECT * FROM CTEmpresas WHERE rut='".$_POST['rut']."'";
		$resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt>0) {
			$mysqli->close();
			header("location:frmEmpresas.php?Err=4");
			exit;
		}else{
			$mysqli->query("INSERT INTO CTEmpresas VALUES('','".$_POST['rsocial']."','".$_POST['rutrep']."','".$_POST['representante']."','".$_POST['rut']."','".$_POST['direccion']."','".$_POST['ciudad']."','".$_POST['correo']."','','".$_POST['giro']."','".$Xperiodo."','S','S','$TPlanCta','A','0')");
	 	}
	    $mysqli->close();    	
    }

    if ($_POST['plancta']=="S") {
	    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_POST['rut']."'";
		$resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt==0) {
			$SQL="SELECT * FROM CTCuentas WHERE estado='A'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
		 		$mysqli->query("INSERT INTO CTCuentasEmpresa VALUES('','".$_POST['rut']."','".$registro['numero']."','".$registro['detalle']."','".$registro['id_categoria']."','".$registro['auxiliar']."','".$registro['ingreso']."','A')");
			}
	 	}
	    $mysqli->close();   
    }

	header("location:frmEmpresas.php");