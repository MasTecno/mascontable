<dir class="col-sm-12">

<?php
    // echo $_POST['t']."<br>------<br>";
    // echo $_POST['ssx']."<br>------<br>";
    
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';

    include 'conexion/secciones.php';

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $cont=1;
		$SQL="SELECT * FROM  CTEmpresas WHERE estado='A' AND razonsocial like '".$_POST['ssx']."%' ORDER BY razonsocial";
	    $resultados = $mysqli->query($SQL);
	    while ($registro = $resultados->fetch_assoc()) {

// echo '
//               <tr>
//                 <td>'.$registro["rut"].'</td>
//                 <td>'.$registro["razonsocial"].'</td>
//                 <td>'.$registro["direccion"].'</td>
//                 <td>'.$registro["giro"].'</td>
//                 <td>'.$registro["periodo"].'</td>
// ';

                if($registro["user"]=="0"){
					$btpanel='<button type="button" class="btn btn-info btn-block" onclick="CargaEmp(\''.randomText(35).''.$registro["id"].''.randomText(8).'\')">Iniciar</button>';
                }else{
					$btpanel='<button type="button" class="btn btn-danger btn-block">En Uso</button>';
                }



			echo'
				<dir class="col-sm-4">
					<div class="panel panel-default">
						<div class="panel-heading">'.$registro["razonsocial"].'</div>
						<div class="panel-body">Rut:'.$registro["rut"].'<br>'.$btpanel.'</div>
					</div>		
				</dir>
			';
			if($cont==3){
				echo '<div class="clearfix"></div>';
				$cont=1;
			}
		}
	$mysqli->close();
?>

<!-- 	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir>
	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir>
	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir>
	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir>
	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir>
	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir>
	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir>
	<dir class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Panel with panel-default class</div>
			<div class="panel-body">Panel Content</div>
		</div>		
	</dir> -->
</dir>

