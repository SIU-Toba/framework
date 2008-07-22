<?php 
class pant_test_dims extends toba_ei_pantalla
{
	function generar_layout()
	{
		if( $this->existe_dependencia('form_elegir_sql')) {
			$this->dep('form_elegir_sql')->generar_html();
			echo '<hr />';			
			$this->dep('form_test')->generar_html();
			echo '<hr />';
			$this->mostrar_resultado_pruebas();
		}
	}
	
	function mostrar_resultado_pruebas()
	{
		$pruebas = $this->controlador->get_pruebas_realizadas();
		if($pruebas) {
			echo "<div style='padding: 5px; overflow: auto; height: 400px; width: 600px; text-align: left; background-color: rgb(255, 255, 255); font-size: 11px;'>";
			//Cabecera
			ei_arbol($this->controlador->get_cabecera_prueba());
			
			// Pruebas
			foreach($pruebas as $id => $prueba) {
				echo '<hr />';
				echo '<h1>SQL ' . ($id + 1) . ' (';
				if(isset($prueba['modificado']) && $prueba['modificado'] ) {
					echo toba_recurso::imagen_toba('aplicar.png',true);
				} else {
					echo toba_recurso::imagen_toba('error.png',true);
				}
				echo ')</h1><hr />';
				//ei_arbol($prueba);
				//-- SQL original
				if(isset($prueba['sql_original'])) {
					echo "<div style='white-space: pre; padding: 5px; width: 95%; text-align: left; background-color: rgb(255, 251, 0)'>";
					echo "<strong>SQL original</strong>\n\n";
					echo $prueba['sql_original'];
					echo "</div>";
				}
				//-- SQL modificado
				if(isset($prueba['sql_modificado'])) {
					echo "<div style='white-space: pre; padding: 5px; width: 95%; text-align: left; background-color: rgb(255, 220, 0)'>";
					echo "<strong>SQL Resultante</strong>\n\n";
					echo $prueba['sql_modificado'];
					echo "</div>";
				}
				//--
				if(isset($prueba['gatillos'])) {
					
				}
				//--
				if(isset($prueba['dimensiones'])) {
					
				}
				//--
				if(isset($prueba['where'])) {
					
				}
				//-- CONTAR filas QUERY
				if(isset($prueba['query_filas_orig'])) {
					echo "<br><div style='font-size: 14px; color: white; padding: 2px; width: 350px; text-align: center; background-color: rgb(255, 0, 0)'>";
					echo "Filas Originales: <strong>" . $prueba['query_filas_orig'] . "</strong>";
					echo " - Filas Resultantes: <strong>" . $prueba['query_filas_modif'] . "</strong>";
					echo "</div><br>";

				}
				//-- QUERY
				if(isset($prueba['query_datos_orig'])) {
					$this->tabla($prueba['query_datos_orig'], "QUERY Original");
					echo "<br>";
					$this->tabla($prueba['query_datos_modif'], "QUERY Resultante");
				}
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