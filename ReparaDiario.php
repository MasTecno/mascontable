<?php 
$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL1="SELECT * FROM CTRegLibroDiario WHERE tipo='' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
	$resultados1 = $mysqli->query($SQL1);
	$row_cnt = $resultados1->num_rows;
	if ($row_cnt>0) {

		$SQL="SELECT * FROM CTRegLibroDiario WHERE tipo='' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {

			$Xkeyas=$registro["keyas"];
			$SQL2="SELECT * FROM CTRegLibroDiario WHERE glosa<>'' AND keyas='".$Xkeyas."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados2 = $mysqli->query($SQL2);
			while ($registro2 = $resultados2->fetch_assoc()) {
				$Xtipo=$registro2["tipo"];
			}
			$mysqli->query("UPDATE CTRegLibroDiario SET tipo='$Xtipo' WHERE tipo='' AND keyas='$Xkeyas' AND rutempresa='".$_SESSION['RUTEMPRESA']."'");
			$Xkeyas="";
		}
	}

$mysqli->close();

?>