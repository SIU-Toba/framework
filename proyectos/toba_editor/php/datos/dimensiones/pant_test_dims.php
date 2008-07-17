<?php 
class pant_test_dims extends toba_ei_pantalla
{
	function generar_layout()
	{
		$this->dep('form_elegir_sql')->generar_html();
		echo '<hr />';
		$this->dep('form_test')->generar_html();
		echo '<hr />';
		$this->mostrar_resultado_pruebas();
	}
	
	function mostrar_resultado_pruebas()
	{
		$pruebas = $this->controlador->get_pruebas_realizadas();
		if($pruebas) {
			echo "<div style='padding: 5px; overflow: auto; height: 400px; width: 600px; text-align: left; background-color: rgb(255, 255, 255); font-size: 11px;'>";
			ei_arbol($this->controlador->get_cabecera_prueba());
			foreach($pruebas as $id => $prueba) {
				echo '<hr />';
				echo 'SQL ' . ($id + 1);
				echo '<hr />';
				ei_arbol($prueba);
			}
			echo "</div>";
		}	
	}

	function tabla($tabla,$titulo=null)
	{
		$fila = (current($tabla));
		echo "<div style=' color:black'>\n";
		echo "<table style='BORDER-COLLAPSE: collapse;
							empty-cells: show; 
							background-color: white;
							border: 2px solid black;
							color:black;
							'>\n";
		if($titulo){
			echo "<tr>\n";
			echo "<td style='font-size: 12px;  ' colspan='".(count($fila)+1)."'>$titulo</td>\n";
			echo "</tr>\n";
		}
		//Cuerpo
		if($tabla) {
			echo "<tr>\n";
			echo "<td></td>\n";
			//Titulos de columnas
			$estilo_titulo = "border: 1px solid red; color:red; background-color: yellow; text-align: center;";
			foreach (array_keys($fila) as $titulo){
				echo "<td style='$estilo_titulo'>$titulo</td>\n";
			}
			echo "</tr>\n";
			//Filas
			foreach ($tabla as $id => $fila){
				echo "</tr>\n";
				echo "<td style='$estilo_titulo'>$id</td>\n";
				foreach ($fila as $valor){
					echo "<td style='border: 1px solid gray; padding: 2px;' >$valor</td>\n";
				}
				echo "</tr>\n";
	
			}
		} else {
			echo "<tr>\n";
			echo "<td style='border: 1px solid gray; padding: 2px;' colspan='".(count($fila)+1)."'>No hay DATOS!</td>\n";
			echo "</tr>\n";
		}
		echo "</table>";
		echo "</div>";		
	}
}
?>