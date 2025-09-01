<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    session_start(); 

    if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
      header("location:index.php?Msj=95");
      exit;
    }
    
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    if($Periodo==""){
      header("location:frmMain.php");
      exit;
    }

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

      $d01=0;
      $d02=0;
      $d03=0;
      $d04=0;
      $d05=0;
      $d06=0;
      $d07=0;
      $d08=0;
      $d09=0;
      $d10=0;
      $d11=0;
      $d12=0;

    $SQL="SELECT * FROM CTFactores WHERE periodo='".$_POST['ano']."'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
      $d01=$registro['mes1'];
      $d02=$registro['mes2'];
      $d03=$registro['mes3'];
      $d04=$registro['mes4'];
      $d05=$registro['mes5'];
      $d06=$registro['mes6'];
      $d07=$registro['mes7'];
      $d08=$registro['mes8'];
      $d09=$registro['mes9'];
      $d10=$registro['mes10'];
      $d11=$registro['mes11'];
      $d12=$registro['mes12'];
    }

    $SQL="SELECT * FROM CTParametros WHERE estado='A'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {

      if($registro['tipo']=="SEPA_MILE"){
        $DMILE=$registro['valor'];  
      }

      if($registro['tipo']=="SEPA_DECI"){
        $DDECI=$registro['valor'];  
      }

      if($registro['tipo']=="NUME_DECI"){
        $NDECI=$registro['valor'];  
      } 
    }
        
    
    $mysqli->close();
?>


	<table class="table table-striped table-bordered" width="100%">
	<thead>
		<tr>
			<th width="50%">Mes</th>
			<th width="50%">Valor</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Enero</td>
			<td class="text-right"><?php echo $d01; ?></td>
		</tr>
		<tr>
			<td>Febrero</td>
			<td class="text-right"><?php echo $d02; ?></td>
		</tr>
		<tr>
			<td>Marzo</td>
			<td class="text-right"><?php echo $d03; ?></td>
		</tr>
		<tr>
			<td>Abril</td>
			<td class="text-right"><?php echo $d04; ?></td>
		</tr>
		<tr>
			<td>Mayo</td>
			<td class="text-right"><?php echo $d05; ?></td>
		</tr>
		<tr>
			<td>Junio</td>
			<td class="text-right"><?php echo $d06; ?></td>
		</tr>
		<tr>
			<td>Julio</td>
			<td class="text-right"><?php echo $d07; ?></td>
		</tr>
		<tr>
			<td>Agosto</td>
			<td class="text-right"><?php echo $d08; ?></td>
		</tr>
		<tr>
			<td>Septiembre</td>
			<td class="text-right"><?php echo $d09; ?></td>
		</tr>
		<tr>
			<td>Octubre</td>
			<td class="text-right"><?php echo $d10; ?></td>
		</tr>
		<tr>
			<td>Noviembre</td>
			<td class="text-right"><?php echo $d11; ?></td>
		</tr>
		<tr>
			<td>Diciembre</td>
			<td class="text-right"><?php echo $d12; ?></td>
		</tr>
	</tbody>
	</table>