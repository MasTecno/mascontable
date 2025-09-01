<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if ($_POST['SwInsert']==1 && $_POST['SelCtaDiv']>0 && $_POST['MontDiv']>0) {
		if (!isset($_SESSION['CARRITO'])) {
			$productos=array(
				'SelCtaDiv'=>$_POST['SelCtaDiv'],
				'SelCCDiv'=>$_POST['SelCCDiv'],
				'MontDiv'=>$_POST['MontDiv']
			);
			$_SESSION['CARRITO'][0]=$productos;
		}else{
			// $IdProducto=array_column($_SESSION['CARRITO'],"SelCtaDiv");
			// $mens="";

			// $CCProducto=array_column($_SESSION['CARRITO'],"SelCCDiv");
			// $mens="";

			// if (in_array($_POST['SelCCDiv'],$CCProducto)) {

				
			// }

			// if (in_array($_POST['SelCtaDiv'],$IdProducto)) {
			// 	$mens="<script> alert('Produto ya Ingresado'); </script>";
			// }else{
				$NProductos=count($_SESSION['CARRITO']);
				$productos=array(
					'SelCtaDiv'=>$_POST['SelCtaDiv'],
					'SelCCDiv'=>$_POST['SelCCDiv'],
					'MontDiv'=>$_POST['MontDiv']
				);
				$_SESSION['CARRITO'][$NProductos]=$productos;
			// }
		}
	}

	if (isset($_POST['SwEliDiv']) && $_POST['SwEliDiv']!="") {
		$L=$_POST['SwEliDiv'];
		unset($_SESSION['CARRITO'][$L]);
		$_SESSION['CARRITO']=array_values($_SESSION['CARRITO']);
	}

	$Cont=1;
	$Total=0;
	if (isset($_SESSION['CARRITO'])) {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		foreach($_SESSION['CARRITO'] as $indice=>$producto){
			$NCta=$producto['SelCtaDiv'];
			$NCC=$producto['SelCCDiv'];
			$NomCC="";

			if ($_SESSION["PLAN"]=="S"){
				$Str="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND numero='$NCta'";
			}else{
				$Str="SELECT * FROM CTCuentas WHERE numero='$NCta'";
			}
			$resul = $mysqli->query($Str);
			while ($reg = $resul->fetch_assoc()) {
				$NomCta=$reg["detalle"];
			}

			$Str="SELECT * FROM CTCCosto WHERE id='$NCC'";
			$resul = $mysqli->query($Str);
			while ($reg = $resul->fetch_assoc()) {
				$NomCC=$reg["nombre"];
			}
			echo '
				<tr>
					<td align="center">'.$Cont.'</td>
					<td>'.$producto['SelCtaDiv'].' - '.$NomCta.'</td>
					<td>'.$NomCC.'</td>
					<td align="right">'.number_format($producto['MontDiv'],0,",",".").'</td>
					<td><a href="#" class="btn btn-xs btn-default" onclick="Elimi('.$indice.')"><span class="glyphicon glyphicon-remove-circle"></span> </a></td>
				</tr>
			';
			$Total=$Total+$producto['MontDiv'];
			$Cont++;
		}
		$mysqli->close();
	}else{
		$SwEliDiv=0;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL1="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".descriptSV($_POST['KeyMov'])."'";
		$resultados1 = $mysqli->query($SQL1);
		$row_cnt = $resultados1->num_rows;
		if ($row_cnt>0) {
			$SwEliDiv=1;
			$Str="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".descriptSV($_POST['KeyMov'])."'";
			$resul = $mysqli->query($Str);
			while ($reg = $resul->fetch_assoc()) {

				if (!isset($_SESSION['CARRITO'])) {
					$productos=array(
						'SelCtaDiv'=>$reg['Cuenta'],
						'SelCCDiv'=>$reg['CCosto'],
						'MontDiv'=>$reg['Monto']
					);
					$_SESSION['CARRITO'][0]=$productos;
				}else{
					$NProductos=count($_SESSION['CARRITO']);
					$productos=array(
						'SelCtaDiv'=>$reg['Cuenta'],
						'SelCCDiv'=>$reg['CCosto'],
						'MontDiv'=>$reg['Monto']
					);
					$_SESSION['CARRITO'][$NProductos]=$productos;
				}
			}
		}

		// $mysqli->close();

		// $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		foreach($_SESSION['CARRITO'] as $indice=>$producto){
			$NCta=$producto['SelCtaDiv'];
			$NCC=$producto['SelCCDiv'];
			$NomCC="";

			if ($_SESSION["PLAN"]=="S"){
				$Str="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND numero='$NCta'";
			}else{
				$Str="SELECT * FROM CTCuentas WHERE numero='$NCta'";
			}
			$resul = $mysqli->query($Str);
			while ($reg = $resul->fetch_assoc()) {
				$NomCta=$reg["detalle"];
			}

			$Str="SELECT * FROM CTCCosto WHERE id='$NCC'";
			$resul = $mysqli->query($Str);
			while ($reg = $resul->fetch_assoc()) {
				$NomCC=$reg["nombre"];
			}
			echo '
				<tr>
					<td align="center">'.$Cont.'</td>
					<td>'.$producto['SelCtaDiv'].' - '.$NomCta.'</td>
					<td>'.$NomCC.'</td>
					<td align="right">'.number_format($producto['MontDiv'],0,",",".").'</td>
					<td><a href="#" class="btn btn-xs btn-default" onclick="Elimi('.$indice.')"><span class="glyphicon glyphicon-remove-circle"></span> </a></td>
				</tr>
			';
			$Total=$Total+$producto['MontDiv'];
			$Cont++;
		}
		$mysqli->close();
	}
?>
			<tr>
				<th colspan="2"></th>
				<th>Total</th>
				<th style="text-align:right"><?php echo number_format($Total,0,',','.'); ?>
					<input type="hidden" name="Mtotal" id="Mtotal" value="<?php echo $Total; ?>">
					<?php if ($mens!="") { echo $mens;}?>
				</th>
			</tr>